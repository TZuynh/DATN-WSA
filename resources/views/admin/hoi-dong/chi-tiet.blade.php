@extends('admin.layout')

@section('title', 'Chi tiết hội đồng')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<div class="container-fluid px-4">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

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
        <div class="col-xl-6">
            <div class="card h-100 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h2 class="card-title mb-0">
                        <i class="fas fa-info-circle text-primary me-2"></i>
                        Thông tin cơ bản
                    </h2>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-borderless">
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
                                    @if($hoiDong->phanCongVaiTros && $hoiDong->phanCongVaiTros->count() > 0)
                                        <div class="d-flex flex-column gap-2">
                                            @foreach($hoiDong->phanCongVaiTros as $phanCong)
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
                                                    @if($phanCong->taiKhoan && $phanCong->taiKhoan->deTais && $phanCong->taiKhoan->deTais->count() > 0)
                                                        <button type="button" class="btn btn-sm btn-info ms-2" data-bs-toggle="modal" data-bs-target="#modalDeTai{{ $phanCong->taiKhoan->id }}">
                                                            <i class="fas fa-book me-1"></i>
                                                            {{ $phanCong->taiKhoan->deTais->count() }} đề tài
                                                        </button>
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
        <div class="col-xl-6">
            <div class="card h-100 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h2 class="card-title mb-0">
                        <i class="fas fa-book text-success me-2"></i>
                        Danh sách đề tài trong hội đồng
                    </h2>
                </div>
                <div class="card-body">
                    @if($hoiDong->chiTietBaoCaos && $hoiDong->chiTietBaoCaos->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Mã đề tài</th>
                                        <th>Tên đề tài</th>
                                        <th>Giảng viên</th>
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
                            <a href="{{ route('admin.hoi-dong.edit', $hoiDong->id) }}" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Thêm đề tài
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal hiển thị danh sách đề tài của giảng viên -->
@foreach($hoiDong->phanCongVaiTros as $phanCong)
    @if($phanCong->taiKhoan && $phanCong->taiKhoan->deTais && $phanCong->taiKhoan->deTais->count() > 0)
        <div class="modal fade" id="modalDeTai{{ $phanCong->taiKhoan->id }}" tabindex="-1" aria-labelledby="modalDeTaiLabel{{ $phanCong->taiKhoan->id }}" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalDeTaiLabel{{ $phanCong->taiKhoan->id }}">
                            <i class="fas fa-book me-2"></i>
                            Danh sách đề tài của giảng viên {{ $phanCong->taiKhoan->ten }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Mã đề tài</th>
                                        <th>Tên đề tài</th>
                                        <th>Trạng thái</th>
                                        <th>Nhóm thực hiện</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($phanCong->taiKhoan->deTais as $deTai)
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
                    <i class="fas fa-info-circle me-2"></i>
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
    });
</script>
@endpush
