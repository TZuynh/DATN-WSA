@extends('components.giangvien.app')

@section('title', 'Danh sách đề tài')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Danh sách đề tài</h3>
                    <div class="card-tools">
                        <a href="{{ route('giangvien.de-tai.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Thêm mới
                        </a>
                    </div>
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
                                    <td>{!! Str::limit($deTai->mo_ta, 20) !!}</td>
                                    <td>{!! Str::limit($deTai->y_kien_giang_vien, 20) !!}</td>
                                    <td>{{ optional($deTai->dotBaoCao)->nam_hoc }} - {{ optional(optional($deTai->dotBaoCao)->hocKy)->ten }}</td>
                                    <td>
                                        @foreach($deTai->nhoms as $nhom)
                                            {{ $nhom->ten }}
                                        @endforeach
                                    </td>
                                    <td>
                                        @php $nhom = $deTai->nhoms->first(); @endphp
                                        @if($nhom && $nhom->sinhViens->count() > 0)
                                            @foreach($nhom->sinhViens as $index => $sinhVien)
                                                {{ Str::limit($sinhVien->ten, 15) }}{{ !$loop->last ? ', ' : '' }}
                                            @endforeach
                                        @else
                                            <span>N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge {{ $deTai->trang_thai_class }}">
                                            {{ $deTai->trang_thai_text }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-1">
                                            <a href="{{ route('giangvien.de-tai.preview-pdf-detail', $deTai) }}" class="btn btn-sm btn-info" target="_blank">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('giangvien.de-tai.export-pdf-detail', $deTai) }}" class="btn btn-sm btn-success">
                                                <i class="fas fa-file-pdf"></i>
                                            </a>
                                            <a href="{{ route('giangvien.de-tai.export-word-detail', $deTai) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-file-word"></i>
                                            </a>
                                            <a href="{{ route('giangvien.de-tai.edit', $deTai) }}" class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('giangvien.de-tai.destroy', $deTai) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc chắn muốn xóa đề tài này?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="10" class="text-center">Không có dữ liệu</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection