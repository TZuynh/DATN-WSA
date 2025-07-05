@php
    use App\Models\PhanCongVaiTro;
    $isPhanBien = PhanCongVaiTro::where('tai_khoan_id', auth()->id() ?? 0)
        ->where('loai_giang_vien', 'Giảng Viên Phản Biện')
        ->exists();
    // Kiểm tra là thư ký
    $isThuKy = PhanCongVaiTro::where('tai_khoan_id', auth()->id() ?? 0)
        ->whereHas('vaiTro', function($q) { $q->where('ten', 'Thư ký'); })
        ->exists();
    // Kiểm tra là hướng dẫn
    $isHuongDan = PhanCongVaiTro::where('tai_khoan_id', auth()->id() ?? 0)
        ->where('loai_giang_vien', 'Giảng Viên Hướng Dẫn')
        ->exists();
    \Log::info('[Sidebar] isPhanBien: ' . ($isPhanBien ? 'true' : 'false') . ', isThuKy: ' . ($isThuKy ? 'true' : 'false') . ', isHuongDan: ' . ($isHuongDan ? 'true' : 'false') . ', user_id: ' . (auth()->id() ?? 0));
@endphp

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
        @if(!$isPhanBien)
        <li class="menu-item {{ request()->routeIs('giangvien.nhom.*') ? 'active' : '' }}">
            <a href="{{ route('giangvien.nhom.index') }}">
                <i class="fas fa-user-plus"></i>
                <span>Quản lý nhóm</span>
            </a>
        </li>
        @endif
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
        @if($isThuKy)
        <li class="menu-item {{ request()->routeIs('giangvien.bien-ban-nhan-xet.*') ? 'active' : '' }}">
            <a href="{{ route('giangvien.bien-ban-nhan-xet.select-detai') }}">
                <i class="fas fa-file-alt"></i>
                <span>Biên bản nhận xét</span>
            </a>
        </li>
        @endif
        @if($isHuongDan)
        <li class="menu-item {{ request()->routeIs('giangvien.bao-cao-qua-trinh.*') ? 'active' : '' }}">
            <a href="{{ route('giangvien.bao-cao-qua-trinh.index') }}">
                <i class="fas fa-file-alt"></i>
                <span>Báo cáo quá trình</span>
            </a>
        </li>
        @endif
    </ul>
</div>