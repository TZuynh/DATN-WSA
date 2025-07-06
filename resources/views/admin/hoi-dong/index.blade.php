@extends('admin.layout')

@section('title', 'Danh sách hội đồng')

@section('content')
    <!-- Thêm Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h1 style="color: #2d3748; font-weight: 700;">Quản lý hội đồng</h1>
        <div style="display: flex; gap: 10px;">
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addDeTaiModal">
                <i class="fas fa-plus-circle"></i> Thêm đề tài
            </button>
            <a href="{{ route('admin.hoi-dong.create') }}" style="padding: 10px 20px; background-color: #4299e1; color: white; border: none; border-radius: 4px; text-decoration: none;">
                <i class="fas fa-plus-circle"></i> Thêm hội đồng mới
            </a>
        </div>
    </div>

    <div style="overflow-x:auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 8px rgb(0 0 0 / 0.1);">
        <table style="width: 100%; border-collapse: collapse; min-width: 600px; font-family: Arial, sans-serif;">
            <thead>
            <tr style="background-color: #2d3748; color: white; text-align: left;">
                <th style="padding: 12px 15px;">ID</th>
                <th style="padding: 12px 15px;">Mã hội đồng</th>
                <th style="padding: 12px 15px;">Tên hội đồng</th>
                <th style="padding: 12px 15px;">Đợt báo cáo</th>
                <th style="padding: 12px 15px;">Phòng</th>
                <th style="padding: 12px 15px;">Thời gian bắt đầu</th>
                <th style="padding: 12px 15px;">Ngày tạo</th>
                <th style="padding: 12px 15px;">Thao tác</th>
            </tr>
            </thead>
            <tbody>
            @forelse ($hoiDongs as $hoiDong)
                <tr style="border-bottom: 1px solid #ddd;">
                    <td style="padding: 12px 15px;">{{ $hoiDong->id }}</td>
                    <td style="padding: 12px 15px;">{{ $hoiDong->ma_hoi_dong }}</td>
                    <td style="padding: 12px 15px; color: #2d3748; font-weight: 600;">{{ $hoiDong->ten }}</td>
                    <td style="padding: 12px 15px;">{{ $hoiDong->dotBaoCao->nam_hoc ?? 'N/A' }}</td>
                    <td style="padding: 12px 15px;">{{ $hoiDong->phong->ten_phong ?? 'Chưa có phòng' }}</td>
                    <td style="padding: 12px 15px;">{{ $hoiDong->thoi_gian_bat_dau ? \Carbon\Carbon::parse($hoiDong->thoi_gian_bat_dau)->format('d/m/Y H:i') : 'Chưa có' }}</td>
                    <td style="padding: 12px 15px;">{{ $hoiDong->created_at->format('d-m-Y') }}</td>
                    <td style="padding: 12px 15px;">
                        <div style="display: flex; gap: 10px;">
                            <a href="{{ route('admin.hoi-dong.show', $hoiDong->id) }}" class="btn-view" style="color: #38a169;" title="Xem chi tiết">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('admin.hoi-dong.edit', $hoiDong->id) }}" class="btn-edit" style="color: #3182ce;" title="Sửa">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.hoi-dong.destroy', $hoiDong->id) }}" method="POST" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-delete" style="background: none; border: none; color: #e53e3e; cursor: pointer;" onclick="return confirm('Bạn có chắc chắn muốn xóa hội đồng này?')" title="Xóa">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="padding: 20px; text-align: center; color: #718096;">
                        <i class="fas fa-info-circle" style="margin-right: 5px;"></i>
                        Chưa có dữ liệu
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
        
        <div style="margin-top: 20px;">
            {{ $hoiDongs->links() }}
        </div>
    </div>

    <!-- Modal Thêm đề tài -->
    <div class="modal fade" id="addDeTaiModal" tabindex="-1" aria-labelledby="addDeTaiModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addDeTaiModalLabel">
                        <i class="fas fa-plus-circle text-success me-2"></i>
                        Thêm đề tài mới
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        Thêm đề tài cơ bản. Giáo viên sẽ được phân công sau.
                    </div>

                    <form id="addDeTaiForm">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="ten_de_tai" class="form-label">Tên đề tài <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" 
                                           id="ten_de_tai" 
                                           name="ten_de_tai" 
                                           placeholder="Nhập tên đề tài" required>
                                </div>

                                <div class="form-group mb-3">
                                    <label for="dot_bao_cao_id" class="form-label">Đợt báo cáo <span class="text-danger">*</span></label>
                                    <select class="form-control" 
                                            id="dot_bao_cao_id" 
                                            name="dot_bao_cao_id" required>
                                        <option value="">Chọn đợt báo cáo</option>
                                        @foreach($dotBaoCaos as $dotBaoCao)
                                            <option value="{{ $dotBaoCao->id }}">
                                                {{ $dotBaoCao->nam_hoc }} - {{ optional($dotBaoCao->hocKy)->ten }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="nhom_id" class="form-label">Nhóm (tùy chọn)</label>
                                    <select class="form-control" 
                                            id="nhom_id" 
                                            name="nhom_id">
                                        <option value="">Chọn nhóm</option>
                                        @foreach($nhoms as $nhom)
                                            <option value="{{ $nhom->id }}">
                                                {{ $nhom->ten }} ({{ $nhom->ma_nhom }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group mb-3">
                                    <label for="hoi_dong_id" class="form-label">Hội đồng (tùy chọn)</label>
                                    <select class="form-control" 
                                            id="hoi_dong_id" 
                                            name="hoi_dong_id">
                                        <option value="">Chọn hội đồng</option>
                                        @foreach($hoiDongs as $hoiDong)
                                            <option value="{{ $hoiDong->id }}">
                                                {{ $hoiDong->ten }} ({{ $hoiDong->ma_hoi_dong }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="form-text text-muted">
                                        Nếu không chọn, đề tài sẽ được tạo độc lập
                                    </small>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="mo_ta" class="form-label">Mô tả (tùy chọn)</label>
                            <textarea class="form-control" 
                                      id="mo_ta" 
                                      name="mo_ta" 
                                      rows="3" 
                                      placeholder="Nhập mô tả đề tài"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Hủy
                    </button>
                    <button type="button" class="btn btn-success" onclick="saveDeTai()">
                        <i class="fas fa-save"></i> Lưu đề tài
                    </button>
                </div>
            </div>
        </div>
    </div>

    <style>
    .btn-view:hover {
        color: #2f855a !important;
        transform: scale(1.1);
    }
    .btn-edit:hover {
        color: #2c5282 !important;
        transform: scale(1.1);
    }
    .btn-delete:hover {
        color: #c53030 !important;
        transform: scale(1.1);
    }
    .btn-success {
        background-color: #28a745;
        border-color: #28a745;
        color: white;
        padding: 10px 20px;
        border-radius: 4px;
        text-decoration: none;
        border: none;
        cursor: pointer;
    }
    .btn-success:hover {
        background-color: #218838;
        border-color: #1e7e34;
        color: white;
    }
    </style>

    @push('scripts')
    <script>
        function saveDeTai() {
            const form = document.getElementById('addDeTaiForm');
            const formData = new FormData(form);

            // Kiểm tra dữ liệu bắt buộc
            const tenDeTai = formData.get('ten_de_tai');
            const dotBaoCao = formData.get('dot_bao_cao_id');

            if (!tenDeTai || !dotBaoCao) {
                alert('Vui lòng điền đầy đủ thông tin bắt buộc!');
                return;
            }

            // Thêm CSRF token
            formData.append('_token', '{{ csrf_token() }}');

            // Gửi request
            fetch('{{ route("admin.de-tai.store") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Thêm đề tài thành công!');
                    // Đóng modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('addDeTaiModal'));
                    modal.hide();
                    // Reset form
                    form.reset();
                    // Reload trang để cập nhật dữ liệu
                    location.reload();
                } else {
                    alert('Có lỗi xảy ra: ' + (data.message || 'Không thể thêm đề tài'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Có lỗi xảy ra khi thêm đề tài');
            });
        }

        // Reset form khi đóng modal
        document.getElementById('addDeTaiModal').addEventListener('hidden.bs.modal', function () {
            document.getElementById('addDeTaiForm').reset();
        });
    </script>
    @endpush
@endsection 