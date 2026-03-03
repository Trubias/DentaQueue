<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Client\DashboardController;
use App\Http\Controllers\Client\AppointmentController;

Route::middleware(['auth'])->prefix('client')->name('client.')->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('book', [AppointmentController::class, 'create'])->name('book');
    Route::post('book', [AppointmentController::class, 'store'])->name('book.store');
    Route::get('appointments', [AppointmentController::class, 'index'])->name('appointments.index');
    Route::get('appointments/{appointment}/edit', [AppointmentController::class, 'edit'])->name('appointments.edit');
    Route::put('appointments/{appointment}', [AppointmentController::class, 'update'])->name('appointments.update');
    Route::delete('appointments/{appointment}', [AppointmentController::class, 'destroy'])->name('appointments.destroy');
    Route::post('appointments/{appointment}/done', [AppointmentController::class, 'done'])->name('appointments.done');
    Route::get('settings', [\App\Http\Controllers\Client\SettingsController::class, 'index'])->name('settings.index');
    Route::post('settings/profile', [\App\Http\Controllers\Client\SettingsController::class, 'updateProfile'])->name('settings.profile.update');
    Route::post('settings/password', [\App\Http\Controllers\Client\SettingsController::class, 'updatePassword'])->name('settings.password.update');
    // Notifications / Inbox
    Route::get('notifications', [\App\Http\Controllers\Client\NotificationController::class, 'index'])->name('notifications');
    Route::get('notifications/{announcement}', [\App\Http\Controllers\Client\NotificationController::class, 'show'])->name('notifications.show');
    Route::post('notifications/clear', [\App\Http\Controllers\Client\NotificationController::class, 'clear'])->name('notifications.clear');
    // Settings: test email
    Route::post('settings/test-email', [\App\Http\Controllers\Client\SettingsController::class, 'sendTestEmail'])->name('settings.test_email');
});
