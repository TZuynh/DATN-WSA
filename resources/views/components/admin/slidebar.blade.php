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
        <li class="menu-item has-submenu {{ request()->routeIs('admin.hoi-dong.*') || request()->routeIs('admin.dot-bao-cao.*') || request()->routeIs('admin.phan-cong-hoi-dong.*') || request()->routeIs('admin.phan-cong-cham.*') ? 'active' : '' }}">
            <a href="javascript:void(0)" style="display: flex; align-items: center; justify-content: space-between;">
                <div style="display: flex; align-items: center;">
                    <i class="fas fa-sitemap"></i> 
                    <span>Quản lý phản biện</span>
                </div>
                <i class="fas fa-chevron-down arrow"></i>
            </a>
            <ul class="submenu">
                <li class="menu-item {{ request()->routeIs('admin.phan-cong-cham.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.phan-cong-cham.index') }}"><i class="fas fa-tasks"></i> Phân công chấm</a>
                </li>
            </ul>
        </li>
        <li class="menu-item has-submenu {{ request()->routeIs('admin.hoi-dong.*') || request()->routeIs('admin.dot-bao-cao.*') || request()->routeIs('admin.phan-cong-hoi-dong.*') || request()->routeIs('admin.phan-cong-cham.*') ? 'active' : '' }}">
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
                <li class="menu-item {{ request()->routeIs('admin.lich-cham.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.lich-cham.index') }}"><i class="fas fa-clock"></i> Lịch chấm</a>
                </li>
            </ul>
        </li>
        <li class="menu-item has-submenu {{ request()->routeIs('admin.sinh-vien.*') || request()->routeIs('admin.nhom.*') || request()->routeIs('admin.lop.*') ? 'active' : '' }}">
            <a href="javascript:void(0)" style="display: flex; align-items: center; justify-content: space-between;">
                <div style="display: flex; align-items: center;">
                    <i class="fas fa-users"></i>
                    <span>Quản lý sinh viên</span>
                </div>
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
.admin-sidebar {
    position: fixed;
    top: 60px;
    left: 0;
    width: 250px;
    height: calc(100vh - 60px);
    background: #1a202c;
    overflow-y: auto;
    z-index: 999;
    /* Ẩn scrollbar cho Chrome, Safari và Opera */
    &::-webkit-scrollbar {
        display: none;
    }
    /* Ẩn scrollbar cho IE, Edge và Firefox */
    -ms-overflow-style: none;  /* IE và Edge */
    scrollbar-width: none;  /* Firefox */
}

.sidebar-header {
    padding: 15px 0;
    text-align: center;
    border-bottom: 1px solid #333;
}

.logo-img {
    width: 80px;
    height: auto;
    object-fit: contain;
    margin-bottom: 10px;
}

.admin-sidebar ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.menu-item a {
    display: flex;
    align-items: center;
    padding: 12px 20px;
    color: #e5e7eb; /* Màu chữ sáng */
    text-decoration: none;
    transition: all 0.3s;
}

.menu-item a:hover {
    background-color: #2d2d2d; /* Màu hover tối hơn */
}

.menu-item a i {
    width: 20px;
    margin-right: 10px;
    font-size: 16px;
    color: #9ca3af; /* Màu icon xám nhạt */
}

.menu-item.has-submenu > a {
    justify-content: space-between;
}

.menu-item.has-submenu > a .arrow {
    transition: transform 0.3s;
    color: #9ca3af; /* Màu arrow xám nhạt */
}

.menu-item.has-submenu.active > a .arrow {
    transform: rotate(180deg);
}

.submenu {
    display: none;
    background-color: #242424; /* Màu nền submenu tối hơn */
}

.menu-item.has-submenu.active .submenu {
    display: block;
}

.submenu .menu-item a {
    padding-left: 50px;
}

.menu-item.active > a {
    background-color: #4299e1; /* Giữ nguyên màu active */
    color: #fff;
}

.menu-item.active > a i {
    color: #fff;
}

.submenu .menu-item.active > a {
    background-color: #4299e1; /* Giữ nguyên màu active */
    color: #fff;
}

.submenu .menu-item.active > a i {
    color: #fff;
}

/* Điều chỉnh main content */
.main-content {
    margin-left: 250px;
    margin-top: 60px;
    padding: 20px;
    min-height: calc(100vh - 60px);
    background-color: #f9fafb; /* Màu nền sáng cho main content */
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
