<?php

use Illuminate\Support\Facades\Route;
use Zenegal\NotificationLogs\Http\Controllers\NotificationLogController;

Route::middleware([
    'web',
    'auth',
    'admin.check_selected_store',
    'apply_selected_store_settings',
    'check_permissions',
    'admin.apply_store_restrictions'
])->group(function () {
    Route::resource('notification-logs', NotificationLogController::class)->names([
        'index' => 'notification-logs',
    ]);
});
