<?php

namespace App\Http\Controllers\GiangVien;

use App\Http\Controllers\Controller;
use App\Models\SinhVien;
use App\Imports\SinhVienImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;

class SinhVienController extends Controller
{
    public function index()
    {
        $sinhViens = SinhVien::paginate(10);
        return view('giangvien.sinh-vien.index', compact('sinhViens'));
    }

    public function create()
    {
        return view('giangvien.sinh-vien.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mssv' => 'required|string|regex:/^0306\\d{6}$/|unique:sinh_viens,mssv',
            'ten' => 'required|string|max:255',
            'lop' => 'nullable|string|max:50',
            'nganh' => 'nullable|string|max:100',
            'khoa_hoc' => 'nullable|string|max:20',
        ], [
            'mssv.required' => 'Mã số sinh viên không được để trống',
            'mssv.unique' => 'Mã số sinh viên đã tồn tại',
            'mssv.regex' => 'Mã số sinh viên phải bắt đầu bằng 0306 và có đủ 10 chữ số.',
            'ten.required' => 'Tên sinh viên không được để trống',
            'lop.max' => 'Tên lớp không được vượt quá 50 ký tự',
            'nganh.max' => 'Tên ngành không được vượt quá 100 ký tự',
            'khoa_hoc.max' => 'Khóa học không được vượt quá 20 ký tự',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                        ->withErrors($validator)
                        ->withInput();
        }

        SinhVien::create($request->only(['mssv', 'ten', 'lop', 'nganh', 'khoa_hoc']));

        return redirect()->route('giangvien.sinh-vien.index')
            ->with('success', 'Sinh viên đã được thêm thành công!');
    }

    public function edit(SinhVien $sinhVien)
    {
        return view('giangvien.sinh-vien.edit', compact('sinhVien'));
    }

    public function update(Request $request, SinhVien $sinhVien)
    {
        $validator = Validator::make($request->all(), [
            'mssv' => 'required|string|regex:/^0306\\d{6}$/|unique:sinh_viens,mssv,' . $sinhVien->id,
            'ten' => 'required|string|max:255',
            'lop' => 'nullable|string|max:50',
            'nganh' => 'nullable|string|max:100',
            'khoa_hoc' => 'nullable|string|max:20',
        ], [
            'mssv.required' => 'Mã số sinh viên không được để trống',
            'mssv.unique' => 'Mã số sinh viên đã tồn tại',
            'mssv.regex' => 'Mã số sinh viên phải bắt đầu bằng 0306 và có đủ 10 chữ số.',
            'ten.required' => 'Tên sinh viên không được để trống',
            'lop.max' => 'Tên lớp không được vượt quá 50 ký tự',
            'nganh.max' => 'Tên ngành không được vượt quá 100 ký tự',
            'khoa_hoc.max' => 'Khóa học không được vượt quá 20 ký tự',
        ]);

         if ($validator->fails()) {
            return redirect()->back()
                        ->withErrors($validator)
                        ->withInput();
        }

        $sinhVien->update($request->only(['mssv', 'ten', 'lop', 'nganh', 'khoa_hoc']));

        return redirect()->route('giangvien.sinh-vien.index')
            ->with('success', 'Sinh viên đã được cập nhật thành công!');
    }

    public function destroy(SinhVien $sinhVien)
    {
        try {
            $sinhVien->delete();
            return redirect()->route('giangvien.sinh-vien.index')
                ->with('success', 'Sinh viên đã được xóa thành công!');
        } catch (\Exception $e) {
            Log::error('Delete SinhVien error: ' . $e->getMessage());
            return redirect()->route('giangvien.sinh-vien.index')
                ->with('error', 'Có lỗi xảy ra khi xóa sinh viên: ' . $e->getMessage());
        }
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

            Excel::import(new SinhVienImport, $tmpPath);

            if (file_exists($tmpPath)) {
                unlink($tmpPath);
            }

            return redirect()->route('giangvien.sinh-vien.index')
                ->with('success', 'Dữ liệu đã được nhập thành công!');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errors = [];
            foreach ($failures as $failure) {
                $errors[] = "Dòng {$failure->row()}: {$failure->errors()[0]}";
            }
            return redirect()->route('giangvien.sinh-vien.index')
                ->with('error', 'Lỗi validate dữ liệu: ' . implode(', ', $errors));
        } catch (\Exception $e) {
            Log::error('Import error: ' . $e->getMessage());
            return redirect()->route('giangvien.sinh-vien.index')
                ->with('error', 'Có lỗi xảy ra khi import file: ' . $e->getMessage());
        }
    }

    public function bulkDelete(Request $request)
    {
        $selectedStudents = $request->input('selected_students', []);

        if (empty($selectedStudents)) {
            return redirect()->route('giangvien.sinh-vien.index')
                ->with('error', 'Vui lòng chọn ít nhất một sinh viên để xóa.');
        }

        try {
            $deletedCount = SinhVien::whereIn('id', $selectedStudents)->delete();

            if ($deletedCount > 0) {
                return redirect()->route('giangvien.sinh-vien.index')
                    ->with('success', "Đã xóa thành công {$deletedCount} sinh viên.");
            } else {
                return redirect()->route('giangvien.sinh-vien.index')
                    ->with('error', 'Không tìm thấy sinh viên nào để xóa.');
            }
        } catch (\Exception $e) {
            Log::error('Bulk delete SinhVien error: ' . $e->getMessage());
            return redirect()->route('giangvien.sinh-vien.index')
                ->with('error', 'Có lỗi xảy ra khi xóa sinh viên: ' . $e->getMessage());
        }
    }
} 