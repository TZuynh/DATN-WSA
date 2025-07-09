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
            $phanCongByHoiDong = $phanCongVaiTros->groupBy(fn($item) => $item->hoiDong->id ?? 0);
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
                            @endphp
                            <tr>
                                <td>{{ $phanCong->id }}</td>
                                <td>{{ $phanCong->taiKhoan->ten }}</td>
                                <td>@if($badge)<span class="{{ $badge }}">{{ $vaiTro }}</span>@endif</td>
                                <td>{{ $phanCong->created_at->format('d-m-Y') }}</td>
                                <td>
                                    <form action="{{ route('admin.phan-cong-hoi-dong.destroy', $phanCong->id) }}"
                                          method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-danger"><i class="fas fa-trash-alt"></i></button>
                                    </form>
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
                                            $chamList = $hoiDongObj->phanCongVaiTros
                                                ->where('de_tai_id', $chiTiet->deTai->id)
                                                ->where('vaiTro.ten', 'Thành viên');
                                        @endphp
                                        <tr>
                                            <td>{{ $chiTiet->deTai->ten_de_tai }}</td>
                                            <td>{{ $chiTiet->deTai->giangVien->ten }}</td>
                                            <td>
                                                @forelse($chamList as $pc)
                                                    <span class="badge bg-info me-1">{{ $pc->taiKhoan->ten }}</span>
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
                                        @php
                                            $vaiTro = $pc->vaiTro->ten;
                                            $badge = match($vaiTro) {
                                                'Trưởng tiểu ban' => 'badge bg-danger',
                                                'Thư ký'           => 'badge bg-dark',
                                                'Thành viên'       => 'badge bg-primary',
                                                default            => '',
                                            };
                                        @endphp
                                        <option value="{{ $pc->tai_khoan_id }}">
                                            {{ $pc->taiKhoan->ten }} ({{ $vaiTro }})
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
    </script>
@endsection
