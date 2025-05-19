<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Admin - @yield('title', 'Dashboard')</title>
    {{-- Chỉ dùng 1 cách gọi CSS --}}
    @vite('resources/scss/admin-style.scss')
</head>
<body>
{{-- Gọi component navbar --}}
<x-admin.navbar />

{{-- Gọi component sidebar --}}
<x-admin.slidebar />

<main class="admin-main">
    @yield('content')
</main>
</body>
</html>
