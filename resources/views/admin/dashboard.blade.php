<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Admin Dashboard</title>
    @vite('resources/scss/admin-style.scss')
</head>
<body>
<header>
    <h1>Chào mừng Admin!</h1>
    <form action="{{ route('admin.logout') }}" method="POST">
        @csrf
        <button type="submit">Đăng xuất</button>
    </form>
</header>
</body>
</html>
