@extends('admin.layout')

@section('title', 'Dashboard - Thống kê')

@vite('resources/scss/dashboard.scss')

@section('content')
    <h2>Thống kê hệ thống</h2>

    <div class="stats-container">
        <div class="stat-card">
            <h3>Tổng người dùng</h3>
            <p class="stat-number">123</p>
        </div>
        <div class="stat-card">
            <h3>Tổng bài viết</h3>
            <p class="stat-number">456</p>
        </div>
        <div class="stat-card">
            <h3>Người dùng Admin</h3>
            <p class="stat-number">789</p>
        </div>
        <div class="stat-card">
            <h3>Người dùng Giảng viên</h3>
            <p class="stat-number">101112</p>
        </div>
    </div>
@endsection
