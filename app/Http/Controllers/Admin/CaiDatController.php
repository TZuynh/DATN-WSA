<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;

class CaiDatController extends Controller
{
    public function index()
    {
        $settings = Setting::pluck('value', 'key')->toArray();
        return view('admin.cai-dat.index', compact('settings'));
    }

    public function updateGeneral(Request $request)
    {
        $request->validate([
            'theme' => 'required|string|in:light,dark',
        ]);

        $data = $request->only(['theme']);

        foreach ($data as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        // Lưu cài đặt vào session
        session(['settings' => $data]);

        return redirect()->back()->with('success', 'Cập nhật cài đặt chung thành công');
    }

    public function updateSecurity(Request $request)
    {
        $request->validate([
            'thoi_gian_timeout' => 'required|integer|min:1|max:120',
            'so_lan_dang_nhap_sai_toi_da' => 'required|integer|min:1|max:10',
        ]);

        $data = $request->only(['thoi_gian_timeout', 'so_lan_dang_nhap_sai_toi_da']);


        foreach ($data as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        // Lưu cài đặt vào session
        session(['settings' => array_merge(session('settings', []), $data)]);

        return redirect()->back()->with('success', 'Cập nhật cài đặt bảo mật thành công');
    }
} 