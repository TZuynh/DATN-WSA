<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('images/logo.jpg') }}" />

    <title>@yield('title', config('app.name', 'Laravel'))</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- SweetAlert2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <!-- Scripts -->
    @vite(['resources/scss/app.scss', 'resources/js/app.js'])
    <!-- Select multiple -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <style>
        /* Ẩn thanh cuộn cho toàn bộ trang */
        ::-webkit-scrollbar {
            display: none;
        }
        
        /* Cho Firefox */
        * {
            scrollbar-width: none;
        }
        
        body {
            margin: 0;
            padding: 0;
            min-height: 100vh;
            background: #f8f9fa;
        }
        
        .content-wrapper {
            margin-left: 250px;
            padding: 20px;
            padding-top: 70px;
            min-height: 100vh;
            transition: all 0.3s;
        }
        
        @media (max-width: 768px) {
            .content-wrapper {
                margin-left: 0;
                padding-top: 70px;
            }
        }
    </style>
</head>
<body>
    @include('components.giangvien.sidebar')
    
    <div class="content-wrapper">
        @include('components.admin.navbar')
        
        <main>
            {{-- Thông báo success/error --}}
            @yield('content')
        </main>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>

    <!-- Flatpickr JS và CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/vi.js"></script>

    <script>
        // Toggle sidebar on mobile
        document.querySelector('.sidebar-toggle-btn').addEventListener('click', function() {
            document.querySelector('.admin-sidebar').classList.toggle('active');
        });
    </script>
    <script>
        //Select multiple
        $(document).ready(function() {
            $('#sinh_vien_ids').select2({
                placeholder: 'Chọn sinh viên',
                allowClear: true
            });
        });
    </script>

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
    
    {{-- ĐÂY LÀ NƠI NHẬN @push('scripts') TỪ VIEW CON --}}
    @stack('scripts')
</body>
</html> 