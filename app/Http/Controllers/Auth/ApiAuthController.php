<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\TaiKhoan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class ApiAuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'mat_khau' => 'required',
        ]);

        $taiKhoan = TaiKhoan::where('email', $request->email)->first();

        if (!$taiKhoan || !Hash::check($request->mat_khau, $taiKhoan->mat_khau)) {
            throw ValidationException::withMessages([
                'email' => ['Thông tin đăng nhập không chính xác.'],
            ]);
        }

        return response()->json([
            'token' => $taiKhoan->createToken('api-token')->plainTextToken,
            'tai_khoan' => [
                'id' => $taiKhoan->id,
                'ten' => $taiKhoan->ten,
                'email' => $taiKhoan->email,
                'vai_tro' => $taiKhoan->vai_tro,
                'created_at' => $taiKhoan->created_at,
                'updated_at' => $taiKhoan->updated_at
            ]
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        
        return response()->json(['message' => 'Đăng xuất thành công']);
    }

    public function user(Request $request)
    {
        $taiKhoan = $request->user();
        return response()->json([
            'id' => $taiKhoan->id,
            'ten' => $taiKhoan->ten,
            'email' => $taiKhoan->email,
            'vai_tro' => $taiKhoan->vai_tro,
            'created_at' => $taiKhoan->created_at,
            'updated_at' => $taiKhoan->updated_at
        ]);
    }
} 