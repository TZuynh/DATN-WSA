@extends('admin.layout')
@section('title', 'Quản lý đăng ký')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Phần Đăng ký hướng dẫn -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Danh sách đăng ký giảng viên hướng dẫn</h3>
                    <div>
                        <form action="{{ route('admin.dang-ky.index') }}" method="GET" class="form-inline d-inline-flex mb-2 me-2">
                            <select name="sinh_vien_id" class="form-select me-2">
                                <option value="">Tất cả sinh viên</option>
                                @foreach($sinhViens as $sinhVien)
                                    <option value="{{ $sinhVien->id }}" {{ request('sinh_vien_id') == $sinhVien->id ? 'selected' : '' }}>
                                        {{ $sinhVien->mssv }} - {{ $sinhVien->ten }}
                                    </option>
                                @endforeach
                            </select>
                            {{-- Dropdown giảng viên --}}
                             <select name="giang_vien_id" class="form-select me-2">
                                 <option value="">Tất cả giảng viên</option>
                                 @foreach($giangViens as $giangVien)
                                     <option value="{{ $giangVien->id }}" {{ request('giang_vien_id') == $giangVien->id ? 'selected' : '' }}>
                                         {{ $giangVien->ten }}
                                     </option>
                                 @endforeach
                             </select>
                            <button type="submit" class="btn btn-outline-primary">Lọc</button>
                        </form>
                         <a href="{{ route('admin.dang-ky.create') }}" class="btn btn-success">
                            <i class="fas fa-plus"></i> Thêm mới
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th style="width: 5%; text-align: center;">STT</th>
                                    <th style="width: 25%;">Giảng viên hướng dẫn</th>
                                    <th style="width: 25%;">Sinh viên</th>
                                    <th style="width: 20%;">Ngày đăng ký</th>
                                    <th style="width: 15%; text-align: center;">Trạng thái</th>
                                    <th style="width: 10%; text-align: center;">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($dangKys as $key => $dangKy)
                                    <tr>
                                        <td style="text-align: center;">{{ $key + 1 }}</td>
                                        <td>{{ $dangKy->giangVien->ten }}</td>
                                        <td>{{ $dangKy->sinhVien->mssv }} - {{ $dangKy->sinhVien->ten }}</td>
                                        <td>{{ $dangKy->created_at->format('d/m/Y H:i') }}</td>
                                        <td style="text-align: center;">
                                            @if($dangKy->trang_thai == 'cho_duyet')
                                                <span class="badge bg-warning">Chờ duyệt</span>
                                            @elseif($dangKy->trang_thai == 'da_duyet')
                                                <span class="badge bg-success">Đã duyệt</span>
                                            @else
                                                <span class="badge bg-danger">Từ chối</span>
                                            @endif
                                        </td>
                                        <td style="text-align: center;">
                                            @if($dangKy->trang_thai == 'cho_duyet')
                                                <form action="{{ route('admin.dang-ky.update', $dangKy) }}" method="POST" class="d-inline me-1">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="trang_thai" value="da_duyet">
                                                    <button type="submit" class="btn btn-success btn-sm" title="Duyệt">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>
                                                <form action="{{ route('admin.dang-ky.update', $dangKy) }}" method="POST" class="d-inline me-1">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="trang_thai" value="tu_choi">
                                                    <button type="submit" class="btn btn-danger btn-sm" title="Từ chối">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </form>
                                            @endif
                                            <a href="{{ route('admin.dang-ky.edit', $dangKy->id) }}" class="btn btn-info btn-sm me-1" title="Sửa">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.dang-ky.destroy', $dangKy->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa đăng ký này?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" title="Xóa">
                                                    <i class="fas fa-trash"></i>
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
