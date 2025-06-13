<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SinhVien;
use App\Models\Lop;
use App\Imports\SinhVienImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;

class SinhVienController extends Controller
{
    public function index()
    {
        $sinhViens = SinhVien::with('lop')->paginate(10);
        return view('admin.sinh-vien.index', compact('sinhViens'));
    }

    public function create()
    {
        $lops = Lop::all();
        return view('admin.sinh-vien.create', compact('lops'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mssv' => 'required|string|regex:/^0306\\d{6}$/|unique:sinh_viens,mssv',
            'ten' => 'required|string|max:255',
            'lop_id' => 'required|exists:lops,id',
        ], [
            'mssv.required' => 'Mã số sinh viên không được để trống',
            'mssv.unique' => 'Mã số sinh viên đã tồn tại',
            'mssv.regex' => 'Mã số sinh viên phải bắt đầu bằng 0306 và có đủ 10 chữ số.',
            'ten.required' => 'Tên sinh viên không được để trống',
            'lop_id.required' => 'Vui lòng chọn lớp',
            'lop_id.exists' => 'Lớp không tồn tại',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                        ->withErrors($validator)
                        ->withInput();
        }

        SinhVien::create($request->only(['mssv', 'ten', 'lop_id']));

        return redirect()->route('admin.sinh-vien.index')
            ->with('success', 'Sinh viên đã được thêm thành công!');
    }

    public function edit(SinhVien $sinhVien)
    {
        $lops = Lop::all();
        return view('admin.sinh-vien.edit', compact('sinhVien', 'lops'));
    }

    public function update(Request $request, SinhVien $sinhVien)
    {
        $validator = Validator::make($request->all(), [
            'mssv' => 'required|string|regex:/^0306\\d{6}$/|unique:sinh_viens,mssv,' . $sinhVien->id,
            'ten' => 'required|string|max:255',
            'lop_id' => 'required|exists:lops,id',
        ], [
            'mssv.required' => 'Mã số sinh viên không được để trống',
            'mssv.unique' => 'Mã số sinh viên đã tồn tại',
            'mssv.regex' => 'Mã số sinh viên phải bắt đầu bằng 0306 và có đủ 10 chữ số.',
            'ten.required' => 'Tên sinh viên không được để trống',
            'lop_id.required' => 'Vui lòng chọn lớp',
            'lop_id.exists' => 'Lớp không tồn tại',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                        ->withErrors($validator)
                        ->withInput();
        }

        $sinhVien->update($request->only(['mssv', 'ten', 'lop_id']));

        return redirect()->route('admin.sinh-vien.index')
            ->with('success', 'Sinh viên đã được cập nhật thành công!');
    }

    public function destroy(SinhVien $sinhVien)
    {
        try {
            $sinhVien->delete();
            return redirect()->route('admin.sinh-vien.index')
                ->with('success', 'Sinh viên đã được xóa thành công!');
        } catch (\Exception $e) {
            Log::error('Delete SinhVien error: ' . $e->getMessage());
            return redirect()->route('admin.sinh-vien.index')
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

            return redirect()->route('admin.sinh-vien.index')
                ->with('success', 'Dữ liệu đã được nhập thành công!');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errors = [];
            foreach ($failures as $failure) {
                $errors[] = "Dòng {$failure->row()}: {$failure->errors()[0]}";
            }
            return redirect()->route('admin.sinh-vien.index')
                ->with('error', 'Lỗi validate dữ liệu: ' . implode(', ', $errors));
        } catch (\Exception $e) {
            Log::error('Import error: ' . $e->getMessage());
            return redirect()->route('admin.sinh-vien.index')
                ->with('error', 'Có lỗi xảy ra khi import file: ' . $e->getMessage());
        }
    }

    public function bulkDelete(Request $request)
    {
        $selectedStudents = $request->input('selected_students', []);

        if (empty($selectedStudents)) {
            return redirect()->route('admin.sinh-vien.index')
                ->with('error', 'Vui lòng chọn ít nhất một sinh viên để xóa.');
        }

        try {
            $deletedCount = SinhVien::whereIn('id', $selectedStudents)->delete();

            if ($deletedCount > 0) {
                return redirect()->route('admin.sinh-vien.index')
                    ->with('success', "Đã xóa thành công {$deletedCount} sinh viên.");
            } else {
                return redirect()->route('admin.sinh-vien.index')
                    ->with('error', 'Không tìm thấy sinh viên nào để xóa.');
            }
        } catch (\Exception $e) {
            Log::error('Bulk delete SinhVien error: ' . $e->getMessage());
            return redirect()->route('admin.sinh-vien.index')
                ->with('error', 'Có lỗi xảy ra khi xóa sinh viên: ' . $e->getMessage());
        }
    }
} 