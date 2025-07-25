@extends('admin.layout')

@section('title', 'Danh sách quản lý người dùng')

@section('content')
    <!-- Thêm Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <div class="user-management-page">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h1 style="color: #2d3748; font-weight: 700;">Quản lý tài khoản</h1>
            <a href="{{ route('admin.taikhoan.create') }}" style="padding: 10px 20px; background-color: #4299e1; color: white; border: none; border-radius: 4px; text-decoration: none;">
                <i class="fas fa-plus"></i> Thêm tài khoản mới
            </a>
        </div>

        <!-- Form Import Excel -->
        <div style="margin-bottom: 20px; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 8px rgb(0 0 0 / 0.1);">
            <h2 style="margin-bottom: 15px; color: #2d3748; font-size: 1.2rem;">Import danh sách tài khoản</h2>
            <form action="{{ route('admin.taikhoan.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div style="display: flex; gap: 10px; align-items: center;">
                    <input type="file" name="import_file" accept=".xlsx, .xls" required
                        style="padding: 8px; border: 1px solid #ddd; border-radius: 4px; flex: 1;">
                    <button type="submit"
                        style="padding: 8px 16px; background-color: #4299e1; color: white; border: none; border-radius: 4px; cursor: pointer;">
                        Import Excel
                    </button>
                </div>
                @error('import_file')
                    <div style="color: #f56565; margin-top: 5px;">{{ $message }}</div>
                @enderror
            </form>
        </div>

        <!-- Bảng danh sách người dùng -->
        <div style="overflow-x:auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 8px rgb(0 0 0 / 0.1);">
            <table style="width: 100%; border-collapse: collapse; min-width: 600px; font-family: Arial, sans-serif;">
                <thead>
                <tr style="background-color: #2d3748; color: white; text-align: left;">
                    <th style="padding: 12px 15px;">ID</th>
                    <th style="padding: 12px 15px;">Họ tên</th>
                    <th style="padding: 12px 15px;">Email</th>
                    <th style="padding: 12px 15px;">Vai trò</th>
                    <th style="padding: 12px 15px;">Ngày tạo</th>
                    <th style="padding: 12px 15px;">Thao tác</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($taikhoans as $taikhoan)
                    <tr style="border-bottom: 1px solid #ddd;">
                        <td style="padding: 12px 15px;">{{ $taikhoan->id }}</td>
                        <td style="padding: 12px 15px; color: #2d3748; font-weight: 600;">{{ $taikhoan->ten }}</td>
                        <td style="padding: 12px 15px;">{{ $taikhoan->email }}</td>
                        <td style="padding: 12px 15px; text-transform: capitalize;">
                            @if($taikhoan->vai_tro == 'admin')
                                <span style="background-color: #3182ce; color: white; padding: 4px 10px; border-radius: 12px; font-size: 0.85rem;">Admin</span>
                            @elseif($taikhoan->vai_tro == 'giang_vien')
                                <span style="background-color: #48bb78; color: white; padding: 4px 10px; border-radius: 12px; font-size: 0.85rem;">Giảng viên</span>
                            @else
                                <span>{{ $taikhoan->vai_tro }}</span>
                            @endif
                        </td>
                        <td style="padding: 12px 15px;">{{ $taikhoan->created_at->format('d-m-Y') }}</td>
                        <td style="padding: 12px 15px;">
                            <div style="display: flex; gap: 10px;">
                                <a href="{{ route('admin.taikhoan.edit', $taikhoan->id) }}" class="btn-edit" style="color: #3182ce;">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.taikhoan.destroy', $taikhoan->id) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-delete" style="background: none; border: none; color: #e53e3e; cursor: pointer;" onclick="return confirm('Bạn có chắc chắn muốn xóa tài khoản này?')">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="padding: 20px; text-align: center; color: #718096;">
                            <i class="fas fa-info-circle" style="margin-right: 5px;"></i>
                            Chưa có dữ liệu
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
