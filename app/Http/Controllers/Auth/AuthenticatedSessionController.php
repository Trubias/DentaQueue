<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        // Ensure the admin account exists (helpful for local/dev environments)
        $adminEmail = 'jaywarrentrubias2019@gmail.com';
        $adminPassword = '123';

        try {
            $userModel = config('auth.providers.users.model');
            $existing = $userModel::where('email', $adminEmail)->first();
            if (! $existing) {
                $userModel::create([
                    'name' => 'Administrator',
                    'email' => $adminEmail,
                    'age' => 21,
                    'sex' => 'Male',
                    'password' => \Illuminate\Support\Facades\Hash::make($adminPassword),
                    'role' => 'admin',
                ]);
            }
        } catch (\Exception $e) {
            // ignore — seeding may not be available in some environments
        }

        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     *
     * @param  \App\Http\Requests\Auth\LoginRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(LoginRequest $request)
    {
        $request->authenticate();

        $request->session()->regenerate();

        return redirect()->intended(RouteServiceProvider::HOME);
    }

    /**
     * Destroy an authenticated session.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
