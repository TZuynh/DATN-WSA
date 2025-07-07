@extends('admin.layout')

@section('title', 'Quản lý đề tài')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Danh sách đề tài</h3>
                    {{-- <div class="card-tools">
                        <a href="{{ route('admin.de-tai.create') }}" class="btn btn-success btn-sm">
                            <i class="fas fa-plus"></i> Thêm mới
                        </a>
                    </div> --}}
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th style="width: 2%">ID</th>
                                    <th style="width: 5%">Mã đề tài</th>
                                    <th style="width: 10%">Tên đề tài</th>
                                    <th style="width: 10%">Mô tả</th>
                                    <th style="width: 10%">Ý kiến GV</th>
                                    <th style="width: 10%">Đợt báo cáo</th>
                                    <th style="width: 5%">Nhóm</th>
                                    <th style="width: 10%">Thành viên</th>
                                    <th style="width: 5%">Giảng viên</th>
                                    <th style="width: 3%">Trạng thái</th>
                                    <th style="width: 5%">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($deTais as $deTai)
                                <tr>
                                    <td>{{ $deTai->id }}</td>
                                    <td>{{ $deTai->ma_de_tai }}</td>
                                    <td>{{ Str::limit($deTai->ten_de_tai, 15) }}</td>
                                    <td>{!! Str::limit(strip_tags($deTai->mo_ta), 20) !!}</td>
                                    <td>{!! Str::limit(strip_tags($deTai->y_kien_giang_vien), 20) !!}</td>
                                    <td>{{ optional($deTai->dotBaoCao)->nam_hoc }} - {{ optional(optional($deTai->dotBaoCao)->hocKy)->ten }}</td>
                                    <td>{{ Str::limit($deTai->nhom ? $deTai->nhom->ten : 'N/A', 10) }}</td>
                                    <td>
                                        @if($deTai->nhom && $deTai->nhom->sinhViens->count() > 0)
                                            @foreach($deTai->nhom->sinhViens as $index => $sinhVien)
                                                {{ Str::limit($sinhVien->ten, 15) }}{{ !$loop->last ? ', ' : '' }}
                                            @endforeach
                                        @else
                                            <span>N/A</span>
                                        @endif
                                    </td>
                                    <td>{{ Str::limit($deTai->giangVien ? $deTai->giangVien->ten : 'N/A', 10) }}</td>
                                    <td>
                                        <span class="badge {{ $deTai->trang_thai_class }}">
                                            {{ $deTai->trang_thai_text }}
                                        </span>
                                    </td>
                                    <td class="text-center align-middle" style="vertical-align: middle !important;">
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('admin.de-tai.edit', $deTai) }}" class="btn btn-warning btn-sm" title="Sửa">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="{{ route('admin.de-tai.preview-pdf', $deTai) }}" class="btn btn-info btn-sm" target="_blank" title="Xem">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.de-tai.export-pdf', $deTai) }}" class="btn btn-primary btn-sm" title="PDF">
                                                <i class="fas fa-file-pdf"></i>
                                            </a>
                                            <a href="{{ route('admin.de-tai.export-word', $deTai) }}" class="btn btn-success btn-sm" title="Word">
                                                <i class="fas fa-file-word"></i>
                                            </a>
                                            <form action="{{ route('admin.de-tai.destroy', $deTai) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc chắn muốn xóa?')" title="Xóa">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                            @if($deTai->trang_thai != \App\Models\DeTai::TRANG_THAI_DANG_THUC_HIEN_GVHD)
                                            <form action="{{ route('admin.de-tai.approve', $deTai->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Duyệt đề tài này?')" title="Duyệt">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="11" class="text-center">Chưa có dữ liệu</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    {{ $deTais->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

<style>
    .btn-group-sm > .btn, .btn-sm {
        padding: 5px 12px !important;
        font-size: 1rem !important;
        line-height: 1.4 !important;
    }
    .btn-group[role="group"] {
        flex-wrap: nowrap !important;
        align-items: center !important;
    }
    td .btn-group, td .btn-group-sm {
        display: flex !important;
        flex-wrap: nowrap !important;
        align-items: center !important;
        gap: 4px;
    }
    td .btn, td form {
        margin-bottom: 0 !important;
    }
</style>