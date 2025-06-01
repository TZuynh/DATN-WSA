<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="icon" type="image/png" href="{{ asset('images/logo.jpg') }}" />
    <title>Admin - @yield('title', 'Dashboard')</title>
    @vite(['resources/scss/admin-style.scss'])
    @yield('styles')
</head>
<body class="theme-{{ $settings['theme'] ?? 'light' }}">
{{-- Gọi component navbar --}}
<x-admin.navbar />

{{-- Gọi component sidebar --}}
<x-admin.slidebar />

<main class="admin-main">
    @yield('content')
</main>

@vite('resources/js/app.js')

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Xử lý thông báo tự động ẩn sau 3 giây
        const alerts = document.querySelectorAll('.admin-alerts .alert');
        alerts.forEach(alert => {
            setTimeout(() => {
                alert.classList.add('fade');
                setTimeout(() => {
                    alert.remove();
                }, 300); // Đợi animation fade hoàn thành
            }, 3000);
        });
    });
</script>
@endpush
</body>
</html>
