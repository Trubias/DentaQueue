<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    // Also accept /admin/dashboard URL for convenience
    Route::get('dashboard', [DashboardController::class, 'index']);
    // Users management
    Route::get('users', [\App\Http\Controllers\Admin\UsersController::class, 'index'])->name('users.index');
    Route::delete('users/{user}', [\App\Http\Controllers\Admin\UsersController::class, 'destroy'])->name('users.destroy');
    // View and manage a user's appointments
    Route::get('users/{user}/appointments', [\App\Http\Controllers\Admin\UsersController::class, 'appointments'])->name('users.appointments');
    Route::post('users/{user}/appointments/{appointment}/done', [\App\Http\Controllers\Admin\UsersController::class, 'appointmentDone'])->name('users.appointments.done');
    Route::get('queue', [\App\Http\Controllers\Admin\QueueController::class, 'index'])->name('queue.index');
    Route::get('queue/{appointment}', [\App\Http\Controllers\Admin\QueueController::class, 'show'])->name('queue.show');
    Route::post('queue/{appointment}/assign', [\App\Http\Controllers\Admin\QueueController::class, 'assign'])->name('queue.assign');
    Route::post('queue/{appointment}/requeue', [\App\Http\Controllers\Admin\QueueController::class, 'requeue'])->name('queue.requeue');
    Route::post('queue/{appointment}/remind', [\App\Http\Controllers\Admin\QueueController::class, 'remind'])->name('queue.remind');
    Route::post('queue/{appointment}/delete', [\App\Http\Controllers\Admin\QueueController::class, 'destroy'])->name('queue.delete');
    
    // Schedule / Calendar
    Route::get('schedule', [\App\Http\Controllers\Admin\ScheduleController::class, 'index'])->name('schedule.index');
    Route::get('schedule/events', [\App\Http\Controllers\Admin\ScheduleController::class, 'events'])->name('schedule.events');
    Route::post('schedule/events', [\App\Http\Controllers\Admin\ScheduleController::class, 'store'])->name('schedule.events.store');
    Route::post('schedule/events/{appointment}/move', [\App\Http\Controllers\Admin\ScheduleController::class, 'updateEvent'])->name('schedule.events.move');
    Route::post('schedule/events/{appointment}/delete', [\App\Http\Controllers\Admin\ScheduleController::class, 'destroyEvent'])->name('schedule.events.delete');

    // Announcements
    Route::get('announcements', [\App\Http\Controllers\Admin\AnnouncementController::class, 'index'])->name('announcements.index');
    Route::get('announcements/create', [\App\Http\Controllers\Admin\AnnouncementController::class, 'create'])->name('announcements.create');
    Route::post('announcements', [\App\Http\Controllers\Admin\AnnouncementController::class, 'store'])->name('announcements.store');
    Route::post('announcements/{announcement}/send', [\App\Http\Controllers\Admin\AnnouncementController::class, 'send'])->name('announcements.send');
    Route::delete('announcements/{announcement}', [\App\Http\Controllers\Admin\AnnouncementController::class, 'destroy'])->name('announcements.destroy');
    // Mail test (admin only) - attempt to send a test email using current mail settings
    Route::post('mail-test', [\App\Http\Controllers\Admin\AnnouncementController::class, 'mailTest'])->name('mail.test');
    // Inventory and exports
    Route::get('announcements/inventory', [\App\Http\Controllers\Admin\AnnouncementController::class, 'inventory'])->name('announcements.inventory');
    Route::get('announcements/inventory/export-appointments', [\App\Http\Controllers\Admin\AnnouncementController::class, 'exportAppointments'])->name('announcements.inventory.exportAppointments');

    // Reports
    Route::get('reports', [\App\Http\Controllers\Admin\ReportsController::class, 'index'])->name('reports.index');
    Route::get('reports/export', [\App\Http\Controllers\Admin\ReportsController::class, 'exportCsv'])->name('reports.export');
    Route::get('reports/export/pdf', [\App\Http\Controllers\Admin\ReportsController::class, 'exportPdf'])->name('reports.export.pdf');

    // Settings
    Route::get('settings', [\App\Http\Controllers\Admin\SettingsController::class, 'index'])->name('settings.index');
    Route::post('settings/profile', [\App\Http\Controllers\Admin\SettingsController::class, 'updateProfile'])->name('settings.profile.update');
    Route::post('settings/password', [\App\Http\Controllers\Admin\SettingsController::class, 'updatePassword'])->name('settings.password.update');
});
