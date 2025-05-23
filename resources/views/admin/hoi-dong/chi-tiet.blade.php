@extends('admin.layout')

@section('styles')
@vite(['resources/scss/hoi-dong/chi-tiet.scss'])
@endsection

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
            <a href="{{ route('admin.hoi-dong.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
        </div>
    </div>

    <div class="row g-4">
        <!-- Thông tin cơ bản -->
        <div class="col-xl-6">
            <div class="card h-100">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-info-circle text-primary me-2"></i>
                        Thông tin cơ bản
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-borderless">
                            <tr>
                                <th style="width: 200px" class="text-muted">Mã hội đồng:</th>
                                <td>
                                    <span class="badge bg-primary">{{ $hoiDong->ma_hoi_dong }}</span>
                                </td>
                            </tr>
                            <tr>
                                <th class="text-muted">Tên hội đồng:</th>
                                <td class="fw-semibold">{{ $hoiDong->ten }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted">Tên Giảng viên:</th>
                                <td class="fw-semibold">
                                    @php
                                        $chiTietBaoCao = $hoiDong->chiTietBaoCaos->first();
                                        $deTai = $chiTietBaoCao ? $chiTietBaoCao->deTai : null;
                                        $giangVien = $deTai ? $deTai->giangVien : null;
                                        $taiKhoan = $giangVien ? $giangVien->taiKhoan : null;
                                    @endphp

                                    @if($chiTietBaoCao && $deTai && $giangVien && $taiKhoan)
                                        {{ $taiKhoan->ten }}
                                        <small class="text-muted d-block">
                                            <i class="fas fa-envelope me-1"></i>
                                            {{ $taiKhoan->email }}
                                        </small>
                                    @else
                                        <div class="text-muted">
                                            @if(!$chiTietBaoCao)
                                                <small>Chưa có chi tiết báo cáo</small>
                                            @elseif(!$deTai)
                                                <small>Chưa có đề tài</small>
                                            @elseif(!$giangVien)
                                                <small>Chưa có giảng viên</small>
                                            @elseif(!$taiKhoan)
                                                <small>Chưa có tài khoản</small>
                                            @endif
                                        </div>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th class="text-muted">Đợt báo cáo:</th>
                                <td>
                                    <span class="badge bg-info">
                                        {{ $hoiDong->dotBaoCao->nam_hoc ?? 'N/A' }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th class="text-muted">Ngày tạo:</th>
                                <td>
                                    <i class="far fa-calendar-alt text-muted me-1"></i>
                                    {{ $hoiDong->created_at->format('d/m/Y H:i') }}
                                </td>
                            </tr>
                            <tr>
                                <th class="text-muted">Cập nhật lần cuối:</th>
                                <td>
                                    <i class="far fa-clock text-muted me-1"></i>
                                    {{ $hoiDong->updated_at->format('d/m/Y H:i') }}
                                </td>
                            </tr>
                        </table>
                    </div>
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
