@extends('components.giangvien.app')

@section('title', 'Danh sách mẫu đề tài')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Danh sách mẫu đề tài</h3>
                    <div class="card-tools">
                        <form action="{{ route('giangvien.de-tai-mau.import') }}" method="POST" enctype="multipart/form-data" class="d-inline-block mr-2">
                            @csrf
                            <div class="input-group">
                                <input type="file" class="form-control form-control-sm @error('file') is-invalid @enderror" id="file" name="file" accept=".xlsx, .xls" required>
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-info btn-sm">
                                        <i class="fas fa-file-import"></i> Import
                                    </button>
                                </div>
                            </div>
                            @error('file')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </form>
                        <a href="{{ route('giangvien.de-tai-mau.create') }}" class="btn btn-success btn-sm">
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
                                    <th>Tên mẫu</th>
                                    <th>Mô tả</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($deTaiMaus as $deTaiMau)
                                <tr>
                                    <td>{{ $deTaiMau->id }}</td>
                                    <td>{{ $deTaiMau->ten }}</td>
                                    <td>{{ $deTaiMau->mo_ta }}</td>
                                    <td>
                                        <a href="{{ route('giangvien.de-tai-mau.edit', $deTaiMau) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-edit"></i> Sửa
                                        </a>
                                        <form action="{{ route('giangvien.de-tai-mau.destroy', $deTaiMau) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc chắn muốn xóa mẫu đề tài này?')">
                                                <i class="fas fa-trash"></i> Xóa
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center">Không có dữ liệu</td>
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