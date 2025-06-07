@extends('components.giangvien.app')

@section('title', 'Danh sách đề tài')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Danh sách đề tài</h3>
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
                                    <th style="width: 2%">ID</th>
                                    <th style="width: 6%">Mã đề tài</th>
                                    <th style="width: 8%">Tên đề tài</th>
                                    <th style="width: 12%">Mô tả</th>
                                    <th style="width: 12%">Ý kiến GV</th>
                                    <th style="width: 6%">Ngày bắt đầu</th>
                                    <th style="width: 6%">Ngày kết thúc</th>
                                    <th style="width: 6%">Nhóm</th>
                                    <th style="width: 4%">Trạng thái</th>
                                    <th style="width: 6%">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($deTais as $deTai)
                                <tr>
                                    <td>{{ $deTai->id }}</td>
                                    <td>{{ $deTai->ma_de_tai }}</td>
                                    <td>{{ Str::limit($deTai->ten_de_tai, 20) }}</td>
                                    <td>{{ Str::limit($deTai->mo_ta, 40) }}</td>
                                    <td>{{ Str::limit($deTai->y_kien_giang_vien, 40) }}</td>
                                    <td>{{ $deTai->ngay_bat_dau ? $deTai->ngay_bat_dau->format('d/m/Y') : 'N/A' }}</td>
                                    <td>{{ $deTai->ngay_ket_thuc ? $deTai->ngay_ket_thuc->format('d/m/Y') : 'N/A' }}</td>
                                    <td>{{ Str::limit($deTai->nhom->ten ?? 'N/A', 15) }}</td>
                                    <td>
                                        <span class="badge {{ $deTai->trang_thai_class }}">
                                            {{ $deTai->trang_thai_text }}
                                        </span>
                                    </td>
                                    <td style="display: flex; gap: 5px; justify-content: center;">
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
                                    <td colspan="11" class="text-center">Không có dữ liệu</td>
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