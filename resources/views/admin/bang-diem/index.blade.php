@extends('admin.layout')

@section('title', 'Quản lý bảng điểm')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Quản lý bảng điểm</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.bang-diem.thong-ke') }}" class="btn btn-danger">
                            <i class="fas fa-chart-bar"></i> Thống kê
                        </a>
                        <a href="{{ route('admin.bang-diem.export') }}" class="btn btn-success">
                            <i class="fas fa-file-excel"></i> Xuất Excel
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Bộ lọc -->
                    <form method="GET" action="{{ route('admin.bang-diem.index') }}" class="mb-4">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="dot_bao_cao_id">Đợt báo cáo</label>
                                    <select class="form-control" id="dot_bao_cao_id" name="dot_bao_cao_id">
                                        <option value="">Tất cả</option>
                                        @foreach($dotBaoCaos as $dotBaoCao)
                                            <option value="{{ $dotBaoCao->id }}"
                                                    {{ request('dot_bao_cao_id') == $dotBaoCao->id ? 'selected' : '' }}>
                                                {{ $dotBaoCao->nam_hoc ?? 'N/A' }} - {{ $dotBaoCao->hocKy->ten ?? 'N/A' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="giang_vien_id">Giảng viên</label>
                                    <select class="form-control" id="giang_vien_id" name="giang_vien_id">
                                        <option value="">Tất cả</option>
                                        @foreach($giangViens as $giangVien)
                                            <option value="{{ $giangVien->id }}"
                                                    {{ request('giang_vien_id') == $giangVien->id ? 'selected' : '' }}>
                                                {{ $giangVien->ten }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="sinh_vien_id">Sinh viên</label>
                                    <select class="form-control" id="sinh_vien_id" name="sinh_vien_id">
                                        <option value="">Tất cả</option>
                                        @foreach($sinhViens as $sinhVien)
                                            <option value="{{ $sinhVien->id }}"
                                                    {{ request('sinh_vien_id') == $sinhVien->id ? 'selected' : '' }}>
                                                {{ $sinhVien->mssv }} - {{ $sinhVien->ten }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <div>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-search"></i> Lọc
                                        </button>
                                        <a href="{{ route('admin.bang-diem.index') }}" class="btn btn-secondary">
                                            <i class="fas fa-times"></i> Xóa lọc
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Bảng điểm -->
                    @if($bangDiemBySinhVien->isEmpty())
                        <div class="alert alert-info">
                            Không có dữ liệu điểm nào.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>STT</th>
                                        <th>MSSV</th>
                                        <th>Tên sinh viên</th>
                                        <th>Đợt báo cáo</th>
                                        <th>Điểm trung bình báo cáo</th>
                                        <th>Tổng điểm trung bình</th>
                                        <th>Điểm tổng kết</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $stt = 1; @endphp
                                    @foreach($bangDiemBySinhVien as $sinhVienId => $group)
                                        @php
                                            $bangDiemList = $group['list'];
                                            $diem_bao_cao_tb = $group['diem_bao_cao_tb'];
                                            $tong_ket = $group['tong_ket'];
                                            $diem_tong_ket = $group['diem_tong_ket'];
                                            $sinhVien = $bangDiemList->first()->sinhVien ?? null;
                                            $dotBaoCao = $bangDiemList->first()->dotBaoCao ?? null;
                                        @endphp
                                        <tr>
                                            <td>{{ $stt++ }}</td>
                                            <td>{{ $sinhVien->mssv ?? '' }}</td>
                                            <td>{{ $sinhVien->ten ?? '' }}</td>
                                            <td>{{ ($dotBaoCao->nam_hoc ?? 'N/A') . ' - ' . ($dotBaoCao->hocKy->ten ?? 'N/A') }}</td>
                                            <td>{{ $diem_bao_cao_tb !== null ? number_format($diem_bao_cao_tb, 2) : '-' }}</td>
                                            <td>{{ $tong_ket !== null ? number_format($tong_ket, 2) : '-' }}</td>
                                            <td>{{ $diem_tong_ket !== null ? number_format(min($diem_tong_ket, 10), 2) : '-' }}</td>
                                            <td>
                                                <a href="{{ route('admin.bang-diem.show', $bangDiemList->first()->id) }}" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                                                <a href="{{ route('admin.bang-diem.edit', $bangDiemList->first()->id) }}" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                                                <form action="{{ route('admin.bang-diem.destroy', $bangDiemList->first()->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc muốn xóa điểm này?')">
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
