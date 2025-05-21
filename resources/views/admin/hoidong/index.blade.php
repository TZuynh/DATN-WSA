@extends('admin.layout')
@section('content')
<div class="container mt-4">
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

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Danh sách hội đồng</h2>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addHoiDongModal">Thêm hội đồng</button>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Mã hội đồng</th>
                    <th>Tên hội đồng</th>
                    <th>Ngày tạo</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                @forelse($hoiDongs as $index => $hoiDong)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $hoiDong->ma_hoi_dong }}</td>
                        <td>{{ $hoiDong->ten }}</td>
                        <td>{{ $hoiDong->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            <a href="{{ route('admin.hoidong.edit', $hoiDong->id) }}" class="btn btn-sm btn-warning">Sửa</a>
                            <form action="{{ route('admin.hoidong.destroy', $hoiDong->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc chắn muốn xóa?')">Xóa</button>
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
</div>

<!-- Modal Thêm Hội Đồng -->
<div class="modal fade" id="addHoiDongModal" tabindex="-1" aria-labelledby="addHoiDongModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addHoiDongModalLabel">Thêm hội đồng mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.hoidong.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="ma_hoi_dong" class="form-label">Mã hội đồng</label>
                        <input type="text" class="form-control @error('ma_hoi_dong') is-invalid @enderror" 
                               id="ma_hoi_dong" name="ma_hoi_dong" value="{{ old('ma_hoi_dong') }}" required>
                        @error('ma_hoi_dong')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="ten" class="form-label">Tên hội đồng</label>
                        <input type="text" class="form-control @error('ten') is-invalid @enderror" 
                               id="ten" name="ten" value="{{ old('ten') }}" required>
                        @error('ten')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-primary">Lưu</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
