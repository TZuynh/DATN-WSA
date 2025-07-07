@extends('admin.layout')

@section('title', 'Thống kê điểm')

@section('content')
<style>
:root {
    --primary: #2563eb;
    --primary-dark: #1d4ed8;
    --secondary: #64748b;
    --success: #059669;
    --warning: #d97706;
    --danger: #dc2626;
    --info: #0891b2;
    --light: #f8fafc;
    --dark: #1e293b;
    --white: #ffffff;
    --gray-50: #f8fafc;
    --gray-100: #f1f5f9;
    --gray-200: #e2e8f0;
    --gray-300: #cbd5e1;
    --gray-400: #94a3b8;
    --gray-500: #64748b;
    --gray-600: #475569;
    --gray-700: #334155;
    --gray-800: #1e293b;
    --gray-900: #0f172a;
    --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
    --shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
    --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
    --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
    --shadow-xl: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
}

body {
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    line-height: 1.6;
    color: var(--gray-800);
}

.container-fluid {
    padding: 2rem;
    max-width: 1400px;
    margin: 0 auto;
}

/* Page Header */
.page-header {
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
    border-radius: 1.5rem;
    padding: 3rem;
    margin-bottom: 2.5rem;
    box-shadow: var(--shadow-xl);
    position: relative;
    overflow: hidden;
}

.page-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="dots" width="20" height="20" patternUnits="userSpaceOnUse"><circle cx="10" cy="10" r="1" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23dots)"/></svg>');
}

.page-header .d-flex {
    position: relative;
    z-index: 2;
}

.page-title {
    color: var(--white);
    font-weight: 800;
    font-size: 2.5rem;
    margin-bottom: 1rem;
    text-shadow: 0 2px 4px rgba(0,0,0,0.1);
    letter-spacing: -0.025em;
}

.page-subtitle {
    color: rgba(255,255,255,0.9);
    font-size: 1.125rem;
    font-weight: 400;
    margin: 0;
    line-height: 1.5;
}

.header-btn {
    display: inline-flex;
    align-items: center;
    padding: 1rem 1.75rem;
    border-radius: 0.875rem;
    font-weight: 600;
    font-size: 0.875rem;
    text-decoration: none;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border: 2px solid transparent;
    backdrop-filter: blur(10px);
    letter-spacing: 0.025em;
}

.header-btn-outline {
    background: rgba(255,255,255,0.1);
    color: var(--white);
    border-color: rgba(255,255,255,0.2);
}

.header-btn-outline:hover {
    background: rgba(255,255,255,0.2);
    border-color: rgba(255,255,255,0.3);
    color: var(--white);
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.header-btn-primary {
    background: rgba(255,255,255,0.95);
    color: var(--primary);
    border-color: rgba(255,255,255,0.95);
}

.header-btn-primary:hover {
    background: var(--white);
    color: var(--primary-dark);
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

/* Filter Section */
.filter-section {
    background: var(--white);
    border-radius: 1rem;
    padding: 2rem;
    margin-bottom: 2.5rem;
    box-shadow: var(--shadow-lg);
    border: 1px solid var(--gray-100);
}

.filter-title {
    color: var(--gray-900);
    font-weight: 700;
    font-size: 1.25rem;
    margin: 0;
    display: flex;
    align-items: center;
}

.form-select {
    border: 2px solid var(--gray-200);
    border-radius: 0.75rem;
    padding: 0.875rem 1rem;
    font-size: 0.875rem;
    font-weight: 500;
    transition: all 0.2s ease;
    background-color: var(--white);
}

.form-select:focus {
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    outline: none;
}

.btn {
    padding: 0.875rem 1.5rem;
    border-radius: 0.75rem;
    font-weight: 600;
    font-size: 0.875rem;
    border: 2px solid transparent;
    transition: all 0.2s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    letter-spacing: 0.025em;
}

.btn-primary {
    background: var(--primary);
    color: var(--white);
    border-color: var(--primary);
}

.btn-primary:hover {
    background: var(--primary-dark);
    border-color: var(--primary-dark);
    transform: translateY(-1px);
    box-shadow: var(--shadow-md);
}

.btn-outline {
    background: transparent;
    color: var(--gray-600);
    border-color: var(--gray-300);
}

.btn-outline:hover {
    background: var(--gray-50);
    color: var(--gray-700);
    border-color: var(--gray-400);
    transform: translateY(-1px);
}

/* Stats Grid */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2.5rem;
}

.stat-card {
    background: var(--white);
    border-radius: 1.25rem;
    padding: 2rem;
    box-shadow: var(--shadow-lg);
    border: 1px solid var(--gray-100);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, var(--primary), var(--primary-dark));
}

