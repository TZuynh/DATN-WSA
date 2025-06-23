@extends('admin.layout')

@section('title', 'Danh sách phân công chấm')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Danh sách đề tài tham gia bảo vệ</h3>                  
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Mã đề tài</th>
                                    <th>Tên đề tài</th>
                                    <th>Nhóm</th>
                                    <th>Thành viên</th>
                                    <th>Hội đồng</th>
                                    <th>GV Hướng dẫn</th>
                                    <th>GV Phản biện</th>
                                    <th>GV Khác</th>
                                    <th>Trạng thái</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($phanCongChams as $phanCongCham)
                                    <tr>
                                        <td>{{ $phanCongCham->deTai->ma_de_tai }}</td>
                                        <td>{{ $phanCongCham->deTai->ten_de_tai }}</td>
                                        <td>
                                            @php $nhom = $phanCongCham->deTai->nhoms->first(); @endphp
                                            {{ $nhom ? $nhom->ten : 'N/A' }}
                                        </td>
                                        <td>
                                            @if($nhom && $nhom->sinhViens->count() > 0)
                                                @foreach($nhom->sinhViens as $index => $sv)
                                                    {{ $sv->ten }}{{ !$loop->last ? ', ' : '' }}
                                                @endforeach
                                            @else
                                                <span>N/A</span>
                                            @endif
                                        </td>
                                        <td>{{ $phanCongCham->hoiDong->ten ?? 'N/A' }}</td>
                                        <td>{{ $phanCongCham->getGiangVienByLoai('Giảng Viên Hướng Dẫn')->ten ?? 'N/A' }}</td>
                                        <td>{{ $phanCongCham->getGiangVienByLoai('Giảng Viên Phản Biện')->ten ?? 'N/A' }}</td>
                                        <td>{{ $phanCongCham->getGiangVienByLoai('Giảng Viên Khác')->ten ?? 'N/A' }}</td>
                                        <td>
                                            <span class="badge {{ $phanCongCham->deTai->trang_thai_class }}">
                                                {{ $phanCongCham->deTai->trang_thai_text }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center justify-content-center gap-2">
                                                <a href="{{ route('admin.phan-cong-cham.edit', $phanCongCham->id) }}"
                                                   class="btn btn-warning btn-xs rounded-circle d-flex align-items-center justify-content-center"
                                                   style="width: 28px; height: 28px; padding: 0; font-size: 1rem;" title="Sửa">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('admin.phan-cong-cham.destroy', $phanCongCham->id) }}"
                                                      method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-xs rounded-circle d-flex align-items-center justify-content-center"
                                                            style="width: 28px; height: 28px; padding: 0; font-size: 1rem;" title="Xóa"
                                                            onclick="return confirm('Bạn có chắc chắn muốn xóa phân công này?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                                @php $coLichCham = \App\Models\LichCham::where('de_tai_id', $phanCongCham->de_tai_id)->exists(); @endphp
                                                @if(!$coLichCham)
                                                    <form action="{{ route('admin.lich-cham.store') }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <input type="hidden" name="de_tai_id" value="{{ $phanCongCham->de_tai_id }}">
                                                        <input type="hidden" name="hoi_dong_id" value="{{ $phanCongCham->hoi_dong_id }}">
                                                        <input type="hidden" name="nhom_id" value="{{ $phanCongCham->deTai->nhom_id }}">
                                                        <input type="hidden" name="dot_bao_cao_id" value="{{ $phanCongCham->deTai->dot_bao_cao_id }}">
                                                        <input type="hidden" name="lich_tao" value="{{ $phanCongCham->lich_cham }}">
                                                        <button type="submit" class="btn btn-primary btn-xs rounded-circle d-flex align-items-center justify-content-center"
                                                                style="width: 28px; height: 28px; padding: 0; font-size: 1rem;" title="Duyệt đề tài này lên lịch chấm">
                                                            <i class="fas fa-arrow-up"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                            
                                @endforelse
                                @foreach($deTais as $deTai)
                                    @if(!$deTai->phanCongCham)
                                        <tr>
                                            <td>{{ $deTai->ma_de_tai }}</td>
                                            <td>{{ $deTai->ten_de_tai }}</td>
                                            <td>
                                                @php $nhom = $deTai->nhoms->first(); @endphp
                                                {{ $nhom ? $nhom->ten : 'N/A' }}
                                            </td>
                                            <td>
                                                @if($nhom && $nhom->sinhViens->count() > 0)
                                                    @foreach($nhom->sinhViens as $index => $sv)
                                                        {{ $sv->ten }}{{ !$loop->last ? ', ' : '' }}
                                                    @endforeach
                                                @else
                                                    <span>N/A</span>
                                                @endif
                                            </td>
                                            <td>{{ optional(optional($deTai->chiTietBaoCao)->hoiDong)->ten ?? 'N/A' }}</td>
                                            <td>
                                                @php
                                                    $hd = optional($deTai->chiTietBaoCao)->hoiDong;
                                                    $gvhd = $hd ? $hd->phanCongVaiTros->firstWhere('loai_giang_vien', 'Giảng Viên Hướng Dẫn') : null;
                                                @endphp
                                                {{ $gvhd && $gvhd->taiKhoan ? $gvhd->taiKhoan->ten : 'N/A' }}
                                            </td>
                                            <td>
                                                @php
                                                    $gvpb = $hd ? $hd->phanCongVaiTros->firstWhere('loai_giang_vien', 'Giảng Viên Phản Biện') : null;
                                                @endphp
                                                {{ $gvpb && $gvpb->taiKhoan ? $gvpb->taiKhoan->ten : 'N/A' }}
                                            </td>
                                            <td>
                                                @php
                                                    $gvk = $hd ? $hd->phanCongVaiTros->firstWhere('loai_giang_vien', 'Giảng Viên Khác') : null;
                                                @endphp
                                                {{ $gvk && $gvk->taiKhoan ? $gvk->taiKhoan->ten : 'N/A' }}
                                            </td>
                                            <td>
                                                <span class="badge {{ $deTai->trang_thai_class }}">
                                                    {{ $deTai->trang_thai_text }}
                                                </span>
                                            </td>
                                            <td>
                                                <form action="{{ route('admin.phan-cong-cham.store') }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <input type="hidden" name="de_tai_id" value="{{ $deTai->id }}">
                                                    <div class="input-group input-group-sm mb-2" style="max-width: 350px; min-width: 220px;">
                                                        <span class="input-group-text" id="label-lich-cham-{{ $deTai->id }}" style="min-width: 80px;">Lịch chấm</span>
                                                        <input type="text" name="lich_cham" class="form-control lich-cham-flatpickr" aria-label="Lịch chấm" aria-describedby="label-lich-cham-{{ $deTai->id }}" required autocomplete="off" style="font-size: 1rem; height: 38px; min-width: 120px;">
                                                    </div>
                                                    <button type="submit" class="btn btn-success btn-sm w-100" title="Lưu vào phân công chấm">
                                                        <i class="fas fa-save"></i> Lưu phân công chấm
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $phanCongChams->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/vi.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.lich-cham-flatpickr').forEach(function(input) {
            flatpickr(input, {
                locale: "vi",
                dateFormat: "Y-m-d H:i",
                enableTime: true,
                time_24hr: true,
                minDate: "today",
                allowInput: true,
                placeholder: "Chọn lịch chấm",
            });
        });
    });
</script>
@endpush
