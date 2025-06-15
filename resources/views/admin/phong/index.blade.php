@extends('admin.layout')
@section('title', 'Quản lý phòng')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="card-title mb-0 text-primary">
                            <i class="fas fa-door-open me-2"></i>Danh sách phòng
                        </h3>
                        <div>
                            <a href="{{ route('admin.phong.create') }}" class="btn btn-success">
                                <i class="fas fa-plus me-2"></i>Thêm phòng mới
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Form Import Excel -->
                    <div style="margin-bottom: 20px; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);">
                        <h4 style="margin-bottom: 15px; color: #2d3748; font-size: 1.2rem;">Import danh sách phòng</h4>
                        <form action="{{ route('admin.phong.import') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div style="display: flex; gap: 10px; align-items: center;">
                                <input type="file" name="import_file" accept=".xlsx, .xls" required
                                    style="padding: 8px; border: 1px solid #ddd; border-radius: 4px; flex: 1;">
                                <button type="submit"
                                    style="padding: 8px 16px; background-color: #4299e1; color: white; border: none; border-radius: 4px; cursor: pointer;">
                                    <i class="fas fa-file-import" style="margin-right: 5px;"></i> Import Excel
                                </button>
                            </div>
                            @error('import_file')
                                <div style="color: #f56565; margin-top: 5px;">{{ $message }}</div>
                            @enderror
                        </form>
                        <div style="margin-top: 10px; font-size: 0.875em; color: #718096;">
                            <i class="fas fa-info-circle" style="margin-right: 5px;"></i>
                            File Excel phải có cột: ten_phong
                        </div>
                    </div>

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
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center" style="width: 80px;">STT</th>
                                    <th>Tên phòng</th>
                                    <th class="text-center" style="width: 120px;">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($phongs as $key => $phong)
                                <tr>
                                    <td class="text-center">{{ ($phongs->currentPage() - 1) * $phongs->perPage() + $key + 1 }}</td>
                                    <td>
                                        <span class="fw-medium">{{ $phong->ten_phong }}</span>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('admin.phong.edit', $phong->id) }}"
                                           class="btn btn-sm btn-primary me-1"
                                           data-bs-toggle="tooltip"
                                           title="Sửa">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.phong.destroy', $phong->id) }}" method="POST" style="display: inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="btn btn-sm btn-danger"
                                                    data-bs-toggle="tooltip"
                                                    title="Xóa"
                                                    onclick="return confirm('Bạn có chắc chắn muốn xóa phòng này không?');">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fas fa-info-circle me-2"></i>Không có dữ liệu
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center mt-4">
                        {{ $phongs->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Khởi tạo tooltip
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
    });
</script>
@endpush
@endsection 