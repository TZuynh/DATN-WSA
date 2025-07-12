@extends('admin.layout')

@section('title', 'Danh sách phân công hội đồng')

@section('content')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Header chính --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 fw-bold text-gray-800">Quản lý phân công hội đồng</h1>
            <p class="text-secondary small mb-0">
                <i class="fas fa-info-circle me-1"></i>
                Mỗi giảng viên chỉ được phân công vào 1 hội đồng duy nhất
            </p>
        </div>
        <a href="{{ route('admin.phan-cong-hoi-dong.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Thêm phân công mới
        </a>
    </div>

    {{-- Danh sách phân công theo hội đồng --}}
    <div class="bg-white p-4 rounded shadow-sm overflow-auto">
        @php
            // Chỉ group và hiển thị các bản ghi phân công hội đồng (de_tai_id = null)
            $phanCongByHoiDong = $phanCongVaiTros->where('de_tai_id', null)->groupBy(fn($item) => $item->hoiDong->id ?? 0);
        @endphp

        @forelse ($phanCongByHoiDong as $hoiDongId => $phanCongs)
            <div class="mb-5 border rounded shadow-sm">
                <div class="bg-dark text-white px-4 py-2 d-flex justify-content-between align-items-center rounded-top">
                    <span class="fw-bold">Hội đồng: {{ $phanCongs->first()->hoiDong->ten }}</span>
                    <button class="btn btn-link text-white p-0"
                            data-bs-toggle="modal"
                            data-bs-target="#modalDeTaiList{{ $hoiDongId }}">
                        <i class="fas fa-list fa-lg"></i> Danh sách đề tài
                    </button>
                </div>
                <table class="table mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Giảng viên</th>
                            <th>Vai trò</th>
                            <th>Ngày tạo</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($phanCongs as $phanCong)
                            @php
                            $vaiTro = $phanCong->vaiTro->ten;
                            $badge = match($vaiTro) {
                                'Trưởng tiểu ban' => 'badge bg-danger',
                                'Thư ký'           => 'badge bg-dark',
                                'Thành viên'       => 'badge bg-primary',
                                default            => '',
                            };
                            $confirmMsg = in_array($vaiTro, ['Trưởng tiểu ban', 'Thư ký'])
                                ? 'Bạn có chắc chắn muốn xóa vai trò quan trọng này không ?'
                                : 'Bạn có chắc chắn muốn xóa?';
                        @endphp
                        @php
                            $vaiTro = $phanCong->vaiTro->ten;
                            $isQuanTrong = in_array($vaiTro, ['Trưởng tiểu ban', 'Thư ký']);
                        @endphp
                    <tr>
                        <td>{{ $phanCong->id }}</td>
                        <td>{{ $phanCong->taiKhoan->ten }}</td>
                        <td>@if($badge)<span class="{{ $badge }}">{{ $vaiTro }}</span>@endif</td>
                        <td>{{ $phanCong->created_at->format('d-m-Y') }}</td>
                        <td>
                                @if ($isQuanTrong)
                                <button type="button"
                                        class="btn btn-sm btn-danger btn-delete-quan-trong"
                                        data-phan-cong-id="{{ $phanCong->id }}"
                                        data-vai-tro="{{ $vaiTro }}"
                                        data-hoi-dong-id="{{ $phanCong->hoi_dong_id }}">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                                @else
                                    <button type="button"
                                            class="btn btn-sm btn-danger btn-confirm-delete"
                                            data-confirm="Bạn có chắc chắn muốn xóa?"
                                            data-form-id="form-delete-{{ $phanCong->id }}">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                    <form id="form-delete-{{ $phanCong->id }}"
                                        action="{{ route('admin.phan-cong-hoi-dong.destroy', $phanCong->id) }}"
                                        method="POST" class="d-none">
                                        @csrf @method('DELETE')
                                    </form>
                                @endif
                                    <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#modalChangeGV{{ $phanCong->id }}">
                                        <i class="fas fa-exchange-alt"></i>
                                    </button>
                                    <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#modalSwapGV{{ $phanCong->id }}">
                                    <i class="fas fa-random"></i>
                                    </button>

                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @empty
            <div class="text-center text-secondary py-5">
                <i class="fas fa-info-circle me-2"></i>Chưa có dữ liệu
            </div>
        @endforelse

        <div class="mt-3">{{ $phanCongVaiTros->links() }}</div>
    </div>

   <!-- Modal chọn người thay thế vai trò quan trọng -->
    <div class="modal fade" id="modalThayTheQuanTrong" tabindex="-1">
        <div class="modal-dialog">
            <form id="form-thay-the-quan-trong"
                method="POST"
                action="{{ route('admin.phan-cong-hoi-dong.replace-and-delete') }}">
                @csrf
                <input type="hidden" name="phan_cong_id" id="phanCongIdThayThe">
                <input type="hidden" name="vai_tro" id="vaiTroThayThe">
                <input type="hidden" name="hoi_dong_id" id="hoiDongIdThayThe">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Thay thế vai trò <span id="vaiTroLabel"></span></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        @if ($errors->has('tai_khoan_id'))
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                {{ $errors->first('tai_khoan_id') }}
                            </div>
                        @endif
                        <div class="mb-3">
                            <label>Chọn giảng viên thay thế:</label>
                            <select name="tai_khoan_id" id="selectThayTheGV" class="form-select @error('tai_khoan_id') is-invalid @enderror" required>
                                <!-- JS sẽ render -->
                            </select>
                            @error('tai_khoan_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Xác nhận thay thế & Xóa</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal danh sách đề tài --}}
    @foreach ($phanCongByHoiDong as $hoiDongId => $phanCongs)
        @php $hoiDongObj = $hoiDongs->firstWhere('id', $hoiDongId); @endphp
        <div class="modal fade" id="modalDeTaiList{{ $hoiDongId }}" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Danh sách đề tài – Hội đồng {{ $hoiDongObj->ten }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        @if($hoiDongObj->chiTietBaoCaos->isNotEmpty())
                            <table class="table table-bordered">
                                <thead>
                                    <tr><th>Đề tài</th><th>GV hướng dẫn</th><th>GV chấm</th></tr>
                                </thead>
                                <tbody>
                                    @foreach($hoiDongObj->chiTietBaoCaos as $chiTiet)
                                        @php
                                            // Lấy tất cả các bản ghi phân công chấm cho đề tài này (không lọc vai trò)
                                            $chamList = $hoiDongObj->phanCongVaiTros
                                                ->where('de_tai_id', $chiTiet->deTai->id);
                                        @endphp
                                        <tr>
                                            <td>{{ $chiTiet->deTai->ten_de_tai }}</td>
                                            <td>{{ $chiTiet->deTai->giangVien->ten }}</td>
                                            <td>
                                                @forelse($chamList as $pc)
                                                    <span class="badge bg-info me-1">
                                                        {{ $pc->taiKhoan->ten }} ({{ $pc->vaiTro->ten }}, chấm đề tài)
                                                    </span>
                                                @empty
                                                    <span class="text-muted">Chưa có</span>
                                                @endforelse
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <div class="text-muted">Chưa có đề tài trong hội đồng này.</div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        @if($hoiDongObj->chiTietBaoCaos->isNotEmpty())
                            <button type="button"
                                    class="btn btn-success"
                                    data-bs-dismiss="modal"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalAssignCham{{ $hoiDongId }}">
                                Phân công chấm
                            </button>
                        @endif
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    {{-- Modal phân công chấm --}}
    @foreach ($phanCongByHoiDong as $hoiDongId => $phanCongs)
        @php $hoiDongObj = $hoiDongs->firstWhere('id', $hoiDongId); @endphp
        <div class="modal fade" id="modalAssignCham{{ $hoiDongId }}" tabindex="-1">
            <div class="modal-dialog">
                <form action="{{ route('admin.phan-cong-hoi-dong.add-cham') }}" method="POST">
                    @csrf
                    <div class="modal-content">
                        {{-- Header chỉ tiêu đề --}}
                        <div class="modal-header">
                            <h5 class="modal-title">Phân công chấm – Hội đồng {{ $hoiDongObj->ten }}</h5>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="hoi_dong_id" value="{{ $hoiDongId }}">
                            <div class="mb-3">
                                <label class="form-label">Đề tài</label>
                                <select name="de_tai_id" class="form-select" required>
                                    <option value="">-- Chọn đề tài --</option>
                                    @foreach($hoiDongObj->chiTietBaoCaos as $chiTiet)
                                        <option value="{{ $chiTiet->deTai->id }}">{{ $chiTiet->deTai->ten_de_tai }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Giảng viên</label>
                                <select name="tai_khoan_id" class="form-select" required>
                                    <option value="">-- Chọn giảng viên --</option>
                                    @foreach($hoiDongObj->phanCongVaiTros->where('de_tai_id', null)->unique('tai_khoan_id') as $pc)
                                        <option value="{{ $pc->tai_khoan_id }}">
                                            {{ $pc->taiKhoan->ten }} ({{ $pc->vaiTro->ten }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Lưu</button>
                            <button type="button"
                                    class="btn btn-secondary btn-back"
                                    data-parent="#modalDeTaiList{{ $hoiDongId }}">
                                Quay lại
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endforeach

    @foreach ($phanCongByHoiDong as $hoiDongId => $phanCongs)
        @foreach ($phanCongs as $phanCong)
            <div class="modal fade" id="modalSwapGV{{ $phanCong->id }}" tabindex="-1">
                <div class="modal-dialog">
                    <form action="{{ route('admin.phan-cong-hoi-dong.swap-giang-vien') }}" method="POST">
                        @csrf
                        <input type="hidden" name="phan_cong_id_1" value="{{ $phanCong->id }}">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Hoán đổi giảng viên</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <strong>Lưu ý:</strong> Có thể hoán đổi giảng viên và vai trò giữa các hội đồng cùng đợt báo cáo và cùng thời gian. <strong>Vai trò sẽ được hoán đổi cùng với giảng viên.</strong> <strong>Có thể hoán đổi giữa các vai trò khác nhau (Trưởng tiểu ban ↔ Thành viên, Thư ký ↔ Thành viên, Thành viên ↔ Thành viên).</strong> Chỉ không thể hoán đổi giữa Trưởng tiểu ban và Thư ký. Loại giảng viên (hướng dẫn/phản biện) sẽ được giữ nguyên. Nếu giảng viên có loại "hướng dẫn" hoặc "phản biện", hệ thống sẽ kiểm tra để đảm bảo không bị trùng lặp loại giảng viên trong hội đồng đích.
                                </div>
                                <label>Chọn giảng viên ở hội đồng khác để hoán đổi:</label>
                                <select name="phan_cong_id_2" class="form-select" required>
                                    <option value="">-- Chọn giảng viên --</option>
                                    @php
                                        // Lọc danh sách giảng viên có thể hoán đổi
                                        $candidates = $allPhanCongVaiTros
                                            ->where('de_tai_id', null) // Chỉ phân công hội đồng
                                            ->where('id', '!=', $phanCong->id) // Không phải chính mình
                                            ->where('hoi_dong_id', '!=', $phanCong->hoi_dong_id); // Khác hội đồng
                                        
                                        // Lọc thêm theo điều kiện đợt báo cáo và thời gian
                                        $candidates = $candidates->filter(function($candidate) use ($phanCong) {
                                            $hd1 = $phanCong->hoiDong;
                                            $hd2 = $candidate->hoiDong;
                                            
                                            if (!$hd1 || !$hd2) return false;
                                            
                                            // Cùng đợt báo cáo
                                            if ($hd1->dot_bao_cao_id !== $hd2->dot_bao_cao_id) return false;
                                            
                                            // Cùng thời gian bắt đầu
                                            if ($hd1->thoi_gian_bat_dau != $hd2->thoi_gian_bat_dau) return false;
                                            
                                            // Kiểm tra vai trò - chỉ không cho phép hoán đổi trực tiếp giữa Trưởng tiểu ban và Thư ký
                                            $truongTieuBanId = \App\Models\VaiTro::where('ten', 'Trưởng tiểu ban')->value('id');
                                            $thuKyId = \App\Models\VaiTro::where('ten', 'Thư ký')->value('id');
                                            
                                            // Chỉ không cho phép hoán đổi trực tiếp giữa Trưởng tiểu ban và Thư ký
                                            if (
                                                ($phanCong->vai_tro_id == $truongTieuBanId && $candidate->vai_tro_id == $thuKyId) ||
                                                ($phanCong->vai_tro_id == $thuKyId && $candidate->vai_tro_id == $truongTieuBanId)
                                            ) {
                                                return false;
                                            }
                                            
                                            // Cho phép hoán đổi giữa các vai trò khác nhau
                                            // (Trưởng tiểu ban ↔ Thành viên, Thư ký ↔ Thành viên, Thành viên ↔ Thành viên)
                                            
                                            return true;
                                        });
                                    @endphp
                                    
                                    @forelse($candidates as $candidate)
                                        <option value="{{ $candidate->id }}">
                                            {{ $candidate->taiKhoan->ten ?? 'N/A' }} 
                                            ({{ $candidate->hoiDong->ten ?? 'N/A' }})
                                            - {{ $candidate->vaiTro->ten ?? 'N/A' }}
                                            @if($candidate->loai_giang_vien)
                                                - {{ $candidate->loai_giang_vien }}
                                            @endif
                                        </option>
                                    @empty
                                        <option value="" disabled>Không có giảng viên phù hợp để hoán đổi</option>
                                    @endforelse
                                </select>
                                @if($candidates->isEmpty())
                                    <div class="mt-2 text-muted small">
                                        <i class="fas fa-exclamation-triangle me-1"></i>
                                        Không có giảng viên nào đáp ứng điều kiện hoán đổi.
                                    </div>
                                @else
                                    @if($phanCong->loai_giang_vien && in_array($phanCong->loai_giang_vien, ['hướng dẫn', 'phản biện']))
                                        <div class="mt-2 alert alert-warning small">
                                            <i class="fas fa-exclamation-triangle me-1"></i>
                                            <strong>Lưu ý:</strong> {{ $phanCong->taiKhoan->ten }} có loại "{{ $phanCong->loai_giang_vien }}". Hệ thống sẽ kiểm tra để đảm bảo hội đồng đích chưa có giảng viên cùng loại này.
                                        </div>
                                    @endif
                                @endif
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary" {{ $candidates->isEmpty() ? 'disabled' : '' }}>
                                    Hoán đổi
                                </button>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        @endforeach
    @endforeach

    @foreach ($phanCongByHoiDong as $hoiDongId => $phanCongs)
        @foreach ($phanCongs as $phanCong)
            <!-- Modal chuyển giảng viên -->
            <div class="modal fade" id="modalChangeGV{{ $phanCong->id }}" tabindex="-1">
                <div class="modal-dialog">
                    <form action="{{ route('admin.phan-cong-hoi-dong.change-giang-vien', $phanCong->id) }}" method="POST">
                        @csrf
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Chuyển giảng viên (vai trò: {{ $phanCong->vaiTro->ten }})</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <label>Chọn giảng viên mới:</label>
                                <select name="tai_khoan_id" class="form-select" required>
                                    @foreach($taiKhoansChuaPhanCong as $gv)
                                        <option value="{{ $gv->id }}">{{ $gv->ten }}</option>
                                    @endforeach
                                </select>
                                @error('tai_khoan_id')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary">Chuyển</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        @endforeach
    @endforeach

    {{-- Bootstrap JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    {{-- Debug: Hiển thị lỗi nếu có --}}
    @if($errors->any())
        <script>
            console.log('Có lỗi validation:', @json($errors->all()));
            @if($errors->has('tai_khoan_id'))
                console.log('Lỗi tai_khoan_id:', '{{ $errors->first("tai_khoan_id") }}');
                // Hiển thị thông báo lỗi bằng SweetAlert2
                Swal.fire({
                    title: 'Lỗi!',
                    text: '{{ $errors->first("tai_khoan_id") }}',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            @endif
        </script>
    @endif
    <script>
    document.querySelectorAll('.btn-confirm-delete').forEach(btn => {
        btn.addEventListener('click', function() {
            const confirmMsg = btn.getAttribute('data-confirm');
            const formId = btn.getAttribute('data-form-id');
            Swal.fire({
                title: 'Xác nhận xóa',
                text: confirmMsg,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Xóa',
                cancelButtonText: 'Hủy',
                reverseButtons: true,
                focusCancel: true
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(formId).submit();
                }
            });
        });
    });
    </script>

    <script>
        window.dsGiangVienThayThe = @json($dsGiangVienThayThe);
    </script>

    <script>
        // Gắn biến JSON từ PHP
        window.dsGiangVienThayThe = @json($dsGiangVienThayThe);

        // Bắt sự kiện nút xóa vai trò quan trọng
        document.querySelectorAll('.btn-delete-quan-trong').forEach(btn => {
            btn.addEventListener('click', function() {
                const phanCongId = btn.getAttribute('data-phan-cong-id');
                const vaiTro = btn.getAttribute('data-vai-tro');
                const hoiDongId = btn.getAttribute('data-hoi-dong-id');

                document.getElementById('vaiTroLabel').textContent = vaiTro;
                document.getElementById('phanCongIdThayThe').value = phanCongId;
                document.getElementById('vaiTroThayThe').value = vaiTro;
                document.getElementById('hoiDongIdThayThe').value = hoiDongId;

                // Đổ giảng viên thay thế vào select
                let listGV = [];
                try {
                    listGV = window.dsGiangVienThayThe[hoiDongId][vaiTro] ?? [];
                } catch { listGV = []; }
                const select = document.getElementById('selectThayTheGV');
                select.innerHTML = '';
                listGV.forEach(gv => {
                    const option = document.createElement('option');
                    option.value = gv.id;
                    option.text = gv.ten;
                    select.appendChild(option);
                });
                if (listGV.length === 0) {
                    const option = document.createElement('option');
                    option.value = '';
                    option.text = 'Không có giảng viên phù hợp để thay thế';
                    select.appendChild(option);
                }

                // Mở modal
                new bootstrap.Modal(document.getElementById('modalThayTheQuanTrong')).show();
            });
        });
    </script>


    {{-- Tự động chuyển modal và gỡ backdrop thừa --}}
    <script>
      // Quay lại
      document.querySelectorAll('.btn-back').forEach(btn => {
        btn.addEventListener('click', () => {
          const parentSel = btn.getAttribute('data-parent');
          const childEl   = btn.closest('.modal');
          bootstrap.Modal.getInstance(childEl).hide();
          bootstrap.Modal.getOrCreateInstance(document.querySelector(parentSel)).show();
        });
      });
      // Gỡ backdrop cũ trước khi mở danh sách đề tài
      document.querySelectorAll('button[data-bs-target^="#modalDeTaiList"]').forEach(btn => {
        btn.addEventListener('click', () => {
          document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
        });
      });
      // Khi không còn modal, gỡ backdrop thừa
      document.addEventListener('hidden.bs.modal', () => {
        if (!document.querySelector('.modal.show')) {
          document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
        }
      });

      // Nếu controller trả về session('openModal'), tự động mở modalDeTaiList đó
      @if(session('openModal'))
        document.addEventListener('DOMContentLoaded', () => {
          document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
          new bootstrap.Modal(document.getElementById('modalDeTaiList{{ session("openModal") }}')).show();
        });
      @endif

      // Nếu có lỗi validation, tự động mở modal thay thế vai trò
      @if($errors->has('tai_khoan_id'))
        document.addEventListener('DOMContentLoaded', () => {
          let modalData = null;
          
          // Thử lấy từ session trước
          @if(session('openReplaceModal'))
            modalData = @json(session('openReplaceModal'));
          @elseif(request()->has('phan_cong_id'))
            // Fallback: lấy từ request nếu không có session
            modalData = {
              phan_cong_id: '{{ request("phan_cong_id") }}',
              vai_tro: '{{ request("vai_tro") }}',
              hoi_dong_id: '{{ request("hoi_dong_id") }}'
            };
          @endif
          
          if (modalData) {
            const phanCongId = modalData.phan_cong_id;
            const vaiTro = modalData.vai_tro;
            const hoiDongId = modalData.hoi_dong_id;
            
            // Thiết lập lại form
            document.getElementById('vaiTroLabel').textContent = vaiTro;
            document.getElementById('phanCongIdThayThe').value = phanCongId;
            document.getElementById('vaiTroThayThe').value = vaiTro;
            document.getElementById('hoiDongIdThayThe').value = hoiDongId;

            // Đổ lại danh sách giảng viên
            let listGV = [];
            try {
              listGV = window.dsGiangVienThayThe[hoiDongId][vaiTro] ?? [];
            } catch { listGV = []; }
            const select = document.getElementById('selectThayTheGV');
            select.innerHTML = '';
            listGV.forEach(gv => {
              const option = document.createElement('option');
              option.value = gv.id;
              option.text = gv.ten;
              // Đánh dấu giá trị đã chọn trước đó
              if (gv.id == '{{ old("tai_khoan_id") }}') {
                option.selected = true;
              }
              select.appendChild(option);
            });
            if (listGV.length === 0) {
              const option = document.createElement('option');
              option.value = '';
              option.text = 'Không có giảng viên phù hợp để thay thế';
              select.appendChild(option);
            }

            // Mở modal
            document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
            new bootstrap.Modal(document.getElementById('modalThayTheQuanTrong')).show();
          }
        });
      @endif


    </script>
@endsection
