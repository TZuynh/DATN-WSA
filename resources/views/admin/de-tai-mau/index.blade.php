@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Danh sách đề tài mẫu</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.de-tai-mau.create') }}" class="btn btn-primary btn-sm">
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

                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Tên đề tài</th>
                                    <th>Số lượng sinh viên</th>
                                    <th>Trạng thái</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($deTaiMau as $deTai)
                                    <tr>
                                        <td>{{ $deTai->id }}</td>
                                        <td>{{ $deTai->ten_de_tai }}</td>
                                        <td>{{ $deTai->so_luong_sinh_vien }}</td>
                                        <td>
                                            <span class="badge badge-{{ $deTai->trang_thai == 'active' ? 'success' : 'danger' }}">
                                                {{ $deTai->trang_thai == 'active' ? 'Hoạt động' : 'Không hoạt động' }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.de-tai-mau.edit', $deTai) }}" class="btn btn-info btn-sm">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.de-tai-mau.destroy', $deTai) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc chắn muốn xóa?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">Không có dữ liệu</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $deTaiMau->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 