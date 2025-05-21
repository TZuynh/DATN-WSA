<aside class="admin-sidebar">
    <ul>
        <li><a href="{{ route('admin.dashboard') }}"><i class="fas fa-chart-line"></i> Thống kê</a></li>
        <li><a href="{{ route('admin.taikhoan.index') }}"><i class="fas fa-users"></i> Quản lý tài khoản</a></li>
        <li class="has-submenu">
            <a href="javascript:void(0)" style="display: flex; align-items: center; justify-content: space-between;">
                <div style="display: flex; align-items: center;">
                    <i class="fas fa-sitemap"></i> 
                    <span>Quản lý hội đồng</span>
                </div>
                <i class="fas fa-chevron-down arrow"></i>
            </a>
            <ul class="submenu">
                <li><a href="{{ route('admin.hoi-dong.index') }}"><i class="fas fa-list"></i> Danh sách hội đồng</a></li>
                <li><a href="{{ route('admin.dot-bao-cao.index') }}"><i class="fas fa-calendar-alt"></i> Đợt báo cáo</a></li>
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
                <li><a href="{{ route('admin.phan-cong-hoi-dong.index') }}"><i class="fas fa-user-plus"></i> Phân công</a></li>
                <li><a href="{{ route('admin.vai-tro.index') }}"><i class="fas fa-user-tag"></i> Quản lý vai trò</a></li>
            </ul>
        </li>
        <li><a href="{{ route('admin.cai-dat.index') }}"><i class="fas fa-cog"></i> Cài đặt</a></li>
    </ul>
</aside>
