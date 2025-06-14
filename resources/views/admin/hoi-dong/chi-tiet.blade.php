@extends('admin.layout')

@section('title', 'Chi tiết hội đồng')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<div class="container-fluid px-4">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Page Header -->
    <div class="page-header d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center">
            <div>
                <h1 class="mb-1">Chi tiết hội đồng</h1>   
            </div>
        </div>
        <div class="header-actions">
            <a href="{{ route('admin.hoi-dong.index') }}" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
        </div>
    </div>

    <div class="row g-4">
        <!-- Thông tin cơ bản -->
        <div class="col-xl-6">
            <div class="card h-100 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h2 class="card-title mb-0">
                        <i class="fas fa-info-circle text-primary me-2"></i>
                        Thông tin cơ bản
                    </h2>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-borderless">
                            <tr>
                                <th style="width: 200px; text-align: left;" class="text-muted text-start">Mã hội đồng:</th>
                                <td class="text-start">
                                    <span class="badge bg-primary px-3 py-2">{{ $hoiDong->ma_hoi_dong }}</span>
                                </td>
                            </tr>
                            <tr>
                                <th class="text-muted text-start" style="width: 200px; text-align: left;">Tên hội đồng:</th>
                                <td class="fw-semibold fs-5 text-start">{{ $hoiDong->ten }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted text-start" style="width: 200px; text-align: left;">Thành viên hội đồng:</th>
                                <td class="fw-semibold text-start">
                                    @if($hoiDong->phanCongVaiTros && $hoiDong->phanCongVaiTros->count() > 0)
                                        <div class="d-flex flex-column gap-2">
                                            @foreach($hoiDong->phanCongVaiTros as $phanCong)
                                                <div class="d-inline-flex align-items-center">
                                                    <i class="fas fa-user-tie text-primary me-2"></i>
                                                    {{ $phanCong->taiKhoan->ten ?? 'N/A' }} 
                                                    <span class="text-muted ms-1">({{ $phanCong->vaiTro->ten ?? 'N/A' }})</span>
                                                    @if($phanCong->taiKhoan && $phanCong->taiKhoan->deTais && $phanCong->taiKhoan->deTais->count() > 0)
                                                        <button type="button" class="btn btn-sm btn-info ms-2" data-bs-toggle="modal" data-bs-target="#modalDeTai{{ $phanCong->taiKhoan->id }}">
                                                            <i class="fas fa-book me-1"></i>
                                                            {{ $phanCong->taiKhoan->deTais->count() }} đề tài
                                                        </button>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="text-muted" style="width: 200px; text-align: left;">
                                            <small>Chưa có thành viên nào trong hội đồng</small>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                           
                            <tr>
                                <th class="text-muted text-start" style="width: 200px; text-align: left;">Ngày tạo:</th>
                                <td class="text-start">
                                    <div class="d-flex align-items-center text-muted">
                                        <i class="far fa-calendar-alt me-2"></i>
                                        {{ $hoiDong->created_at->format('d/m/Y H:i') }}
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th class="text-muted text-start" style="width: 200px; text-align: left;">Cập nhật lần cuối:</th>
                                <td class="text-start">
                                    <div class="d-flex align-items-center text-muted">
                                        <i class="far fa-clock me-2"></i>
                                        {{ $hoiDong->updated_at->format('d/m/Y H:i') }}
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal hiển thị danh sách đề tài của giảng viên -->
@foreach($hoiDong->phanCongVaiTros as $phanCong)
    @if($phanCong->taiKhoan && $phanCong->taiKhoan->deTais && $phanCong->taiKhoan->deTais->count() > 0)
        <div class="modal fade" id="modalDeTai{{ $phanCong->taiKhoan->id }}" tabindex="-1" aria-labelledby="modalDeTaiLabel{{ $phanCong->taiKhoan->id }}" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalDeTaiLabel{{ $phanCong->taiKhoan->id }}">
                            <i class="fas fa-book me-2"></i>
                            Danh sách đề tài của giảng viên {{ $phanCong->taiKhoan->ten }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Mã đề tài</th>
                                        <th>Tên đề tài</th>
                                        <th>Trạng thái</th>
                                        <th>Nhóm thực hiện</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($phanCong->taiKhoan->deTais as $deTai)
                                        <tr>
                                            <td>{{ $deTai->ma_de_tai }}</td>
                                            <td>{{ $deTai->ten_de_tai }}</td>
                                            <td>
                                                @switch($deTai->trang_thai)
                                                    @case(0)
                                                        <span class="badge bg-warning">Chờ duyệt</span>
                                                        @break
                                                    @case(1)
                                                        <span class="badge bg-info">Đang thực hiện (GVHD)</span>
                                                        @break
                                                    @case(2)
                                                        <span class="badge bg-primary">Đang thực hiện (GVPB)</span>
                                                        @break
                                                    @case(3)
                                                        <span class="badge bg-danger">Không xảy ra (GVHD)</span>
                                                        @break
                                                    @case(4)
                                                        <span class="badge bg-danger">Không xảy ra (GVPB)</span>
                                                        @break
                                                    @default
                                                        <span class="badge bg-secondary">Không xác định</span>
                                                @endswitch
                                            </td>
                                            <td>
                                                @if($deTai->nhom)
                                                    <span class="badge bg-success">{{ $deTai->nhom->ma_nhom }}</span>
                                                @else
                                                    <span class="text-muted">Chưa có nhóm</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endforeach
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Xử lý xóa đề tài
        $('.btn-xoa-de-tai').click(function(e) {
            e.preventDefault();
            const url = $(this).attr('data-href');
            Swal.fire({
                title: 'Xác nhận xóa?',
                text: "Bạn có chắc chắn muốn xóa đề tài này khỏi hội đồng?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Xóa',
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: url,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            Swal.fire({
                                title: 'Thành công!',
                                text: 'Đã xóa đề tài khỏi hội đồng',
                                icon: 'success',
                                confirmButtonColor: '#3085d6'
                            }).then(() => {
                                location.reload();
                            });
                        },
                        error: function(xhr) {
                            Swal.fire({
                                title: 'Lỗi!',
                                text: xhr.responseJSON.message || 'Có lỗi xảy ra',
                                icon: 'error',
                                confirmButtonColor: '#3085d6'
                            });
                        }
                    });
                }
            });
        });

        // Xử lý xóa lịch chấm
        $('.btn-xoa-lich-cham').click(function(e) {
            e.preventDefault();
            const url = $(this).attr('data-href');
            Swal.fire({
                title: 'Xác nhận xóa?',
                text: "Bạn có chắc chắn muốn xóa lịch chấm này?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Xóa',
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: url,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            Swal.fire({
                                title: 'Thành công!',
                                text: 'Đã xóa lịch chấm',
                                icon: 'success',
                                confirmButtonColor: '#3085d6'
                            }).then(() => {
                                location.reload();
                            });
                        },
                        error: function(xhr) {
                            Swal.fire({
                                title: 'Lỗi!',
                                text: xhr.responseJSON.message || 'Có lỗi xảy ra',
                                icon: 'error',
                                confirmButtonColor: '#3085d6'
                            });
                        }
                    });
                }
            });
        });

        // Validate thời gian kết thúc phải sau thời gian bắt đầu
        $('form').submit(function(e) {
            const thoiGianBatDau = $('input[name="thoi_gian_bat_dau"]').val();
            const thoiGianKetThuc = $('input[name="thoi_gian_ket_thuc"]').val();
            
            if (thoiGianBatDau && thoiGianKetThuc && thoiGianBatDau >= thoiGianKetThuc) {
                e.preventDefault();
                Swal.fire({
                    title: 'Lỗi!',
                    text: 'Thời gian kết thúc phải sau thời gian bắt đầu!',
                    icon: 'error',
                    confirmButtonColor: '#3085d6'
                });
            }
        });
    });
</script>
@endpush
