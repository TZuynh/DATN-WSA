<div class="admin-sidebar">
    <div class="sidebar-header">
        <img src="{{ asset('images/logo-caothang.png') }}" alt="Logo" class="logo-img" />
        <h5>Giảng Viên</h5>
    </div>

    <ul class="sidebar-menu">
        <li class="menu-item {{ request()->routeIs('giangvien.dashboard') ? 'active' : '' }}">
            <a href="{{ route('giangvien.dashboard') }}">
                <i class="fas fa-tachometer-alt"></i>
                <span>Thống kê</span>
            </a>
        </li>
        <li class="menu-item {{ request()->routeIs('giangvien.sinh-vien.*') ? 'active' : '' }}">
            <a href="{{ route('giangvien.sinh-vien.index') }}">
                <i class="fas fa-users"></i>
                <span>Sinh viên</span>
            </a>
        </li>
        <li class="menu-item {{ request()->routeIs('giangvien.dang-ky.*') ? 'active' : '' }}">
            <a href="{{ route('giangvien.dang-ky.index') }}">
                <i class="fas fa-user-plus"></i>
                <span>Quản lý đăng ký</span>
            </a>
        </li>
        <li class="menu-item {{ request()->routeIs('giangvien.nhom.*') ? 'active' : '' }}">
            <a href="{{ route('giangvien.nhom.index') }}">
                <i class="fas fa-user-plus"></i>
                <span>Quản lý nhóm</span>
            </a>
        </li>
{{--        <li class="menu-item">--}}
{{--            <a href="#">--}}
{{--                <i class="fas fa-book"></i>--}}
{{--                <span>Lớp Học</span>--}}
{{--            </a>--}}
{{--        </li>--}}
{{--        <li class="menu-item">--}}
{{--            <a href="#">--}}
{{--                <i class="fas fa-tasks"></i>--}}
{{--                <span>Bài Tập</span>--}}
{{--            </a>--}}
{{--        </li>--}}
{{--        <li class="menu-item">--}}
{{--            <a href="#">--}}
{{--                <i class="fas fa-chart-bar"></i>--}}
{{--                <span>Báo Cáo</span>--}}
{{--            </a>--}}
{{--        </li>--}}
{{--        <li class="menu-item">--}}
{{--            <a href="#">--}}
{{--                <i class="fas fa-cog"></i>--}}
{{--                <span>Cài Đặt</span>--}}
{{--            </a>--}}
{{--        </li>--}}
    </ul>
</div>
