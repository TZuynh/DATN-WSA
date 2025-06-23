@extends('admin.layout')

@section('title', 'Chỉnh sửa điểm')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Chỉnh sửa điểm</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.bang-diem.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Quay lại
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5>Thông tin sinh viên</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th width="30%">Mã sinh viên:</th>
                                    <td>{{ $bangDiem->sinhVien->ma_sinh_vien }}</td>
                                </tr>
                                <tr>
                                    <th>Họ và tên:</th>
                                    <td>{{ $bangDiem->sinhVien->ten }}</td>
                                </tr>
                                <tr>
                                    <th>Lớp:</th>
                                    <td>{{ $bangDiem->sinhVien->lop->ten_lop ?? 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5>Thông tin đợt báo cáo</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th width="30%">Tên hội đồng:</th>
                                    <td>
                                        @php
                                            $lichCham = $bangDiem->dotBaoCao->lichChams->first();
                                            $tenHoiDong = $lichCham && $lichCham->hoiDong ? $lichCham->hoiDong->ten : 'N/A';
                                        @endphp
                                        {{ $tenHoiDong }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>Năm học:</th>
                                    <td>{{ $bangDiem->dotBaoCao->nam_hoc }}</td>
                                </tr>
                                <tr>
                                    <th>Học kỳ:</th>
                                    <td>{{ $dotBaoCao->hocKy->ten }}</td>
                                </tr>
                                <tr>
                                    <th>Tên nhóm:</th>
                                    <td>
                                        @php
                                            $chiTietNhom = $bangDiem->sinhVien->chiTietNhom;
                                            $tenNhom = $chiTietNhom && $chiTietNhom->nhom ? $chiTietNhom->nhom->ten : 'N/A';
                                        @endphp
                                        {{ $tenNhom }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>Tên đề tài:</th>
                                    <td>
                                        @php
                                            $tenDeTai = $chiTietNhom && $chiTietNhom->nhom && $chiTietNhom->nhom->deTai ? $chiTietNhom->nhom->deTai->ten_de_tai : 'N/A';
                                        @endphp
                                        {{ $tenDeTai }}
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <form action="{{ route('admin.bang-diem.update', $bangDiem->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="diem_bao_cao">Điểm báo cáo <span class="text-danger">*</span></label>
                                    <input type="number"
                                           class="form-control @error('diem_bao_cao') is-invalid @enderror"
                                           id="diem_bao_cao"
                                           name="diem_bao_cao"
                                           value="{{ old('diem_bao_cao', $bangDiem->diem_bao_cao) }}"
                                           min="0"
                                           max="10"
                                           step="0.1"
                                           required>
                                    @error('diem_bao_cao')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="diem_thuyet_trinh">Điểm thuyết trình <span class="text-danger">*</span></label>
                                    <input type="number"
                                           class="form-control @error('diem_thuyet_trinh') is-invalid @enderror"
                                           id="diem_thuyet_trinh"
                                           name="diem_thuyet_trinh"
                                           value="{{ old('diem_thuyet_trinh', $bangDiem->diem_thuyet_trinh) }}"
                                           min="0"
                                           max="10"
                                           step="0.1"
                                           required>
                                    @error('diem_thuyet_trinh')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="diem_demo">Điểm demo <span class="text-danger">*</span></label>
                                    <input type="number"
                                           class="form-control @error('diem_demo') is-invalid @enderror"
                                           id="diem_demo"
                                           name="diem_demo"
                                           value="{{ old('diem_demo', $bangDiem->diem_demo) }}"
                                           min="0"
                                           max="10"
                                           step="0.1"
                                           required>
                                    @error('diem_demo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="diem_cau_hoi">Điểm câu hỏi <span class="text-danger">*</span></label>
                                    <input type="number"
                                           class="form-control @error('diem_cau_hoi') is-invalid @enderror"
                                           id="diem_cau_hoi"
                                           name="diem_cau_hoi"
                                           value="{{ old('diem_cau_hoi', $bangDiem->diem_cau_hoi) }}"
                                           min="0"
                                           max="10"
                                           step="0.1"
                                           required>
                                    @error('diem_cau_hoi')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="diem_cong">Điểm cộng</label>
                                    <input type="number"
                                           class="form-control @error('diem_cong') is-invalid @enderror"
                                           id="diem_cong"
                                           name="diem_cong"
                                           value="{{ old('diem_cong', $bangDiem->diem_cong) }}"
                                           min="0"
                                           max="2"
                                           step="0.1">
                                    <small class="form-text text-muted">Điểm cộng tối đa là 2 điểm</small>
                                    @error('diem_cong')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="tong_diem">Tổng điểm</label>
                                    <input type="text"
                                           class="form-control"
                                           id="tong_diem"
                                           readonly
                                           value="{{ number_format($bangDiem->diem_bao_cao + $bangDiem->diem_thuyet_trinh + $bangDiem->diem_demo + $bangDiem->diem_cau_hoi + $bangDiem->diem_cong, 1) }}">
                                    <small class="form-text text-muted">Tổng điểm sẽ được tính tự động</small>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="binh_luan">Bình luận</label>
                            <textarea class="form-control @error('binh_luan') is-invalid @enderror"
                                      id="binh_luan"
                                      name="binh_luan"
                                      rows="4"
                                      maxlength="1000">{{ old('binh_luan', $bangDiem->binh_luan) }}</textarea>
                            <small class="form-text text-muted">Tối đa 1000 ký tự</small>
                            @error('binh_luan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Cập nhật điểm
                            </button>
                            <a href="{{ route('admin.bang-diem.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Hủy
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const diemBaoCao = document.getElementById('diem_bao_cao');
    const diemThuyetTrinh = document.getElementById('diem_thuyet_trinh');
    const diemDemo = document.getElementById('diem_demo');
    const diemCauHoi = document.getElementById('diem_cau_hoi');
    const diemCong = document.getElementById('diem_cong');
    const tongDiem = document.getElementById('tong_diem');

    function tinhTongDiem() {
        const baoCao = parseFloat(diemBaoCao.value) || 0;
        const thuyetTrinh = parseFloat(diemThuyetTrinh.value) || 0;
        const demo = parseFloat(diemDemo.value) || 0;
        const cauHoi = parseFloat(diemCauHoi.value) || 0;
        const cong = parseFloat(diemCong.value) || 0;

        const tong = baoCao + thuyetTrinh + demo + cauHoi + cong;
        tongDiem.value = tong.toFixed(1);
    }

    diemBaoCao.addEventListener('input', tinhTongDiem);
    diemThuyetTrinh.addEventListener('input', tinhTongDiem);
    diemDemo.addEventListener('input', tinhTongDiem);
    diemCauHoi.addEventListener('input', tinhTongDiem);
    diemCong.addEventListener('input', tinhTongDiem);
});
</script>
@endsection
