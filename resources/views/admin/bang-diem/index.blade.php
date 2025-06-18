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
                                                {{ $dotBaoCao->ten_dot_bao_cao }}
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
                    @if($bangDiems->isEmpty())
                        <div class="alert alert-info">
                            Không có dữ liệu điểm nào.
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
                                        <th>Giảng viên chấm</th>
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
                                            <td>{{ $bangDiem->giangVien->ten }}</td>
                                            <td>{{ $bangDiem->diem_bao_cao }}</td>
                                            <td>{{ $bangDiem->diem_thuyet_trinh }}</td>
                                            <td>{{ $bangDiem->diem_demo }}</td>
                                            <td>{{ $bangDiem->diem_cau_hoi }}</td>
                                            <td>{{ $bangDiem->diem_cong }}</td>
                                            <td><strong>{{ number_format($tongDiem, 2) }}</strong></td>
                                            <td>{{ $bangDiem->created_at->format('d/m/Y H:i') }}</td>
                                            <td>
                                                <a href="{{ route('admin.bang-diem.show', $bangDiem->id) }}" 
                                                   class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.bang-diem.edit', $bangDiem->id) }}" 
                                                   class="btn btn-sm btn-warning">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('admin.bang-diem.destroy', $bangDiem->id) }}" 
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

                        <!-- Phân trang -->
                        <div class="d-flex justify-content-center">
                            {{ $bangDiems->appends(request()->query())->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 