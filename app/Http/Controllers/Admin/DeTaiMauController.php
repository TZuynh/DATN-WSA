<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeTaiMau;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\DeTaiMauImport;
use Illuminate\Support\Facades\Log;

class DeTaiMauController extends Controller
{
    public function index()
    {
        $deTaiMaus = DeTaiMau::latest()->paginate(10);
        return view('admin.de-tai-mau.index', compact('deTaiMaus'));
    }

    public function create()
    {
        return view('admin.de-tai-mau.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'ten' => 'required|string|max:255'
        ]);

        DeTaiMau::create($request->all());

        return redirect()->route('admin.de-tai-mau.index')
            ->with('success', 'Mẫu đề tài đã được tạo thành công.');
    }

    public function edit(DeTaiMau $deTaiMau)
    {
        return view('admin.de-tai-mau.edit', compact('deTaiMau'));
    }

    public function update(Request $request, DeTaiMau $deTaiMau)
    {
        $request->validate([
            'ten' => 'required|string|max:255'
        ]);

        $deTaiMau->update($request->all());

        return redirect()->route('admin.de-tai-mau.index')
            ->with('success', 'Mẫu đề tài đã được cập nhật thành công.');
    }

    public function destroy(DeTaiMau $deTaiMau)
    {
        try {
            if ($deTaiMau->deTais()->exists()) {
                return redirect()->route('admin.de-tai-mau.index')
                    ->with('error', 'Không thể xóa mẫu đề tài này vì đang có đề tài đang sử dụng.');
            }

            $deTaiMau->delete();
            return redirect()->route('admin.de-tai-mau.index')
                ->with('success', 'Xóa mẫu đề tài thành công');
        } catch (\Exception $e) {
            Log::error('Lỗi xóa mẫu đề tài: ' . $e->getMessage());
            return redirect()->route('admin.de-tai-mau.index')
                ->with('error', 'Không thể xóa mẫu đề tài. Vui lòng thử lại sau.');
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

            return redirect()->route('admin.de-tai-mau.index')
                ->with('success', 'Dữ liệu đã được nhập thành công!');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errors = [];
            foreach ($failures as $failure) {
                $errors[] = "Dòng {$failure->row()}: {$failure->errors()[0]}";
            }
            return redirect()->route('admin.de-tai-mau.index')
                ->with('error', 'Lỗi validate dữ liệu: ' . implode(', ', $errors));
        } catch (\Exception $e) {
            Log::error('Import error: ' . $e->getMessage());
            return redirect()->route('admin.de-tai-mau.index')
                ->with('error', 'Có lỗi xảy ra khi import file: ' . $e->getMessage());
        }
    }
} 