<?php

namespace App\Http\Controllers\GiangVien;

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
use App\Models\DeTai;

class NhomController extends Controller
{
    public function index()
    {
        $nhoms = Nhom::with(['sinhViens', 'giangVien'])
            ->where('giang_vien_id', auth()->id())
            ->latest()
            ->paginate(10);
        return view('giangvien.nhom.index', compact('nhoms'));
    }

    public function create()
    {
        $sinhViens = SinhVien::whereDoesntHave('nhoms')->get();
        return view('giangvien.nhom.create', compact('sinhViens'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ten' => 'required|string|max:255',
            'sinh_vien_ids' => 'required|array|min:1|max:3',
            'sinh_vien_ids.*' => 'exists:sinh_viens,id',
        ], [
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

        try {
            // Tạo mã nhóm tự động
            $maNhom = Nhom::taoMaNhom();

            $nhom = Nhom::create([
                'ma_nhom' => $maNhom,
                'ten' => $request->ten,
                'giang_vien_id' => auth()->id(),
                'trang_thai' => 'hoat_dong'
            ]);

            // Thêm các sinh viên vào nhóm
            $nhom->sinhViens()->attach($request->sinh_vien_ids);

            return redirect()->route('giangvien.nhom.index')
                ->with('success', 'Tạo nhóm thành công!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    public function edit(Nhom $nhom)
    {
        if ($nhom->giang_vien_id !== auth()->id()) {
            return redirect()->route('giangvien.nhom.index')
                ->with('error', 'Bạn không có quyền chỉnh sửa nhóm này.');
        }

        $sinhVienIds = $nhom->sinhViens->pluck('id')->toArray();
        $sinhViens = SinhVien::whereDoesntHave('nhoms', function($query) use ($nhom) {
            $query->where('nhom_id', '!=', $nhom->id);
        })->orWhereIn('id', $sinhVienIds)->get();

        return view('giangvien.nhom.edit', compact('nhom', 'sinhViens', 'sinhVienIds'));
    }

    public function update(Request $request, Nhom $nhom)
    {
        if ($nhom->giang_vien_id !== auth()->id()) {
            return redirect()->route('giangvien.nhom.index')
                ->with('error', 'Bạn không có quyền cập nhật nhóm này.');
        }

        $validator = Validator::make($request->all(), [
            'ten' => 'required|string|max:255',
            'sinh_vien_ids' => 'required|array|min:1|max:3',
            'sinh_vien_ids.*' => 'exists:sinh_viens,id',
            'trang_thai' => 'required|in:hoat_dong,khong_hoat_dong',
        ], [
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

        try {
            $nhom->update([
                'ten' => $request->ten,
                'trang_thai' => $request->trang_thai
            ]);

            // Cập nhật danh sách sinh viên trong nhóm
            $nhom->sinhViens()->sync($request->sinh_vien_ids);

            return redirect()->route('giangvien.nhom.index')
                ->with('success', 'Cập nhật nhóm thành công!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    public function destroy(Nhom $nhom)
    {
        if ($nhom->giang_vien_id !== auth()->id()) {
            return redirect()->route('giangvien.nhom.index')
                ->with('error', 'Bạn không có quyền xóa nhóm này.');
        }

        try {
            // Kiểm tra xem nhóm có đang được sử dụng trong đề tài không
            if ($nhom->deTais()->exists()) {
                return redirect()->route('giangvien.nhom.index')
                    ->with('error', 'Không thể xóa nhóm này vì đang có đề tài được gán cho nhóm. Vui lòng xóa hoặc gỡ liên kết đề tài trước.');
            }

            // Xóa các bản ghi liên quan trong bảng chi_tiet_nhoms
            $nhom->chiTietNhoms()->delete();

            // Xóa các liên kết với sinh viên
            $nhom->sinhViens()->detach();

            // Xóa nhóm
            $nhom->delete();

            return redirect()->route('giangvien.nhom.index')
                ->with('success', 'Xóa nhóm thành công!');
        } catch (\Exception $e) {
            Log::error('Delete Nhom error: ' . $e->getMessage());
            return redirect()->route('giangvien.nhom.index')
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

            return redirect()->route('giangvien.nhom.index')
                ->with('success', 'Dữ liệu đã được nhập thành công!');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errors = [];
            foreach ($failures as $failure) {
                $errors[] = "Dòng {$failure->row()}: {$failure->errors()[0]}";
            }
            return redirect()->route('giangvien.nhom.index')
                ->with('error', 'Lỗi validate dữ liệu: ' . implode(', ', $errors));
        } catch (\Exception $e) {
            Log::error('Import error: ' . $e->getMessage());
            return redirect()->route('giangvien.nhom.index')
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

    public function showChangeDeTaiForm($id)
    {
        $nhom = Nhom::findOrFail($id);
        $deTais = DeTai::with(['nhoms.sinhViens'])->get(); // hoặc lọc theo điều kiện phù hợp
        return view('giangvien.nhom.change_detai', compact('nhom', 'deTais'));
    }

    public function changeDeTai(Request $request, $id)
    {
        $request->validate([
            'de_tai_id' => 'required|exists:de_tais,id'
        ]);

        $nhom = Nhom::findOrFail($id);
        $deTaiMoi = DeTai::findOrFail($request->de_tai_id);

        // Nếu đề tài mới đã được giảng viên phản biện đồng ý thì không cho chuyển
        if ($deTaiMoi->trang_thai == 2) {
            return redirect()->route('giangvien.nhom.index')
                ->with('error', 'Không thể chuyển sang đề tài đã được giảng viên phản biện đồng ý!');
        }

        $deTaiCu = $nhom->deTai;
        $nhomCu = $deTaiMoi->nhom;

        // Cập nhật đề tài cũ (nếu có): bỏ liên kết với nhóm hiện tại
        if ($deTaiCu) {
            $deTaiCu->nhom_id = null;
            $deTaiCu->save();
        }

        // Cập nhật nhóm hiện tại với đề tài mới
        $nhom->de_tai_id = $deTaiMoi->id;
        $nhom->save();

        $deTaiMoi->nhom_id = $nhom->id;
        $deTaiMoi->save();

        // Nếu đề tài mới từng gắn với nhóm khác => gán đề tài cũ cho nhóm đó
        if ($nhomCu && $nhomCu->id != $nhom->id) {
            $nhomCu->de_tai_id = $deTaiCu?->id;
            $nhomCu->save();

            if ($deTaiCu) {
                $deTaiCu->nhom_id = $nhomCu->id;
                $deTaiCu->save();
            }
        }

        return redirect()->route('giangvien.nhom.index')->with('success', 'Chuyển đề tài thành công!');
    }
}
