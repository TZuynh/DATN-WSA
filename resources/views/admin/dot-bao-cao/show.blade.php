@extends('admin.layout')

@section('title', 'Chi tiết đợt báo cáo')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h1 style="font-size: 24px; color: #2d3748;">Chi tiết đợt báo cáo</h1>
        <a href="{{ route('admin.dot-bao-cao.index') }}" 
           style="padding: 10px 20px; background-color: #718096; color: white; border-radius: 4px; text-decoration: none;">
            <i class="fas fa-arrow-left"></i> Quay lại
        </a>
    </div>

    <!-- Thông tin cơ bản -->
    <div class="card mb-6">
        <div class="card-header">
            <h2 class="text-xl font-semibold text-primary">Thông tin cơ bản</h2>
        </div>
        <div class="card-body">
            <div class="overflow-x-auto">
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <th class="w-1/3 text-left">Năm học</th>
                            <td>{{ $dotBaoCao->nam_hoc }}</td>
                        </tr>
                        <tr>
                            <th class="w-1/3 text-left">Học kỳ</th>
                            <td>{{ $dotBaoCao->hocKy->ten ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th class="w-1/3 text-left">Trạng thái</th>
                            <td>
                                <span class="py-1 rounded-full text-sm {{ $dotBaoCao->trang_thai_class }}">
                                    {{ $dotBaoCao->trang_thai_text }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th class="w-1/3 text-left">Ngày bắt đầu</th>
                            <td>{{ $dotBaoCao->ngay_bat_dau->format('d/m/Y') }}</td>
                        </tr>
                        <tr>
                            <th class="w-1/3 text-left">Ngày kết thúc</th>
                            <td>{{ $dotBaoCao->ngay_ket_thuc->format('d/m/Y') }}</td>
                        </tr>
                        <tr>
                            <th class="w-1/3 text-left">Mô tả</th>
                            <td>{{ $dotBaoCao->mo_ta ?? 'Không có mô tả' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Danh sách hội đồng -->
    <div class="card mb-6">
        <div class="card-header">
            <h2 class="text-xl font-semibold text-primary">Danh sách hội đồng</h2>
        </div>
        <div class="card-body">
            <div class="overflow-x-auto">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th style="width: 15%">Mã hội đồng</th>
                            <th style="width: 40%">Tên hội đồng</th>
                            <th style="width: 22.5%">Số đề tài</th>
                            <th style="width: 22.5%">Số thành viên</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dotBaoCao->hoiDongs as $hoiDong)
                        <tr>
                            <td>{{ $hoiDong['ma_hoi_dong'] }}</td>
                            <td>{{ $hoiDong['ten'] }}</td>
                            <td class="text-center">{{ count($hoiDong->chiTietBaoCaos ?? []) }}</td>
                            <td class="text-center">{{ count($hoiDong->phanCongVaiTros ?? []) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" style="text-align: center; padding: 1rem;">
                                <div style="display: flex; flex-direction: column; align-items: center; gap: 0.5rem;">
                                    <i class="fas fa-info-circle" style="color: #718096;"></i>
                                    <span style="color: #718096;">Chưa có hội đồng nào</span>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Danh sách đề tài -->
    <div class="card">
        <div class="card-header">
            <h2 class="text-xl font-semibold text-primary">Danh sách đề tài</h2>
        </div>
        <div class="card-body">
            <div class="overflow-x-auto">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th style="width: 15%">Mã đề tài</th>
                            <th style="width: 35%">Tên đề tài</th>
                            <th style="width: 15%">Nhóm thực hiện</th>
                            <th style="width: 20%">Giảng viên hướng dẫn</th>
                            <th style="width: 15%">Hội đồng</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dotBaoCao->deTais as $deTai)
                        <tr>
                            <td>{{ $deTai['ma_de_tai'] }}</td>
                            <td>{{ $deTai['ten_de_tai'] }}</td>
                            <td>{{ $deTai['nhom']['ten'] ?? 'Chưa có nhóm' }}</td>
                            <td>{{ $deTai->giangVien->ten ?? 'Chưa có giảng viên' }}</td>
                            <td>
                                @php
                                    $chiTiet = $deTai->chiTietBaoCao;
                                    $hoiDongTen = $chiTiet && $chiTiet->hoiDong ? $chiTiet->hoiDong->ten : 'Chưa phân hội đồng';
                                @endphp
                                {{ $hoiDongTen }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 1rem;">
                                <div style="display: flex; flex-direction: column; align-items: center; gap: 0.5rem;">
                                    <i class="fas fa-info-circle" style="color: #718096;"></i>
                                    <span style="color: #718096;">Chưa có đề tài nào</span>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    /* Thêm các style bổ sung nếu cần */
    .btn {
        @apply px-4 py-2 rounded transition-colors duration-200;
    }
    .btn-primary {
        @apply bg-blue-500 text-white hover:bg-blue-600;
    }
    .btn-sm {
        @apply px-2 py-1 text-sm;
    }
    .card {
        @apply bg-white rounded-lg shadow-md;
    }
    .card-header {
        @apply px-6 py-4 border-b border-gray-200;
    }
    .card-body {
        @apply p-6;
    }
    .text-primary {
        @apply text-blue-600;
    }
    .table {
        @apply w-full;
    }
    .table th {
        @apply bg-gray-50 px-6 py-3 text-left text-sm font-medium text-gray-500;
    }
    .table td {
        @apply px-6 py-4 text-sm text-gray-800;
    }
    .table-bordered th,
    .table-bordered td {
        @apply border border-gray-200;
    }
    /* Thêm style cho dark mode */
    .theme-dark .card {
        @apply bg-gray-800;
    }
    .theme-dark .card-header {
        @apply border-gray-700;
    }
    .theme-dark .text-gray-600 {
        @apply text-gray-400;
    }
    .theme-dark .text-gray-800 {
        @apply text-gray-200;
    }
    .theme-dark .border-gray-200 {
        @apply border-gray-700;
    }
    .theme-dark .table th {
        @apply bg-gray-700 text-gray-300;
    }
    .theme-dark .table td {
        @apply text-gray-200;
    }
    .theme-dark .table-bordered th,
    .theme-dark .table-bordered td {
        @apply border-gray-700;
    }
    /* Style cho thông báo trống */
    .theme-dark td i.fa-info-circle,
    .theme-dark td span {
        @apply text-gray-400;
    }
</style>
@endpush
@endsection 