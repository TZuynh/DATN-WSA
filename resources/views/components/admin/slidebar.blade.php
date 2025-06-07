<aside class="admin-sidebar">
    <div class="sidebar-header" style="display: flex; flex-direction: column; align-items: center; padding: 15px 0;">
        <img src="{{ asset('images/logo-caothang.png') }}" alt="Logo" class="logo-img" style="width: 80px; height: auto; object-fit: contain; margin-bottom: 10px;" />
        <h5 style="margin: 0;">Quản Trị Viên</h5>
    </div>
    <ul>
        <li class="menu-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <a href="{{ route('admin.dashboard') }}"><i class="fas fa-chart-line"></i> Thống kê</a>
        </li>
        <li class="menu-item {{ request()->routeIs('admin.taikhoan.*') ? 'active' : '' }}">
            <a href="{{ route('admin.taikhoan.index') }}"><i class="fas fa-users"></i> Quản lý tài khoản</a>
        </li>
        <li class="menu-item has-submenu {{ request()->routeIs('admin.hoi-dong.*') || request()->routeIs('admin.dot-bao-cao.*') || request()->routeIs('admin.phan-cong-hoi-dong.*') ? 'active' : '' }}">
            <a href="javascript:void(0)" style="display: flex; align-items: center; justify-content: space-between;">
                <div style="display: flex; align-items: center;">
                    <i class="fas fa-sitemap"></i> 
                    <span>Quản lý hội đồng</span>
                </div>
                <i class="fas fa-chevron-down arrow"></i>
            </a>
            <ul class="submenu">
                <li class="menu-item {{ request()->routeIs('admin.hoi-dong.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.hoi-dong.index') }}"><i class="fas fa-list"></i> Danh sách hội đồng</a>
                </li>
                <li class="menu-item {{ request()->routeIs('admin.dot-bao-cao.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.dot-bao-cao.index') }}"><i class="fas fa-calendar-alt"></i> Đợt báo cáo</a>
                </li>
                <li class="menu-item {{ request()->routeIs('admin.phan-cong-hoi-dong.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.phan-cong-hoi-dong.index') }}"><i class="fas fa-user-plus"></i> Phân công</a>
                </li>
            </ul>
        </li>
        <li class="menu-item {{ request()->routeIs('admin.de-tai.*') ? 'active' : '' }}">
            <a href="{{ route('admin.de-tai.index') }}"><i class="fas fa-book"></i> Quản lý đề tài</a>
        </li>
        <li class="menu-item has-submenu {{ request()->routeIs('admin.cai-dat.*') || request()->routeIs('admin.api-doc.*') ? 'active' : '' }}">
            <a href="javascript:void(0)" style="display: flex; align-items: center; justify-content: space-between;">
                <div style="display: flex; align-items: center;">
                    <i class="fas fa-cog"></i>
                    <span>Cài đặt & Tài liệu</span>
                </div>
                <i class="fas fa-chevron-down arrow"></i>
            </a>
            <ul class="submenu">
                <li class="menu-item {{ request()->routeIs('admin.cai-dat.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.cai-dat.index') }}"><i class="fas fa-cog"></i> Cài đặt hệ thống</a>
                </li>
                <li class="menu-item {{ request()->routeIs('admin.api-doc.*') ? 'active' : '' }}">
                    <a href="{{ route('api-doc.index') }}"><i class="fas fa-book"></i> Tài liệu API</a>
                </li>
            </ul>
        </li>
    </ul>
</aside>

<style>
.admin-sidebar .menu-item.active > a {
    background-color: #4299e1;
    color: white;
}

.admin-sidebar .menu-item.active > a i {
    color: white;
}

.admin-sidebar .submenu .menu-item.active > a {
    background-color: #4299e1;
    color: white;
}

.admin-sidebar .submenu .menu-item.active > a i {
    color: white;
}

.admin-sidebar .submenu {
    display: none;
    padding-left: 20px;
}

.admin-sidebar .menu-item.has-submenu.active .submenu {
    display: block;
}

.admin-sidebar .menu-item.has-submenu > a .arrow {
    transition: transform 0.3s;
}

.admin-sidebar .menu-item.has-submenu.active > a .arrow {
    transform: rotate(180deg);
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
