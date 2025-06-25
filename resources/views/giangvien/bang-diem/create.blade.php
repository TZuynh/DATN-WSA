@extends('components.giangvien.app')

@section('title', 'Chấm điểm')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Chấm điểm sinh viên</h3>
                    <div class="card-tools">
                        <a href="{{ route('giangvien.bang-diem.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Quay lại
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(request()->has('diemCuId'))
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> Bạn đang cập nhật điểm cho đợt báo cáo mới. 
                            Điểm báo cáo và thuyết trình đã được điền từ điểm cũ.
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5>Thông tin sinh viên</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th width="30%">Mã sinh viên:</th>
                                    <td>{{ $sinhVien->mssv }}</td>
                                </tr>
                                <tr>
                                    <th>Họ và tên:</th>
                                    <td>{{ $sinhVien->ten }}</td>
                                </tr>
                                <tr>
                                    <th>Lớp:</th>
                                    <td>{{ $sinhVien->lop->ten_lop ?? 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                        @if($dotBaoCao)
                            <div class="col-md-6">
                                <h5>Thông tin đợt báo cáo</h5>
                                <table class="table table-bordered">
                                    <tr>
                                        <th width="30%">Tên hội đồng:</th>
                                        <td>
                                            @php
                                                $lichCham = $dotBaoCao->lichChams->first();
                                                $tenHoiDong = $lichCham && $lichCham->hoiDong ? $lichCham->hoiDong->ten : 'N/A';
                                            @endphp
                                            {{ $tenHoiDong }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Năm học:</th>
                                        <td>{{ $dotBaoCao->nam_hoc }}</td>
                                    </tr>
                                    <tr>
                                        <th>Học kỳ:</th>
                                        <td>{{ $dotBaoCao->hocKy->ten }}</td>
                                    </tr>
                                    <tr>
                                        <th>Tên nhóm:</th>
                                        <td>{{ $tenNhom }}</td>
                                    </tr>
                                    <tr>
                                        <th>Tên đề tài:</th>
                                        <td>{{ $tenDeTai }}</td>
                                    </tr>
                                </table>
                            </div>
                        @endif
                    </div>

                    <form action="{{ route('giangvien.bang-diem.store', [$sinhVien->id, $dotBaoCao->id ?? null]) }}" method="POST">
                        @csrf
                        <input type="hidden" name="sinh_vien_id" value="{{ $sinhVien->id }}">
                        @if($dotBaoCao)
                            <input type="hidden" name="dot_bao_cao_id" value="{{ $dotBaoCao->id }}">
                        @endif

                        @php
                            $canGradeBaoCaoAndThuyetTrinh = in_array($vaiTroCham, ['Giảng Viên Hướng Dẫn', 'Giảng Viên Phản Biện']);
                            $diemCu = null;
                            if ($hasDotBaoCao) {
                                $diemCu = App\Models\BangDiem::where('sinh_vien_id', $sinhVien->id)
                                    ->whereNull('dot_bao_cao_id')
                                    ->where('giang_vien_id', Auth::id())
                                    ->first();
                            }
                            $shouldDisableBasicScores = $hasDotBaoCao && $diemCu;
                        @endphp

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
                                           @if(!$canGradeBaoCaoAndThuyetTrinh) readonly @endif>
                                    @if(!$canGradeBaoCaoAndThuyetTrinh)
                                        <small class="text-muted">Chỉ Giảng viên hướng dẫn và Giảng viên phản biện mới được chấm điểm này</small>
                                    @endif
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
                                           {{ ($shouldDisableBasicScores || !$canGradeBaoCaoAndThuyetTrinh) ? '' : '' }}>
                                    @if($shouldDisableBasicScores)
                                        <small class="text-muted">Điểm thuyết trình được giữ nguyên từ điểm cũ</small>
                                    @elseif(!$canGradeBaoCaoAndThuyetTrinh)
                                        <small class="text-muted">Chỉ Giảng viên hướng dẫn và Giảng viên phản biện mới được chấm điểm này</small>
                                    @endif
                                    @error('diem_thuyet_trinh')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        @if($hasDotBaoCao)
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="diem_demo">Điểm demo <span class="text-danger">*</span></label>
                                    <input type="number"
                                           class="form-control @if($errors->has('diem_demo')) is-invalid @endif"
                                           id="diem_demo"
                                           name="diem_demo"
                                           value="{{ old('diem_demo') }}"
                                           min="0"
                                           max="10"
                                           step="0.1"
                                           required>
                                    @if($errors->has('diem_demo'))
                                        <div class="invalid-feedback">{{ $errors->first('diem_demo') }}</div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="diem_cau_hoi">Điểm câu hỏi <span class="text-danger">*</span></label>
                                    <input type="number"
                                           class="form-control @if($errors->has('diem_cau_hoi')) is-invalid @endif"
                                           id="diem_cau_hoi"
                                           name="diem_cau_hoi"
                                           value="{{ old('diem_cau_hoi') }}"
                                           min="0"
                                           max="10"
                                           step="0.1"
                                           required>
                                    @if($errors->has('diem_cau_hoi'))
                                        <div class="invalid-feedback">{{ $errors->first('diem_cau_hoi') }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="diem_cong">Điểm cộng</label>
                                    <input type="number"
                                           class="form-control @if($errors->has('diem_cong')) is-invalid @endif"
                                           id="diem_cong"
                                           name="diem_cong"
                                           value="{{ old('diem_cong', 0) }}"
                                           min="0"
                                           max="2"
                                           step="0.1">
                                    <small class="form-text text-muted">Điểm cộng tối đa là 2 điểm</small>
                                    @if($errors->has('diem_cong'))
                                        <div class="invalid-feedback">{{ $errors->first('diem_cong') }}</div>
                                    @endif
                                </div>
                            </div>
                            @endif
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="tong_diem">Tổng điểm</label>
                                    <input type="text"
                                           class="form-control"
                                           id="tong_diem"
                                           readonly
                                           value="0.0">
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
                                      maxlength="1000">{{ old('binh_luan') }}</textarea>
                            <small class="form-text text-muted">Tối đa 1000 ký tự</small>
                            @error('binh_luan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Lưu điểm
                            </button>
                            <a href="{{ route('giangvien.bang-diem.index') }}" class="btn btn-secondary">
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
        const demo = parseFloat(diemDemo?.value) || 0;
        const cauHoi = parseFloat(diemCauHoi?.value) || 0;
        const cong = parseFloat(diemCong?.value) || 0;

        const tong = baoCao + thuyetTrinh + demo + cauHoi + cong;
        tongDiem.value = tong.toFixed(1);
    }

    diemBaoCao.addEventListener('input', tinhTongDiem);
    diemThuyetTrinh.addEventListener('input', tinhTongDiem);
    if (diemDemo) diemDemo.addEventListener('input', tinhTongDiem);
    if (diemCauHoi) diemCauHoi.addEventListener('input', tinhTongDiem);
    if (diemCong) diemCong.addEventListener('input', tinhTongDiem);
});
</script>
@endsection
