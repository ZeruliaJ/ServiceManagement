<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function create()
    {
        $title = trans('lang.login');
        return view('auth.login', compact('title'));
    }

    public function store(Request $request)
    {
       $credentials = $request->validate([
    'username' => ['required', 'string'],
    'password' => ['required'],
]);

if (Auth::attempt([
    'username' => $request->username,
    'password' => $request->password
], $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'username' => trans('lang.invalid_credentials'),
        ])->onlyInput('username');
    }

    public function destroy(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
