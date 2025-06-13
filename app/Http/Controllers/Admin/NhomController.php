<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Nhom;
use App\Models\SinhVien;
use App\Models\ChiTietNhom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Imports\NhomImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class NhomController extends Controller
{
    public function index()
    {
        $nhoms = Nhom::with('sinhViens')->latest()->paginate(10);
        return view('admin.nhom.index', compact('nhoms'));
    }

    public function create()
    {
        $sinhViens = SinhVien::whereDoesntHave('nhoms')->get();
        return view('admin.nhom.create', compact('sinhViens'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ma_nhom' => 'required|string|unique:nhoms,ma_nhom',
            'ten' => 'required|string|max:255',
            'sinh_vien_ids' => 'required|array|min:1|max:3',
            'sinh_vien_ids.*' => 'exists:sinh_viens,id',
        ], [
            'ma_nhom.required' => 'Mã nhóm không được để trống',
            'ma_nhom.unique' => 'Mã nhóm đã tồn tại',
            'ten.required' => 'Tên nhóm không được để trống',
            'sinh_vien_ids.required' => 'Vui lòng chọn ít nhất 1 sinh viên',
            'sinh_vien_ids.min' => 'Vui lòng chọn ít nhất 1 sinh viên',
            'sinh_vien_ids.max' => 'Chỉ được chọn tối đa 3 sinh viên',
            'sinh_vien_ids.*.exists' => 'Sinh viên không tồn tại',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $nhom = Nhom::create([
            'ma_nhom' => $request->ma_nhom,
            'ten' => $request->ten,
            'trang_thai' => 'hoat_dong'
        ]);

        // Thêm các sinh viên vào nhóm
        $nhom->sinhViens()->attach($request->sinh_vien_ids);

        return redirect()->route('admin.nhom.index')
            ->with('success', 'Tạo nhóm thành công!');
    }

    public function edit(Nhom $nhom)
    {
        $sinhVienIds = $nhom->sinhViens->pluck('id')->toArray();
        $sinhViens = SinhVien::whereDoesntHave('nhoms', function($query) use ($nhom) {
            $query->where('nhom_id', '!=', $nhom->id);
        })->orWhereIn('id', $sinhVienIds)->get();

        return view('admin.nhom.edit', compact('nhom', 'sinhViens', 'sinhVienIds'));
    }

    public function update(Request $request, Nhom $nhom)
    {
        $validator = Validator::make($request->all(), [
            'ma_nhom' => 'required|string|unique:nhoms,ma_nhom,' . $nhom->id,
            'ten' => 'required|string|max:255',
            'sinh_vien_ids' => 'required|array|min:1|max:3',
            'sinh_vien_ids.*' => 'exists:sinh_viens,id',
            'trang_thai' => 'required|in:hoat_dong,khong_hoat_dong',
        ], [
            'ma_nhom.required' => 'Mã nhóm không được để trống',
            'ma_nhom.unique' => 'Mã nhóm đã tồn tại',
            'ten.required' => 'Tên nhóm không được để trống',
            'sinh_vien_ids.required' => 'Vui lòng chọn ít nhất 1 sinh viên',
            'sinh_vien_ids.min' => 'Vui lòng chọn ít nhất 1 sinh viên',
            'sinh_vien_ids.max' => 'Chỉ được chọn tối đa 3 sinh viên',
            'sinh_vien_ids.*.exists' => 'Sinh viên không tồn tại',
            'trang_thai.required' => 'Vui lòng chọn trạng thái',
            'trang_thai.in' => 'Trạng thái không hợp lệ',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $nhom->update([
            'ma_nhom' => $request->ma_nhom,
            'ten' => $request->ten,
            'trang_thai' => $request->trang_thai
        ]);

        // Cập nhật danh sách sinh viên trong nhóm
        $nhom->sinhViens()->sync($request->sinh_vien_ids);

        return redirect()->route('admin.nhom.index')
            ->with('success', 'Cập nhật nhóm thành công!');
    }

    public function destroy(Nhom $nhom)
    {
        try {
            // Kiểm tra xem nhóm có đang được sử dụng trong đề tài không
            if ($nhom->deTais()->exists()) {
                return redirect()->route('admin.nhom.index')
                    ->with('error', 'Không thể xóa nhóm này vì đang có đề tài được gán cho nhóm. Vui lòng xóa hoặc gỡ liên kết đề tài trước.');
            }

            // Xóa các bản ghi liên quan trong bảng chi_tiet_nhoms
            $nhom->chiTietNhoms()->delete();

            // Xóa các liên kết với sinh viên
            $nhom->sinhViens()->detach();

            // Xóa nhóm
            $nhom->delete();

            return redirect()->route('admin.nhom.index')
                ->with('success', 'Xóa nhóm thành công!');
        } catch (\Exception $e) {
            Log::error('Delete Nhom error: ' . $e->getMessage());
            return redirect()->route('admin.nhom.index')
                ->with('error', 'Có lỗi xảy ra khi xóa nhóm. Vui lòng kiểm tra lại các ràng buộc dữ liệu.');
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

            Excel::import(new NhomImport, $tmpPath);

            if (file_exists($tmpPath)) {
                unlink($tmpPath);
            }

            return redirect()->route('admin.nhom.index')
                ->with('success', 'Dữ liệu đã được nhập thành công!');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errors = [];
            foreach ($failures as $failure) {
                $errors[] = "Dòng {$failure->row()}: {$failure->errors()[0]}";
            }
            return redirect()->route('admin.nhom.index')
                ->with('error', 'Lỗi validate dữ liệu: ' . implode(', ', $errors));
        } catch (\Exception $e) {
            Log::error('Import error: ' . $e->getMessage());
            return redirect()->route('admin.nhom.index')
                ->with('error', 'Có lỗi xảy ra khi import file: ' . $e->getMessage());
        }
    }

    public function downloadTemplate()
    {
        $path = storage_path('app/templates/nhom_template.xlsx');
        
        // Tạo thư mục templates nếu chưa tồn tại
        if (!file_exists(storage_path('app/templates'))) {
            mkdir(storage_path('app/templates'), 0755, true);
        }

        if (!file_exists($path)) {
            // Tạo file mẫu nếu chưa tồn tại
            $headers = [
                'Mã nhóm',
                'Tên nhóm',
                'MSSV (cách nhau bằng dấu phẩy)'
            ];
            
            $data = [
                ['NH001', 'Nhóm 1', '0306211506,0306211507'],
                ['NH002', 'Nhóm 2', '0306211508,0306211509']
            ];

            Excel::store(
                new class($headers, $data) implements \Maatwebsite\Excel\Concerns\FromArray {
                    protected $headers;
                    protected $data;

                    public function __construct($headers, $data)
                    {
                        $this->headers = $headers;
                        $this->data = $data;
                    }

                    public function array(): array
                    {
                        return array_merge([$this->headers], $this->data);
                    }
                },
                'templates/nhom_template.xlsx',
                'local'
            );
        }

        return response()->download($path, 'nhom_template.xlsx');
    }
} 