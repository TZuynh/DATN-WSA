<h1>Chào mừng Admin!</h1>
<form action="{{ route('admin.logout') }}" method="POST">
    @csrf
    <button type="submit">Đăng xuất</button>
</form>
