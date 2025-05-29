@extends('components.giangvien.app')
@section('title', 'Quản lý sinh viên')
@vite(['resources/scss/giangvien/sinh-vien.scss', 'resources/js/app.js'])
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="card-title mb-0 text-primary">
                            <i class="fas fa-users me-2"></i>Danh sách sinh viên
                        </h3>
                        <a href="{{ route('giangvien.sinh-vien.create') }}" class="btn btn-success">
                            <i class="fas fa-plus me-2"></i>Thêm sinh viên mới
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <!-- Form Import Excel (Styled like TaiKhoan Import) -->
                    <div style="margin-bottom: 20px; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);">
                        <h4 style="margin-bottom: 15px; color: #2d3748; font-size: 1.2rem;">Import danh sách sinh viên</h4>
                        <form action="{{ route('giangvien.sinh-vien.import') }}" method="POST" enctype="multipart/form-data">
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
                            File Excel phải có các cột: mssv, ten
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center" style="width: 80px;">STT</th>
                                    <th>Mã số sinh viên</th>
                                    <th>Họ tên</th>
                                    <th class="text-center" style="width: 120px;">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($sinhViens as $key => $sinhVien)
                                    <tr>
                                        <td class="text-center">{{ ($sinhViens->currentPage() - 1) * $sinhViens->perPage() + $key + 1 }}</td>
                                        <td>
                                            <span class="fw-medium">{{ $sinhVien->mssv }}</span>
                                        </td>
                                        <td>{{ $sinhVien->ten }}</td>
                                        <td class="text-center">
                                            <a href="{{ route('giangvien.sinh-vien.edit', $sinhVien) }}"
                                               class="btn btn-sm btn-primary me-1"
                                               data-bs-toggle="tooltip"
                                               title="Sửa">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('giangvien.sinh-vien.destroy', $sinhVien) }}" method="POST" style="display: inline-block;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="btn btn-sm btn-danger"
                                                        data-bs-toggle="tooltip"
                                                        title="Xóa"
                                                        onclick="return confirm('Bạn có chắc chắn muốn xóa sinh viên này không?');">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4">
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
                        {{ $sinhViens->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Remove Modal Import structure --}}

@push('scripts')
<script>
    // Khởi tạo tooltips (Giữ lại nếu vẫn dùng tooltips)
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })
</script>
@endpush
@endsection
