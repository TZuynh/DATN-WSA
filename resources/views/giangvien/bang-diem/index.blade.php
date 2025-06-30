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
                    @if($dsSinhVien->isEmpty())
                        <div class="alert alert-info">
                            <p>Hiện tại không có sinh viên nào cần chấm điểm.</p>
                            <p>Điều này có thể do:</p>
                            <ul>
                                <li>Bạn chưa được phân công chấm điểm</li>
                                @if($coDeTaiNhungKhongCoLichCham)
                                    <li><strong>Có đề tài nhưng chưa được đưa vào lịch chấm</strong></li>
                                @else
                                    <li>Đề tài chưa được thêm vào lịch chấm</li>
                                @endif
                                <li>Nhóm chưa có sinh viên</li>
                                <li>Chưa có đợt báo cáo nào</li>
                            </ul>
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
                                        <th>Vai trò chấm</th>
                                        @php $showDiemBaoCao = $dsSinhVien->contains(function($item) { return in_array($item['vai_tro_cham'], ['Hướng dẫn', 'Phản biện']); }); @endphp
                                        @if($showDiemBaoCao)
                                            <th>Điểm báo cáo</th>
                                        @endif
                                        <th>Điểm thuyết trình</th>
                                        <th>Điểm demo</th>
                                        <th>Điểm câu hỏi</th>
                                        <th>Điểm cộng</th>
                                        <th>Tổng điểm</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($dsSinhVien as $item)
                                        @php
                                            $sinhVien = $item['sinh_vien'];
                                            $nhom = $item['nhom'];
                                            $deTai = $item['de_tai'];
                                            $phanCongCham = $item['phan_cong'];
                                            $vai_tro_cham = $item['vai_tro_cham'];
                                            $dotBaoCaoId = $deTai?->lichCham?->dot_bao_cao_id;
                                            $daChamDiem = $bangDiems->where('sinh_vien_id', $sinhVien->id)
                                                ->where('dot_bao_cao_id', $dotBaoCaoId)
                                                ->first();
                                            $diemChuaLichCham = $bangDiems->where('sinh_vien_id', $sinhVien->id)
                                                ->whereNull('dot_bao_cao_id')
                                                ->first();
                                            $tongDiem = 0;
                                            if ($daChamDiem) {
                                                $tongDiem = $daChamDiem->diem_bao_cao + $daChamDiem->diem_thuyet_trinh +
                                                           $daChamDiem->diem_demo + $daChamDiem->diem_cau_hoi + $daChamDiem->diem_cong;
                                            } elseif ($diemChuaLichCham) {
                                                $tongDiem = $diemChuaLichCham->diem_bao_cao + $diemChuaLichCham->diem_thuyet_trinh;
                                            }
                                            $daChamDiemCoBan = ($daChamDiem && 
                                                !is_null($daChamDiem->diem_bao_cao) && 
                                                !is_null($daChamDiem->diem_thuyet_trinh)) ||
                                                ($diemChuaLichCham && 
                                                !is_null($diemChuaLichCham->diem_bao_cao) && 
                                                !is_null($diemChuaLichCham->diem_thuyet_trinh));
                                            $canGradeBaoCaoAndThuyetTrinh = in_array($vai_tro_cham, ['Hướng dẫn', 'Phản biện']);
                                        @endphp
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $sinhVien->mssv }}</td>
                                            <td>{{ $sinhVien->ten }}</td>
                                            <td>{{ $nhom->ten }}</td>
                                            <td>{{ $deTai->ten_de_tai }}</td>
                                            <td>{{ $deTai->lichCham?->dotBaoCao?->nam_hoc ?? 'N/A' }} - {{ $deTai->lichCham?->dotBaoCao?->hocKy?->ten ?? 'N/A' }}</td>
                                            <td>{{ $deTai->lichCham?->lich_tao ? \Carbon\Carbon::parse($deTai->lichCham->lich_tao)->format('d/m/Y H:i') : 'N/A' }}</td>
                                            <td>
                                                @if($vai_tro_cham == 'Phản biện')
                                                    <span class="badge bg-primary">{{ $vai_tro_cham }}</span>
                                                    <small class="d-block text-muted">Được chấm: Điểm báo cáo, Điểm thuyết trình, và các điểm khác</small>
                                                @elseif($vai_tro_cham == 'Giảng viên khác')
                                                    <span class="badge bg-info">{{ $vai_tro_cham }}</span>
                                                    <small class="d-block text-muted">Được chấm: Điểm thuyết trình, Điểm demo, Điểm câu hỏi, Điểm cộng</small>
                                                @elseif($vai_tro_cham == 'Hướng dẫn')
                                                    <span class="badge bg-success">{{ $vai_tro_cham }}</span>
                                                    <small class="d-block text-muted">Được chấm: Điểm báo cáo, Điểm thuyết trình, và các điểm khác</small>
                                                @elseif($vai_tro_cham == 'Trưởng tiểu ban')
                                                    <span class="badge bg-warning text-dark">{{ $vai_tro_cham }}</span>
                                                    <small class="d-block text-muted">Được chấm: Điểm thuyết trình, Điểm demo, Điểm câu hỏi, Điểm cộng</small>
                                                @elseif($vai_tro_cham == 'Thư ký')
                                                    <span class="badge bg-secondary">{{ $vai_tro_cham }}</span>
                                                    <small class="d-block text-muted">Được chấm: Điểm thuyết trình, Điểm demo, Điểm câu hỏi, Điểm cộng</small>
                                                @else
                                                    <span class="badge bg-secondary">{{ $vai_tro_cham ?: 'N/A' }}</span>
                                                    <small class="d-block text-muted">Được chấm: Điểm thuyết trình, Điểm demo, Điểm câu hỏi, Điểm cộng</small>
                                                @endif
                                            </td>
                                            @if(in_array($vai_tro_cham, ['Hướng dẫn', 'Phản biện']))
                                                <td>{{ $daChamDiem ? number_format($daChamDiem->diem_bao_cao, 1) : ($diemChuaLichCham ? number_format($diemChuaLichCham->diem_bao_cao, 1) : 'Chưa chấm') }}</td>
                                            @endif
                                            <td>{{ $daChamDiem ? number_format($daChamDiem->diem_thuyet_trinh, 1) : ($diemChuaLichCham ? number_format($diemChuaLichCham->diem_thuyet_trinh, 1) : 'Chưa chấm') }}</td>
                                            <td>{{ $daChamDiem ? number_format($daChamDiem->diem_demo, 1) : 'Chưa chấm' }}</td>
                                            <td>{{ $daChamDiem ? number_format($daChamDiem->diem_cau_hoi, 1) : 'Chưa chấm' }}</td>
                                            <td>{{ $daChamDiem ? number_format($daChamDiem->diem_cong, 1) : '0.0' }}</td>
                                            <td><strong>{{ number_format($tongDiem, 1) }}</strong></td>
                                            <td>
                                                @if($daChamDiem)
                                                    <a href="{{ route('giangvien.bang-diem.edit', $daChamDiem->id) }}" 
                                                       class="btn btn-sm btn-warning">
                                                        <i class="fas fa-edit"></i> 
                                                        @if($daChamDiemCoBan && !$canGradeBaoCaoAndThuyetTrinh)
                                                            Nhập điểm còn lại
                                                        @else
                                                            Sửa điểm
                                                        @endif
                                                    </a>
                                                @elseif($diemChuaLichCham && $dotBaoCaoId)
                                                    <a href="{{ route('giangvien.bang-diem.edit', $diemChuaLichCham->id) }}"
                                                       class="btn btn-sm btn-info">
                                                        <i class="fas fa-sync"></i> Sửa điểm
                                                    </a>
                                                @else
                                                    @if(!$daChamDiemCoBan || $canGradeBaoCaoAndThuyetTrinh)
                                                        @php
                                                            $routeParams = ['sinhVienId' => $sinhVien->id];
                                                            if ($dotBaoCaoId) {
                                                                $routeParams['dotBaoCaoId'] = $dotBaoCaoId;
                                                            }
                                                        @endphp
                                                        <a href="{{ route('giangvien.bang-diem.create', $routeParams) }}"
                                                           class="btn btn-sm btn-primary">
                                                            <i class="fas fa-plus"></i> Chấm điểm
                                                        </a>
                                                    @endif
                                                @endif
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

    <!-- Danh sách điểm đã chấm -->
    {{-- <div class="row mt-4">
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
                                        <th>Vai trò chấm</th>
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
                                            <td>{{ $bangDiem->dotBaoCao->nam_hoc ?? 'N/A' }}</td>
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
    </div> --}}
</div>
@endsection
