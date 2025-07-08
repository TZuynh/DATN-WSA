@extends('admin.layout')

@section('title', 'Danh sách phân công hội đồng')

@section('content')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <div>
            <h1 style="color: #2d3748; font-weight: 700;">Quản lý phân công hội đồng</h1>
            <p style="color: #718096; margin: 0; font-size: 0.9rem;">
                <i class="fas fa-info-circle me-1"></i>
                Mỗi giảng viên chỉ được phân công vào 1 hội đồng duy nhất
            </p>
        </div>
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
                <div style="background: #2d3748; color: #fff; padding: 12px 20px; border-top-left-radius: 8px; border-top-right-radius: 8px; font-weight: bold; font-size: 1.1rem; display: flex; align-items: center; justify-content: space-between;">
                    <span>Hội đồng: {{ $phanCongs->first()->hoiDong->ten ?? 'N/A' }}</span>
                    <span style="display: flex; align-items: center; gap: 8px;">
                        <span>Danh sách đề tài</span>
                        <button class="btn btn-link" data-bs-toggle="modal" data-bs-target="#modalDeTaiList{{ $hoiDongId }}" style="color: #fff; font-size: 1.2rem;">
                            <i class="fas fa-list"></i>
                        </button>
                    </span>
                </div>
                <div class="collapse" id="deTaiList{{ $hoiDongId }}">
                    <div style="padding: 16px;">
                        @php
                            $hoiDongObj = $hoiDongs->firstWhere('id', $hoiDongId);
                        @endphp
                        @if($hoiDongObj && $hoiDongObj->chiTietBaoCaos->count())
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Tên đề tài</th>
                                        <th>Giảng viên hướng dẫn</th>
                                        <th>Giảng viên chấm</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach($hoiDongObj->chiTietBaoCaos as $chiTiet)
                                    <tr>
                                        <td>{{ $chiTiet->deTai->ten_de_tai ?? 'N/A' }}</td>
                                        <td>{{ $chiTiet->deTai->giangVien->ten ?? 'N/A' }}</td>
                                        <td>
                                            <span style="color:red">TEST BUTTON</span>
                                            @foreach($hoiDongObj->phanCongVaiTros as $pc)
                                                @if($pc->loai_giang_vien)
                                                    @php
                                                        $badgeClass = 'bg-secondary';
                                                        if ($pc->loai_giang_vien === 'Giảng Viên Hướng Dẫn') $badgeClass = 'bg-success';
                                                        elseif ($pc->loai_giang_vien === 'Giảng Viên Phản Biện') $badgeClass = 'bg-primary';
                                                    @endphp
                                                    <span class="badge {{ $badgeClass }}">{{ $pc->taiKhoan->ten ?? 'N/A' }} ({{ $pc->loai_giang_vien }})</span>
                                                @endif
                                            @endforeach
                                            <!-- Nút phân công giảng viên chấm trong modal -->
                                            <button type="button" class="btn btn-sm btn-outline-success mt-2"
                                                onclick="document.getElementById('formAddChamModal-{{ $hoiDongObj->id }}-{{ $chiTiet->deTai->id }}-modal').style.display='flex'; this.style.display='none';">
                                                <i class="fas fa-plus"></i> Phân công giảng viên chấm
                                            </button>
                                            <!-- Form thêm giảng viên chấm trong modal -->
                                            <form id="formAddChamModal-{{ $hoiDongObj->id }}-{{ $chiTiet->deTai->id }}-modal"
                                                action="{{ route('admin.phan-cong-hoi-dong.add-cham') }}" method="POST"
                                                style="margin-top: 8px; display: none; gap: 8px; align-items: center;">
                                                @csrf
                                                <input type="hidden" name="hoi_dong_id" value="{{ $hoiDongObj->id }}">
                                                <input type="hidden" name="de_tai_id" value="{{ $chiTiet->deTai->id }}">
                                                <select name="tai_khoan_id" class="form-select form-select-sm" style="width: 160px;">
                                                    <option value="">Chọn giảng viên chấm...</option>
                                                    @foreach($hoiDongObj->phanCongVaiTros as $pc)
                                                        @if(
                                                            $pc->loai_giang_vien !== 'Giảng Viên Hướng Dẫn' &&
                                                            $pc->loai_giang_vien !== 'Giảng Viên Phản Biện' &&
                                                            !$hoiDongObj->phanCongVaiTros->where('de_tai_id', $chiTiet->deTai->id)->where('tai_khoan_id', $pc->tai_khoan_id)->count()
                                                        )
                                                            <option value="{{ $pc->tai_khoan_id }}">{{ $pc->taiKhoan->ten ?? 'N/A' }}</option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                                <button type="submit" class="btn btn-sm btn-success">Thêm chấm</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        @else
                            <div class="text-muted">Chưa có đề tài nào trong hội đồng này.</div>
                        @endif
                    </div>
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
                    <!-- Hàng danh sách đề tài đã bị loại bỏ theo yêu cầu -->
                    </thead>
                    <tbody>
                    @foreach ($phanCongs as $phanCong)
                        <tr style="border-bottom: 1px solid #ddd;">
                            <td style="padding: 10px 15px;">{{ $phanCong->id }}</td>
                            <td style="padding: 10px 15px;">{{ $phanCong->taiKhoan->ten ?? 'N/A' }}</td>
                            <td style="padding: 10px 15px;">
                                @php
                                    $tenVaiTro = $phanCong->vaiTro->ten ?? '';
                                    $class = '';
                                    if ($tenVaiTro == 'Trưởng tiểu ban') $class = 'badge bg-danger';
                                    elseif ($tenVaiTro == 'Thư ký') $class = 'badge bg-dark';
                                    elseif ($tenVaiTro == 'Thành viên') $class = 'badge bg-primary';
                                    else $class = 'd-none'; // Ẩn các vai trò khác
                                @endphp
                                @if(in_array($tenVaiTro, ['Trưởng tiểu ban', 'Thư ký', 'Thành viên']))
                                    <span class="{{ $class }}">
                                        {{ $tenVaiTro }}
                                    </span>
                                @endif
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
                                    <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#modalSwapGV{{ $phanCong->id }}">
                                        <i class="fas fa-random"></i>
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
        <div class="modal fade" id="modalSwapGV{{ $phanCong->id }}" tabindex="-1">
            <div class="modal-dialog">
                <form action="{{ route('admin.phan-cong-hoi-dong.swap-giang-vien') }}" method="POST">
                    @csrf
                    <input type="hidden" name="phan_cong_id_1" value="{{ $phanCong->id }}">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Hoán đổi giảng viên (vai trò: {{ $phanCong->vaiTro->ten }})</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <label>Chọn giảng viên ở hội đồng khác để hoán đổi:</label>
                            <select name="phan_cong_id_2" class="form-select" required>
                                @foreach($phanCongVaiTros->where('vai_tro_id', $phanCong->vai_tro_id)->where('id', '!=', $phanCong->id) as $otherPC)
                                    @if($otherPC->hoi_dong_id != $phanCong->hoi_dong_id)
                                        <option value="{{ $otherPC->id }}">
                                            {{ $otherPC->taiKhoan->ten ?? 'N/A' }} ({{ $otherPC->hoiDong->ten ?? 'N/A' }})
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Hoán đổi</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endforeach

    <!-- Modal danh sách đề tài -->
    @foreach ($phanCongByHoiDong as $hoiDongId => $phanCongs)
        <div class="modal fade" id="modalDeTaiList{{ $hoiDongId }}" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Danh sách đề tài của hội đồng: {{ $phanCongs->first()->hoiDong->ten ?? 'N/A' }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        @php
                            $hoiDongObj = $hoiDongs->firstWhere('id', $hoiDongId);
                        @endphp
                        @if($hoiDongObj && $hoiDongObj->chiTietBaoCaos->count())
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Tên đề tài</th>
                                        <th>Giảng viên hướng dẫn</th>
                                        <th>Giảng viên chấm</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach($hoiDongObj->chiTietBaoCaos as $chiTiet)
                                    <tr>
                                        <td>{{ $chiTiet->deTai->ten_de_tai ?? 'N/A' }}</td>
                                        <td>{{ $chiTiet->deTai->giangVien->ten ?? 'N/A' }}</td>
                                        <td>
                                            @foreach($hoiDongObj->phanCongVaiTros as $pc)
                                                @if($pc->loai_giang_vien)
                                                    @php
                                                        $badgeClass = 'bg-secondary';
                                                        if ($pc->loai_giang_vien === 'Giảng Viên Hướng Dẫn') $badgeClass = 'bg-success';
                                                        elseif ($pc->loai_giang_vien === 'Giảng Viên Phản Biện') $badgeClass = 'bg-primary';
                                                    @endphp
                                                    <span class="badge {{ $badgeClass }}">{{ $pc->taiKhoan->ten ?? 'N/A' }} ({{ $pc->loai_giang_vien }})</span>
                                                @endif
                                            @endforeach
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        @else
                            <div class="text-muted">Chưa có đề tài nào trong hội đồng này.</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@endsection
