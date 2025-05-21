<aside class="admin-sidebar">
    <ul>
        <li><a href="{{ url('/admin/dashboard') }}"><i class="fas fa-chart-line"></i> Thống kê</a></li>
        <li><a href="{{ url('/admin/tai-khoan') }}"><i class="fas fa-users"></i> Quản lý tài khoản</a></li>
        <li class="has-submenu">
            <a href="javascript:void(0)" style="display: flex; align-items: center; justify-content: space-between;">
                <div style="display: flex; align-items: center;">
                    <i class="fas fa-sitemap"></i> 
                    <span>Quản lý hội đồng</span>
                </div>
                <i class="fas fa-chevron-down arrow"></i>
            </a>
            <ul class="submenu">
                <li><a href="{{ url('/admin/hoi-dong') }}"><i class="fas fa-list"></i> Danh sách hội đồng</a></li>
                <li><a href="{{ url('/admin/dot-bao-cao') }}"><i class="fas fa-calendar-alt"></i> Đợt báo cáo</a></li>
            </ul>
        </li>
        <li class="has-submenu">
            <a href="javascript:void(0)" style="display: flex; align-items: center; justify-content: space-between;">
                <div style="display: flex; align-items: center;">
                    <i class="fas fa-tasks"></i> 
                    <span>Phân công hội đồng</span>
                </div>
                <i class="fas fa-chevron-down arrow"></i>
            </a>
            <ul class="submenu">
                <li><a href="{{ url('/admin/phan-cong-hoi-dong') }}"><i class="fas fa-user-plus"></i> Phân công</a></li>
                <li><a href="{{ url('/admin/vai-tro') }}"><i class="fas fa-user-tag"></i> Quản lý vai trò</a></li>
            </ul>
        </li>
        <li><a href="{{ url('/admin/caidat') }}"><i class="fas fa-cog"></i> Cài đặt</a></li>
    </ul>
</aside>

<style>
.admin-sidebar {
    background: #2d3748;
    min-height: 100vh;
    width: 250px;
    padding: 20px 0;
}

.admin-sidebar ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.admin-sidebar li {
    margin: 5px 0;
}

.admin-sidebar a {
    color: #cbd5e0;
    text-decoration: none;
    padding: 12px 20px;
    transition: all 0.3s ease;
}

.admin-sidebar a:hover {
    background: #4a5568;
    color: #fff;
}

.admin-sidebar a i {
    margin-right: 10px;
    width: 20px;
    text-align: center;
}

.admin-sidebar .has-submenu {
    position: relative;
}

.admin-sidebar .arrow {
    transition: transform 0.3s ease;
}

.admin-sidebar .has-submenu:hover .arrow {
    transform: rotate(180deg);
}

.admin-sidebar .submenu {
    display: none;
    background: #1a202c;
    padding: 5px 0;
}

.admin-sidebar .has-submenu:hover .submenu {
    display: block;
}

.admin-sidebar .submenu li {
    margin: 0;
}

.admin-sidebar .submenu a {
    padding: 10px 20px 10px 50px;
    font-size: 0.9em;
}

.admin-sidebar .submenu a:hover {
    background: #2d3748;
}

/* Active state */
.admin-sidebar a.active {
    background: #4299e1;
    color: #fff;
}

.admin-sidebar .submenu a.active {
    background: #2b6cb0;
}
</style>
