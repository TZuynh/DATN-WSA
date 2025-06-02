<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;

class CommonAuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.common-login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'mat_khau' => ['required'],
        ]);

        $user = \App\Models\TaiKhoan::where('email', $credentials['email'])->first();

        if ($user && Hash::check($credentials['mat_khau'], $user->mat_khau)) {
            Auth::login($user);

            $request->session()->regenerate();

            if ($user->vai_tro === 'admin') {
                return redirect('http://admin.project.test/admin/dashboard');
            } elseif ($user->vai_tro === 'giang_vien') {
                return redirect('http://giangvien.project.test/giang-vien/dashboard');
            }

            return redirect('/');
        }
        return back()
            ->withInput($request->only('email'))
            ->withErrors([
                'email' => 'Thông tin đăng nhập không chính xác.',
            ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
