<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Imports\TaiKhoanImport;
use App\Models\TaiKhoan;
use Illuminate\Http\Request;
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

            // Lưu file tạm bằng move
            $tmpPath = storage_path('app/imports/import_' . time() . '.' . $file->getClientOriginalExtension());
            $file->move(storage_path('app/imports'), basename($tmpPath));

            // Import từ file đã lưu
            \Maatwebsite\Excel\Facades\Excel::import(new \App\Imports\TaiKhoanImport, $tmpPath);

            // Xóa file tạm sau khi import
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
            \Log::error('Import error: ' . $e->getMessage());
            return redirect()->route('admin.taikhoan.index')
                ->with('error', 'Có lỗi xảy ra khi import file: ' . $e->getMessage());
        }
    }
}
