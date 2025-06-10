<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('images/logo.jpg') }}" />
    <title>Admin - @yield('title', 'Dashboard')</title>

    {{-- Admin custom styles (nếu có) --}}
    @vite(['resources/scss/admin-style.scss'])
    
    {{-- Bootstrap CSS (Load sau custom styles) --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Font Awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    {{-- SweetAlert2 CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css" rel="stylesheet">

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

    <div style="padding-top: 20px">
        @yield(section: 'content')
    </div>
</main>

{{-- Bootstrap JS --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

{{-- SweetAlert2 JS --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>

@vite('resources/js/app.js')

@stack('scripts')

@if(session('success'))
<script>
    Swal.fire({
        title: 'Thành công!',
        text: "{{ session('success') }}",
        icon: 'success',
        timer: 3000,
        timerProgressBar: true,
        showConfirmButton: false,
        position: 'top-end',
        toast: true
    });
</script>
@endif

@if(session('error'))
<script>
    Swal.fire({
        title: 'Lỗi!',
        text: "{{ session('error') }}",
        icon: 'error',
        timer: 5000,
        timerProgressBar: true,
        showConfirmButton: false,
        position: 'top-end',
        toast: true
    });
</script>
@endif
</body>
</html>
