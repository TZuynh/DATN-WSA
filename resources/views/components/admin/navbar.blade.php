<nav class="admin-navbar">
    <div class="container">
        <button class="sidebar-toggle-btn" onclick="document.querySelector('.admin-sidebar').classList.toggle('active')">
            ☰
        </button>
        <div class="logo">
            <a href="{{ url('/admin') }}">
                <img src="{{ asset('images/logo-caothang.png') }}" alt="Logo" class="logo-img" />
                Quản Lý Admin
            </a>
            @vite('resources/scss/navbar.scss')
        </div>
        <div class="logout">
            <form action="{{ route('admin.logout') }}" method="POST" style="display:inline;">
                @csrf
                <button type="submit" class="btn-logout">Đăng xuất</button>
            </form>
        </div>
    </div>
</nav>
