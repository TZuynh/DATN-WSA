@extends('components.giangvien.app')

@section('title', $isPhanBien ? 'Danh sách đề tài phản biện' : 'Danh sách đề tài hướng dẫn')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        @if($isPhanBien)
                            Danh sách đề tài được phân công phản biện
                        @else
                            Danh sách đề tài hướng dẫn
                        @endif
                    </h3>
                    <div class="card-tools">
                        @if(!$isPhanBien)
                            <a href="{{ route('giangvien.de-tai.create') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus"></i> Thêm mới
                            </a>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th style="width: 2%">ID</th>
                                    <th style="width: 5%">Mã đề tài</th>
                                    <th style="width: 15%">Tên đề tài</th>
                                    <th style="width: 15%">Mô tả</th>
                                    @if(!$isPhanBien)
                                        <th style="width: 10%">Ý kiến GV</th>
                                    @endif
                                    <th style="width: 10%">Đợt báo cáo</th>
                                    <th style="width: 10%">Nhóm SV thực hiện</th>
                                    @if($isPhanBien)
                                        <th style="width: 10%">GVHD</th>
                                    @endif
                                    <th style="width: 8%">Trạng thái</th>
                                    <th style="width: 10%">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($deTais as $deTai)
                                <tr>
                                    <td>{{ $deTai->id }}</td>
                                    <td>{{ $deTai->ma_de_tai }}</td>
                                    <td>{{ $deTai->ten_de_tai }}</td>
                                    <td>{!! Str::limit($deTai->mo_ta, 100) !!}</td>
                                    @if(!$isPhanBien)
                                        <td>{!! Str::limit($deTai->y_kien_giang_vien, 100) !!}</td>
                                    @endif
                                    <td>{{ optional($deTai->dotBaoCao)->nam_hoc }} - {{ optional(optional($deTai->dotBaoCao)->hocKy)->ten }}</td>
                                    <td>
                                        @php $nhom = $deTai->nhoms->first(); @endphp
                                        @if($nhom && $nhom->sinhViens->count() > 0)
                                            @foreach($nhom->sinhViens as $sinhVien)
                                                <div>{{ $sinhVien->ten }} ({{ $sinhVien->mssv }})</div>
                                            @endforeach
                                        @else
                                            <span>Chưa có sinh viên</span>
                                        @endif
                                    </td>
                                    @if($isPhanBien)
                                        <td>{{ $deTai->giangVien->ten ?? 'N/A' }}</td>
                                    @endif
                                    <td>
                                        <span class="badge {{ $deTai->trang_thai_class }}">
                                            {{ $deTai->trang_thai_text }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($isPhanBien)
                                            <form action="{{ route('giangvien.de-tai.phanbien-duyet', $deTai) }}" method="POST" class="d-flex gap-1">
                                                @csrf
                                                <button type="submit" name="action" value="approve" class="btn btn-success btn-sm" title="Duyệt đề tài">
                                                    <i class="fas fa-check"></i> Duyệt
                                                </button>
                                                <button type="submit" name="action" value="reject" class="btn btn-danger btn-sm" title="Từ chối đề tài">
                                                    <i class="fas fa-times"></i> Từ chối
                                                </button>
                                            </form>
                                        @else
                                            <div class="d-flex gap-1">
                                                <a href="{{ route('giangvien.de-tai.preview-pdf-detail', $deTai) }}" class="btn btn-info btn-sm" target="_blank">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('giangvien.de-tai.export-pdf-detail', $deTai) }}" class="btn btn-success btn-sm">
                                                    <i class="fas fa-file-pdf"></i>
                                                </a>
                                                <a href="{{ route('giangvien.de-tai.export-word-detail', $deTai) }}" class="btn btn-primary btn-sm">
                                                    <i class="fas fa-file-word"></i>
                                                </a>
                                                <a href="{{ route('giangvien.de-tai.edit', $deTai) }}" class="btn btn-warning btn-sm">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('giangvien.de-tai.destroy', $deTai) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc chắn muốn xóa đề tài này?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="{{ $isPhanBien ? '9' : '10' }}" class="text-center">
                                        @if($isPhanBien)
                                            Không có đề tài nào được phân công phản biện
                                        @else
                                            Không có đề tài nào
                                        @endif
                                    </td>
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