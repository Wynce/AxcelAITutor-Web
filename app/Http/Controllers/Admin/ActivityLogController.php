<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\ActivityLogService;
use App\Models\AdminActivityLog;

class ActivityLogController extends Controller
{
    protected $activityLogService;

    public function __construct(ActivityLogService $activityLogService)
    {
        $this->activityLogService = $activityLogService;
    }

    /**
     * Show activity logs
     */
    public function index(Request $request)
    {
        $filters = [
            'admin_id' => $request->input('admin_id'),
            'action' => $request->input('action'),
            'model_type' => $request->input('model_type'),
            'date_from' => $request->input('date_from'),
            'date_to' => $request->input('date_to'),
            'per_page' => $request->input('per_page', 50),
        ];

        $data = [];
        $data['pageTitle'] = "Activity Logs";
        $data['current_module_name'] = "Activity Logs";
        $data['module_name'] = "Activity Logs";
        $data['module_url'] = route('admin.activity-logs.index');
        $data['logs'] = $this->activityLogService->getLogs($filters);
        $data['filters'] = $filters;
        $data['admins'] = \App\Admin::select('id', 'name')->get();
        $data['actions'] = AdminActivityLog::distinct()->pluck('action')->toArray();

        return view('Admin/ActivityLogs/index', $data);
    }
}

