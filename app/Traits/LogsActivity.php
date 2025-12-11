<?php

namespace App\Traits;

use App\Services\ActivityLogService;

trait LogsActivity
{
    /**
     * Get the activity log service
     */
    protected function getActivityLogService()
    {
        return app(ActivityLogService::class);
    }

    /**
     * Log activity
     */
    protected function logActivity($action, $model = null, $description = null, $changes = null)
    {
        return $this->getActivityLogService()->log($action, $model, $description, $changes);
    }

    /**
     * Log created activity
     */
    protected function logCreated($model, $description = null)
    {
        return $this->getActivityLogService()->logCreated($model, $description);
    }

    /**
     * Log updated activity
     */
    protected function logUpdated($model, $oldData, $newData, $description = null)
    {
        return $this->getActivityLogService()->logUpdated($model, $oldData, $newData, $description);
    }

    /**
     * Log deleted activity
     */
    protected function logDeleted($model, $description = null)
    {
        return $this->getActivityLogService()->logDeleted($model, $description);
    }

    /**
     * Log status change activity
     */
    protected function logStatusChange($model, $oldStatus, $newStatus, $description = null)
    {
        return $this->getActivityLogService()->logStatusChange($model, $oldStatus, $newStatus, $description);
    }
}

