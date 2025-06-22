@extends('admin.layout')

@section('title', 'Danh sách phân công hội đồng')

@section('content')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h1 style="color: #2d3748; font-weight: 700;">Quản lý phân công hội đồng</h1>
        <a href="{{ route('admin.phan-cong-hoi-dong.create') }}" style="padding: 10px 20px; background-color: #4299e1; color: white; border: none; border-radius: 4px; text-decoration: none;">
            <i class="fas fa-plus"></i> Thêm phân công mới
        </a>
    </div>

    <div style="overflow-x:auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 8px rgb(0 0 0 / 0.1);">
        @php
            $phanCongByHoiDong = $phanCongVaiTros->groupBy(function($item) {
                return $item->hoiDong->id ?? 0;
            });
        @endphp
        @forelse ($phanCongByHoiDong as $hoiDongId => $phanCongs)
            <div style="margin-bottom: 32px; border: 1px solid #e2e8f0; border-radius: 8px; box-shadow: 0 1px 4px rgba(0,0,0,0.04);">
                <div style="background: #2d3748; color: #fff; padding: 12px 20px; border-top-left-radius: 8px; border-top-right-radius: 8px; font-weight: bold; font-size: 1.1rem;">
                    Hội đồng: {{ $phanCongs->first()->hoiDong->ten ?? 'N/A' }}
                </div>
                <table style="width: 100%; border-collapse: collapse; min-width: 600px; font-family: Arial, sans-serif;">
                    <thead>
                    <tr style="background-color: #f7fafc; color: #2d3748; text-align: left;">
                        <th style="padding: 10px 15px;">ID</th>
                        <th style="padding: 10px 15px;">Giảng viên</th>
                        <th style="padding: 10px 15px;">Vai trò</th>
                        <th style="padding: 10px 15px;">Ngày tạo</th>
                        <th style="padding: 10px 15px;">Thao tác</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($phanCongs as $phanCong)
                        <tr style="border-bottom: 1px solid #ddd;">
                            <td style="padding: 10px 15px;">{{ $phanCong->id }}</td>
                            <td style="padding: 10px 15px;">{{ $phanCong->taiKhoan->ten ?? 'N/A' }}</td>
                            <td style="padding: 10px 15px;">
                                @php
                                    $tenVaiTro = $phanCong->vaiTro->ten;
                                    if ($tenVaiTro === 'Thành viên' && $phanCong->loai_giang_vien) {
                                        $loai = $phanCong->loai_giang_vien;
                                    } else {
                                        $loai = $tenVaiTro;
                                    }
                                    $color = '#3182ce';
                                    if (str_contains($loai, 'Trưởng tiểu ban')) {
                                        $color = '#e53e3e'; // Đỏ
                                    } elseif (str_contains($loai, 'Thư ký')) {
                                        $color = '#1a365d'; // Xanh dương đậm
                                    } elseif (str_contains($loai, 'Hướng Dẫn')) {
                                        $color = '#38a169';
                                    } elseif (str_contains($loai, 'Phản Biện')) {
                                        $color = '#ed8936';
                                    } elseif (str_contains($loai, 'Khác')) {
                                        $color = '#805ad5';
                                    }
                                @endphp
                                <span style="background-color: {{ $color }}; color: white; padding: 4px 10px; border-radius: 12px; font-size: 0.85rem;">
                                    {{ $loai }}
                                </span>
                            </td>
                            <td style="padding: 10px 15px;">{{ $phanCong->created_at->format('d-m-Y') }}</td>
                            <td style="padding: 10px 15px;">
                                <div style="display: flex; gap: 10px;">
                                    {{-- <a href="{{ route('admin.phan-cong-hoi-dong.edit', $phanCong->id) }}" class="btn-edit" style="color: #3182ce;">
                                        <i class="fas fa-edit"></i>
                                    </a> --}}
                                    <form action="{{ route('admin.phan-cong-hoi-dong.destroy', $phanCong->id) }}" method="POST" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-delete" style="background: none; border: none; color: #e53e3e; cursor: pointer;" onclick="return confirm('Bạn có chắc chắn muốn xóa phân công này?')">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                    <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#modalChangeGV{{ $phanCong->id }}">
                                        <i class="fas fa-exchange-alt"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @empty
            <div style="padding: 20px; text-align: center; color: #718096;">
                <i class="fas fa-info-circle" style="margin-right: 5px;"></i>
                Chưa có dữ liệu
            </div>
        @endforelse
        <div style="margin-top: 20px;">
            {{ $phanCongVaiTros->links() }}
        </div>
    </div>

    <!-- Modal -->
    @foreach ($phanCongVaiTros as $phanCong)
        <div class="modal fade" id="modalChangeGV{{ $phanCong->id }}" tabindex="-1">
            <div class="modal-dialog">
                <form action="{{ route('admin.phan-cong-hoi-dong.change-giang-vien', $phanCong->id) }}" method="POST">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Chuyển giảng viên cho vai trò: {{ $phanCong->vaiTro->ten }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <select name="tai_khoan_id" class="form-select" required>
                                @foreach($taiKhoansChuaPhanCong as $gv)
                                    <option value="{{ $gv->id }}">{{ $gv->ten }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Lưu</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endforeach

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@endsection
