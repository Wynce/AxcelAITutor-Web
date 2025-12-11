<?php

namespace App\Services;

use App\Models\AdminActivityLog;
use Illuminate\Support\Facades\Auth;

class ActivityLogService
{
    /**
     * Log an admin activity
     */
    public function log($action, $model = null, $description = null, $changes = null)
    {
        $admin = Auth::guard('admin')->user();
        
        $data = [
            'admin_id' => $admin ? $admin->id : null,
            'action' => $action,
            'description' => $description ?? $this->generateDescription($action, $model),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ];

        if ($model) {
            $data['model_type'] = get_class($model);
            $data['model_id'] = $model->id;
        }

        if ($changes) {
            $data['changes'] = $changes;
        }

        return AdminActivityLog::create($data);
    }

    /**
     * Log a created action
     */
    public function logCreated($model, $description = null)
    {
        return $this->log('created', $model, $description);
    }

    /**
     * Log an updated action
     */
    public function logUpdated($model, $oldData, $newData, $description = null)
    {
        $changes = [
            'before' => $oldData,
            'after' => $newData,
        ];

        return $this->log('updated', $model, $description, $changes);
    }

    /**
     * Log a deleted action
     */
    public function logDeleted($model, $description = null)
    {
        return $this->log('deleted', $model, $description);
    }

    /**
     * Log a status change
     */
    public function logStatusChange($model, $oldStatus, $newStatus, $description = null)
    {
        $changes = [
            'status' => [
                'from' => $oldStatus,
                'to' => $newStatus,
            ],
        ];

        return $this->log('status_changed', $model, $description, $changes);
    }

    /**
     * Generate a description from action and model
     */
    private function generateDescription($action, $model = null)
    {
        if (!$model) {
            return ucfirst($action);
        }

        $modelName = class_basename($model);
        
        return ucfirst($action) . ' ' . $modelName . ' #' . $model->id;
    }

    /**
     * Get activity logs with filters
     */
    public function getLogs($filters = [])
    {
        $query = AdminActivityLog::with('admin')->latest();

        if (isset($filters['admin_id'])) {
            $query->where('admin_id', $filters['admin_id']);
        }

        if (isset($filters['action'])) {
            $query->where('action', $filters['action']);
        }

        if (isset($filters['model_type'])) {
            $query->where('model_type', $filters['model_type']);
        }

        if (isset($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        return $query->paginate($filters['per_page'] ?? 50);
    }
}

