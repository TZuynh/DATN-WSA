<?php

namespace App\Http\Controllers\GiangVien;

use App\Http\Controllers\Controller;
use App\Models\Lop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Imports\LopImport;
use Maatwebsite\Excel\Facades\Excel;

class LopController extends Controller
{
    public function index()
    {
        $lops = Lop::withCount('sinhViens')->paginate(10);
        return view('giangvien.lop.index', compact('lops'));
    }

    public function create()
    {
        return view('giangvien.lop.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ten_lop' => 'required|string|max:50|unique:lops,ten_lop',
        ], [
            'ten_lop.required' => 'Tên lớp không được để trống',
            'ten_lop.max' => 'Tên lớp không được vượt quá 50 ký tự',
            'ten_lop.unique' => 'Tên lớp đã tồn tại',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                        ->withErrors($validator)
                        ->withInput();
        }

        Lop::create($request->only(['ten_lop']));

        return redirect()->route('giangvien.lop.index')
            ->with('success', 'Lớp đã được thêm thành công!');
    }

    public function edit(Lop $lop)
    {
        return view('giangvien.lop.edit', compact('lop'));
    }

    public function update(Request $request, Lop $lop)
    {
        $validator = Validator::make($request->all(), [
            'ten_lop' => 'required|string|max:50|unique:lops,ten_lop,' . $lop->id,
        ], [
            'ten_lop.required' => 'Tên lớp không được để trống',
            'ten_lop.max' => 'Tên lớp không được vượt quá 50 ký tự',
            'ten_lop.unique' => 'Tên lớp đã tồn tại',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                        ->withErrors($validator)
                        ->withInput();
        }

        $lop->update($request->only(['ten_lop']));

        return redirect()->route('giangvien.lop.index')
            ->with('success', 'Lớp đã được cập nhật thành công!');
    }

    public function destroy(Lop $lop)
    {
        try {
            if ($lop->sinhViens()->count() > 0) {
                return redirect()->route('giangvien.lop.index')
                    ->with('error', 'Không thể xóa lớp này vì đang có sinh viên thuộc lớp.');
            }

            $lop->delete();
            return redirect()->route('giangvien.lop.index')
                ->with('success', 'Lớp đã được xóa thành công!');
        } catch (\Exception $e) {
            Log::error('Delete Lop error: ' . $e->getMessage());
            return redirect()->route('giangvien.lop.index')
                ->with('error', 'Có lỗi xảy ra khi xóa lớp: ' . $e->getMessage());
        }
    }

    public function bulkDelete(Request $request)
    {
        $selectedLops = $request->input('selected_lops', []);

        if (empty($selectedLops)) {
            return redirect()->route('giangvien.lop.index')
                ->with('error', 'Vui lòng chọn ít nhất một lớp để xóa.');
        }

        try {
            $lops = Lop::whereIn('id', $selectedLops);
            
            // Kiểm tra xem có lớp nào đang có sinh viên không
            foreach ($lops->get() as $lop) {
                if ($lop->sinhViens()->count() > 0) {
                    return redirect()->route('giangvien.lop.index')
                        ->with('error', 'Không thể xóa lớp ' . $lop->ten_lop . ' vì đang có sinh viên thuộc lớp.');
                }
            }

            $deletedCount = $lops->delete();

            if ($deletedCount > 0) {
                return redirect()->route('giangvien.lop.index')
                    ->with('success', "Đã xóa thành công {$deletedCount} lớp.");
            } else {
                return redirect()->route('giangvien.lop.index')
                    ->with('error', 'Không tìm thấy lớp nào để xóa.');
            }
        } catch (\Exception $e) {
            Log::error('Bulk delete Lop error: ' . $e->getMessage());
            return redirect()->route('giangvien.lop.index')
                ->with('error', 'Có lỗi xảy ra khi xóa lớp: ' . $e->getMessage());
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

            Excel::import(new LopImport, $tmpPath);

            if (file_exists($tmpPath)) {
                unlink($tmpPath);
            }

            return redirect()->route('giangvien.lop.index')
                ->with('success', 'Dữ liệu đã được nhập thành công!');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errors = [];
            foreach ($failures as $failure) {
                $errors[] = "Dòng {$failure->row()}: {$failure->errors()[0]}";
            }
            return redirect()->route('giangvien.lop.index')
                ->with('error', 'Lỗi validate dữ liệu: ' . implode(', ', $errors));
        } catch (\Exception $e) {
            Log::error('Import error: ' . $e->getMessage());
            return redirect()->route('giangvien.lop.index')
                ->with('error', 'Có lỗi xảy ra khi import file: ' . $e->getMessage());
        }
    }
} 