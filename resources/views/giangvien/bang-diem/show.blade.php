@extends('components.giangvien.app')

@section('title', 'Chi tiết điểm')

@section('content')
@php
    $dotBaoCao = $bangDiem->dotBaoCao;
@endphp
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Chi tiết điểm</h3>
                    <div class="card-tools">
                        <a href="{{ route('giangvien.bang-diem.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Quay lại
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
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

                    <div class="row mt-4">
                        <div class="col-md-8">
                            <h5>Chi tiết điểm</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th width="25%">Điểm báo cáo:</th>
                                    <td>{{ $bangDiem->diem_bao_cao }}</td>
                                </tr>
                                <tr>
                                    <th>Điểm thuyết trình:</th>
                                    <td>{{ $bangDiem->diem_thuyet_trinh }}</td>
                                </tr>
                                <tr>
                                    <th>Điểm demo:</th>
                                    <td>{{ $bangDiem->diem_demo }}</td>
                                </tr>
                                <tr>
                                    <th>Điểm câu hỏi:</th>
                                    <td>{{ $bangDiem->diem_cau_hoi }}</td>
                                </tr>
                                <tr>
                                    <th>Điểm cộng:</th>
                                    <td>{{ $bangDiem->diem_cong }}</td>
                                </tr>
                                <tr class="table-primary">
                                    <th><strong>Tổng điểm:</strong></th>
                                    <td><strong>{{ number_format(($bangDiem->diem_bao_cao * 0.2) + ((($bangDiem->diem_thuyet_trinh ?? 0) + ($bangDiem->diem_demo ?? 0) + ($bangDiem->diem_cau_hoi ?? 0) + ($bangDiem->diem_cong ?? 0)) * 0.8), 2) }}</strong></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-4">
                            <h5>Thông tin chấm</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th width="40%">Giảng viên:</th>
                                    <td>{{ $bangDiem->giangVien->ten }}</td>
                                </tr>
                                <tr>
                                    <th>Vai trò chấm:</th>
                                    <td>
                                        @if($bangDiem->vai_tro_cham == 'Phản biện')
                                            <span class="badge bg-primary">{{ $bangDiem->vai_tro_cham }}</span>
                                        @elseif($bangDiem->vai_tro_cham == 'Giảng viên khác')
                                            <span class="badge bg-info">{{ $bangDiem->vai_tro_cham }}</span>
                                        @elseif($bangDiem->vai_tro_cham == 'Hướng dẫn')
                                            <span class="badge bg-success">{{ $bangDiem->vai_tro_cham }}</span>
                                        @else
                                            <span class="badge bg-secondary">{{ $bangDiem->vai_tro_cham ?: 'N/A' }}</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Ngày chấm:</th>
                                    <td>{{ $bangDiem->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <th>Cập nhật:</th>
                                    <td>{{ $bangDiem->updated_at->format('d/m/Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    @if($bangDiem->binh_luan)
                    <div class="row mt-4">
                        <div class="col-12">
                            <h5>Bình luận</h5>
                            <div class="alert alert-info">
                                {{ $bangDiem->binh_luan }}
                            </div>
                        </div>
                    </div>
                    @endif

                    @php
                        // Lấy hội đồng hiện tại từ dotBaoCao hoặc từ route/controller truyền vào
                        $dotBaoCaoId = $bangDiem->dot_bao_cao_id;
                        $hoiDongId = null;
                        // Ưu tiên lấy từ lichCham nếu có
                        if (isset($bangDiem->dotBaoCao->lichChams)) {
                            foreach ($bangDiem->dotBaoCao->lichChams as $lichCham) {
                                if ($lichCham->nhom_id == ($bangDiem->sinhVien->chiTietNhom->nhom_id ?? null)) {
                                    $hoiDongId = $lichCham->hoi_dong_id;
                                    break;
                                }
                            }
                        }
                        // Lấy tất cả bảng điểm của sinh viên này trong cùng đợt báo cáo và hội đồng
                        $allBangDiem = \App\Models\BangDiem::where('sinh_vien_id', $bangDiem->sinh_vien_id)
                            ->where('dot_bao_cao_id', $dotBaoCaoId)
                            ->with(['giangVien'])
                            ->get()
                            ->filter(function($bd) use ($hoiDongId) {
                                // Lọc theo hội đồng nếu có
                                if (!$hoiDongId) return true;
                                $phanCong = \App\Models\PhanCongVaiTro::where('tai_khoan_id', $bd->giang_vien_id)
                                    ->whereHas('hoiDong', function($q) use ($hoiDongId) {
                                        $q->where('id', $hoiDongId);
                                    })->first();
                                return $phanCong !== null;
                            });
                        // Tính toán điểm trung bình, tổng kết
                        $validBangDiem = $allBangDiem->filter(function($bd) {
                            $tong =
                                ($bd->diem_thuyet_trinh ?? 0)
                              + ($bd->diem_demo ?? 0)
                              + ($bd->diem_cau_hoi ?? 0)
                              + ($bd->diem_cong ?? 0);
                            return $tong > 0;
                        });
                        $diemBaoCaoTB = $validBangDiem->avg('diem_bao_cao');
                        $tongDiemTB = $validBangDiem->map(function($bd) {
                            return
                                ($bd->diem_thuyet_trinh ?? 0)
                              + ($bd->diem_demo ?? 0)
                              + ($bd->diem_cau_hoi ?? 0)
                              + ($bd->diem_cong ?? 0);
                        })->avg();
                        $diemTongKet = $diemBaoCaoTB !== null && $tongDiemTB !== null
                            ? min(round($diemBaoCaoTB * 0.2 + $tongDiemTB * 0.8, 2), 10)
                            : null;
                    @endphp
                    <div class="row mt-4">
                        <div class="col-12">
                            <h5>Bảng tổng hợp tất cả các lần chấm của sinh viên trong hội đồng này</h5>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>STT</th>
                                            <th>Giảng viên chấm</th>
                                            <th>Vai trò chấm</th>
                                            <th>Điểm báo cáo</th>
                                            <th>Thuyết trình</th>
                                            <th>Demo</th>
                                            <th>Câu hỏi</th>
                                            <th>Cộng</th>
                                            <th>Tổng điểm</th>
                                            <th>Ngày chấm</th>
                                            <th>Bình luận</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($allBangDiem as $i => $bd)
                                            @php
                                                $phanCongVaiTro = \App\Models\PhanCongVaiTro::where('tai_khoan_id', $bd->giang_vien_id)
                                                    ->whereHas('hoiDong', function($q) use ($hoiDongId) {
                                                        $q->where('id', $hoiDongId);
                                                    })->first();
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
                                                <td class="text-center">{{ number_format((($bd->diem_bao_cao ?? 0) * 0.2) + ((($bd->diem_thuyet_trinh ?? 0) + ($bd->diem_demo ?? 0) + ($bd->diem_cau_hoi ?? 0) + ($bd->diem_cong ?? 0)) * 0.8), 2) }}</td>
                                                <td class="text-center">{{ $bd->created_at ? $bd->created_at->format('d/m/Y H:i') : '-' }}</td>
                                                <td>{{ $bd->binh_luan ?? '' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr class="table-info">
                                            <td colspan="3" class="text-end"><strong>Kết quả tổng hợp</strong></td>
                                            <td colspan="2" class="text-center">
                                                <strong>Điểm trung bình báo cáo:<br>
                                                {{ $diemBaoCaoTB !== null ? number_format($diemBaoCaoTB, 2) : '-' }}</strong>
                                            </td>
                                            <td colspan="2" class="text-center">
                                                <strong>Tổng điểm trung bình:<br>
                                                {{ $tongDiemTB   !== null ? number_format($tongDiemTB, 2)   : '-' }}</strong>
                                            </td>
                                            <td colspan="2" class="text-center">
                                                <strong>Điểm tổng kết:<br>
                                                {{ $diemTongKet  !== null ? number_format($diemTongKet,  2)   : '-' }}</strong>
                                            </td>
                                            <td colspan="2"></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <a href="{{ route('giangvien.bang-diem.edit', $bangDiem->id) }}" class="btn btn-warning">
                                <i class="fas fa-edit"></i> Chỉnh sửa
                            </a>
                            <form action="{{ route('giangvien.bang-diem.destroy', $bangDiem->id) }}"
                                  method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger"
                                        onclick="return confirm('Bạn có chắc muốn xóa điểm này?')">
                                    <i class="fas fa-trash"></i> Xóa
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
