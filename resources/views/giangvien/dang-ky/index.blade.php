@extends('components.giangvien.app')
@section('title', content: 'Quản lý đăng ký')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Phần Đăng ký hướng dẫn -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Danh sách đăng ký giảng viên hướng dẫn</h3>
                    <div>
                        <form action="{{ route('giangvien.dang-ky.index') }}" method="GET" class="form-inline d-inline-flex mb-2">
                            <select name="sinh_vien_id" class="form-select me-2">
                                <option value="">Tất cả sinh viên</option>
                                @foreach($sinhViens as $sinhVien)
                                    <option value="{{ $sinhVien->id }}" {{ request('sinh_vien_id') == $sinhVien->id ? 'selected' : '' }}>
                                        {{ $sinhVien->mssv }} - {{ $sinhVien->ten }}
                                    </option>
                                @endforeach
                            </select>
                            <button type="submit" class="btn btn-outline-primary me-2">Lọc</button>
                        </form>
                        <a href="{{ route('giangvien.dang-ky.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Thêm mới
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>STT</th>
                                    <th>Giảng viên hướng dẫn</th>
                                    <th>Sinh viên</th>
                                    <th>Ngày đăng ký</th>
                                    <th>Trạng thái</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($dangKys as $key => $dangKy)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $dangKy->giangVien->ten }}</td>
                                        <td>{{ $dangKy->sinhVien->mssv }} - {{ $dangKy->sinhVien->ten }}</td>
                                        <td>{{ $dangKy->created_at->format('d/m/Y H:i') }}</td>
                                        <td>
                                            @if($dangKy->trang_thai == 'cho_duyet')
                                                <span class="badge bg-warning">Chờ duyệt</span>
                                            @elseif($dangKy->trang_thai == 'da_duyet')
                                                <span class="badge bg-success">Đã duyệt</span>
                                            @else
                                                <span class="badge bg-danger">Từ chối</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($dangKy->trang_thai == 'cho_duyet')
                                                <form action="{{ route('giangvien.dang-ky.update', $dangKy) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="trang_thai" value="da_duyet">
                                                    <button type="submit" class="btn btn-success btn-sm">
                                                        <i class="fas fa-check"></i> Duyệt
                                                    </button>
                                                </form>
                                                <form action="{{ route('giangvien.dang-ky.update', $dangKy) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="trang_thai" value="tu_choi">
                                                    <button type="submit" class="btn btn-danger btn-sm">
                                                        <i class="fas fa-times"></i> Từ chối
                                                    </button>
                                                </form>
                                            @endif
                                            <a href="{{ route('giangvien.dang-ky.edit', $dangKy) }}" class="btn btn-info btn-sm">
                                                <i class="fas fa-edit"></i> Sửa
                                            </a>
                                            <form action="{{ route('giangvien.dang-ky.destroy', $dangKy) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc chắn muốn xóa?')">
                                                    <i class="fas fa-trash"></i> Xóa
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">Không có dữ liệu</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        {{ $dangKys->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
