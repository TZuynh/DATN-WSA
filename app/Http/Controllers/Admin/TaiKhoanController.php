<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Imports\TaiKhoanImport;
use App\Models\TaiKhoan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class TaiKhoanController extends Controller
{
    public function index()
    {
        $taikhoans = TaiKhoan::whereIn('vai_tro', ['admin', 'giang_vien'])->get();
        return view('admin.taikhoan.index', compact('taikhoans'));
    }

    public function create()
    {
        return view('admin.taikhoan.create');
    }

    public function store(Request $request)
    {
        $validator = $request->validate([
            'ten' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:tai_khoans',
            'mat_khau' => 'required|string|min:8',
            'vai_tro' => 'required|in:admin,giang_vien,sinh_vien',
        ]);

        TaiKhoan::create([
            'ten' => $request->ten,
            'email' => $request->email,
            'mat_khau' => Hash::make($request->mat_khau),
            'vai_tro' => $request->vai_tro,
        ]);

        return redirect()->route('admin.taikhoan.index')
            ->with('success', 'Tài khoản đã được tạo thành công!');
    }

    public function edit($id)
    {
        $taikhoan = TaiKhoan::findOrFail($id);
        return view('admin.taikhoan.edit', compact('taikhoan'));
    }

    public function update(Request $request, $id)
    {
        $taikhoan = TaiKhoan::findOrFail($id);

        $validator = $request->validate([
            'ten' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:tai_khoans,email,' . $id,
            'vai_tro' => 'required|in:admin,giang_vien,sinh_vien',
        ]);

        $data = [
            'ten' => $request->ten,
            'email' => $request->email,
            'vai_tro' => $request->vai_tro,
        ];

        if ($request->filled('mat_khau')) {
            $data['mat_khau'] = Hash::make($request->mat_khau);
        }

        $taikhoan->update($data);

        return redirect()->route('admin.taikhoan.index')
            ->with('success', 'Tài khoản đã được cập nhật thành công!');
    }

    public function destroy($id)
    {
        $taikhoan = TaiKhoan::findOrFail($id);
        $taikhoan->delete();

        return redirect()->route('admin.taikhoan.index')
            ->with('success', 'Tài khoản đã được xóa thành công!');
    }

    public function import(Request $request)
    {
        try {
            $request->validate([
                'import_file' => 'required|file|mimes:xlsx,xls|max:2048'
            ]);

            if (!$request->hasFile('import_file')) {
                throw new \Exception('Không tìm thấy file upload');
            }

            $file = $request->file('import_file');
            if (!$file->isValid()) {
                throw new \Exception('File upload không hợp lệ');
            }

            $tmpPath = storage_path('app/imports/import_' . time() . '.' . $file->getClientOriginalExtension());
            $file->move(storage_path('app/imports'), basename($tmpPath));

            Excel::import(new TaiKhoanImport, $tmpPath);

            if (file_exists($tmpPath)) {
                unlink($tmpPath);
            }

            return redirect()->route('admin.taikhoan.index')
                ->with('success', 'Dữ liệu đã được nhập thành công!');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errors = [];
            foreach ($failures as $failure) {
                $errors[] = "Dòng {$failure->row()}: {$failure->errors()[0]}";
            }
            return redirect()->route('admin.taikhoan.index')
                ->with('error', 'Lỗi validate dữ liệu: ' . implode(', ', $errors));
        } catch (\Exception $e) {
            Log::error('Import error: ' . $e->getMessage());
            return redirect()->route('admin.taikhoan.index')
                ->with('error', 'Có lỗi xảy ra khi import file: ' . $e->getMessage());
        }
    }
}
