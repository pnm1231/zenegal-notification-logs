<?php

namespace Zenegal\NotificationLogs\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Zenegal\NotificationLogs\Models\NotificationLog;

class NotificationLogController extends Controller
{
    public function index(Request $request)
    {
        $page_title = 'Notification Logs';

        $filters = $request->validate([
            'sent_status' => ['nullable', 'string', 'in:sent,not_sent'],
            'limit' => ['nullable', 'integer', 'min:10', 'max:1000'],
        ]);

        $notificationLogs = NotificationLog::latest()->filter($filters)->paginate(10);

        return view('notification-logs::index', compact('page_title', 'notificationLogs'));
    }
}
