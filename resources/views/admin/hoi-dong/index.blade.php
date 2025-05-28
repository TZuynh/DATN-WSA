@extends('admin.layout')

@section('title', 'Danh sách hội đồng')

@section('content')
    <!-- Thêm Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h1 style="color: #2d3748; font-weight: 700;">Quản lý hội đồng</h1>
        <a href="{{ route('admin.hoi-dong.create') }}" style="padding: 10px 20px; background-color: #4299e1; color: white; border: none; border-radius: 4px; text-decoration: none;">
            <i class="fas fa-plus-circle"></i> Thêm hội đồng mới
        </a>
    </div>

    <div style="overflow-x:auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 8px rgb(0 0 0 / 0.1);">
        <table style="width: 100%; border-collapse: collapse; min-width: 600px; font-family: Arial, sans-serif;">
            <thead>
            <tr style="background-color: #2d3748; color: white; text-align: left;">
                <th style="padding: 12px 15px;">ID</th>
                <th style="padding: 12px 15px;">Mã hội đồng</th>
                <th style="padding: 12px 15px;">Tên hội đồng</th>
                <th style="padding: 12px 15px;">Đợt báo cáo</th>
                <th style="padding: 12px 15px;">Ngày tạo</th>
                <th style="padding: 12px 15px;">Thao tác</th>
            </tr>
            </thead>
            <tbody>
            @forelse ($hoiDongs as $hoiDong)
                <tr style="border-bottom: 1px solid #ddd;">
                    <td style="padding: 12px 15px;">{{ $hoiDong->id }}</td>
                    <td style="padding: 12px 15px;">{{ $hoiDong->ma_hoi_dong }}</td>
                    <td style="padding: 12px 15px; color: #2d3748; font-weight: 600;">{{ $hoiDong->ten }}</td>
                    <td style="padding: 12px 15px;">{{ $hoiDong->dotBaoCao->nam_hoc ?? 'N/A' }}</td>
                    <td style="padding: 12px 15px;">{{ $hoiDong->created_at->format('d-m-Y') }}</td>
                    <td style="padding: 12px 15px;">
                        <div style="display: flex; gap: 10px;">
                            <a href="{{ route('admin.hoi-dong.show', $hoiDong->id) }}" class="btn-view" style="color: #38a169;" title="Xem chi tiết">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('admin.hoi-dong.edit', $hoiDong->id) }}" class="btn-edit" style="color: #3182ce;" title="Sửa">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.hoi-dong.destroy', $hoiDong->id) }}" method="POST" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-delete" style="background: none; border: none; color: #e53e3e; cursor: pointer;" onclick="return confirm('Bạn có chắc chắn muốn xóa hội đồng này?')" title="Xóa">
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
        
        <div style="margin-top: 20px;">
            {{ $hoiDongs->links() }}
        </div>
    </div>

    <style>
    .btn-view:hover {
        color: #2f855a !important;
        transform: scale(1.1);
    }
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