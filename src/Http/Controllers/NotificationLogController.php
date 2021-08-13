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

        $models = NotificationLog::select('model')->whereNotNull('model')->groupBy('model')->get();

        $mailableNames = NotificationLog::select('mailable_name')->whereNotNull('mailable_name')->groupBy('mailable_name')->get();

        $filters = $request->validate([
            'object' => ['nullable', 'string', 'in:'.implode(', ', $models->pluck('model')->toArray())],
            'object_id' => ['nullable', 'integer', 'min:1'],
            'notification_type' => ['nullable', 'string', 'in:'.implode(',', $mailableNames->pluck('mailable_name')->toArray())],
            'sent_status' => ['nullable', 'string', 'in:sent,not_sent'],
            'limit' => ['nullable', 'integer', 'min:10', 'max:1000'],
        ]);

        $notificationLogs = NotificationLog::latest()->filter($filters)->paginate(10);

        return view('notification-logs::index', compact('page_title', 'models', 'mailableNames', 'notificationLogs'));
    }
}