.stat-card.blue::before { background: linear-gradient(90deg, #3b82f6, #1d4ed8); }
.stat-card.green::before { background: linear-gradient(90deg, #10b981, #059669); }
.stat-card.orange::before { background: linear-gradient(90deg, #f59e0b, #d97706); }
.stat-card.red::before { background: linear-gradient(90deg, #ef4444, #dc2626); }

.stat-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-xl);
}

.stat-header {
    display: flex;
    align-items: flex-start;
    gap: 1.25rem;
    margin-bottom: 1.5rem;
}

.stat-icon {
    width: 3.5rem;
    height: 3.5rem;
    border-radius: 1rem;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    color: var(--white);
    flex-shrink: 0;
}

.stat-icon.blue { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }
.stat-icon.green { background: linear-gradient(135deg, #10b981, #059669); }
.stat-icon.orange { background: linear-gradient(135deg, #f59e0b, #d97706); }
.stat-icon.red { background: linear-gradient(135deg, #ef4444, #dc2626); }

.stat-content {
    flex: 1;
}

.stat-number {
    font-size: 2.25rem;
    font-weight: 800;
    color: var(--gray-900);
    line-height: 1;
    margin-bottom: 0.5rem;
    letter-spacing: -0.025em;
}

.stat-label {
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--gray-600);
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: 0.25rem;
}

.stat-description {
    font-size: 0.875rem;
    color: var(--gray-500);
    line-height: 1.4;
}

.progress {
    height: 0.5rem;
    background: var(--gray-100);
    border-radius: 0.25rem;
    overflow: hidden;
}

.progress-bar {
    height: 100%;
    background: linear-gradient(90deg, var(--primary), var(--primary-dark));
    border-radius: 0.25rem;
    transition: width 0.6s ease;
}

/* Content Cards */
.content-card {
    background: var(--white);
    border-radius: 1.25rem;
    box-shadow: var(--shadow-lg);
    border: 1px solid var(--gray-100);
    margin-bottom: 2rem;
    overflow: hidden;
}

.content-header {
    padding: 1.5rem 2rem;
    border-bottom: 1px solid var(--gray-100);
    background: var(--gray-50);
}

.content-title {
    color: var(--gray-900);
    font-weight: 700;
    font-size: 1.125rem;
    margin: 0;
    align-items: center;
}

.content-body {
    padding: 2rem;
}

/* Tables */
.table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    margin-bottom: 0;
}

.table th {
    background: var(--gray-50);
    color: var(--gray-700);
    font-weight: 600;
    font-size: 0.875rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    padding: 1rem 1.5rem;
    border-bottom: 2px solid var(--gray-200);
    text-align: left;
}

.table td {
    padding: 1rem 1.5rem;
    border-bottom: 1px solid var(--gray-100);
    color: var(--gray-800);
    font-size: 0.875rem;
    vertical-align: middle;
}

.table tbody tr:hover {
    background: var(--gray-50);
}

.table tbody tr:last-child td {
    border-bottom: none;
}

/* Badges */
.badge {
    display: inline-flex;
    align-items: center;
    padding: 0.375rem 0.75rem;
    border-radius: 0.5rem;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.025em;
}

.badge-success {
    background: rgba(16, 185, 129, 0.1);
    color: var(--success);
}

.badge-warning {
    background: rgba(245, 158, 11, 0.1);
    color: var(--warning);
}

.badge-danger {
    background: rgba(239, 68, 68, 0.1);
    color: var(--danger);
}

.badge-info {
    background: rgba(8, 145, 178, 0.1);
    color: var(--info);
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 3rem 2rem;
}

.empty-state-icon {
    width: 4rem;
    height: 4rem;
    border-radius: 50%;
    background: var(--gray-100);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--gray-400);
    font-size: 1.5rem;
    margin: 0 auto 1.5rem;
}

.empty-state-title {
    color: var(--gray-700);
    font-weight: 600;
    margin-bottom: 0.5rem;
    font-size: 1.125rem;
}

.empty-state-text {
    color: var(--gray-500);
    font-size: 0.875rem;
    line-height: 1.5;
}

/* Chart Container */
.chart-container {
    background: var(--white);
    border: 1px solid var(--gray-200);
    border-radius: 1rem;
    padding: 2rem;
    box-shadow: var(--shadow-sm);
    margin-bottom: 2rem;
}

.chart-title {
    color: var(--gray-900);
    font-weight: 700;
    font-size: 1.25rem;
    margin-bottom: 1.5rem;
    text-align: center;
}

/* User Avatar */
.user-avatar {
    width: 2.5rem;
    height: 2.5rem;
    border-radius: 50%;
    background: var(--primary);
    color: var(--white);
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 0.875rem;
}

/* Score Range */
.score-range {
    display: inline-flex;
    align-items: center;
    padding: 0.375rem 0.75rem;
    background: var(--gray-100);
    border-radius: 0.5rem;
    font-size: 0.75rem;
    font-weight: 600;
    color: var(--gray-700);
    margin-right: 0.75rem;
}

/* Responsive */
@media (max-width: 768px) {
    .container-fluid {
        padding: 1rem;
    }
    
    .page-header {
        padding: 2rem 1.5rem;
    }
    
    .page-title {
        font-size: 2rem;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .stat-card {
        padding: 1.5rem;
    }
    
    .content-body {
        padding: 1.5rem;
    }
    
    .table-responsive {
        overflow-x: auto;
    }
}
</style>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">
                    <i class="fas fa-chart-line mr-3"></i>
                    Thống kê điểm
                </h1>
                <p class="page-subtitle">Phân tích và báo cáo chi tiết về điểm số hệ thống</p>
            </div>
            <div class="d-flex gap-3 ms-auto">
                <a href="{{ route('admin.bang-diem.index') }}" class="header-btn header-btn-outline">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Quay lại
                </a>
                {{-- <a href="{{ route('admin.bang-diem.debug') }}" class="header-btn header-btn-primary" target="_blank">
                    <i class="fas fa-bug mr-2"></i>
                    Debug
                </a> --}}
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="filter-section">
        <div class="row align-items-center">
            <div class="col-md-4">
                <h4 class="filter-title mb-0">
                    <i class="fas fa-filter mr-2 text-primary"></i>
                    Bộ lọc dữ liệu
                </h4>
            </div>
            <div class="col-md-8">
                <form method="GET" action="{{ route('admin.bang-diem.thong-ke') }}" class="d-flex gap-2 align-items-center">
                    @php
                        // Gom nhóm theo năm học và học kỳ, chỉ lấy duy nhất mỗi cặp
                        $dotBaoCaoOptions = collect($dotBaoCaos)->unique(function($item) {
                            return $item->nam_hoc . '-' . ($item->hocKy->ten ?? 'Không xác định');
                        });
                    @endphp
                    <select class="form-select flex-grow-1" id="dot_bao_cao_id" name="dot_bao_cao_id">
                        <option value="">Tất cả</option>
                        @foreach($dotBaoCaoOptions as $dotBaoCao)
                            <option value="{{ $dotBaoCao->id }}" {{ $dotBaoCaoId == $dotBaoCao->id ? 'selected' : '' }}>
                                {{ $dotBaoCao->nam_hoc ?? 'N/A' }} - {{ $dotBaoCao->hocKy->ten ?? 'Không xác định' }}
                            </option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-primary px-3">
                        <i class="fas fa-search"></i>
                    </button>
                    <a href="{{ route('admin.bang-diem.thong-ke') }}" class="btn btn-outline px-3">
                        <i class="fas fa-times"></i>
                    </a>
                </form>
            </div>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="stats-grid">
        <div class="stat-card blue">
            <div class="stat-header">
                <div class="stat-icon blue">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number">{{ number_format($thongKe['tong_so_diem']) }}</div>
                    <div class="stat-label">Tổng số điểm</div>
                    <div class="stat-description">Tổng số bản ghi điểm trong hệ thống</div>
                </div>
            </div>
            <div class="progress">
                <div class="progress-bar" style="width: 100%"></div>
            </div>
        </div>
        
        <div class="stat-card green">
            <div class="stat-header">
                <div class="stat-icon green">
                    <i class="fas fa-file-alt"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number">{{ number_format($thongKe['diem_trung_binh_bao_cao'], 2) }}</div>
                    <div class="stat-label">Điểm TB báo cáo</div>
                    <div class="stat-description">Điểm trung bình phần báo cáo</div>
                </div>
            </div>
            <div class="progress">
                <div class="progress-bar" style="width: {{ ($thongKe['diem_trung_binh_bao_cao'] / 10) * 100 }}%"></div>
            </div>
        </div>
        
        <div class="stat-card orange">
            <div class="stat-header">
                <div class="stat-icon orange">
                    <i class="fas fa-microphone"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number">{{ number_format($thongKe['diem_trung_binh_thuyet_trinh'], 2) }}</div>
                    <div class="stat-label">Điểm TB thuyết trình</div>
                    <div class="stat-description">Điểm trung bình phần thuyết trình</div>
                </div>
            </div>
            <div class="progress">
                <div class="progress-bar" style="width: {{ ($thongKe['diem_trung_binh_thuyet_trinh'] / 10) * 100 }}%"></div>
            </div>
        </div>
        
        <div class="stat-card red">
            <div class="stat-header">
                <div class="stat-icon red">
                    <i class="fas fa-trophy"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number">{{ number_format($thongKe['diem_cao_nhat'], 2) }}</div>
                    <div class="stat-label">Điểm cao nhất</div>
                    <div class="stat-description">Điểm tổng cao nhất đạt được</div>
                </div>
            </div>
            <div class="progress">
                <div class="progress-bar" style="width: {{ ($thongKe['diem_cao_nhat'] / 10) * 100 }}%"></div>
            </div>
        </div>
    </div>

    <!-- Data Tables -->
    <div class="row">
        <div class="col-md-6">
            <div class="content-card">
                <div class="content-header">
                    <h5 class="content-title">
                        <i class="fas fa-users mr-2 text-primary"></i>
                        Thống kê theo giảng viên
                    </h5>
                </div>
                <div class="content-body">
                    @if($thongKeTheoGiangVien->isEmpty())
                        <div class="empty-state">
                            <div class="empty-state-icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <h5 class="empty-state-title">Không có dữ liệu</h5>
                            <p class="empty-state-text">Chưa có thống kê nào được tạo</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Giảng viên</th>
                                        <th class="text-center"></th>
                                        <th class="text-center">Điểm TB</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($thongKeTheoGiangVien as $item)
                                        <tr>
                                            <td>
                                                <div class="align-items-center">
                                                    <div class="user-avatar mr-3">
                                                        {{ substr($item['giang_vien']->ten, 0, 1) }}
                                                    </div>
                                                    <span class="font-weight-semibold">{{ $item['giang_vien']->ten }}</span>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge badge-primary">{{ $item['so_luong'] }}</span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge badge-success">{{ number_format($item['diem_trung_binh'], 2) }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="content-card">
                <div class="content-header">
                    <h5 class="content-title">
                        <i class="fas fa-chart-pie mr-2 text-primary"></i>
                        Phân bố điểm
                    </h5>
                </div>
                <div class="content-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Khoảng điểm</th>
                                    <th class="text-center">Tỷ lệ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $danhSachKhoang = ['0-5', '5-6', '6-7', '7-8', '8-9', '9-10'];
                                    $tongSo = array_sum($khoangDiem);
                                @endphp
                                @foreach($danhSachKhoang as $khoang)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <span class="score-range">{{ $khoang }}</span>
                                                <span class="font-weight-semibold">{{ $khoang }}</span>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            @if($tongSo > 0)
                                                <div class="progress mb-2">
                                                    <div class="progress-bar" 
                                                         style="width: {{ (($khoangDiem[$khoang] ?? 0) / $tongSo) * 100 }}%;">
                                                    </div>
                                                </div>
                                                <small class="text-muted">
                                                    {{ number_format((($khoangDiem[$khoang] ?? 0) / $tongSo) * 100, 1) }}%
                                                </small>
                                            @else
                                                <span class="text-muted">0%</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="row">
        <div class="col-md-6">
            <div class="chart-container">
                <h5 class="chart-title">
                    <i class="fas fa-chart-bar mr-2 text-primary"></i>
                    Biểu đồ phân bố điểm
                </h5>
                <canvas id="diemChart" width="400" height="200"></canvas>
            </div>
        </div>
        <div class="col-md-6">
            <div class="chart-container">
                <h5 class="chart-title">
                    <i class="fas fa-chart-pie mr-2 text-primary"></i>
                    Biểu đồ điểm theo giảng viên
                </h5>
                <canvas id="giangVienChart" width="400" height="200"></canvas>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    try {
        // Biểu đồ phân bố điểm
        const diemCtx = document.getElementById('diemChart').getContext('2d');
        new Chart(diemCtx, {
            type: 'bar',
            data: {
                labels: ['0-5', '5-6', '6-7', '7-8', '8-9', '9-10'],
                datasets: [{
                    label: 'Số lượng sinh viên',
                    data: [
                        {{ isset($khoangDiem['0-5']) ? $khoangDiem['0-5'] : 0 }},
                        {{ isset($khoangDiem['5-6']) ? $khoangDiem['5-6'] : 0 }},
                        {{ isset($khoangDiem['6-7']) ? $khoangDiem['6-7'] : 0 }},
                        {{ isset($khoangDiem['7-8']) ? $khoangDiem['7-8'] : 0 }},
                        {{ isset($khoangDiem['8-9']) ? $khoangDiem['8-9'] : 0 }},
                        {{ isset($khoangDiem['9-10']) ? $khoangDiem['9-10'] : 0 }}
                    ],
                    backgroundColor: [
                        '#dc2626',
                        '#d97706',
                        '#059669',
                        '#2563eb',
                        '#7c3aed',
                        '#ec4899'
                    ],
                    borderColor: [
                        '#b91c1c',
                        '#b45309',
                        '#047857',
                        '#1d4ed8',
                        '#6d28d9',
                        '#db2777'
                    ],
                    borderWidth: 1,
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,
                            font: {
                                weight: '500'
                            }
                        },
                        grid: {
                            color: '#e5e7eb'
                        }
                    },
                    x: {
                        ticks: {
                            font: {
                                weight: '500'
                            }
                        },
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });

        // Biểu đồ điểm theo giảng viên
        const giangVienCtx = document.getElementById('giangVienChart').getContext('2d');
        new Chart(giangVienCtx, {
            type: 'doughnut',
            data: {
                labels: [
                    @foreach($thongKeTheoGiangVien as $item)
                        '{{ $item['giang_vien']->ten }}',
                    @endforeach
                ],
                datasets: [{
                    data: [
                        @foreach($thongKeTheoGiangVien as $item)
                            {{ $item['so_luong'] }},
                        @endforeach
                    ],
                    backgroundColor: [
                        '#2563eb',
                        '#7c3aed',
                        '#dc2626',
                        '#059669',
                        '#d97706',
                        '#0891b2',
                        '#ec4899',
                        '#8b5cf6'
                    ],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true,
                            font: {
                                weight: '500'
                            }
                        }
                    }
                }
            }
        });
    } catch (error) {
        console.error('Lỗi khi tạo biểu đồ:', error);
    }
});
</script>
@endsection 