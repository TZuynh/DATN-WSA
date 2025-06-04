@extends('admin.layout')

@section('title', 'Quản lý đề tài')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Danh sách các nhóm chọn đề tài</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.de-tai.create') }}" class="btn btn-success btn-sm">
                            <i class="fas fa-plus"></i> Thêm mới
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Mã đề tài</th>
                                    <th>Tên mẫu</th>
                                    <th>Mô tả</th>
                                    <th>Ngày bắt đầu</th>
                                    <th>Ngày kết thúc</th>
                                    <th>Nhóm</th>
                                    <th>Giảng viên</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($deTais as $deTai)
                                <tr>
                                    <td>{{ $deTai->id }}</td>
                                    <td>{{ $deTai->ma_de_tai }}</td>
                                    <td>{{ $deTai->deTaiMau->ten }}</td>
                                    <td>{{ Str::limit($deTai->mo_ta, 50) }}</td>
                                    <td>{{ $deTai->ngay_bat_dau ? $deTai->ngay_bat_dau->format('d/m/Y') : 'N/A' }}</td>
                                    <td>{{ $deTai->ngay_ket_thuc ? $deTai->ngay_ket_thuc->format('d/m/Y') : 'N/A' }}</td>
                                    <td>{{ $deTai->nhom ? $deTai->nhom->ten : 'N/A' }}</td>
                                    <td>{{ $deTai->giangVien ? $deTai->giangVien->ten : 'N/A' }}</td>
                                    <td>
                                        <a href="{{ route('admin.de-tai.edit', $deTai) }}" class="btn btn-warning btn-sm">
                                            <i class="fas fa-edit"></i> Sửa
                                        </a>
                                        <form action="{{ route('admin.de-tai.destroy', $deTai) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc chắn muốn xóa?')">
                                                <i class="fas fa-trash"></i> Xóa
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    {{ $deTais->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection