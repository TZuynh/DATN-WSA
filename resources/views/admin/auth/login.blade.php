<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng nhập</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss/dist/tailwind.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .tooltip {
            position: relative;
            display: inline-block;
        }
        .tooltip .tooltip-text {
            visibility: hidden;
            width: max-content;
            background-color: #333;
            color: #fff;
            text-align: center;
            border-radius: 6px;
            padding: 10px 16px;
            position: absolute;
            z-index: 10;
            bottom: 125%;
            left: 50%;
            transform: translateX(-50%);
            font-size: 1.15rem;
            font-weight: 500;
            opacity: 0;
            transition: opacity 0.2s;
            white-space: nowrap;
        }
        .tooltip:hover .tooltip-text {
            visibility: visible;
            opacity: 1;
        }
        .password-container {
            position: relative;
        }
        .toggle-password {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #666;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-400 via-purple-400 to-pink-300 flex items-center justify-center h-screen">
<form method="POST" action="{{ route('admin.login.submit') }}" class="bg-white p-6 rounded shadow-md w-96">
    @csrf
    <div class="mb-4">
        <label class="block text-gray-700 mb-1 flex items-center">Tên đăng nhập:
            <span class="ml-1 text-green-600 tooltip">
                <i class="fas fa-question-circle"></i>
                <span class="tooltip-text">Nhập email đăng nhập của bạn</span>
            </span>
        </label>
        <input type="text" name="email" class="w-full px-3 py-2 border rounded" placeholder="Email" required autofocus>
    </div>
    <div class="mb-4">
        <label class="block text-gray-700 mb-1 flex items-center">Mật Khẩu:
            <span class="ml-1 text-green-600 tooltip">
                <i class="fas fa-question-circle"></i>
                <span class="tooltip-text">Nhập mật khẩu được cấp</span>
            </span>
        </label>
        <div class="password-container">
            <input type="password" name="mat_khau" id="password" class="w-full px-3 py-2 border rounded" placeholder="Nhập mật khẩu" required>
            <span class="toggle-password" onclick="togglePassword()">
                <i class="fas fa-eye"></i>
            </span>
        </div>
    </div>
    <div class="mb-4">
        <button type="submit" class="w-full bg-blue-500 text-white py-2 rounded hover:bg-blue-600 flex items-center justify-center">
            <i class="fas fa-sign-in-alt mr-2"></i> Đăng nhập
        </button>
    </div>
    <div class="text-center text-sm mt-2">
        <span>Quên mật khẩu? Vui lòng liên hệ <span class="text-blue-500 italic" style="text-decoration: underline;">Admin</span></span>
    </div>
</form>

<script>
function togglePassword() {
    const passwordInput = document.getElementById('password');
    const toggleIcon = document.querySelector('.toggle-password i');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleIcon.classList.remove('fa-eye');
        toggleIcon.classList.add('fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        toggleIcon.classList.remove('fa-eye-slash');
        toggleIcon.classList.add('fa-eye');
    }
}
</script>
</body>
</html>
