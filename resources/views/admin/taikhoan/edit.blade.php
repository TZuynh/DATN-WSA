@extends('admin.layout')

@section('title', 'Chỉnh sửa tài khoản')

@section('content')    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
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
    <div style="max-width: 800px; margin: 0 auto;">
        <h1 style="margin-bottom: 20px; color: #2d3748; font-weight: 700;">Chỉnh sửa tài khoản</h1>

        @if($errors->any())
            <div style="background-color: #f56565; color: white; padding: 10px; border-radius: 4px; margin-bottom: 20px;">
                <ul style="margin: 0; padding-left: 20px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.taikhoan.update', $taikhoan->id) }}" method="POST" style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 8px rgb(0 0 0 / 0.1);">
            @csrf
            @method('PUT')
            
            <div style="margin-bottom: 20px;">
                <label for="ten" style="display: block; margin-bottom: 5px; color: #4a5568;">Họ tên <span style="color: #e53e3e;">*</span></label>
                <input type="text" name="ten" id="ten" value="{{ old('ten', $taikhoan->ten) }}" required
                    style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;" placeholder="Nhập họ tên">
            </div>

            <div style="margin-bottom: 20px;">
                <label for="email" style="display: block; margin-bottom: 5px; color: #4a5568;">Email <span style="color: #e53e3e;">*</span></label>
                <input type="email" name="email" id="email" value="{{ old('email', $taikhoan->email) }}" required
                    style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;" placeholder="Nhập email">
            </div>

            <div style="margin-bottom: 20px;">
                <label for="mat_khau" style="display: block; margin-bottom: 5px; color: #4a5568;">Mật khẩu mới (để trống nếu không muốn thay đổi)</label>
                <div class="password-container">
                    <input type="password" name="mat_khau" id="mat_khau"
                        style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;" placeholder="Nhập mật khẩu mới (để trống nếu không đổi)">
                    <span class="toggle-password" onclick="togglePassword()">
                        <i class="fas fa-eye"></i>
                    </span>
                </div>
            </div>

            <div style="margin-bottom: 20px;">
                <label for="vai_tro" style="display: block; margin-bottom: 5px; color: #4a5568;">Vai trò <span style="color: #e53e3e;">*</span></label>
                @if($taikhoan->id == 1)
                    <select name="vai_tro" id="vai_tro" disabled
                        style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;" placeholder="Chọn vai trò">
                        <option value="admin" selected>Admin</option>
                    </select>
                    <input type="hidden" name="vai_tro" value="admin">
                @else
                    <select name="vai_tro" id="vai_tro" required
                        style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;" placeholder="Chọn vai trò">
                        <option value="admin" {{ old('vai_tro', $taikhoan->vai_tro) == 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="giang_vien" {{ old('vai_tro', $taikhoan->vai_tro) == 'giang_vien' ? 'selected' : '' }}>Giảng viên</option>
                    </select>
                @endif
            </div>

            <div style="display: flex; gap: 10px;">
                <button type="submit" style="padding: 10px 20px; background-color: #4299e1; color: white; border: none; border-radius: 4px; cursor: pointer;">
                    Cập nhật tài khoản
                </button>
                <a href="{{ route('admin.taikhoan.index') }}" style="padding: 10px 20px; background-color: #a0aec0; color: white; border: none; border-radius: 4px; text-decoration: none;">
                    Hủy
                </a>
            </div>
        </form>
    </div>

    <script>
    function togglePassword() {
        const passwordInput = document.getElementById('mat_khau');
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
@endsection 