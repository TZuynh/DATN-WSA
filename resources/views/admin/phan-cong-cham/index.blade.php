@extends('admin.layout')

@section('title', 'Danh sách phân công chấm')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Danh sách phân công chấm</h3>
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
                                    <th>Hội đồng</th>
                                    <th>GV Hướng dẫn</th>
                                    <th>GV Phản biện</th>
                                    <th>GV Khác</th>
                                    <th>Lịch chấm</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($phanCongChams as $phanCongCham)
                                    <tr>
                                        <td>{{ $phanCongCham->deTai->ma_de_tai }}</td>
                                        <td>{{ $phanCongCham->deTai->ten_de_tai }}</td>
                                        <td>{{ $phanCongCham->hoiDong->ten ?? 'N/A' }}</td>
                                        <td>{{ $phanCongCham->giangVienHuongDan->ten ?? 'N/A' }}</td>
                                        <td>{{ $phanCongCham->giangVienPhanBien->ten ?? 'N/A' }}</td>
                                        <td>{{ $phanCongCham->giangVienKhac->ten ?? 'N/A' }}</td>
                                        <td>{{ $phanCongCham->lich_cham ? \Carbon\Carbon::parse($phanCongCham->lich_cham)->format('d/m/Y H:i') : 'N/A' }}</td>
                                        <td>
                                            <a href="{{ route('admin.phan-cong-cham.edit', $phanCongCham->id) }}"
                                               class="btn btn-warning btn-sm">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.phan-cong-cham.destroy', $phanCongCham->id) }}"
                                                  method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm"
                                                        onclick="return confirm('Bạn có chắc chắn muốn xóa phân công này?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">Chưa có dữ liệu</td>
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