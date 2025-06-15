<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Phong;
use App\Imports\PhongImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;

class PhongController extends Controller
{
    public function index()
    {
        $phongs = Phong::latest()->paginate(10);
        return view('admin.phong.index', compact('phongs'));
    }

    public function create()
    {
        return view('admin.phong.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ten_phong' => 'required|string|max:255|unique:phongs',
        ], [
            'ten_phong.required' => 'Tên phòng không được để trống',
            'ten_phong.unique' => 'Tên phòng đã tồn tại',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            Phong::create($request->all());
            return redirect()->route('admin.phong.index')
                ->with('success', 'Thêm phòng thành công!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    public function edit(Phong $phong)
    {
        return view('admin.phong.edit', compact('phong'));
    }

    public function update(Request $request, Phong $phong)
    {
        $validator = Validator::make($request->all(), [
            'ten_phong' => 'required|string|max:255|unique:phongs,ten_phong,' . $phong->id,
        ], [
            'ten_phong.required' => 'Tên phòng không được để trống',
            'ten_phong.unique' => 'Tên phòng đã tồn tại',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $phong->update([
                'ten_phong' => $request->ten_phong
            ]);
            return redirect()->route('admin.phong.index')
                ->with('success', 'Cập nhật phòng thành công!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    public function destroy(Phong $phong)
    {
        try {
            // Xóa trực tiếp từ database
            Phong::where('id', $phong->id)->delete();
            
            return redirect()->route('admin.phong.index')
                ->with('success', 'Xóa phòng thành công!');
        } catch (\Exception $e) {
            return redirect()->route('admin.phong.index')
                ->with('error', 'Có lỗi xảy ra khi xóa phòng: ' . $e->getMessage());
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

            Excel::import(new PhongImport, $tmpPath);

            if (file_exists($tmpPath)) {
                unlink($tmpPath);
            }

            return redirect()->route('admin.phong.index')
                ->with('success', 'Dữ liệu đã được nhập thành công!');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errors = [];
            foreach ($failures as $failure) {
                $errors[] = "Dòng {$failure->row()}: {$failure->errors()[0]}";
            }
            return redirect()->route('admin.phong.index')
                ->with('error', 'Lỗi validate dữ liệu: ' . implode(', ', $errors));
        } catch (\Exception $e) {
            Log::error('Import error: ' . $e->getMessage());
            return redirect()->route('admin.phong.index')
                ->with('error', 'Có lỗi xảy ra khi import file: ' . $e->getMessage());
        }
    }
} 