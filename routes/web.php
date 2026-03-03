<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RedirectController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('redirect');
    }

    return redirect()->route('login');
});

/*
|--------------------------------------------------------------------------
| Redirect after login (role-based)
|--------------------------------------------------------------------------
*/
Route::get('/redirect', [RedirectController::class, 'index'])
    ->middleware(['auth'])
    ->name('redirect');

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
// Admin routes are defined in routes/admin.php (included below)

/*
|--------------------------------------------------------------------------
| Auth Routes (Breeze)
|--------------------------------------------------------------------------
*/
require __DIR__.'/auth.php';
require __DIR__.'/admin.php';
require __DIR__.'/client.php';