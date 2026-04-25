<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request )
    {
         = ->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt()) {
            ->session()->regenerate();
            return redirect()->intended('/invoices');
        }

        return back()->withErrors([
            'email' => 'Les identifiants ne correspondent pas.',
        ]);
    }

    public function logout(Request )
    {
        Auth::logout();
        ->session()->invalidate();
        ->session()->regenerateToken();
        return redirect('/');
    }
}