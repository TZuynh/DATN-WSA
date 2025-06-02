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
                        <div>
                            <button type="submit" form="bulk-delete-form" class="btn btn-danger me-2" id="bulk-delete-btn">
                                <i class="fas fa-trash-alt me-2"></i>Xóa đã chọn
                            </button>
                            <a href="{{ route('giangvien.sinh-vien.create') }}" class="btn btn-success">
                                <i class="fas fa-plus me-2"></i>Thêm sinh viên mới
                            </a>
                        </div>
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
                            File Excel phải có các cột: mssv, ten, lop, nganh, khoa_hoc
                        </div>
                    </div>

                    <form id="bulk-delete-form" action="{{ route('giangvien.sinh-vien.bulkDelete') }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th class="text-center" style="width: 50px;">
                                            Chọn
                                        </th>
                                        <th class="text-center" style="width: 80px;">STT</th>
                                        <th>Mã số sinh viên</th>
                                        <th>Họ tên</th>
                                        <th>Lớp</th>
                                        <th>Ngành</th>
                                        <th>Khóa học</th>
                                        <th class="text-center" style="width: 120px;">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($sinhViens as $key => $sinhVien)
                                        <tr>
                                            <td class="text-center">
                                                <input type="checkbox" name="selected_students[]" value="{{ $sinhVien->id }}" class="student-checkbox">
                                            </td>
                                            <td class="text-center">{{ ($sinhViens->currentPage() - 1) * $sinhViens->perPage() + $key + 1 }}</td>
                                            <td>
                                                <span class="fw-medium">{{ $sinhVien->mssv }}</span>
                                            </td>
                                            <td>{{ $sinhVien->ten }}</td>
                                            <td>{{ $sinhVien->lop }}</td>
                                            <td>{{ $sinhVien->nganh }}</td>
                                            <td>{{ $sinhVien->khoa_hoc }}</td>
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
                                            <td colspan="8" class="text-center py-4">
                                                <div class="text-muted">
                                                    <i class="fas fa-info-circle me-2"></i>Không có dữ liệu
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </form>

                    <div class="d-flex justify-content-center mt-4">
                        {{ $sinhViens->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        console.log('Script started');
        
        const selectAllCheckbox = document.getElementById('select-all');
        const studentCheckboxes = document.querySelectorAll('.student-checkbox');
        const bulkDeleteBtn = document.getElementById('bulk-delete-btn');
        const bulkDeleteForm = document.getElementById('bulk-delete-form');

        console.log('Elements found:', {
            selectAllCheckbox: !!selectAllCheckbox,
            studentCheckboxes: studentCheckboxes.length,
            bulkDeleteBtn: !!bulkDeleteBtn,
            bulkDeleteForm: !!bulkDeleteForm
        });

        if (bulkDeleteBtn && studentCheckboxes.length > 0) {
            function updateBulkDeleteButtonState() {
                const anyChecked = Array.from(studentCheckboxes).some(checkbox => checkbox.checked);
                bulkDeleteBtn.disabled = !anyChecked;
                console.log('Bulk Delete button state updated:', {
                    anyChecked,
                    buttonDisabled: bulkDeleteBtn.disabled
                });
            }

            if (selectAllCheckbox) {
                selectAllCheckbox.addEventListener('change', function () {
                    console.log('Select All checkbox changed:', this.checked);
                    const isChecked = this.checked;
                    studentCheckboxes.forEach(checkbox => {
                        checkbox.checked = isChecked;
                    });
                    updateBulkDeleteButtonState();
                });
            }

            studentCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function () {
                    console.log('Individual checkbox changed:', {
                        id: this.value,
                        checked: this.checked
                    });
                    if (selectAllCheckbox) {
                        if (!this.checked) {
                            selectAllCheckbox.checked = false;
                        } else {
                            const allChecked = Array.from(studentCheckboxes).every(cb => cb.checked);
                            selectAllCheckbox.checked = allChecked;
                        }
                    }
                    updateBulkDeleteButtonState();
                });
            });

            updateBulkDeleteButtonState();

            if (bulkDeleteForm) {
                bulkDeleteForm.addEventListener('submit', function(e) {
                    const selectedCount = document.querySelectorAll('.student-checkbox:checked').length;
                    console.log('Form submitted with', selectedCount, 'items selected');
                    
                    if (!confirm(`Bạn có chắc chắn muốn xóa ${selectedCount} sinh viên đã chọn không?`)) {
                        e.preventDefault();
                        console.log('Form submission cancelled by user');
                    } else {
                        console.log('Form submission proceeding');
                    }
                });
            }
        } else {
            console.warn('Required elements not found:', {
                bulkDeleteBtn: !!bulkDeleteBtn,
                studentCheckboxesCount: studentCheckboxes.length
            });
            if (bulkDeleteBtn) {
                bulkDeleteBtn.disabled = true;
            }
        }

        // Khởi tạo tooltip
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
    });
</script>
@endpush
@endsection
