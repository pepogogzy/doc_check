<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $query = AuditLog::with('user')->latest();

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('entity_type')) {
            $query->where('entity_type', $request->entity_type);
        }

        $logs = $query->paginate(30);

        $actions = AuditLog::select('action')->distinct()->pluck('action');
        $entityTypes = AuditLog::select('entity_type')->distinct()->pluck('entity_type');

        return view('audit-logs.index', compact('logs', 'actions', 'entityTypes'));
    }
}
