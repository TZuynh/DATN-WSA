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
                                            <a href="{{ route('admin.phan-cong-cham.edit', $phanCongCham->id) }}"
                                               class="btn btn-warning btn-sm">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.phan-cong-cham.destroy', $phanCongCham->id) }}"
                                                  method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm"
                                                        onclick="return confirm('Bạn có chắc chắn muốn xóa phân công này?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
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
                                                <span class="text-muted">Chưa phân công chấm</span>
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
