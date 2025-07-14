<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Hệ thống')</title>
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Thêm các thẻ meta, css, js khác nếu cần -->
    <style>
        body {
            margin: 0;
            padding: 0;
            background: #f8f9fa;
        }
        .app-flex {
            display: flex;
            min-height: 100vh;
        }
        .sidebar-area {
            width: 240px;
            min-width: 200px;
            background: #fff;
            box-shadow: 2px 0 8px rgba(0,0,0,0.04);
            z-index: 10;
        }
        .main-area {
            flex: 1;
            padding: 32px 24px 24px 24px;
            background: #f8f9fa;
            min-height: 100vh;
        }
        .app-header {
            width: 100%;
            background: #003366;
            color: #fff;
            padding: 10px 32px;
            font-size: 1.1rem;
            font-weight: 500;
            letter-spacing: 1px;
        }
        @media (max-width: 900px) {
            .app-flex { flex-direction: column; }
            .sidebar-area { width: 100%; min-width: unset; }
            .main-area { padding: 16px 4vw; }
        }
    </style>
</head>
<body>
    <div class="app-header">
        @yield('title', 'Hệ thống Quản lý')
    </div>
    <div class="app-flex">
        <div class="sidebar-area">
            @include('components.giangvien.sidebar')
        </div>
        <main class="main-area">
            @yield('content')
        </main>
    </div>
</body>
</html> 