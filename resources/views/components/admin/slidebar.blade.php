<aside class="admin-sidebar">
    <div class="sidebar-header">
        <img src="{{ asset('images/logo-caothang.png') }}" alt="Logo" class="logo-img" />
        <h5>Quản Trị Viên</h5>
    </div>
    <ul>
        <li class="menu-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <a href="{{ route('admin.dashboard') }}"><i class="fas fa-chart-line"></i> Thống kê</a>
        </li>
        <li class="menu-item has-submenu {{ request()->routeIs('admin.taikhoan.*') || request()->routeIs('admin.phan-cong-hoi-dong.*') ? 'active' : '' }}">
            <a href="javascript:void(0)">
                <i class="fas fa-users"></i>
                <span>Người dùng</span>
                <i class="fas fa-chevron-down arrow"></i>
            </a>
            <ul class="submenu">
                <li class="menu-item {{ request()->routeIs('admin.taikhoan.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.taikhoan.index') }}"><i class="fas fa-user"></i> Tài khoản</a>
                </li>
                <li class="menu-item {{ request()->routeIs('admin.phan-cong-hoi-dong.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.phan-cong-hoi-dong.index') }}"><i class="fas fa-user-plus"></i> Phân công</a>
                </li>
            </ul>
        </li>
        <li class="menu-item has-submenu {{ request()->routeIs('admin.phan-bien.*') || request()->routeIs('admin.phan-cong-cham.*') ? 'active' : '' }}">
            <a href="javascript:void(0)">
                <i class="fas fa-tasks"></i>
                <span>Phản biện</span>
                <i class="fas fa-chevron-down arrow"></i>
            </a>
            <ul class="submenu">
                <li class="menu-item {{ request()->routeIs('admin.phan-cong-cham.phan-bien') ? 'active' : '' }}">
                    <a href="{{ route('admin.phan-cong-cham.phan-bien') }}"><i class="fas fa-user-check"></i>Phân công giảng viên phản biện</a>
                </li>
                <li class="menu-item {{ request()->routeIs('admin.phan-cong-cham.*') && !request()->routeIs('admin.phan-cong-cham.phan-bien') ? 'active' : '' }}">
                    <a href="{{ route('admin.phan-cong-cham.index') }}"><i class="fas fa-tasks"></i>Quản lý phản biện</a>
                </li>
            </ul>
        </li>
        <li class="menu-item has-submenu {{ request()->routeIs('admin.hoi-dong.*') || request()->routeIs('admin.dot-bao-cao.*') || request()->routeIs('admin.lich-cham.*') ? 'active' : '' }}">
            <a href="javascript:void(0)">
                <i class="fas fa-calendar-alt"></i>
                <span>Hội đồng</span>
                <i class="fas fa-chevron-down arrow"></i>
            </a>
            <ul class="submenu">
                <li class="menu-item {{ request()->routeIs('admin.phong.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.phong.index') }}">
                        <i class="fas fa-door-open"></i>
                        <span>Phòng</span>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('admin.dot-bao-cao.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.dot-bao-cao.index') }}"><i class="fas fa-calendar"></i> Đợt báo cáo</a>
                </li>
                <li class="menu-item {{ request()->routeIs('admin.hoi-dong.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.hoi-dong.index') }}"><i class="fas fa-users"></i> Hội đồng</a>
                </li>
                <li class="menu-item {{ request()->routeIs('admin.lich-cham.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.lich-cham.index') }}"><i class="fas fa-clock"></i> Lịch bảo vệ</a>
                </li>
            </ul>
        </li>
        <li class="menu-item has-submenu {{ request()->routeIs('admin.sinh-vien.*') || request()->routeIs('admin.nhom.*') || request()->routeIs('admin.lop.*') || request()->routeIs('admin.de-tai.*') ? 'active' : '' }}">
            <a href="javascript:void(0)">
                <i class="fas fa-graduation-cap"></i>
                <span>Học tập</span>
                <i class="fas fa-chevron-down arrow"></i>
            </a>
            <ul class="submenu">
                <li class="menu-item {{ request()->routeIs('admin.lop.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.lop.index') }}"><i class="fas fa-chalkboard"></i> Lớp</a>
                </li>
                <li class="menu-item {{ request()->routeIs('admin.sinh-vien.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.sinh-vien.index') }}"><i class="fas fa-user-graduate"></i> Sinh viên</a>
                </li>
                <li class="menu-item {{ request()->routeIs('admin.nhom.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.nhom.index') }}"><i class="fas fa-users-cog"></i> Nhóm</a>
                </li>
                <li class="menu-item {{ request()->routeIs('admin.de-tai.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.de-tai.index') }}"><i class="fas fa-book"></i> Đề tài</a>
                </li>
            </ul>
        </li>
        <li class="menu-item {{ request()->routeIs('admin.bang-diem.*') ? 'active' : '' }}">
            <a href="{{ route('admin.bang-diem.index') }}">
                <i class="fas fa-star"></i>
                <span>Bảng điểm</span>
            </a>
        </li>
        <li class="menu-item has-submenu {{ request()->routeIs('admin.cai-dat.*') || request()->routeIs('admin.api-doc.*') ? 'active' : '' }}">
            <a href="javascript:void(0)">
                <i class="fas fa-cog"></i>
                <span>Cài đặt</span>
                <i class="fas fa-chevron-down arrow"></i>
            </a>
            <ul class="submenu">
                <li class="menu-item {{ request()->routeIs('admin.cai-dat.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.cai-dat.index') }}"><i class="fas fa-cog"></i> Hệ thống</a>
                </li>
                <li class="menu-item {{ request()->routeIs('admin.api-doc.*') ? 'active' : '' }}">
                    <a href="{{ route('api-doc.index') }}"><i class="fas fa-book"></i> API</a>
                </li>
            </ul>
        </li>
    </ul>
