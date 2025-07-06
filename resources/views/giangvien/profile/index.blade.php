@extends('components.giangvien.app')
@section('title', 'Thông tin giảng viên')

@vite('resources/scss/profile.scss')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<div class="profile-container">
    <div class="profile-card">
        <!-- Header với pattern -->
        <div class="header-pattern" style="position: relative; height: 180px; background: linear-gradient(to right, #4f46e5, #3b82f6);">
            <div class="avatar-container" style="position: absolute; left: 32px; bottom: -80px;">
                <div class="avatar" style="width: 160px; height: 160px; border-radius: 50%; border: 4px solid #fff; background: #fff; box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1); display: flex; align-items: center; justify-content: center; font-size: 64px; font-weight: bold; color: #4f46e5;">
                    {{ substr($user->ten, 0, 1) }}
                </div>
            </div>
        </div>
        <div style="padding: 120px 32px 32px 32px;">
            <div style="display: flex; justify-content: space-between; align-items: flex-end; flex-wrap: wrap; gap: 16px;">
                <div>
                    <h1 style="font-size: 2.5rem; font-weight: bold; margin-bottom: 0.25rem;">{{ $user->ten }}</h1>
                    <p style="color: #6b7280; font-size: 1.1rem; margin-bottom: 0;">{{ $user->email }}</p>
                </div>
                <span style="padding: 8px 24px; border-radius: 9999px; background: linear-gradient(to right, #e0e7ff, #bae6fd); color: #3730a3; font-weight: 600; font-size: 1rem;">{{ $user->vai_tro === 'giang_vien' ? 'Giảng Viên' : 'Admin' }}</span>
            </div>
            <div style="margin-top: 48px; display: grid; grid-template-columns: 1fr 1fr; gap: 32px;">
                <div class="info-card">
                    <div style="display: flex; align-items: center; gap: 16px;">
                        <div class="info-icon" style="padding: 16px; background: linear-gradient(to bottom right, #e0e7ff, #bae6fd); border-radius: 16px;">
                            <svg width="32" height="32" fill="none" stroke="#4f46e5" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </div>
                        <div>
                            <div style="color: #6b7280; font-size: 0.95rem; text-transform: uppercase; letter-spacing: 1px;">Vai trò</div>
                            <div style="font-size: 1.3rem; font-weight: 600; color: #22223b; margin-top: 0.5rem;">{{ $user->vai_tro === 'giang_vien' ? 'Giảng Viên' : 'Admin' }}</div>
                        </div>
                    </div>
                </div>
                <div class="info-card">
                    <div style="display: flex; align-items: center; gap: 16px;">
                        <div class="info-icon" style="padding: 16px; background: linear-gradient(to bottom right, #e0e7ff, #bae6fd); border-radius: 16px;">
                            <svg width="32" height="32" fill="none" stroke="#4f46e5" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        <div>
                            <div style="color: #6b7280; font-size: 0.95rem; text-transform: uppercase; letter-spacing: 1px;">Ngày tạo tài khoản</div>
                            <div style="font-size: 1.3rem; font-weight: 600; color: #22223b; margin-top: 0.5rem;">{{ $user->created_at->format('d/m/Y') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Form đổi mật khẩu -->
    <div class="profile-card" style="margin-top: 32px; background: #fff; border-radius: 16px; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1); overflow: hidden;">
        <div style="padding: 32px;">
            <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 32px;">
                <div style="padding: 16px; background: linear-gradient(to bottom right, #fef3c7, #fde68a); border-radius: 16px;">
                    <svg width="32" height="32" fill="none" stroke="#d97706" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                    </svg>
                </div>
                <div>
                    <h2 style="font-size: 1.5rem; font-weight: 600; color: #22223b; margin: 0;">Đổi mật khẩu</h2>
                    <p style="color: #6b7280; margin: 0.25rem 0 0 0;">Cập nhật mật khẩu tài khoản của bạn</p>
                </div>
            </div>

            <form action="{{ route('giangvien.profile.change-password') }}" method="POST" style="display: grid; gap: 24px;">
                @csrf
                @method('PUT')
                
                <div style="display: grid; gap: 8px;">
                    <label for="current_password" style="font-weight: 600; color: #374151; font-size: 0.95rem;">Mật khẩu hiện tại</label>
                    <div style="position: relative;">
                        <input type="password" 
                               id="current_password" 
                               name="current_password" 
                               required
                               style="padding: 12px 16px; padding-right: 48px; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 1rem; transition: border-color 0.3s ease; outline: none; width: 100%; box-sizing: border-box;"
                               placeholder="Nhập mật khẩu hiện tại">
                        <button type="button" 
                                class="password-toggle" 
                                data-target="current_password"
                                style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; padding: 4px; color: #6b7280; transition: color 0.3s ease;">
                            <i class="fas fa-eye-slash" style="font-size: 16px;"></i>
                        </button>
                    </div>
                    @error('current_password')
                        <span style="color: #ef4444; font-size: 0.875rem;">{{ $message }}</span>
                    @enderror
                </div>

                <div style="display: grid; gap: 8px;">
                    <label for="new_password" style="font-weight: 600; color: #374151; font-size: 0.95rem;">Mật khẩu mới</label>
                    <div style="position: relative;">
                        <input type="password" 
                               id="new_password" 
                               name="new_password" 
                               required
                               style="padding: 12px 16px; padding-right: 48px; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 1rem; transition: border-color 0.3s ease; outline: none; width: 100%; box-sizing: border-box;"
                               placeholder="Nhập mật khẩu mới (tối thiểu 8 ký tự)">
                        <button type="button" 
                                class="password-toggle" 
                                data-target="new_password"
                                style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; padding: 4px; color: #6b7280; transition: color 0.3s ease;">
                            <i class="fas fa-eye-slash" style="font-size: 16px;"></i>
                        </button>
                    </div>
                    @error('new_password')
                        <span style="color: #ef4444; font-size: 0.875rem;">{{ $message }}</span>
                    @enderror
                </div>

                <div style="display: grid; gap: 8px;">
                    <label for="new_password_confirmation" style="font-weight: 600; color: #374151; font-size: 0.95rem;">Xác nhận mật khẩu mới</label>
                    <div style="position: relative;">
                        <input type="password" 
                               id="new_password_confirmation" 
                               name="new_password_confirmation" 
                               required
                               style="padding: 12px 16px; padding-right: 48px; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 1rem; transition: border-color 0.3s ease; outline: none; width: 100%; box-sizing: border-box;"
                               placeholder="Nhập lại mật khẩu mới">
                        <button type="button" 
                                class="password-toggle" 
                                data-target="new_password_confirmation"
                                style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; padding: 4px; color: #6b7280; transition: color 0.3s ease;">
                            <i class="fas fa-eye-slash" style="font-size: 16px;"></i>
                        </button>
                    </div>
                    @error('new_password_confirmation')
                        <span style="color: #ef4444; font-size: 0.875rem;">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit" 
                        style="padding: 12px 24px; background: linear-gradient(to right, #4f46e5, #3b82f6); color: white; border: none; border-radius: 8px; font-weight: 600; font-size: 1rem; cursor: pointer; transition: all 0.3s ease; display: inline-flex; align-items: center; gap: 8px; justify-content: center; max-width: 200px;">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                    Cập nhật mật khẩu
                </button>
            </form>
        </div>
    </div>
</div>

<style>
input:focus {
    border-color: #4f46e5 !important;
    box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
}

button:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1);
}

.password-toggle:hover {
    color: #4f46e5 !important;
    transform: translateY(-50%) scale(1.1);
}

.alert {
    animation: slideIn 0.3s ease;
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Xử lý toggle password visibility
    const passwordToggles = document.querySelectorAll('.password-toggle');
    
    passwordToggles.forEach(toggle => {
        toggle.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            const passwordInput = document.getElementById(targetId);
            const icon = this.querySelector('i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
                this.style.color = '#4f46e5';
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
                this.style.color = '#6b7280';
            }
        });
    });
});
</script>
@endsection 