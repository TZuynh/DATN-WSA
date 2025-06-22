@extends('admin.layout')

@section('title', 'Danh sách đợt báo cáo')

@section('content')
    <!-- Thêm Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <div style="padding: 20px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h1 style="font-size: 24px; color: #2d3748;">Quản lý đợt báo cáo</h1>
            <a href="{{ route('admin.dot-bao-cao.create') }}" 
               style="padding: 10px 20px; background-color: #4299e1; color: white; border-radius: 4px; text-decoration: none;">
                <i class="fas fa-plus"></i> Thêm đợt báo cáo
            </a>
        </div>

        <!-- Thống kê tổng quan -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px;">
            <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                <h3 style="color: #4a5568; font-size: 14px; margin-bottom: 10px;">Tổng số đợt báo cáo</h3>
                <p style="color: #2d3748; font-size: 24px; font-weight: bold;">{{ $dotBaoCaos->total() }}</p>
            </div>
            <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                <h3 style="color: #4a5568; font-size: 14px; margin-bottom: 10px;">Đang diễn ra</h3>
                <p style="color: #2d3748; font-size: 24px; font-weight: bold;">
                    {{ $dotBaoCaos->where('trang_thai', 'dang_dien_ra')->count() }}
                </p>
            </div>
            <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                <h3 style="color: #4a5568; font-size: 14px; margin-bottom: 10px;">Chưa bắt đầu</h3>
                <p style="color: #2d3748; font-size: 24px; font-weight: bold;">
                    {{ $dotBaoCaos->where('trang_thai', 'chua_bat_dau')->count() }}
                </p>
            </div>
            <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                <h3 style="color: #4a5568; font-size: 14px; margin-bottom: 10px;">Đã kết thúc</h3>
                <p style="color: #2d3748; font-size: 24px; font-weight: bold;">
                    {{ $dotBaoCaos->where('trang_thai', 'da_ket_thuc')->count() }}
                </p>
            </div>
        </div>

        <!-- Bảng danh sách -->
        <div style="background: white; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; min-width: 800px;">
                <thead>
                    <tr style="background-color: #2d3748; color: white;">
                        <th style="padding: 12px 15px; text-align: left;">ID</th>
                        <th style="padding: 12px 15px; text-align: left;">Năm học</th>
                        <th style="padding: 12px 15px; text-align: left;">Học kỳ</th>
                        <th style="padding: 12px 15px; text-align: left;">Thời gian</th>
                        <th style="padding: 12px 15px; text-align: left;">Trạng thái</th>
                        <th style="padding: 12px 15px; text-align: left;">Thống kê</th>
                        <th style="padding: 12px 15px; text-align: left;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($dotBaoCaos as $dotBaoCao)
                        <tr style="border-bottom: 1px solid #e2e8f0;">
                            <td style="padding: 12px 15px;">{{ $dotBaoCao->id }}</td>
                            <td style="padding: 12px 15px; font-weight: 600;">{{ $dotBaoCao->nam_hoc }}</td>
                            <td style="padding: 12px 15px;">{{ optional($dotBaoCao->hocKy)->ten ?? 'N/A' }}</td>
                            <td style="padding: 12px 15px;">
                                <div style="font-size: 14px;">
                                    <div>Từ: {{ $dotBaoCao->ngay_bat_dau ? date('d/m/Y', strtotime($dotBaoCao->ngay_bat_dau)) : 'N/A' }}</div>
                                    <div>Đến: {{ $dotBaoCao->ngay_ket_thuc ? date('d/m/Y', strtotime($dotBaoCao->ngay_ket_thuc)) : 'N/A' }}</div>
                                </div>
                            </td>
                            <td style="padding: 12px 15px;">
                                <span style="padding: 4px 8px; border-radius: 4px; font-size: 14px; {{ $dotBaoCao->trang_thai_class }}">
                                    {{ $dotBaoCao->trang_thai_text }}
                                </span>
                            </td>
                            <td style="padding: 12px 15px;">
                                <div style="font-size: 14px;">
                                    Hội đồng: {{ $dotBaoCao->so_luong_hoi_dong_thuc_te }} | Đề tài: {{ $dotBaoCao->so_luong_de_tai_thuc_te }} | Nhóm: {{ $dotBaoCao->so_luong_nhom_thuc_te }}
                                </div>
                            </td>
                            <td style="padding: 12px 15px;">
                                <div style="display: flex; gap: 8px;">
                                    <a href="{{ route('admin.dot-bao-cao.show', $dotBaoCao->id) }}" 
                                       style="padding: 6px 12px; background: #48bb78; color: white; border-radius: 4px; text-decoration: none;"
                                       title="Xem chi tiết">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.dot-bao-cao.edit', $dotBaoCao->id) }}" 
                                       style="padding: 6px 12px; background: #4299e1; color: white; border-radius: 4px; text-decoration: none;"
                                       title="Chỉnh sửa">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.dot-bao-cao.destroy', $dotBaoCao->id) }}" 
                                          method="POST" 
                                          onsubmit="return confirm('Bạn có chắc chắn muốn xóa đợt báo cáo này?');"
                                          style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                style="padding: 6px 12px; background: #f56565; color: white; border: none; border-radius: 4px; cursor: pointer;"
                                                title="Xóa">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="padding: 20px; text-align: center; color: #718096;">
                                Không có đợt báo cáo nào
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Phân trang -->
        <div style="margin-top: 20px;">
            {{ $dotBaoCaos->links() }}
        </div>
    </div>

    @push('scripts')
    <script>
        // Cập nhật trạng thái ngay khi trang được load
        document.addEventListener('DOMContentLoaded', function() {
            fetch('{{ route("admin.dot-bao-cao.update-status") }}')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.reload();
                    }
                });
        });

        // Tự động cập nhật trạng thái mỗi phút
        setInterval(function() {
            fetch('{{ route("admin.dot-bao-cao.update-status") }}')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.reload();
                    }
                });
        }, 60000);
    </script>
    @endpush
@endsection 