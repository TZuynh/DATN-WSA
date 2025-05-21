@extends('admin.layout')

@section('title', 'Dashboard - Thống kê')

@vite('resources/scss/dashboard.scss')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <div class="page-header">
        <h1>Thống kê hệ thống</h1>
    </div>

    <div class="stats-container">
        <div class="stat-card">
            <div class="icon-container users">
                <i class="fas fa-users"></i>
            </div>
            <div>
                <h3>Tổng tài khoản</h3>
                <p class="stat-number">
                    {{ $totalTaiKhoan ?? 0 }}
                </p>
            </div>
        </div>

        <div class="stat-card">
             <div class="icon-container teachers">
                <i class="fas fa-chalkboard-teacher"></i>
            </div>
            <div>
                <h3>Giảng viên</h3>
                <p class="stat-number">
                    {{ $totalGiangVien ?? 0 }}
                </p>
            </div>
        </div>

        <div class="stat-card">
            <div class="icon-container councils">
                <i class="fas fa-users-cog"></i>
            </div>
            <div>
                <h3>Hội đồng</h3>
                <p class="stat-number">
                    {{ $totalHoiDong ?? 0 }}
                </p>
            </div>
        </div>

        <div class="stat-card">
            <div class="icon-container reports">
                <i class="fas fa-file-alt"></i>
            </div>
            <div>
                <h3>Đợt báo cáo</h3>
                <p class="stat-number">
                    {{ $totalDotBaoCao ?? 0 }}
                </p>
            </div>
        </div>
    </div>
@endsection
