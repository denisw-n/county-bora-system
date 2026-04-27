<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;

class AuditLogController extends Controller
{
    public function index()
    {
        // Fetch logs with the admin (user) who performed the action
        $logs = AuditLog::with('admin')->latest()->paginate(20);
        return view('admin.logs.index', compact('logs'));
    }
}