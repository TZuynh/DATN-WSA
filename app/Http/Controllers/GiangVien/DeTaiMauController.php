<?php

namespace App\Http\Controllers\GiangVien;

use App\Http\Controllers\Controller;
use App\Models\DeTaiMau;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\DeTaiMauImport;

class DeTaiMauController extends Controller
{
    public function index()
    {
        $deTaiMaus = DeTaiMau::all();
        return view('giangvien.de-tai-mau.index', compact('deTaiMaus'));
    }

    public function create()
    {
        return view('giangvien.de-tai-mau.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'ten' => 'required|string|max:255',
            'mo_ta' => 'nullable|string'
        ]);

        try {
            DeTaiMau::create($request->all());
            return redirect()->route('giangvien.de-tai-mau.index')->with('success', 'Thêm mẫu đề tài thành công');
        } catch (\Exception $e) {
            Log::error('Lỗi khi thêm mẫu đề tài: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi thêm mẫu đề tài');
        }
    }

    public function edit(DeTaiMau $deTaiMau)
    {
        return view('giangvien.de-tai-mau.edit', compact('deTaiMau'));
    }

    public function update(Request $request, DeTaiMau $deTaiMau)
    {
        $request->validate([
            'ten' => 'required|string|max:255',
            'mo_ta' => 'nullable|string'
        ]);

        try {
            $deTaiMau->update($request->all());
            return redirect()->route('giangvien.de-tai-mau.index')->with('success', 'Cập nhật mẫu đề tài thành công');
        } catch (\Exception $e) {
            Log::error('Lỗi khi cập nhật mẫu đề tài: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi cập nhật mẫu đề tài');
        }
    }

    public function destroy(DeTaiMau $deTaiMau)
    {
        try {
            // Kiểm tra xem mẫu đề tài có đang được sử dụng không
            if ($deTaiMau->deTais()->exists()) {
                return redirect()->back()->with('error', 'Không thể xóa mẫu đề tài này vì đang được sử dụng');
            }

            $deTaiMau->delete();
            return redirect()->route('giangvien.de-tai-mau.index')->with('success', 'Xóa mẫu đề tài thành công');
        } catch (\Exception $e) {
            Log::error('Lỗi khi xóa mẫu đề tài: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi xóa mẫu đề tài');
        }
    }

    public function import(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|file|mimes:xlsx,xls|max:2048'
            ]);

            if (!$request->hasFile('file')) {
                throw new \Exception('Không tìm thấy file upload');
            }

            $file = $request->file('file');
            if (!$file->isValid()) {
                throw new \Exception('File upload không hợp lệ');
            }

            $tmpPath = storage_path('app/imports/import_' . time() . '.' . $file->getClientOriginalExtension());
            $file->move(storage_path('app/imports'), basename($tmpPath));

            Excel::import(new DeTaiMauImport, $tmpPath);

            if (file_exists($tmpPath)) {
                unlink($tmpPath);
            }

            return redirect()->route('giangvien.de-tai-mau.index')
                ->with('success', 'Dữ liệu đã được nhập thành công!');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errors = [];
            foreach ($failures as $failure) {
                $errors[] = "Dòng {$failure->row()}: {$failure->errors()[0]}";
            }
            return redirect()->route('giangvien.de-tai-mau.index')
                ->with('error', 'Lỗi validate dữ liệu: ' . implode(', ', $errors));
        } catch (\Exception $e) {
            Log::error('Import error: ' . $e->getMessage());
            return redirect()->route('giangvien.de-tai-mau.index')
                ->with('error', 'Có lỗi xảy ra khi import file: ' . $e->getMessage());
        }
    }
} 