<nav class="admin-navbar">
    <div class="container">
        <button class="sidebar-toggle-btn" onclick="document.querySelector('.admin-sidebar').classList.toggle('active')">
            ☰
        </button>
        <div class="logo">
            <a href="{{ route('admin.profile') }}">
                <img src="{{ asset('images/logo-caothang.png') }}" alt="Logo" class="logo-img" />
                Xin chào! {{ Auth::user()->ten }}
            </a>
            @vite('resources/scss/navbar.scss')
        </div>
        <div class="logout">
            <a href="{{ route('logout') }}" class="btn-logout">Đăng xuất</a>
        </div>
    </div>
</nav>
