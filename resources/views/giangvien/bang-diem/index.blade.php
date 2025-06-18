@extends('components.giangvien.app')

@section('title', 'Chấm điểm')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Danh sách sinh viên cần chấm điểm</h3>
                </div>
                <div class="card-body">
                    @if($phanCongChamsFiltered->isEmpty())
                        <div class="alert alert-info">
                            <p>Hiện tại không có sinh viên nào cần chấm điểm.</p>
                            <p>Điều này có thể do:</p>
                            <ul>
                                <li>Bạn chưa được phân công chấm điểm</li>
                                <li>Đề tài chưa được thêm vào lịch chấm</li>
                                <li>Nhóm chưa có sinh viên</li>
                                <li>Chưa có đợt báo cáo nào</li>
                            </ul>
                        </div>

                        <!-- Debug Information -->
                        <div class="alert alert-warning">
                            <h5>Thông tin Debug:</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>Thông tin giảng viên hiện tại:</h6>
                                    <ul>
                                        <li>ID: {{ $debugInfo['giang_vien_id'] }}</li>
                                        <li>Tên: {{ $debugInfo['giang_vien_info']->ten }}</li>
                                        <li>Email: {{ $debugInfo['giang_vien_info']->email }}</li>
                                        <li>Vai trò: {{ $debugInfo['giang_vien_info']->vai_tro }}</li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <h6>Tổng quan hệ thống:</h6>
                                    <ul>
                                        <li>Tổng phân công chấm: {{ $debugInfo['phan_cong_chams_total'] }}</li>
                                        <li>Phân công cho giảng viên này: {{ $debugInfo['phan_cong_chams_for_giang_vien'] }}</li>
                                        <li>Tổng lịch chấm: {{ $debugInfo['lich_chams_total'] }}</li>
                                        <li>Tổng đề tài: {{ $debugInfo['de_tais_total'] }}</li>
                                        <li>Tổng nhóm: {{ $debugInfo['nhoms_total'] }}</li>
                                        <li>Tổng sinh viên: {{ $debugInfo['sinh_viens_total'] }}</li>
                                        <li>Tổng đợt báo cáo: {{ $debugInfo['dot_bao_caos_total'] }}</li>
                                    </ul>
                                </div>
                            </div>
                            
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <h6>Chi tiết từng bước:</h6>
                                    <ul>
                                        <li>Phân công chấm cơ bản: {{ $debugDetails['phan_cong_cham_basic'] }}</li>
                                        <li>Phân công có lịch chấm: {{ $debugDetails['phan_cong_cham_with_lich_cham'] }}</li>
                                        <li>Phân công có nhóm: {{ $debugDetails['phan_cong_cham_with_nhom'] }}</li>
                                        <li>Phân công có sinh viên: {{ $debugDetails['phan_cong_cham_with_sinh_vien'] }}</li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <h6>Hướng dẫn khắc phục:</h6>
                                    <ul>
                                        @if($debugDetails['phan_cong_cham_basic'] == 0)
                                            <li class="text-danger">❌ Bạn chưa được phân công chấm đề tài nào</li>
                                            <li>→ Vào Admin → Phân công chấm → Tạo mới</li>
                                        @elseif($debugDetails['phan_cong_cham_with_lich_cham'] == 0)
                                            <li class="text-warning">⚠️ Đề tài chưa có lịch chấm</li>
                                            <li>→ Vào Admin → Lịch chấm → Tạo mới</li>
                                        @elseif($debugDetails['phan_cong_cham_with_nhom'] == 0)
                                            <li class="text-warning">⚠️ Đề tài chưa có nhóm</li>
                                            <li>→ Vào Admin → Đề tài → Chỉnh sửa → Chọn nhóm</li>
                                        @elseif($debugDetails['phan_cong_cham_with_sinh_vien'] == 0)
                                            <li class="text-warning">⚠️ Nhóm chưa có sinh viên</li>
                                            <li>→ Vào Admin → Nhóm → Chỉnh sửa → Thêm sinh viên</li>
                                        @else
                                            <li class="text-success">✅ Tất cả điều kiện đã đủ!</li>
                                        @endif
                                    </ul>
                                </div>
                            </div>
                            
                            @if(!empty($debugDetails['phan_cong_cham_details']))
                                <h6 class="mt-3">Chi tiết phân công chấm:</h6>
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Đề tài ID</th>
                                                <th>Tên đề tài</th>
                                                <th>Nhóm ID</th>
                                                <th>Tên nhóm</th>
                                                <th>Số SV</th>
                                                <th>Lịch chấm ID</th>
                                                <th>Ngày chấm</th>
                                                <th>GV Phản biện</th>
                                                <th>GV Khác</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($debugDetails['phan_cong_cham_details'] as $detail)
                                                <tr>
                                                    <td>{{ $detail['id'] }}</td>
                                                    <td>{{ $detail['de_tai_id'] }}</td>
                                                    <td>{{ $detail['de_tai_ten'] }}</td>
                                                    <td>{{ $detail['nhom_id'] }}</td>
                                                    <td>{{ $detail['nhom_ten'] }}</td>
                                                    <td>{{ $detail['sinh_viens_count'] }}</td>
                                                    <td>{{ $detail['lich_cham_id'] }}</td>
                                                    <td>{{ $detail['lich_cham_date'] }}</td>
                                                    <td>{{ $detail['giang_vien_phan_bien_id'] }}</td>
                                                    <td>{{ $detail['giang_vien_khac_id'] }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>STT</th>
                                        <th>Mã sinh viên</th>
                                        <th>Tên sinh viên</th>
                                        <th>Nhóm</th>
                                        <th>Đề tài</th>
                                        <th>Đợt báo cáo</th>
                                        <th>Lịch chấm</th>
                                        <th>Trạng thái</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $stt = 1; @endphp
                                    @foreach($phanCongChamsFiltered as $phanCongCham)
                                        @foreach($phanCongCham->deTai->nhom->sinhViens as $sinhVien)
                                            @php
                                                $daChamDiem = $bangDiems->where('sinh_vien_id', $sinhVien->id)
                                                    ->where('dot_bao_cao_id', $phanCongCham->deTai->lichCham->dot_bao_cao_id)
                                                    ->first();
                                            @endphp
                                            <tr>
                                                <td>{{ $stt++ }}</td>
                                                <td>{{ $sinhVien->mssv }}</td>
                                                <td>{{ $sinhVien->ten }}</td>
                                                <td>{{ $phanCongCham->deTai->nhom->ten }}</td>
                                                <td>{{ $phanCongCham->deTai->ten_de_tai }}</td>
                                                <td>{{ $phanCongCham->deTai->lichCham->dotBaoCao->nam_hoc }}</td>
                                                <td>{{ \Carbon\Carbon::parse($phanCongCham->deTai->lichCham->lich_tao)->format('d/m/Y H:i') }}</td>
                                                <td>
                                                    @if($daChamDiem)
                                                        <span class="badge bg-success">Đã chấm</span>
                                                    @else
                                                        <span class="badge bg-danger">Chưa chấm</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($daChamDiem)
                                                        <a href="{{ route('giangvien.bang-diem.show', $daChamDiem->id) }}" 
                                                           class="btn btn-sm btn-info">
                                                            <i class="fas fa-eye"></i> Xem
                                                        </a>
                                                        <a href="{{ route('giangvien.bang-diem.edit', $daChamDiem->id) }}" 
                                                           class="btn btn-sm btn-warning">
                                                            <i class="fas fa-edit"></i> Sửa
                                                        </a>
                                                    @else
                                                        <a href="{{ route('giangvien.bang-diem.create', [$sinhVien->id, $phanCongCham->deTai->lichCham->dot_bao_cao_id]) }}" 
                                                           class="btn btn-sm btn-primary">
                                                            <i class="fas fa-plus"></i> Chấm điểm
                                                        </a>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Danh sách điểm đã chấm -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Điểm đã chấm</h3>
                </div>
                <div class="card-body">
                    @if($bangDiems->isEmpty())
                        <div class="alert alert-info">
                            Bạn chưa chấm điểm cho sinh viên nào.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>STT</th>
                                        <th>Mã sinh viên</th>
                                        <th>Tên sinh viên</th>
                                        <th>Đợt báo cáo</th>
                                        <th>Điểm báo cáo</th>
                                        <th>Điểm thuyết trình</th>
                                        <th>Điểm demo</th>
                                        <th>Điểm câu hỏi</th>
                                        <th>Điểm cộng</th>
                                        <th>Tổng điểm</th>
                                        <th>Ngày chấm</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($bangDiems as $bangDiem)
                                        @php
                                            $tongDiem = $bangDiem->diem_bao_cao + $bangDiem->diem_thuyet_trinh + 
                                                       $bangDiem->diem_demo + $bangDiem->diem_cau_hoi + $bangDiem->diem_cong;
                                        @endphp
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $bangDiem->sinhVien->mssv }}</td>
                                            <td>{{ $bangDiem->sinhVien->ten }}</td>
                                            <td>{{ $bangDiem->dotBaoCao->nam_hoc }}</td>
                                            <td>{{ $bangDiem->diem_bao_cao }}</td>
                                            <td>{{ $bangDiem->diem_thuyet_trinh }}</td>
                                            <td>{{ $bangDiem->diem_demo }}</td>
                                            <td>{{ $bangDiem->diem_cau_hoi }}</td>
                                            <td>{{ $bangDiem->diem_cong }}</td>
                                            <td><strong>{{ number_format($tongDiem, 2) }}</strong></td>
                                            <td>{{ $bangDiem->created_at->format('d/m/Y H:i') }}</td>
                                            <td>
                                                <a href="{{ route('giangvien.bang-diem.show', $bangDiem->id) }}" 
                                                   class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('giangvien.bang-diem.edit', $bangDiem->id) }}" 
                                                   class="btn btn-sm btn-warning">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('giangvien.bang-diem.destroy', $bangDiem->id) }}" 
                                                      method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" 
                                                            onclick="return confirm('Bạn có chắc muốn xóa điểm này?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 