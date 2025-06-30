@extends('admin.layout')

@section('title', 'Chi tiết điểm')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Thông tin sinh viên & đợt báo cáo -->
        <div class="col-12 mb-4">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <div class="card h-100">
                        <div class="card-header bg-info text-white">
                            <strong>Thông tin sinh viên</strong>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered mb-0">
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
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="card h-100">
                        <div class="card-header bg-secondary text-white">
                            <strong>Thông tin đợt báo cáo</strong>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered mb-0">
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
                                    <td>{{ $bangDiem->dotBaoCao->hocKy->ten ?? 'N/A' }}</td>
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
                </div>
            </div>
        </div>

        <!-- Bảng tổng hợp các lần chấm -->
        <div class="col-12">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white text-center">
                    <h5 class="mb-0">Bảng tổng hợp tất cả các lần chấm của giảng viên</h5>
                </div>
                <div class="card-body p-0">
                    <table class="table table-bordered table-striped mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 40px;">STT</th>
                                <th style="min-width: 120px;">Tên giảng viên</th>
                                <th style="min-width: 100px;">Vai trò chấm</th>
                                <th style="width: 80px;">Điểm báo cáo</th>
                                <th style="width: 80px;">Điểm thuyết trình</th>
                                <th style="width: 80px;">Điểm demo</th>
                                <th style="width: 80px;">Điểm câu hỏi</th>
                                <th style="width: 80px;">Điểm cộng</th>
                                <th style="width: 100px;">Tổng điểm</th>
                                <th style="min-width: 120px;">Ngày chấm</th>
                                <th style="min-width: 120px;">Bình luận</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $allBangDiem = \App\Models\BangDiem::where('sinh_vien_id', $bangDiem->sinh_vien_id)
                                    ->where('dot_bao_cao_id', $bangDiem->dot_bao_cao_id)
                                    ->with(['giangVien'])
                                    ->get();
                            @endphp
                            @foreach($allBangDiem as $i => $bd)
                                @php
                                    $phanCongVaiTro = null;
                                    if ($bd->giang_vien_id && $bd->dot_bao_cao_id) {
                                        $phanCongVaiTro = \App\Models\PhanCongVaiTro::whereHas('hoiDong.dotBaoCao.lichChams', function($q) use ($bd) {
                                            $q->where('dot_bao_cao_id', $bd->dot_bao_cao_id);
                                        })->where('tai_khoan_id', $bd->giang_vien_id)->first();
                                    }
                                    if ($phanCongVaiTro) {
                                        if (($phanCongVaiTro->vaiTro->ten ?? null) === 'Thành viên') {
                                            $vaiTroText = $phanCongVaiTro->loai_giang_vien ?? 'Thành viên';
                                        } else {
                                            $vaiTroText = $phanCongVaiTro->vaiTro->ten ?? $phanCongVaiTro->loai_giang_vien ?? 'N/A';
                                        }
                                    } else {
                                        $vaiTroText = $bd->vai_tro_cham ?? 'N/A';
                                    }
                                @endphp
                                <tr>
                                    <td class="text-center">{{ $i + 1 }}</td>
                                    <td>{{ $bd->giangVien->ten ?? 'N/A' }}</td>
                                    <td>{{ $vaiTroText }}</td>
                                    <td class="text-center">{{ $bd->diem_bao_cao !== null ? number_format($bd->diem_bao_cao, 2) : '-' }}</td>
                                    <td class="text-center">{{ $bd->diem_thuyet_trinh !== null ? number_format($bd->diem_thuyet_trinh, 2) : '-' }}</td>
                                    <td class="text-center">{{ $bd->diem_demo !== null ? number_format($bd->diem_demo, 2) : '-' }}</td>
                                    <td class="text-center">{{ $bd->diem_cau_hoi !== null ? number_format($bd->diem_cau_hoi, 2) : '-' }}</td>
                                    <td class="text-center">{{ $bd->diem_cong !== null ? number_format($bd->diem_cong, 2) : '-' }}</td>
                                    <td class="text-center">{{ number_format(($bd->diem_thuyet_trinh ?? 0) + ($bd->diem_demo ?? 0) + ($bd->diem_cau_hoi ?? 0) + ($bd->diem_cong ?? 0), 2) }}</td>
                                    <td class="text-center">{{ $bd->created_at ? $bd->created_at->format('d/m/Y H:i') : '-' }}</td>
                                    <td>{{ $bd->binh_luan ?? '' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            @php
                                // Tính toán điểm trung bình báo cáo, tổng điểm trung bình, điểm tổng kết
                                $diemBaoCaoTB = $allBangDiem->avg('diem_bao_cao');
                                $tongDiemTB = $allBangDiem->map(function($bd) {
                                    return ($bd->diem_thuyet_trinh ?? 0) + ($bd->diem_demo ?? 0) + ($bd->diem_cau_hoi ?? 0) + ($bd->diem_cong ?? 0);
                                })->avg();
                                $diemTongKet = $diemBaoCaoTB !== null && $tongDiemTB !== null ? min(round($diemBaoCaoTB * 0.2 + $tongDiemTB * 0.8, 2), 10) : null;
                            @endphp
                            <tr class="table-info">
                                <td colspan="3" class="text-end"><strong>Kết quả tổng hợp</strong></td>
                                <td colspan="2" class="text-center"><strong>Điểm trung bình báo cáo:<br>{{ $diemBaoCaoTB !== null ? number_format($diemBaoCaoTB, 2) : '-' }}</strong></td>
                                <td colspan="2" class="text-center"><strong>Tổng điểm trung bình:<br>{{ $tongDiemTB !== null ? number_format($tongDiemTB, 2) : '-' }}</strong></td>
                                <td colspan="2" class="text-center"><strong>Điểm tổng kết:<br>{{ $diemTongKet !== null ? number_format($diemTongKet, 2) : '-' }}</strong></td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection