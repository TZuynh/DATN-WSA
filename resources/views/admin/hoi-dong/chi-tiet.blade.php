@extends('admin.layout')

@section('title', 'Chi tiết hội đồng')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<div class="container-fluid px-0">

    <!-- Page Header -->
    <div class="page-header d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center">
            <div>
                <h1 class="mb-1">Chi tiết hội đồng</h1>   
            </div>
        </div>
        <div class="header-actions">
            <a href="{{ route('admin.hoi-dong.index') }}" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
        </div>
    </div>

    <div class="row g-4">
        <!-- Thông tin cơ bản -->
        <div class="col-12 mb-3">
            <div class="card h-100 shadow-sm w-100">
                <div class="card-header bg-white py-3">
                    <h2 class="card-title mb-0">
                        <i class="fas fa-info-circle text-primary me-2"></i>
                        Thông tin cơ bản
                    </h2>
                </div>
                <div class="card-body">
                    <div class="table-responsive w-100">
                        <table class="table table-borderless w-100">
                            <tr>
                                <th style="width: 200px; text-align: left;" class="text-muted text-start">Mã hội đồng:</th>
                                <td class="text-start">
                                    <span class="badge bg-primary px-3 py-2">{{ $hoiDong->ma_hoi_dong }}</span>
                                </td>
                            </tr>
                            <tr>
                                <th class="text-muted text-start" style="width: 200px; text-align: left;">Tên hội đồng:</th>
                                <td class="fw-semibold fs-5 text-start">{{ $hoiDong->ten }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted text-start" style="width: 200px; text-align: left;">Đợt báo cáo:</th>
                                <td class="text-start">
                                    @if($hoiDong->dotBaoCao)
                                        <span class="badge bg-info">{{ $hoiDong->dotBaoCao->nam_hoc }} - {{ optional($hoiDong->dotBaoCao->hocKy)->ten }}</span>
                                    @else
                                        <span class="text-muted">Chưa có đợt báo cáo</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th class="text-muted text-start" style="width: 200px; text-align: left;">Phòng:</th>
                                <td class="text-start">
                                    @if($hoiDong->phong)
                                        <span class="badge bg-secondary">{{ $hoiDong->phong->ten_phong }}</span>
                                    @else
                                        <span class="text-muted">Chưa có phòng</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th class="text-muted text-start" style="width: 200px; text-align: left;">Thời gian bắt đầu:</th>
                                <td class="text-start">
                                    @if($hoiDong->thoi_gian_bat_dau)
                                        <div class="d-flex align-items-center text-muted">
                                            <i class="far fa-calendar-alt me-2"></i>
                                            {{ $hoiDong->thoi_gian_bat_dau->format('d/m/Y H:i') }}
                                        </div>
                                    @else
                                        <span class="text-muted">Chưa có thời gian</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th class="text-muted text-start" style="width: 200px; text-align: left;">Thành viên hội đồng:</th>
                                <td class="fw-semibold text-start">
                                    @if($hoiDong->phanCongVaiTros && $hoiDong->phanCongVaiTros->where('de_tai_id', null)->count() > 0)
                                        <div class="d-flex flex-column gap-2">
                                            @foreach($hoiDong->phanCongVaiTros->where('de_tai_id', null) as $phanCong)
                                                <div class="d-inline-flex align-items-center">
                                                    <i class="fas fa-user-tie text-primary me-2"></i>
                                                    {{ $phanCong->taiKhoan->ten ?? 'N/A' }} 
                                                    <span class="text-muted ms-1">({{ $phanCong->vaiTro->ten ?? 'N/A' }})</span>
                                                    @if($phanCong->loai_giang_vien == 'Giảng Viên Phản Biện')
                                                        <span class="badge bg-warning ms-2">
                                                            <i class="fas fa-user-check me-1"></i>Giảng viên phản biện
                                                        </span>
                                                    @elseif($phanCong->loai_giang_vien == 'Giảng Viên Hướng Dẫn')
                                                        <span class="badge bg-success ms-2">
                                                            <i class="fas fa-user-graduate me-1"></i>Giảng viên hướng dẫn
                                                        </span>
                                                    @endif
                                                    @php
                                                        // Kiểm tra có đề tài nào chưa thuộc hội đồng này không
                                                        $coDeTaiChuaThuocHoiDong = false;
                                                        if(isset($phanCong->taiKhoan) && $phanCong->taiKhoan->deTais) {
                                                            foreach($phanCong->taiKhoan->deTais as $deTai) {
                                                                if(!($deTai->chiTietBaoCao && $deTai->chiTietBaoCao->hoi_dong_id == $hoiDong->id)) {
                                                                    $coDeTaiChuaThuocHoiDong = true;
                                                                    break;
                                                                }
                                                            }
                                                        }
                                                    @endphp
                                                    @if($phanCong->taiKhoan && $phanCong->taiKhoan->deTais && $phanCong->taiKhoan->deTais->count() > 0)
                                                        <button type="button" class="btn btn-sm btn-info ms-2" data-bs-toggle="modal" data-bs-target="#modalTatCaDeTai{{ $phanCong->taiKhoan->id }}">
                                                            <i class="fas fa-book me-1"></i>
                                                            {{ $phanCong->taiKhoan->deTais->count() }} đề tài
                                                        </button>
                                                        @if($coDeTaiChuaThuocHoiDong)
                                                            <button type="button" class="btn btn-sm btn-primary ms-2 btn-mo-modal-chon-detai" data-bs-toggle="modal" data-bs-target="#modalDeTai{{ $phanCong->taiKhoan->id }}">
                                                                <i class="fas fa-plus"></i> Chọn đề tài vào hội đồng
                                                            </button>
                                                        @endif
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="text-muted" style="width: 200px; text-align: left;">
                                            <small>Chưa có thành viên nào trong hội đồng</small>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                           
                            <tr>
                                <th class="text-muted text-start" style="width: 200px; text-align: left;">Ngày tạo:</th>
                                <td class="text-start">
                                    <div class="d-flex align-items-center text-muted">
                                        <i class="far fa-calendar-alt me-2"></i>
                                        {{ $hoiDong->created_at->format('d/m/Y H:i') }}
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th class="text-muted text-start" style="width: 200px; text-align: left;">Cập nhật lần cuối:</th>
                                <td class="text-start">
                                    <div class="d-flex align-items-center text-muted">
                                        <i class="far fa-clock me-2"></i>
                                        {{ $hoiDong->updated_at->format('d/m/Y H:i') }}
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Danh sách đề tài trong hội đồng -->
        <div class="col-12 mb-3">
            <div class="card h-100 shadow-sm w-100">
                <div class="card-header bg-white py-3">
                    <h2 class="card-title mb-0">
                        <i class="fas fa-book text-success me-2"></i>
                        Danh sách đề tài trong hội đồng
                    </h2>
                </div>
                <div class="card-body">
                    @if($hoiDong->chiTietBaoCaos && $hoiDong->chiTietBaoCaos->count() > 0)
                        <div class="table-responsive w-100">
                            <table class="table table-hover w-100">
                                <thead>
                                    <tr>
                                        <th>Mã đề tài</th>
                                        <th>Tên đề tài</th>
                                        <th>Giảng viên</th>
                                        <th>Nhóm</th>
                                        <th>Trạng thái</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($hoiDong->chiTietBaoCaos as $chiTiet)
                                        @if($chiTiet->deTai)
                                            <tr>
                                                <td>
                                                    <span class="badge bg-primary">{{ $chiTiet->deTai->ma_de_tai }}</span>
                                                </td>
                                                <td>
                                                    <strong>{{ $chiTiet->deTai->ten_de_tai }}</strong>
                                                    @if($chiTiet->deTai->mo_ta)
                                                        <br><small class="text-muted">{{ Str::limit($chiTiet->deTai->mo_ta, 50) }}</small>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($chiTiet->deTai->giangVien)
                                                        <span class="badge bg-info">{{ $chiTiet->deTai->giangVien->ten }}</span>
                                                    @else
                                                        <span class="text-muted">Chưa có</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($chiTiet->deTai->nhom)
                                                        <span class="badge bg-success">{{ $chiTiet->deTai->nhom->ten }} ({{ $chiTiet->deTai->nhom->ma_nhom }})</span>
                                                    @else
                                                        <span class="text-muted">Chưa có nhóm</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @switch($chiTiet->deTai->trang_thai)
                                                        @case(0)
                                                            <span class="badge bg-warning">Chờ duyệt</span>
                                                            @break
                                                        @case(1)
                                                            <span class="badge bg-info">Đang thực hiện (GVHD)</span>
                                                            @break
                                                        @case(2)
                                                            <span class="badge bg-primary">Đang thực hiện (GVPB)</span>
                                                            @break
                                                        @case(3)
                                                            <span class="badge bg-danger">Không xảy ra (GVHD)</span>
                                                            @break
                                                        @case(4)
                                                            <span class="badge bg-danger">Không xảy ra (GVPB)</span>
                                                            @break
                                                        @default
                                                            <span class="badge bg-secondary">Không xác định</span>
                                                    @endswitch
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm" role="group">
                                                        <a href="{{ route('admin.de-tai.edit', $chiTiet->deTai->id) }}" 
                                                           class="btn btn-outline-primary" 
                                                           title="Sửa đề tài">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <a href="{{ route('admin.de-tai.preview-pdf', $chiTiet->deTai->id) }}" 
                                                           class="btn btn-outline-info" 
                                                           title="Xem chi tiết"
                                                           target="_blank">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <button type="button" 
                                                                class="btn btn-outline-warning btn-chuyen-hoi-dong" 
                                                                data-detai-id="{{ $chiTiet->deTai->id }}"
                                                                data-current-hoidong="{{ $hoiDong->id }}"
                                                                title="Chuyển sang hội đồng khác">
                                                            <i class="fas fa-random"></i>
                                                        </button>
                                                        <a href="{{ route('admin.phan-cong-cham.phan-bien', ['hoi_dong_id' => $hoiDong->id, 'de_tai_id' => $chiTiet->deTai->id]) }}" class="btn btn-outline-secondary btn-xs" title="Phân công phản biện">
                                                            <i class="fas fa-user-check"></i>
                                                        </a>
                                                        <a href="{{ route('admin.phan-cong-cham.index',  ['hoi_dong_id' => $hoiDong->id, 'de_tai_id' => $chiTiet->deTai->id])  }}" class="btn btn-outline-secondary btn-xs" title="Quản lý phản biện">
                                                            <i class="fas fa-tasks"></i>
                                                        </a>
                                                        <button type="button" 
                                                                class="btn btn-outline-danger btn-xoa-de-tai" 
                                                                data-href="{{ route('admin.hoi-dong.xoa-de-tai', ['hoiDong' => $hoiDong->id, 'deTai' => $chiTiet->deTai->id]) }}"
                                                                title="Xóa khỏi hội đồng">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-book-open text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-3">Chưa có đề tài nào trong hội đồng này</p>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addDeTaiModal">
                                <i class="fas fa-plus me-2"></i>Thêm đề tài
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal hiển thị danh sách đề tài của giảng viên -->
@foreach($hoiDong->phanCongVaiTros->where('de_tai_id', null) as $phanCong)
    @if($phanCong->taiKhoan && $phanCong->taiKhoan->deTais && $phanCong->taiKhoan->deTais->count() > 0)
        <div class="modal fade" id="modalDeTai{{ $phanCong->taiKhoan->id }}" tabindex="-1" aria-labelledby="modalDeTaiLabel{{ $phanCong->taiKhoan->id }}" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-fullscreen-sm-down">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalDeTaiLabel{{ $phanCong->taiKhoan->id }}">
                            <i class="fas fa-book me-2"></i>
                            Danh sách đề tài của giảng viên {{ $phanCong->taiKhoan->ten }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="table-responsive w-100">
                            <table class="table table-hover w-100">
                                <thead>
                                    <tr>
                                        <th>Mã đề tài</th>
                                        <th>Tên đề tài</th>
                                        <th>Trạng thái</th>
                                        <th>Nhóm thực hiện</th>
                                        <th>Chọn</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($phanCong->taiKhoan->deTais as $deTai)
                                        @php
                                            // Kiểm tra đề tài đã thuộc hội đồng này chưa
                                            $daThuocHoiDong = $deTai->chiTietBaoCao && $deTai->chiTietBaoCao->hoi_dong_id == $hoiDong->id;
                                        @endphp
                                        @if(!$daThuocHoiDong)
                                        <tr>
                                            <td>{{ $deTai->ma_de_tai }}</td>
                                            <td>{{ $deTai->ten_de_tai }}</td>
                                            <td>
                                                @switch($deTai->trang_thai)
                                                    @case(0)
                                                        <span class="badge bg-warning">Chờ duyệt</span>
                                                        @break
                                                    @case(1)
                                                        <span class="badge bg-info">Đang thực hiện (GVHD)</span>
                                                        @break
                                                    @case(2)
                                                        <span class="badge bg-primary">Đang thực hiện (GVPB)</span>
                                                        @break
                                                    @case(3)
                                                        <span class="badge bg-danger">Không xảy ra (GVHD)</span>
                                                        @break
                                                    @case(4)
                                                        <span class="badge bg-danger">Không xảy ra (GVPB)</span>
                                                        @break
                                                    @default
                                                        <span class="badge bg-secondary">Không xác định</span>
                                                @endswitch
                                            </td>
                                            <td>
                                                @if($deTai->nhom)
                                                    <span class="badge bg-success">{{ $deTai->nhom->ma_nhom }}</span>
                                                @else
                                                    <span class="text-muted">Chưa có nhóm</span>
                                                @endif
                                            </td>
                                            <td>
                                                <input type="checkbox" name="chon_de_tai[]" value="{{ $deTai->id }}" data-giang-vien="{{ $phanCong->taiKhoan->id }}">
                                            </td>
                                        </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                        <button type="button" class="btn btn-primary btn-xac-nhan-chon-detai" data-giang-vien="{{ $phanCong->taiKhoan->id }}" data-hoi-dong="{{ $hoiDong->id }}">
                            <i class="fas fa-plus"></i> Xác nhận chọn đề tài vào hội đồng
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endforeach

{{-- Modal hiển thị tất cả đề tài của giảng viên --}}
@foreach($hoiDong->phanCongVaiTros->where('de_tai_id', null) as $phanCong)
    @if($phanCong->taiKhoan && $phanCong->taiKhoan->deTais && $phanCong->taiKhoan->deTais->count() > 0)
        <div class="modal fade" id="modalTatCaDeTai{{ $phanCong->taiKhoan->id }}" tabindex="-1" aria-labelledby="modalTatCaDeTaiLabel{{ $phanCong->taiKhoan->id }}" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-fullscreen-sm-down">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTatCaDeTaiLabel{{ $phanCong->taiKhoan->id }}">
                            <i class="fas fa-book me-2"></i>
                            Tất cả đề tài của giảng viên {{ $phanCong->taiKhoan->ten }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="table-responsive w-100">
                            <table class="table table-hover w-100">
                                <thead>
                                    <tr>
                                        <th>Mã đề tài</th>
                                        <th>Tên đề tài</th>
                                        <th>Trạng thái</th>
                                        <th>Nhóm thực hiện</th>
                                        <th>Thuộc hội đồng</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($phanCong->taiKhoan->deTais as $deTai)
                                        <tr>
                                            <td>{{ $deTai->ma_de_tai }}</td>
                                            <td>{{ $deTai->ten_de_tai }}</td>
                                            <td>{{ $deTai->trang_thai }}</td>
                                            <td>
                                                @if($deTai->nhom)
                                                    <span class="badge bg-success">{{ $deTai->nhom->ma_nhom }}</span>
                                                @else
                                                    <span class="text-muted">Chưa có nhóm</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($deTai->chiTietBaoCao && $deTai->chiTietBaoCao->hoiDong)
                                                    {{ $deTai->chiTietBaoCao->hoiDong->ten }}
                                                @else
                                                    <span class="text-muted">Chưa thuộc hội đồng</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endforeach

<!-- Modal chuyển hội đồng -->
<div class="modal fade" id="modalChuyenHoiDong" tabindex="-1" aria-labelledby="modalChuyenHoiDongLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalChuyenHoiDongLabel">
                    <i class="fas fa-random text-warning me-2"></i>
                    Chuyển đề tài sang hội đồng khác
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info mb-3">
                    <i class="fas fa-info-circle"></i>
                    <strong>Lưu ý:</strong> Khi chuyển đề tài sang hội đồng khác:
                    <ul class="mb-0 mt-2">
                        <li>Giảng viên phản biện và hướng dẫn sẽ được giữ nguyên</li>
                        <li>Các giảng viên khác sẽ thay đổi theo hội đồng mới</li>
                    </ul>
                </div>
                <form id="formChuyenHoiDong">
                    <input type="hidden" id="chuyen_de_tai_id" name="de_tai_id">
                    <div class="form-group mb-3">
                        <label for="chuyen_hoi_dong_id">Chọn hội đồng mới</label>
                        <select class="form-control" id="chuyen_hoi_dong_id" name="hoi_dong_id" required>
                            <option value="">-- Chọn hội đồng --</option>
                            @foreach(App\Models\HoiDong::where('id', '!=', $hoiDong->id)->get() as $hd)
                                <option value="{{ $hd->id }}">{{ $hd->ten }} ({{ $hd->ma_hoi_dong }})</option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-warning" id="btnSubmitChuyenHoiDong">
                    <i class="fas fa-random"></i> Chuyển hội đồng
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Thêm đề tài -->
<div class="modal fade" id="addDeTaiModal" tabindex="-1" aria-labelledby="addDeTaiModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addDeTaiModalLabel">
                    <i class="fas fa-plus-circle text-success me-2"></i>
                    Thêm đề tài mới
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    Thêm đề tài cơ bản. Giáo viên sẽ được phân công sau.
                </div>
                <form id="addDeTaiForm">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="ten_de_tai" class="form-label">Tên đề tài <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="ten_de_tai" name="ten_de_tai" placeholder="Nhập tên đề tài" required>
                            </div>
                            <div class="form-group mb-3">
                                <label for="dot_bao_cao_id" class="form-label">Đợt báo cáo <span class="text-danger">*</span></label>
                                <select class="form-control" id="dot_bao_cao_id" name="dot_bao_cao_id" required>
                                    <option value="{{ $hoiDong->dot_bao_cao_id }}">{{ $hoiDong->dotBaoCao->nam_hoc }} - {{ optional($hoiDong->dotBaoCao->hocKy)->ten }}</option>
                                </select>
                            </div>
                            <div class="form-group mb-3">
                                <label for="giang_vien_id" class="form-label">Giảng viên hướng dẫn (tùy chọn)</label>
                                <select class="form-control" id="giang_vien_id" name="giang_vien_id">
                                    <option value="">-- Chọn giảng viên --</option>
                                    @foreach($giangViens as $gv)
                                        <option value="{{ $gv->id }}"
                                            data-nhoms-json='@json($gv->nhomsHuongDan->map(function($n){return["id"=>$n->id,"ten"=>$n->ten,"ma_nhom"=>$n->ma_nhom];}))'>
                                            {{ $gv->ten }}
                                        </option>
                                    @endforeach
                                </select>
                                <div id="nhomGiangVien" class="mt-2 text-success" style="display:none;"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="nhom_id" class="form-label">Nhóm (tùy chọn)</label>
                                <select class="form-control" id="nhom_id" name="nhom_id" disabled>
                                    <option value="">Chọn nhóm</option>
                                    {{-- JS sẽ render các option nhóm ở đây --}}
                                </select>
                            </div>
                            <div class="form-group mb-3">
                                <label for="hoi_dong_id" class="form-label">
                                    Hội đồng <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" value="{{ $hoiDong->ten }} ({{ $hoiDong->ma_hoi_dong }})" readonly>
                                <input type="hidden" name="hoi_dong_id" id="hoi_dong_id" value="{{ $hoiDong->id }}">
                            </div>
                        </div>
                    </div>
                    <div class="form-group mb-3">
                        <label for="mo_ta" class="form-label">Mô tả (tùy chọn)</label>
                        <textarea class="form-control" id="mo_ta" name="mo_ta" rows="3" placeholder="Nhập mô tả đề tài"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i> Hủy
                </button>
                <button type="button" class="btn btn-success" onclick="saveDeTai()">
                    <i class="fas fa-save"></i> Lưu đề tài
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Xử lý xóa đề tài
        $('.btn-xoa-de-tai').click(function(e) {
            e.preventDefault();
            const url = $(this).attr('data-href');
            
            if (confirm('Bạn có chắc chắn muốn xóa đề tài này khỏi hội đồng? Hành động này không thể hoàn tác.')) {
                // Tạo form ẩn để submit DELETE request
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = url;
                
                // Thêm CSRF token
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                form.appendChild(csrfToken);
                
                // Thêm method override
                const methodField = document.createElement('input');
                methodField.type = 'hidden';
                methodField.name = '_method';
                methodField.value = 'DELETE';
                form.appendChild(methodField);
                
                document.body.appendChild(form);
                form.submit();
            }
        });

        // Xử lý xóa lịch chấm
        $('.btn-xoa-lich-cham').click(function(e) {
            e.preventDefault();
            const url = $(this).attr('data-href');
            Swal.fire({
                title: 'Xác nhận xóa?',
                text: "Bạn có chắc chắn muốn xóa lịch chấm này?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Xóa',
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: url,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            Swal.fire({
                                title: 'Thành công!',
                                text: 'Đã xóa lịch chấm',
                                icon: 'success',
                                confirmButtonColor: '#3085d6'
                            }).then(() => {
                                location.reload();
                            });
                        },
                        error: function(xhr) {
                            Swal.fire({
                                title: 'Lỗi!',
                                text: xhr.responseJSON.message || 'Có lỗi xảy ra',
                                icon: 'error',
                                confirmButtonColor: '#3085d6'
                            });
                        }
                    });
                }
            });
        });

        // Validate thời gian kết thúc phải sau thời gian bắt đầu
        $('form').submit(function(e) {
            const thoiGianBatDau = $('input[name="thoi_gian_bat_dau"]').val();
            const thoiGianKetThuc = $('input[name="thoi_gian_ket_thuc"]').val();
            
            if (thoiGianBatDau && thoiGianKetThuc && thoiGianBatDau >= thoiGianKetThuc) {
                e.preventDefault();
                Swal.fire({
                    title: 'Lỗi!',
                    text: 'Thời gian kết thúc phải sau thời gian bắt đầu!',
                    icon: 'error',
                    confirmButtonColor: '#3085d6'
                });
            }
        });

        // Xử lý mở modal chuyển hội đồng
        $('.btn-chuyen-hoi-dong').click(function() {
            const deTaiId = $(this).data('detai-id');
            $('#chuyen_de_tai_id').val(deTaiId);
            $('#chuyen_hoi_dong_id').val('');
            $('#modalChuyenHoiDong').modal('show');
        });

        // Xử lý submit chuyển hội đồng
        $('#btnSubmitChuyenHoiDong').click(function() {
            const deTaiId = $('#chuyen_de_tai_id').val();
            const hoiDongId = $('#chuyen_hoi_dong_id').val();
            if (!hoiDongId) {
                alert('Vui lòng chọn hội đồng mới!');
                return;
            }
            $.ajax({
                url: '{{ route('admin.hoi-dong.index') }}/chuyen-de-tai',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    de_tai_id: deTaiId,
                    hoi_dong_id: hoiDongId
                },
                success: function(res) {
                    $('#modalChuyenHoiDong').modal('hide');
                    if (res.success) {
                        Swal.fire({
                            title: 'Thành công!',
                            text: res.message,
                            icon: 'success',
                            confirmButtonColor: '#3085d6'
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            title: 'Lỗi!',
                            text: res.message,
                            icon: 'error',
                            confirmButtonColor: '#3085d6'
                        });
                    }
                },
                error: function(xhr) {
                    let errorMessage = 'Có lỗi xảy ra khi chuyển hội đồng!';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    Swal.fire({
                        title: 'Lỗi!',
                        text: errorMessage,
                        icon: 'error',
                        confirmButtonColor: '#3085d6'
                    });
                }
            });
        });

        // Xử lý submit thêm đề tài
        $('#addDeTaiForm').submit(function(e) {
            e.preventDefault();
            const form = $(this);
            const url = form.attr('action');
            const method = form.attr('method');
            const data = form.serialize();

            Swal.fire({
                title: 'Xác nhận thêm đề tài?',
                text: "Bạn có chắc chắn muốn thêm đề tài này vào hội đồng?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#dc3545',
                confirmButtonText: 'Thêm',
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: url,
                        method: method,
                        data: data,
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            Swal.fire({
                                title: 'Thành công!',
                                text: response.message || 'Đã thêm đề tài vào hội đồng',
                                icon: 'success',
                                confirmButtonColor: '#3085d6'
                            }).then(() => {
                                $('#addDeTaiModal').modal('hide');
                                location.reload();
                            });
                        },
                        error: function(xhr) {
                            let errorMessage = 'Có lỗi xảy ra khi thêm đề tài!';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            }
                            Swal.fire({
                                title: 'Lỗi!',
                                text: errorMessage,
                                icon: 'error',
                                confirmButtonColor: '#3085d6'
                            });
                        }
                    });
                }
            });
        });

        // Hiển thị nhóm của giảng viên hướng dẫn khi chọn
        $('#giang_vien_id').on('change', function() {
            var nhoms = $(this).find('option:selected').data('nhoms-json');
            var $nhomSelect = $('#nhom_id');
            $nhomSelect.empty();
            $nhomSelect.append('<option value="">Chọn nhóm</option>');
            if (nhoms && nhoms.length > 0) {
                nhoms.forEach(function(nhom) {
                    $nhomSelect.append('<option value="' + nhom.id + '">' + nhom.ten + ' (' + nhom.ma_nhom + ')</option>');
                });
                $nhomSelect.prop('disabled', false);
            } else {
                $nhomSelect.prop('disabled', true);
            }
        });

        // Xác nhận chọn đề tài vào hội đồng
        $('.btn-xac-nhan-chon-detai').click(function() {
            var giangVienId = $(this).data('giang-vien');
            var hoiDongId = $(this).data('hoi-dong');
            var deTaiIds = [];
            $("#modalDeTai" + giangVienId + " input[name='chon_de_tai[]']:checked").each(function() {
                deTaiIds.push($(this).val());
            });
            if (deTaiIds.length === 0) {
                alert('Vui lòng chọn ít nhất một đề tài!');
                return;
            }
            if (!confirm('Bạn có chắc chắn muốn thêm các đề tài này vào hội đồng?')) return;
            $.ajax({
                url: '/admin/hoi-dong/' + hoiDongId + '/them-de-tai',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    de_tai_ids: deTaiIds
                },
                success: function(res) {
                    if (res.success) {
                        alert(res.message || 'Thêm đề tài vào hội đồng thành công!');
                        location.reload();
                    } else {
                        alert(res.message || 'Có lỗi xảy ra!');
                    }
                },
                error: function(xhr) {
                    let errorMessage = 'Có lỗi xảy ra khi thêm đề tài vào hội đồng!';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    alert(errorMessage);
                }
            });
        });
    });
</script>
<script>
    function saveDeTai() {
        const form = document.getElementById('addDeTaiForm');
        const formData = new FormData(form);
        // Kiểm tra dữ liệu bắt buộc
        const tenDeTai = formData.get('ten_de_tai');
        const dotBaoCao = formData.get('dot_bao_cao_id');
        if (!tenDeTai || !dotBaoCao) {
            alert('Vui lòng điền đầy đủ thông tin bắt buộc!');
            return;
        }
        // Thêm CSRF token
        formData.append('_token', '{{ csrf_token() }}');
        // Gửi request
        fetch('{{ route("admin.de-tai.store") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Thêm đề tài thành công!');
                // Đóng modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('addDeTaiModal'));
                modal.hide();
                // Reset form
                form.reset();
                // Reload trang để cập nhật dữ liệu
                location.reload();
            } else {
                alert('Có lỗi xảy ra: ' + (data.message || 'Không thể thêm đề tài'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Có lỗi xảy ra khi thêm đề tài');
        });
    }
    // Reset form khi đóng modal
    document.getElementById('addDeTaiModal').addEventListener('hidden.bs.modal', function () {
        document.getElementById('addDeTaiForm').reset();
    });
</script>
@endpush
