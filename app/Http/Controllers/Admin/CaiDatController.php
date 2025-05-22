<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;

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
        ]);

        $data = $request->only(['thoi_gian_timeout']);

        foreach ($data as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        // Cập nhật cấu hình hệ thống
        config(['session.lifetime' => $data['thoi_gian_timeout']]);

        // Lưu cấu hình vào file config
        $this->updateConfigFile('session', ['lifetime' => $data['thoi_gian_timeout']]);

        // Cập nhật session hiện tại
        Session::put('settings', array_merge(Session::get('settings', []), $data));
        Session::put('lifetime', $data['thoi_gian_timeout']);

        // Làm mới session
        Session::regenerate();

        return redirect()->back()->with('success', 'Cập nhật cài đặt bảo mật thành công');
    }

    private function updateConfigFile($configName, $data)
    {
        $path = config_path($configName . '.php');
        if (file_exists($path)) {
            $config = require $path;
            $config = array_merge($config, $data);
            $content = '<?php return ' . var_export($config, true) . ';';
            file_put_contents($path, $content);
        }
    }
} 