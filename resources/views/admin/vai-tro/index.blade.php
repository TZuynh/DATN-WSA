@extends('admin.layout')

@section('title', 'Danh sách vai trò')

@section('content')
    <!-- Thêm Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h1 style="color: #2d3748; font-weight: 700;">Quản lý vai trò</h1>
        <a href="{{ route('admin.vai-tro.create') }}" style="padding: 10px 20px; background-color: #4299e1; color: white; border: none; border-radius: 4px; text-decoration: none;">
            <i class="fas fa-plus-circle"></i> Thêm vai trò mới
        </a>
    </div>

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

    <div style="overflow-x:auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 8px rgb(0 0 0 / 0.1);">
        <table style="width: 100%; border-collapse: collapse; min-width: 600px; font-family: Arial, sans-serif;">
            <thead>
            <tr style="background-color: #2d3748; color: white; text-align: left;">
                <th style="padding: 12px 15px;">ID</th>
                <th style="padding: 12px 15px;">Tên vai trò</th>
                <th style="padding: 12px 15px;">Ngày tạo</th>
                <th style="padding: 12px 15px;">Thao tác</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($vaiTros as $vaiTro)
                <tr style="border-bottom: 1px solid #ddd;">
                    <td style="padding: 12px 15px;">{{ $vaiTro->id }}</td>
                    <td style="padding: 12px 15px; color: #2d3748; font-weight: 600;">{{ $vaiTro->ten }}</td>
                    <td style="padding: 12px 15px;">{{ $vaiTro->created_at->format('d-m-Y') }}</td>
                    <td style="padding: 12px 15px;">
                        <div style="display: flex; gap: 10px;">
                            <a href="{{ route('admin.vai-tro.edit', $vaiTro->id) }}" class="btn-edit" style="color: #3182ce;">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.vai-tro.destroy', $vaiTro->id) }}" method="POST" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-delete" style="background: none; border: none; color: #e53e3e; cursor: pointer;" onclick="return confirm('Bạn có chắc chắn muốn xóa vai trò này?')">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        
        <div style="margin-top: 20px;">
            {{ $vaiTros->links() }}
        </div>
    </div>

    <style>
    .btn-edit:hover {
        color: #2c5282 !important;
        transform: scale(1.1);
    }
    .btn-delete:hover {
        color: #c53030 !important;
        transform: scale(1.1);
    }
    </style>
@endsection 