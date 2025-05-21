@extends('admin.layout')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<style>
    .theme-option {
        flex: 1;
        padding: 20px;
        border: 2px solid #e9ecef;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s ease;
        position: relative;
    }
    .theme-option:hover {
        border-color: #0d6efd;
    }
    .theme-option input[type="radio"] {
        position: absolute;
        opacity: 0;
        width: 100%;
        height: 100%;
        top: 0;
        left: 0;
        margin: 0;
        cursor: pointer;
    }
    .theme-option input[type="radio"]:checked + label {
        color: #0d6efd;
    }
    .theme-option input[type="radio"]:checked + label .theme-icon {
        color: #0d6efd;
    }
    .theme-option input[type="radio"]:checked ~ .theme-option-content {
        border-color: #0d6efd;
    }
    .theme-icon {
        font-size: 2rem;
        margin-bottom: 10px;
    }
    .theme-label {
        font-size: 1.1rem;
        font-weight: 500;
    }
    .theme-option-content {
        display: flex;
        flex-direction: column;
        align-items: center;
        transition: all 0.3s ease;
    }
    .form-control:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
    }
    .form-check-input:checked {
        background-color: #0d6efd;
        border-color: #0d6efd;
    }
    .form-check-input:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
    }
    .security-input {
        font-size: 1.1rem;
        padding: 0.6rem 1rem;
    }
    .security-label {
        font-size: 1.1rem;
        margin-bottom: 0.5rem;
    }
    .form-switch .form-check-input {
        width: 3em;
        height: 1.5em;
    }
    .form-switch .form-check-input:checked {
        background-color: #0d6efd;
        border-color: #0d6efd;
    }
</style>
<div class="container-fluid px-4 settings-page">
    <h1 class="h3 mb-4 text-gray-800">Cài đặt hệ thống</h1>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
        </div>
    @endif

    <div class="row g-4">
        <!-- Cài đặt chung -->
        <div class="col-xl-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-cog text-primary me-3" style="font-size: 1.8rem;"></i>
                        <h2 class="m-0 font-weight-bold" style="font-size: 1.5rem;">Cài đặt chung</h2>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.cai-dat.update-general') }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label class="form-label fw-bold mb-3">Màu sắc chủ đạo</label>
                            <div class="d-flex gap-3">
                                <div class="theme-option">
                                    <input type="radio" name="theme" id="themeLight" value="light" {{ ( $settings['theme'] ?? 'light') == 'light' ? 'checked' : '' }}>
                                    <label for="themeLight" class="d-flex flex-column align-items-center">
                                        <i class="fas fa-sun theme-icon"></i>
                                        <span class="theme-label">Sáng</span>
                                    </label>
                                </div>
                                <div class="theme-option">
                                    <input type="radio" name="theme" id="themeDark" value="dark" {{ ( $settings['theme'] ?? 'light') == 'dark' ? 'checked' : '' }}>
                                    <label for="themeDark" class="d-flex flex-column align-items-center">
                                        <i class="fas fa-moon theme-icon"></i>
                                        <span class="theme-label">Tối</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Lưu thay đổi
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Cài đặt bảo mật -->
        <div class="col-xl-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-shield-alt text-primary me-2" style="font-size: 1.8rem;"></i>
                        <h2 class="m-0 font-weight-bold">Cài đặt bảo mật</h2>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.cai-dat.update-security') }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label class="form-label fw-bold security-label">Thời gian timeout (phút)</label>
                            <input type="number" class="form-control security-input" name="thoi_gian_timeout" value="{{ $settings['thoi_gian_timeout'] ?? '30' }}" min="1" max="120">
                            <small class="text-muted">Thời gian tự động đăng xuất khi không hoạt động (1-120 phút)</small>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold security-label">Số lần đăng nhập sai tối đa</label>
                            <input type="number" class="form-control security-input" name="so_lan_dang_nhap_sai_toi_da" value="{{ $settings['so_lan_dang_nhap_sai_toi_da'] ?? '5' }}" min="1" max="10">
                            <small class="text-muted">Số lần đăng nhập sai tối đa trước khi tài khoản bị khóa (1-10 lần)</small>
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-save me-2"></i>Lưu thay đổi
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 