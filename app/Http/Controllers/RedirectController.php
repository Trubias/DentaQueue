<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class RedirectController extends Controller
{
    public function index()
    {
        if (Auth::user()->role === 'admin') {
            return redirect('/admin/dashboard');
        } else {
            return redirect('/client/dashboard');
        }
    }
}