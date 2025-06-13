@extends('admin.layout')
@section('title', 'Danh sách nhóm')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Danh sách nhóm</h3>
                    <div class="card-tools d-flex flex-column flex-md-row align-items-end align-items-md-center">
                        <div class="mb-2 mb-md-0 me-md-2">
                            <form action="{{ route('admin.nhom.import') }}" method="POST" enctype="multipart/form-data" class="d-inline-flex">
                                @csrf
                                <div class="input-group">
                                    <input type="file" class="form-control" name="file" accept=".xlsx, .xls" required>
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-file-excel"></i> Import Excel
                                    </button>
                                </div>
                            </form>
                        </div>
                        <div>
                            <a href="{{ route('admin.nhom.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Thêm mới
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>STT</th>
                                    <th>Mã nhóm</th>
                                    <th>Tên nhóm</th>
                                    <th>Sinh viên</th>
                                    <th>Trạng thái</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($nhoms as $index => $nhom)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $nhom->ma_nhom }}</td>
                                        <td>{{ $nhom->ten }}</td>
                                        <td>
                                            <ul class="list-unstyled mb-0">
                                                @forelse($nhom->chiTietNhoms as $chiTiet)
                                                    <li>{{ $chiTiet->sinhVien->mssv }} - {{ $chiTiet->sinhVien->ten }}</li>
                                                @empty
                                                    <li>Chưa có sinh viên</li>
                                                @endforelse
                                            </ul>
                                        </td>
                                        <td>
                                            @if($nhom->trang_thai == 'hoat_dong')
                                                <span class="badge bg-success">Hoạt động</span>
                                            @else
                                                <span class="badge bg-danger">Không hoạt động</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.nhom.edit', $nhom) }}" class="btn btn-info btn-sm">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.nhom.destroy', $nhom) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc chắn muốn xóa nhóm này?')">
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
                        {{ $nhoms->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 