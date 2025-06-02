<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="icon" type="image/png" href="{{ asset('images/logo.jpg') }}" />
    <title>Admin - @yield('title', 'Dashboard')</title>

    {{-- Admin custom styles (nếu có) --}}
    @vite(['resources/scss/admin-style.scss'])
    
    {{-- Bootstrap CSS (Load sau custom styles) --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Font Awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    @yield('styles')
</head>
<body class="theme-{{ $settings['theme'] ?? 'light' }}">
{{-- Gọi component navbar --}}
<x-admin.navbar />

{{-- Gọi component sidebar --}}
<x-admin.slidebar />

<main class="admin-main">
    {{-- Thông báo success/error --}}
    <div class="admin-alerts px-3"> {{-- Thêm div bọc để dễ quản lý --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
    </div>

    @yield('content')
</main>

{{-- Bootstrap JS --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

@vite('resources/js/app.js') {{-- Giữ lại app.js --}}

@stack('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Xử lý thông báo tự động ẩn sau 3 giây
        const alerts = document.querySelectorAll('.admin-alerts .alert');
        alerts.forEach(alert => {
            setTimeout(() => {
                // Kiểm tra nếu alert chưa bị đóng thủ công
                if(alert.classList.contains('show')) {
                     alert.classList.remove('show');
                     alert.classList.add('fade');
                     setTimeout(() => {
                         alert.remove();
                     }, 300); // Đợi animation fade hoàn thành
                }
            }, 3000);
        });
    });
</script>
</body>
</html>
