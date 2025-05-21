@extends('admin.layout')

@section('title', 'Dashboard - Thống kê')

@vite('resources/scss/dashboard.scss')

@section('content')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h1 style="color: #2d3748; font-weight: 700;">Thống kê hệ thống</h1>
    </div>

    <div class="stats-container">
        <div class="stat-card">
            <div style="display: flex; align-items: center; gap: 15px;">
                <div style="background-color: #4299e1; padding: 15px; border-radius: 8px;">
                    <i class="fas fa-users" style="color: white; font-size: 24px;"></i>
                </div>
                <div>
                    <h3 style="color: #4a5568; margin: 0 0 5px 0;">Tổng tài khoản</h3>
                    <p class="stat-number" style="color: #2d3748; font-size: 24px; font-weight: bold; margin: 0;">
                        {{ $totalTaiKhoan ?? 0 }}
                    </p>
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div style="display: flex; align-items: center; gap: 15px;">
                <div style="background-color: #48bb78; padding: 15px; border-radius: 8px;">
                    <i class="fas fa-chalkboard-teacher" style="color: white; font-size: 24px;"></i>
                </div>
                <div>
                    <h3 style="color: #4a5568; margin: 0 0 5px 0;">Giảng viên</h3>
                    <p class="stat-number" style="color: #2d3748; font-size: 24px; font-weight: bold; margin: 0;">
                        {{ $totalGiangVien ?? 0 }}
                    </p>
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div style="display: flex; align-items: center; gap: 15px;">
                <div style="background-color: #ed8936; padding: 15px; border-radius: 8px;">
                    <i class="fas fa-users-cog" style="color: white; font-size: 24px;"></i>
                </div>
                <div>
                    <h3 style="color: #4a5568; margin: 0 0 5px 0;">Hội đồng</h3>
                    <p class="stat-number" style="color: #2d3748; font-size: 24px; font-weight: bold; margin: 0;">
                        {{ $totalHoiDong ?? 0 }}
                    </p>
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div style="display: flex; align-items: center; gap: 15px;">
                <div style="background-color: #9f7aea; padding: 15px; border-radius: 8px;">
                    <i class="fas fa-file-alt" style="color: white; font-size: 24px;"></i>
                </div>
                <div>
                    <h3 style="color: #4a5568; margin: 0 0 5px 0;">Đợt báo cáo</h3>
                    <p class="stat-number" style="color: #2d3748; font-size: 24px; font-weight: bold; margin: 0;">
                        {{ $totalDotBaoCao ?? 0 }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <style>
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgb(0 0 0 / 0.1);
        }

        .stat-card:hover {
            transform: translateY(-2px);
            transition: transform 0.2s ease-in-out;
        }
    </style>
@endsection
