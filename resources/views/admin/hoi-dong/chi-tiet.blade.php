@extends('admin.layout')

@section('styles')
<style>
    /* Container styles */
    .container {
        max-width: 1200px;
        padding: 2rem;
    }

    /* Header styles */
    .page-header {
        margin-bottom: 2rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid #e2e8f0;
    }

    .page-header h2 {
        color: #2d3748;
        font-size: 1.875rem;
        font-weight: 700;
        margin: 0;
    }

    .header-actions {
        display: flex;
        gap: 1rem;
    }

    /* Card styles */
    .card {
        border: none;
        border-radius: 0.5rem;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        margin-bottom: 1.5rem;
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .card-header {
        background-color: #fff;
        border-bottom: 1px solid #e2e8f0;
        padding: 1rem 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .card-title {
        color: #2d3748;
        font-size: 1.25rem;
        font-weight: 600;
        margin: 0;
    }

    .card-body {
        padding: 1.5rem;
    }

    /* Table styles */
    .table {
        width: 100%;
        margin-bottom: 0;
    }

    .table th {
        background-color: #f7fafc;
        color: #4a5568;
        font-weight: 600;
        padding: 1rem;
        border-bottom: 2px solid #e2e8f0;
    }

    .table td {
        padding: 1rem;
        vertical-align: middle;
        border-bottom: 1px solid #e2e8f0;
    }

    .table tr:last-child td {
        border-bottom: none;
    }

    /* Button styles */
    .btn {
        padding: 0.5rem 1rem;
        border-radius: 0.375rem;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.2s;
    }

    .btn i {
        font-size: 0.875rem;
    }

    .btn-primary {
        background-color: #4299e1;
        border-color: #4299e1;
        color: white;
    }

    .btn-primary:hover {
        background-color: #3182ce;
        border-color: #3182ce;
        transform: translateY(-1px);
    }

    .btn-warning {
        background-color: #ed8936;
        border-color: #ed8936;
        color: white;
    }

    .btn-warning:hover {
        background-color: #dd6b20;
        border-color: #dd6b20;
        transform: translateY(-1px);
    }

    .btn-secondary {
        background-color: #718096;
        border-color: #718096;
        color: white;
    }

    .btn-secondary:hover {
        background-color: #4a5568;
        border-color: #4a5568;
        transform: translateY(-1px);
    }

    /* Badge styles */
    .badge {
        padding: 0.5rem 0.75rem;
        border-radius: 9999px;
        font-weight: 500;
        font-size: 0.875rem;
    }

    .bg-success {
        background-color: #48bb78 !important;
    }

    .bg-warning {
        background-color: #ed8936 !important;
    }

    /* Stats card styles */
    .stats-card {
        background: linear-gradient(135deg, #4299e1 0%, #3182ce 100%);
        color: white;
        border-radius: 0.5rem;
        padding: 1.5rem;
        height: 100%;
    }

    .stats-card.success {
        background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
    }

    .stats-card .card-title {
        color: white;
        font-size: 1rem;
        margin-bottom: 0.5rem;
    }

    .stats-card .stats-number {
        font-size: 2rem;
        font-weight: 700;
        margin: 0;
    }

    /* Modal styles */
    .modal-content {
        border: none;
        border-radius: 0.5rem;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .modal-header {
        background-color: #f7fafc;
        border-bottom: 1px solid #e2e8f0;
        padding: 1rem 1.5rem;
    }

    .modal-title {
        color: #2d3748;
        font-size: 1.25rem;
        font-weight: 600;
    }

    .modal-body {
        padding: 1.5rem;
    }

    .modal-footer {
        background-color: #f7fafc;
        border-top: 1px solid #e2e8f0;
        padding: 1rem 1.5rem;
    }

    /* Form styles */
    .form-label {
        color: #4a5568;
        font-weight: 500;
        margin-bottom: 0.5rem;
    }

    .form-control {
        border: 1px solid #e2e8f0;
        border-radius: 0.375rem;
        padding: 0.5rem 0.75rem;
        transition: border-color 0.2s;
    }

    .form-control:focus {
        border-color: #4299e1;
        box-shadow: 0 0 0 3px rgba(66, 153, 225, 0.1);
    }

    /* Alert styles */
    .alert {
        border: none;
        border-radius: 0.5rem;
        padding: 1rem 1.5rem;
        margin-bottom: 1.5rem;
    }

    .alert-success {
        background-color: #c6f6d5;
        color: #2f855a;
    }

    .alert-danger {
        background-color: #fed7d7;
        color: #c53030;
    }

    /* Action buttons in table */
    .action-buttons {
        display: flex;
        gap: 0.5rem;
    }

    .btn-action {
        padding: 0.375rem 0.75rem;
        border-radius: 0.375rem;
        transition: all 0.2s;
    }

    .btn-action:hover {
        transform: translateY(-1px);
    }

    .btn-info {
        background-color: #63b3ed;
        border-color: #63b3ed;
        color: white;
    }

    .btn-info:hover {
        background-color: #4299e1;
        border-color: #4299e1;
    }

    .btn-danger {
        background-color: #fc8181;
        border-color: #fc8181;
        color: white;
    }

    .btn-danger:hover {
        background-color: #f56565;
        border-color: #f56565;
    }
</style>
@endsection

@section('content')
<div class="container">
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

    <div class="page-header d-flex justify-content-between align-items-center">
        <h2>Chi tiết hội đồng</h2>
        <div class="header-actions">
            <a href="{{ route('admin.hoi-dong.edit', $hoiDong->id) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Sửa
            </a>
            <a href="{{ route('admin.hoi-dong.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Thông tin cơ bản -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Thông tin cơ bản</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th style="width: 200px">Mã hội đồng:</th>
                            <td>{{ $hoiDong->ma_hoi_dong }}</td>
                        </tr>
                        <tr>
                            <th>Tên hội đồng:</th>
                            <td>{{ $hoiDong->ten }}</td>
                        </tr>
                        <tr>
                            <th>Đợt báo cáo:</th>
                            <td>{{ $hoiDong->dotBaoCao->ten ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Ngày tạo:</th>
                            <td>{{ $hoiDong->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Cập nhật lần cuối:</th>
                            <td>{{ $hoiDong->updated_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Thống kê -->
        <div class="col-md-6">
            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="stats-card">
                        <h6 class="card-title">Số đề tài</h6>
                        <h3 class="stats-number">{{ $hoiDong->chiTietBaoCaos->count() }}</h3>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="stats-card success">
                        <h6 class="card-title">Số lịch chấm</h6>
                        <h3 class="stats-number">{{ $hoiDong->lichChams->count() }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Danh sách đề tài -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title">Danh sách đề tài</h5>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addDeTaiModal">
                <i class="fas fa-plus"></i> Thêm đề tài
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Mã đề tài</th>
                            <th>Tên đề tài</th>
                            <th>Giảng viên hướng dẫn</th>
                            <th>Sinh viên thực hiện</th>
                            <th>Trạng thái</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($hoiDong->chiTietBaoCaos as $index => $chiTiet)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $chiTiet->deTai->ma_de_tai ?? 'N/A' }}</td>
                                <td>{{ $chiTiet->deTai->ten ?? 'N/A' }}</td>
                                <td>{{ $chiTiet->deTai->giangVien->ten ?? 'N/A' }}</td>
                                <td>
                                    @foreach($chiTiet->deTai->sinhViens ?? [] as $sv)
                                        <span class="badge bg-info">{{ $sv->ten }}</span>
                                    @endforeach
                                </td>
                                <td>
                                    <span class="badge bg-{{ $chiTiet->trang_thai == 'hoan_thanh' ? 'success' : 'warning' }}">
                                        {{ $chiTiet->trang_thai == 'hoan_thanh' ? 'Hoàn thành' : 'Đang thực hiện' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="#" class="btn btn-action btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <button class="btn btn-action btn-danger btn-xoa-de-tai" data-href="{{ route('admin.hoi-dong.xoa-de-tai', [$hoiDong->id, $chiTiet->id]) }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">Chưa có đề tài nào</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Lịch chấm -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title">Lịch chấm</h5>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addLichChamModal">
                <i class="fas fa-plus"></i> Thêm lịch chấm
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Ngày chấm</th>
                            <th>Thời gian bắt đầu</th>
                            <th>Thời gian kết thúc</th>
                            <th>Địa điểm</th>
                            <th>Trạng thái</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($hoiDong->lichChams as $index => $lichCham)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $lichCham->ngay_cham->format('d/m/Y') }}</td>
                                <td>{{ $lichCham->thoi_gian_bat_dau }}</td>
                                <td>{{ $lichCham->thoi_gian_ket_thuc }}</td>
                                <td>{{ $lichCham->dia_diem }}</td>
                                <td>
                                    <span class="badge bg-{{ $lichCham->trang_thai == 'da_ket_thuc' ? 'success' : 'warning' }}">
                                        {{ $lichCham->trang_thai == 'da_ket_thuc' ? 'Đã kết thúc' : 'Chưa diễn ra' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="#" class="btn btn-action btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <button class="btn btn-action btn-danger btn-xoa-lich-cham" data-href="{{ route('admin.hoi-dong.xoa-lich-cham', [$hoiDong->id, $lichCham->id]) }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">Chưa có lịch chấm nào</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Thêm Đề Tài -->
<div class="modal fade" id="addDeTaiModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Thêm đề tài vào hội đồng</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.hoi-dong.them-de-tai', $hoiDong->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Chọn đề tài</label>
                        <select class="form-select" name="de_tai_id" required>
                            <option value="">-- Chọn đề tài --</option>
                            @foreach($deTais as $deTai)
                                <option value="{{ $deTai->id }}">{{ $deTai->ma_de_tai }} - {{ $deTai->ten }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-primary">Thêm</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Thêm Lịch Chấm -->
<div class="modal fade" id="addLichChamModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Thêm lịch chấm</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.hoi-dong.them-lich-cham', $hoiDong->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Ngày chấm</label>
                        <input type="date" class="form-control" name="ngay_cham" required min="{{ date('Y-m-d') }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Thời gian bắt đầu</label>
                        <input type="time" class="form-control" name="thoi_gian_bat_dau" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Thời gian kết thúc</label>
                        <input type="time" class="form-control" name="thoi_gian_ket_thuc" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Địa điểm</label>
                        <input type="text" class="form-control" name="dia_diem" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-primary">Thêm</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Xử lý xóa đề tài
        $('.btn-xoa-de-tai').click(function(e) {
            e.preventDefault();
            const url = $(this).attr('data-href');
            if (confirm('Bạn có chắc chắn muốn xóa đề tài này khỏi hội đồng?')) {
                $.ajax({
                    url: url,
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        location.reload();
                    },
                    error: function(xhr) {
                        alert('Có lỗi xảy ra: ' + xhr.responseJSON.message);
                    }
                });
            }
        });

        // Xử lý xóa lịch chấm
        $('.btn-xoa-lich-cham').click(function(e) {
            e.preventDefault();
            const url = $(this).attr('data-href');
            if (confirm('Bạn có chắc chắn muốn xóa lịch chấm này?')) {
                $.ajax({
                    url: url,
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        location.reload();
                    },
                    error: function(xhr) {
                        alert('Có lỗi xảy ra: ' + xhr.responseJSON.message);
                    }
                });
            }
        });

        // Validate thời gian kết thúc phải sau thời gian bắt đầu
        $('form').submit(function(e) {
            const thoiGianBatDau = $('input[name="thoi_gian_bat_dau"]').val();
            const thoiGianKetThuc = $('input[name="thoi_gian_ket_thuc"]').val();
            
            if (thoiGianBatDau && thoiGianKetThuc && thoiGianBatDau >= thoiGianKetThuc) {
                e.preventDefault();
                alert('Thời gian kết thúc phải sau thời gian bắt đầu!');
            }
        });
    });
</script>
@endpush
