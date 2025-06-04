@extends('components.giangvien.app')

@section('title', 'Danh sách đề tài')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Danh sách nhóm chọn đề tài</h3>
                    <div class="card-tools">
                        <a href="{{ route('giangvien.de-tai.create') }}" class="btn btn-primary">
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
                                    <th>Mẫu đề tài</th>
                                    <th>Mô tả</th>
                                    <th>Ngày bắt đầu</th>
                                    <th>Ngày kết thúc</th>
                                    <th>Nhóm</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($deTais as $deTai)
                                <tr>
                                    <td>{{ $deTai->id }}</td>
                                    <td>{{ $deTai->ma_de_tai }}</td>
                                    <td>{{ $deTai->deTaiMau->ten ?? 'N/A' }}</td>
                                    <td>{{ $deTai->mo_ta }}</td>
                                    <td>{{ $deTai->ngay_bat_dau ? $deTai->ngay_bat_dau->format('d/m/Y') : 'N/A' }}</td>
                                    <td>{{ $deTai->ngay_ket_thuc ? $deTai->ngay_ket_thuc->format('d/m/Y') : 'N/A' }}</td>
                                    <td>{{ $deTai->nhom->ten ?? 'N/A' }}</td>
                                    <td>
                                        <a href="{{ route('giangvien.de-tai.edit', $deTai) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-edit"></i> Sửa
                                        </a>
                                        <form action="{{ route('giangvien.de-tai.destroy', $deTai) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc chắn muốn xóa đề tài này?')">
                                                <i class="fas fa-trash"></i> Xóa
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center">Không có dữ liệu</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 