<?php

namespace App\Http\Controllers\GiangVien;

use App\Http\Controllers\Controller;
use App\Http\Requests\GiangVien\ChangePasswordRequest;
use App\Models\TaiKhoan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function index()
    {
        $user = TaiKhoan::find(Auth::id());
        return view('giangvien.profile.index', compact('user'));
    }

    public function changePassword(ChangePasswordRequest $request)
    {
        $user = TaiKhoan::find(Auth::id());
        
        // Cập nhật mật khẩu mới sử dụng trường mat_khau
        $user->mat_khau = Hash::make($request->new_password);
        $user->save();

        return redirect()->route('giangvien.profile')
            ->with('success', 'Đổi mật khẩu thành công!');
    }
} 