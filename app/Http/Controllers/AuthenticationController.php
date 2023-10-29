<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request; 
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;

class AuthenticationController extends Controller
{
    public function index()
    {
        return view('auth.login');
    }

    public function authenticate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => ['required'],
            'password' => ['required'],
        ], [
            '*.required' => "Harap isi kolom :attribute",
        ], [
            'username' => "Nama pengguna",
            'password' => "Password",
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->onlyInput('username');
        }

        if (Auth::attempt(['username' => $request->username, 'password' => $request->password])) {
            $request->session()->regenerate();
            if (Auth::user()->role->nama == "kasir") {
                return redirect()->to('/kasir');
            }
            return redirect()->to('/');
        }

        return redirect()->back()->with('error_login', "Nama pengguna atau Password salah")->onlyInput('username');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
