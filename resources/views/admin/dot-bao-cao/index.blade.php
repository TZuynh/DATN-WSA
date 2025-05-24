@extends('admin.layout')

@section('title', 'Danh sách đợt báo cáo')

@section('content')
    <!-- Thêm Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h1 style="color: #2d3748; font-weight: 700;">Quản lý đợt báo cáo</h1>
        <a href="{{ route('admin.dot-bao-cao.create') }}" style="padding: 10px 20px; background-color: #4299e1; color: white; border: none; border-radius: 4px; text-decoration: none;">
            <i class="fas fa-plus-circle"></i> Thêm đợt báo cáo mới
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
                <th style="padding: 12px 15px;">Năm học</th>
                <th style="padding: 12px 15px;">Ngày tạo</th>
                <th style="padding: 12px 15px;">Thao tác</th>
            </tr>
            </thead>
            <tbody>
            @forelse ($dotBaoCaos as $dotBaoCao)
                <tr style="border-bottom: 1px solid #ddd;">
                    <td style="padding: 12px 15px;">{{ $dotBaoCao->id }}</td>
                    <td style="padding: 12px 15px; color: #2d3748; font-weight: 600;">{{ $dotBaoCao->nam_hoc }}</td>
                    <td style="padding: 12px 15px;">{{ $dotBaoCao->created_at->format('d-m-Y') }}</td>
                    <td style="padding: 12px 15px;">
                        <div style="display: flex; gap: 10px;">
                            <a href="{{ route('admin.dot-bao-cao.edit', $dotBaoCao->id) }}" class="btn-edit" style="color: #3182ce;">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.dot-bao-cao.destroy', $dotBaoCao->id) }}" method="POST" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-delete" style="background: none; border: none; color: #e53e3e; cursor: pointer;" onclick="return confirm('Bạn có chắc chắn muốn xóa đợt báo cáo này?')">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="padding: 20px; text-align: center; color: #718096;">
                        <i class="fas fa-info-circle" style="margin-right: 5px;"></i>
                        Chưa có dữ liệu
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
        
        <div style="margin-top: 20px;">
            {{ $dotBaoCaos->links() }}
        </div>
    </div>
@endsection 