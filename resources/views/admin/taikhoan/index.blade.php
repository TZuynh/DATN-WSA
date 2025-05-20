@extends('admin.layout')

@section('title', 'Danh sách quản lý người dùng')

@section('content')
    <h1 style="margin-bottom: 20px; color: #2d3748; font-weight: 700;">Quản lý tài khoản</h1>

    @if(session('success'))
        <div style="background-color: #48bb78; color: white; padding: 10px; border-radius: 4px; margin-bottom: 20px;">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div style="background-color: #f56565; color: white; padding: 10px; border-radius: 4px; margin-bottom: 20px;">
            {{ session('error') }}
        </div>
    @endif

    <!-- Form Import Excel -->
    <div style="margin-bottom: 20px; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 8px rgb(0 0 0 / 0.1);">
        <h2 style="margin-bottom: 15px; color: #2d3748; font-size: 1.2rem;">Import danh sách nhân viên</h2>
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
                <th style="padding: 12px 15px;">Tên</th>
                <th style="padding: 12px 15px;">Email</th>
                <th style="padding: 12px 15px;">Vai trò</th>
                <th style="padding: 12px 15px;">Ngày tạo</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($taikhoans as $taikhoan)
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
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection
