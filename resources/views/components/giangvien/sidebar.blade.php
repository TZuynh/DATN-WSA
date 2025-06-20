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
        <li class="menu-item {{ request()->routeIs('giangvien.lop.*') ? 'active' : '' }}">
            <a href="{{ route('giangvien.lop.index') }}">
                <i class="fas fa-chalkboard"></i>
                <span>Danh sách lớp</span>
            </a>
        </li>
        <li class="menu-item {{ request()->routeIs('giangvien.sinh-vien.*') ? 'active' : '' }}">
            <a href="{{ route('giangvien.sinh-vien.index') }}">
                <i class="fas fa-users"></i>
                <span>Danh sách sinh viên</span>
            </a>
        </li>
        {{-- <li class="menu-item {{ request()->routeIs('giangvien.dang-ky.*') ? 'active' : '' }}">
            <a href="{{ route('giangvien.dang-ky.index') }}">
                <i class="fas fa-user-plus"></i>
                <span>Quản lý đăng ký</span>
            </a>
        </li> --}}
        <li class="menu-item {{ request()->routeIs('giangvien.nhom.*') ? 'active' : '' }}">
            <a href="{{ route('giangvien.nhom.index') }}">
                <i class="fas fa-user-plus"></i>
                <span>Quản lý nhóm</span>
            </a>
        </li>
        {{-- <li class="menu-item {{ request()->routeIs('giangvien.de-tai-mau.*') ? 'active' : '' }}">
            <a href="{{ route('giangvien.de-tai-mau.index') }}">
                <i class="fas fa-copy"></i>
                <span>Quản lý mẫu đề tài</span>
            </a>
        </li> --}}
        <li class="menu-item {{ request()->routeIs('giangvien.de-tai.*') ? 'active' : '' }}">
            <a href="{{ route('giangvien.de-tai.index') }}">
                <i class="fas fa-tasks"></i>
                <span>Quản lý đề tài</span>
            </a>
        </li>
        <li class="menu-item {{ request()->routeIs('giangvien.bang-diem.*') ? 'active' : '' }}">
            <a href="{{ route('giangvien.bang-diem.index') }}">
                <i class="fas fa-star"></i>
                <span>Chấm điểm</span>
            </a>
        </li>
    </ul>
</div>