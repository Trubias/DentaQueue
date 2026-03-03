<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class SettingsController extends Controller
{
    public function index()
    {
        // Placeholder for system settings (templates, SMTP, clinic hours)
        return view('admin.settings.index');
    }

    public function updateProfile(Request $request)
    {
        // Conditional validation: allow avatar-only uploads or name/email updates
        $rules = [];
        if ($request->has('name') || $request->has('email')) {
            $rules['name'] = 'required';
            $rules['email'] = 'required|email';
        }
        if ($request->hasFile('avatar')) {
            $rules['avatar'] = 'image|max:2048';
        }

        if (!empty($rules)) {
            $request->validate($rules);
        }

        $user = Auth::user();
        if ($request->has('name')) {
            $user->name = $request->name;
        }
        if ($request->has('email')) {
            $user->email = $request->email;
        }

        // handle avatar upload if present
        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars','public');
            $user->avatar = $path;
        }

        $user->save();

        return redirect()->back()->with('success','Profile updated.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate(['current'=>'required','password'=>'required|confirmed|min:6']);
        $user = Auth::user();
        if (!Hash::check($request->current, $user->password)) {
            return redirect()->back()->with('error','Current password incorrect.');
        }
        $user->password = Hash::make($request->password);
        $user->save();
        return redirect()->back()->with('success','Password updated.');
    }
}
