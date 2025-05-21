@extends('admin.layout')

@section('title', 'Danh sách phân công hội đồng')

@section('content')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h1 style="color: #2d3748; font-weight: 700;">Quản lý phân công hội đồng</h1>
        <a href="{{ route('admin.phan-cong-hoi-dong.create') }}" style="padding: 10px 20px; background-color: #4299e1; color: white; border: none; border-radius: 4px; text-decoration: none;">
            <i class="fas fa-plus"></i> Thêm phân công mới
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
                <th style="padding: 12px 15px;">Hội đồng</th>
                <th style="padding: 12px 15px;">Giảng viên</th>
                <th style="padding: 12px 15px;">Vai trò</th>
                <th style="padding: 12px 15px;">Ngày tạo</th>
                <th style="padding: 12px 15px;">Thao tác</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($phanCongVaiTros as $phanCong)
                <tr style="border-bottom: 1px solid #ddd;">
                    <td style="padding: 12px 15px;">{{ $phanCong->id }}</td>
                    <td style="padding: 12px 15px;">{{ $phanCong->hoiDong->ten ?? 'N/A' }}</td>
                    <td style="padding: 12px 15px;">{{ $phanCong->taiKhoan->ten ?? 'N/A' }}</td>
                    <td style="padding: 12px 15px;">
                        @if($phanCong->vaiTro)
                            <span style="background-color: #3182ce; color: white; padding: 4px 10px; border-radius: 12px; font-size: 0.85rem;">
                                {{ $phanCong->vaiTro->ten }}
                            </span>
                        @else
                            <span style="background-color: #718096; color: white; padding: 4px 10px; border-radius: 12px; font-size: 0.85rem;">
                                N/A
                            </span>
                        @endif
                    </td>
                    <td style="padding: 12px 15px;">{{ $phanCong->created_at->format('d-m-Y') }}</td>
                    <td style="padding: 12px 15px;">
                        <div style="display: flex; gap: 10px;">
                            <a href="{{ route('admin.phan-cong-hoi-dong.edit', $phanCong->id) }}" class="btn-edit" style="color: #3182ce;">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.phan-cong-hoi-dong.destroy', $phanCong->id) }}" method="POST" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-delete" style="background: none; border: none; color: #e53e3e; cursor: pointer;" onclick="return confirm('Bạn có chắc chắn muốn xóa phân công này?')">
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
            {{ $phanCongVaiTros->links() }}
        </div>
    </div>
@endsection 