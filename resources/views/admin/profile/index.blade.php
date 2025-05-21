@extends('admin.layout')

@section('title', 'Thông tin cá nhân')

@vite('resources/scss/profile.scss')

@section('content')
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
                <span style="padding: 8px 24px; border-radius: 9999px; background: linear-gradient(to right, #e0e7ff, #bae6fd); color: #3730a3; font-weight: 600; font-size: 1rem;">{{ ucfirst($user->vai_tro) }}</span>
            </div>
            <div style="margin-top: 48px; display: grid; grid-template-columns: 1fr 1fr; gap: 32px;">
                <div class="info-card">
                    <div style="display: flex; align-items: center; gap: 16px;">
                        <div class="info-icon" style="padding: 16px; background: linear-gradient(to bottom right, #e0e7ff, #bae6fd); border-radius: 16px;">
                            <svg width="32" height="32" fill="none" stroke="#4f46e5" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </div>
                        <div>
                            <div style="color: #6b7280; font-size: 0.95rem; text-transform: uppercase; letter-spacing: 1px;">Vai trò</div>
                            <div style="font-size: 1.3rem; font-weight: 600; color: #22223b; margin-top: 0.5rem;">{{ ucfirst($user->vai_tro) }}</div>
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
            <div style="margin-top: 48px; display: flex; justify-content: flex-end;">
                <button class="edit-button" style="padding: 16px 32px; background: linear-gradient(to right, #4f46e5, #3b82f6); color: #fff; font-weight: 600; border-radius: 16px; font-size: 1.1rem; display: flex; align-items: center; gap: 10px; border: none; cursor: pointer;">
                    <svg class="button-icon" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    Chỉnh sửa thông tin
                </button>
            </div>
        </div>
    </div>
</div>
@endsection 