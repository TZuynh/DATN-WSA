<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng nhập Admin</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss/dist/tailwind.min.css">
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">
<form method="POST" action="{{ route('admin.login.submit') }}" class="bg-white p-6 rounded shadow-md w-96">
    @csrf
    <h2 class="text-xl font-bold mb-4 text-center">Đăng nhập Admin</h2>

    @if ($errors->any())
        <div class="mb-3 text-red-500 text-sm">
            {{ $errors->first() }}
        </div>
    @endif

    <div class="mb-4">
        <label class="block text-gray-700">Email</label>
        <input type="email" name="email" class="w-full px-3 py-2 border rounded" required autofocus>
    </div>

    <div class="mb-4">
        <label class="block text-gray-700">Mật khẩu</label>
        <input type="password" name="mat_khau" class="w-full px-3 py-2 border rounded" required>
    </div>

    <div>
        <button type="submit" class="w-full bg-blue-500 text-white py-2 rounded hover:bg-blue-600">Đăng nhập</button>
    </div>
</form>
</body>
</html>