</aside>

<style>
.admin-sidebar {
    position: fixed;
    top: 60px;
    left: 0;
    width: 220px;
    height: calc(100vh - 60px);
    background: #1a202c;
    overflow-y: auto;
    z-index: 999;
    font-size: 0.9rem;
}

.sidebar-header {
    padding: 10px 0;
    text-align: center;
    border-bottom: 1px solid #333;
}

.logo-img {
    width: 60px;
    height: auto;
    object-fit: contain;
    margin-bottom: 5px;
}

.admin-sidebar ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.menu-item a {
    padding: 8px 15px;
    font-size: 0.9rem;
    display: flex;
    align-items: center;
    color: #e5e7eb;
    text-decoration: none;
    transition: all 0.3s;
}

.menu-item a:hover {
    background-color: #2d2d2d;
}

.menu-item a i {
    width: 18px;
    margin-right: 8px;
    font-size: 14px;
}

.menu-item.has-submenu > a {
    justify-content: space-between;
}

.menu-item.has-submenu > a .arrow {
    transition: transform 0.3s;
    color: #9ca3af;
}

.menu-item.has-submenu.active > a .arrow {
    transform: rotate(180deg);
}

.submenu {
    display: none;
    background-color: #242424;
}

.menu-item.has-submenu.active .submenu {
    display: block;
}

.submenu .menu-item a {
    padding-left: 40px;
    font-size: 0.85rem;
}

.menu-item.active > a {
    background-color: #4299e1;
    color: #fff;
}

.menu-item.active > a i {
    color: #fff;
}

.submenu .menu-item.active > a {
    background-color: #4299e1;
    color: #fff;
}

.submenu .menu-item.active > a i {
    color: #fff;
}

.admin-sidebar::-webkit-scrollbar {
    width: 4px;
}

.admin-sidebar::-webkit-scrollbar-track {
    background: #1a202c;
}

.admin-sidebar::-webkit-scrollbar-thumb {
    background: #4a5568;
    border-radius: 2px;
}

.admin-sidebar::-webkit-scrollbar-thumb:hover {
    background: #718096;
}

.main-content {
    margin-left: 220px;
    margin-top: 60px;
    padding: 20px;
    min-height: calc(100vh - 60px);
    background-color: #f9fafb;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const submenuToggles = document.querySelectorAll('.menu-item.has-submenu > a');

    submenuToggles.forEach(toggle => {
        toggle.addEventListener('click', function(e) {
            e.preventDefault();
            const menuItem = this.parentElement;
            menuItem.classList.toggle('active');
        });
    });
});
</script>
