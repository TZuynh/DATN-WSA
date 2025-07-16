@extends('components.giangvien.app')

@section('title', 'Chỉnh sửa điểm')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Chỉnh sửa điểm</h3>
                        <div class="card-tools">
                            <a href="{{ route('giangvien.bang-diem.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Quay lại
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        @if(session('error'))
                            <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif
                        @if(!$bangDiem)
                            <div class="alert alert-danger">
                                Không tìm thấy thông tin điểm cần chỉnh sửa.
                            </div>
                        @else
                            @php
                                if (!isset($canGradeBaoCaoAndThuyetTrinh)) {
                                    $canGradeBaoCaoAndThuyetTrinh = !$hasDotBaoCao;
                                }
                                $canEditBaoCao = in_array($vaiTroCham, ['Giảng Viên Hướng Dẫn', 'Giảng Viên Phản Biện']);
                                $canEditBaoCaoAndThuyetTrinh = in_array($vaiTroCham, [
                                    'Giảng Viên Hướng Dẫn', 
                                    'Giảng Viên Phản Biện',
                                    'Trưởng tiểu ban'
                                ]);
                            @endphp

                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <h5>Thông tin sinh viên</h5>
                                    <table class="table table-bordered">
                                        <tr>
                                            <th width="30%">Mã sinh viên:</th>
                                            <td>{{ $bangDiem->sinhVien->mssv }}</td>
                                        </tr>
                                        <tr>
                                            <th>Họ và tên:</th>
                                            <td>{{ $bangDiem->sinhVien->ten }}</td>
                                        </tr>
                                        <tr>
                                            <th>Lớp:</th>
                                            <td>{{ $bangDiem->sinhVien->lop->ten_lop ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Vai trò chấm:</th>
                                            <td>
                                                <span class="badge {{ $vaiTroCham=='Phản biện'?'bg-primary':'' }} {{ $vaiTroCham=='Hướng dẫn'?'bg-success':'' }} {{ !in_array($vaiTroCham,['Phản biện','Hướng dẫn'])?'bg-secondary':'' }}">
                                                    {{ $vaiTroCham }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Loại giảng viên:</th>
                                            <td>{{ $loaiGiangVien ?? 'Không xác định' }}</td>
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
                                                    $lichCham = $bangDiem->dotBaoCao?->lichChams->first();
                                                    $tenHoiDong = $lichCham?->hoiDong?->ten ?? 'N/A';
                                                @endphp
                                                {{ $tenHoiDong }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Năm học:</th>
                                            <td>{{ $bangDiem->dotBaoCao->nam_hoc ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Học kỳ:</th>
                                            <td>{{ $bangDiem->dotBaoCao?->hocKy?->ten ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Tên nhóm:</th>
                                            <td>
                                                @php
                                                    $chiTietNhom = $bangDiem->sinhVien->chiTietNhom;
                                                    $tenNhom = $chiTietNhom?->nhom?->ten ?? 'N/A';
                                                @endphp
                                                {{ $tenNhom }}@if(!empty($maNhom)) - {{ $maNhom }}@endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Tên đề tài:</th>
                                            <td>
                                                @php
                                                    $tenDeTai = $chiTietNhom?->nhom?->deTai?->ten_de_tai ?? 'N/A';
                                                @endphp
                                                {{ $tenDeTai }}@if(!empty($maDeTai)) - {{ $maDeTai }}@endif
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <div class="alert alert-info mb-4">
                                <strong>Hướng dẫn nhập điểm:</strong><br>
                                - Điểm báo cáo: tối đa <b>10.0</b><br>
                                - Điểm thuyết trình: tối đa <b>3.0</b><br>
                                - Điểm demo: tối đa <b>4.0</b><br>
                                - Điểm câu hỏi (phản biện): tối đa <b>1.0</b><br>
                                - Điểm cộng: tối đa <b>1.0</b><br>
                                <span class="text-muted">Bạn có thể nhập số thực (ví dụ: 2.5, 3.0, ...)</span>
                            </div>

                            <form action="{{ route('giangvien.bang-diem.update', $bangDiem->id) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="diem_bao_cao">Điểm báo cáo <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control @error('diem_bao_cao') is-invalid @enderror"
                                                   id="diem_bao_cao"
                                                   name="diem_bao_cao"
                                                   value="{{ old('diem_bao_cao', $bangDiem->diem_bao_cao) }}"
                                                   min="0" max="10" step="0.1"
                                                   @if(!$canGradeBaoCao) readonly @endif>
                                            @if(!$canGradeBaoCao)
                                                <small class="text-muted">Chỉ Giảng viên hướng dẫn và Giảng viên phản biện mới được sửa điểm này</small>
                                            @endif
                                            @error('diem_bao_cao')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="diem_thuyet_trinh">Điểm thuyết trình <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control @error('diem_thuyet_trinh') is-invalid @enderror"
                                                   id="diem_thuyet_trinh"
                                                   name="diem_thuyet_trinh"
                                                   value="{{ old('diem_thuyet_trinh', $bangDiem->diem_thuyet_trinh) }}"
                                                   min="0" max="3" step="0.1"
                                                   @if(!$canGradeOtherScores) readonly @endif
                                                   placeholder="Tối đa 3.0">
                                            @if(!$canGradeOtherScores)
                                                <small class="text-muted">Bạn không có quyền chấm điểm này</small>
                                            @endif
                                            @error('diem_thuyet_trinh')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                @if ($canGradeOtherScores)
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="diem_demo">Điểm demo <span class="text-danger">*</span></label>
                                                <input type="number" class="form-control @if($errors->has('diem_demo')) is-invalid @endif"
                                                       id="diem_demo" name="diem_demo"
                                                       value="{{ old('diem_demo', $bangDiem->diem_demo) }}"
                                                       min="0" max="4" step="0.1" 
                                                       placeholder="Tối đa 4.0">
                                                @if($errors->has('diem_demo'))
                                                    <div class="invalid-feedback">{{ $errors->first('diem_demo') }}</div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="diem_cau_hoi">Điểm câu hỏi <span class="text-danger">*</span></label>
                                                <input type="number" class="form-control @if($errors->has('diem_cau_hoi')) is-invalid @endif"
                                                       id="diem_cau_hoi" name="diem_cau_hoi"
                                                       value="{{ old('diem_cau_hoi', $bangDiem->diem_cau_hoi) }}"
                                                       min="0" max="1" step="0.1" 
                                                       @if($hasDotBaoCao) required @endif
                                                       placeholder="Tối đa 1.0">
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
                                                <input type="number" class="form-control @if($errors->has('diem_cong')) is-invalid @endif"
                                                       id="diem_cong" name="diem_cong"
                                                       value="{{ old('diem_cong', $bangDiem->diem_cong) }}"
                                                       min="0" max="1" step="0.1"
                                                       placeholder="Tối đa 1.0">
                                                <small class="form-text text-muted">Tối đa: 1 điểm</small>
                                                @if($errors->has('diem_cong'))
                                                    <div class="invalid-feedback">{{ $errors->first('diem_cong') }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            @php
                                                $tong = ($bangDiem->diem_bao_cao ?? 0) * 0.2 + ($bangDiem->diem_thuyet_trinh ?? 0) + ($bangDiem->diem_demo ?? 0) + ($bangDiem->diem_cau_hoi ?? 0) + ($bangDiem->diem_cong ?? 0);
                                            @endphp
                                            <label for="tong_diem">Tổng điểm</label>
                                            <input type="text" class="form-control" id="tong_diem" readonly value="{{ number_format($tong, 1) }}">
                                            <small class="form-text text-muted">Tổng điểm sẽ được tính tự động</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="binh_luan">Bình luận</label>
                                    <textarea class="form-control @error('binh_luan') is-invalid @enderror"
                                              id="binh_luan" name="binh_luan" rows="4" maxlength="1000">{{ old('binh_luan', $bangDiem->binh_luan) }}</textarea>
                                    <small class="form-text text-muted">Tối đa 1000 ký tự</small>
                                    @error('binh_luan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Cập nhật điểm
                                    </button>
                                    <a href="{{ route('giangvien.bang-diem.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-times"></i> Hủy
                                    </a>
                                </div>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const diemBaoCao = document.getElementById('diem_bao_cao');
            const diemThuyetTrinh = document.getElementById('diem_thuyet_trinh');
            const diemDemo = document.getElementById('diem_demo');
            const diemCauHoi = document.getElementById('diem_cau_hoi');
            const diemCong = document.getElementById('diem_cong');
            const tongDiem = document.getElementById('tong_diem');

            function tinhTongDiem() {
                const baoCao = parseFloat(diemBaoCao?.value) || 0;
                const thuyetTrinh = parseFloat(diemThuyetTrinh?.value) || 0;
                const demo = parseFloat(diemDemo?.value) || 0;
                const cauHoi = parseFloat(diemCauHoi?.value) || 0;
                let cong = parseFloat(diemCong?.value) || 0;

                // Tổng điểm không tính điểm cộng
                const tongKhongCong = (baoCao * 0.2) + thuyetTrinh + demo + cauHoi;
                let maxCong = +(10 - tongKhongCong).toFixed(1);
                if (maxCong < 0) maxCong = 0;

                if (diemCong) {
                    if (tongKhongCong >= 10) {
                        diemCong.value = 0;
                        diemCong.setAttribute('readonly', 'readonly');
                        diemCong.setAttribute('disabled', 'disabled');
                        diemCong.setAttribute('max', '0');
                    } else {
                        diemCong.removeAttribute('readonly');
                        diemCong.removeAttribute('disabled');
                        diemCong.setAttribute('max', maxCong);
                        // Nếu giá trị hiện tại lớn hơn max mới thì đặt lại
                        if (cong > maxCong) {
                            diemCong.value = maxCong;
                            cong = maxCong;
                        }
                    }
                    // Cập nhật lại giá trị cộng sau khi có thể bị reset
                    cong = parseFloat(diemCong.value) || 0;
                }
                const tong = tongKhongCong + cong;
                tongDiem.value = tong.toFixed(1);
            }

            [diemBaoCao, diemThuyetTrinh, diemDemo, diemCauHoi, diemCong].forEach(el => {
                if (el) el.addEventListener('input', tinhTongDiem);
            });
            // Gọi lần đầu để cập nhật trạng thái điểm cộng nếu có dữ liệu sẵn
            tinhTongDiem();
        });
    </script>
@endsection
