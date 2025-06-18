@extends('admin.layout')

@section('title', 'Danh sách phản biện')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Danh sách phản biện</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.phan-cong-cham.create') }}" class="btn btn-success btn-sm">
                            <i class="fas fa-plus"></i> Thêm mới
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Mã đề tài</th>
                                    <th>Tên đề tài</th>
                                    <th>Giảng viên hướng dẫn</th>
                                    <th>Giảng viên phản biện</th>
                                    <th>Giảng viên khác</th>
                                    <th>Lịch chấm</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($phanCongChams as $phanCongCham)
                                    <tr>
                                        <td>{{ $phanCongCham->deTai->ma_de_tai }}</td>
                                        <td>{{ $phanCongCham->deTai->ten_de_tai }}</td>
                                        <td>{{ $phanCongCham->giangVienHuongDan->ten }}</td>
                                        <td>{{ $phanCongCham->giangVienPhanBien->ten }}</td>
                                        <td>{{ $phanCongCham->giangVienKhac->ten }}</td>
                                        <td>{{ \Carbon\Carbon::parse($phanCongCham->lich_cham)->format('d/m/Y H:i') }}</td>
                                        <td>
                                            <a href="{{ route('admin.phan-cong-cham.edit', $phanCongCham) }}" 
                                               class="btn btn-warning btn-sm">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.phan-cong-cham.destroy', $phanCongCham) }}" 
                                                  method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" 
                                                        onclick="return confirm('Bạn có chắc chắn muốn xóa phản biện này?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">Chưa có dữ liệu</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $phanCongChams->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 