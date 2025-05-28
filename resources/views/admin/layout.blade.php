<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
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
    {{-- Container cho alerts --}}
    @if(session('success') || session('error'))
    <div id="alert-box"
         class="fixed top-5 right-5 z-50 px-4 py-3 rounded-md shadow-md text-white transition-opacity duration-500
                {{ session('success') ? 'bg-green-500' : 'bg-red-500' }}">
        <div class="flex items-center justify-between space-x-2">
            <span>
                {{ session('success') ?? session('error') }}
            </span>
            <button onclick="closeAlert()" class="font-bold text-xl leading-none">&times;</button>
        </div>
    </div>

    <script>
        // Tự động ẩn sau 3 giây
        setTimeout(() => {
            closeAlert();
        }, 3000);

        function closeAlert() {
            const alertBox = document.getElementById('alert-box');
            if (alertBox) {
                alertBox.style.opacity = 0;
                setTimeout(() => alertBox.remove(), 500); // đợi hiệu ứng mờ rồi xóa
            }
        }
    </script>
@endif
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
