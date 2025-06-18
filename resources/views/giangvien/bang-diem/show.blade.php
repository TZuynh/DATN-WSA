@extends('components.giangvien.app')

@section('title', 'Chi tiết điểm')

@section('content')
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
                                    <td><strong>{{ number_format($bangDiem->diem_bao_cao + $bangDiem->diem_thuyet_trinh + $bangDiem->diem_demo + $bangDiem->diem_cau_hoi + $bangDiem->diem_cong, 2) }}</strong></td>
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