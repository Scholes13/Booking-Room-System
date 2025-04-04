<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    /**
     * Display a listing of the activity logs.
     */
    public function index(Request $request)
    {
        $query = ActivityLog::with('user')
            ->orderBy('created_at', 'desc');
        
        // Filter by user if specified
        if ($request->has('user_id') && !empty($request->user_id)) {
            $query->where('user_id', $request->user_id);
        }
        
        // Filter by action if specified
        if ($request->has('action') && !empty($request->action)) {
            $query->where('action', $request->action);
        }
        
        // Filter by module if specified
        if ($request->has('module') && !empty($request->module)) {
            $query->where('module', $request->module);
        }
        
        // Filter by date range
        if ($request->has('date_from') && !empty($request->date_from)) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->has('date_to') && !empty($request->date_to)) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        // Search in description
        if ($request->has('search') && !empty($request->search)) {
            $query->where('description', 'like', '%' . $request->search . '%');
        }
        
        $logs = $query->paginate(15);
        $admins = User::where('role', 'admin')->orderBy('name')->get();
        
        // Get unique action types and modules for filter dropdowns
        $actions = ActivityLog::distinct()->pluck('action');
        $modules = ActivityLog::distinct()->pluck('module');
        
        return view('superadmin.logs.index', compact('logs', 'admins', 'actions', 'modules'));
    }
    
    /**
     * Export activity logs to CSV.
     */
    public function export(Request $request)
    {
        $query = ActivityLog::with('user');
        
        // Apply filters similarly to index method
        if ($request->has('user_id') && !empty($request->user_id)) {
            $query->where('user_id', $request->user_id);
        }
        
        if ($request->has('action') && !empty($request->action)) {
            $query->where('action', $request->action);
        }
        
        if ($request->has('module') && !empty($request->module)) {
            $query->where('module', $request->module);
        }
        
        if ($request->has('date_from') && !empty($request->date_from)) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->has('date_to') && !empty($request->date_to)) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        $logs = $query->orderBy('created_at', 'desc')->get();
        
        $filename = 'activity_logs_' . date('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];
        
        $columns = ['ID', 'User', 'Action', 'Module', 'Description', 'Created At'];
        
        $callback = function() use($logs, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            
            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->id,
                    $log->user->name ?? 'Unknown',
                    $log->action,
                    $log->module,
                    $log->description,
                    $log->created_at->format('Y-m-d H:i:s'),
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
    
    /**
     * Show the form for viewing details of the specified log.
     */
    public function show($id)
    {
        $log = ActivityLog::with('user')->findOrFail($id);
        return view('superadmin.logs.show', compact('log'));
    }
}
