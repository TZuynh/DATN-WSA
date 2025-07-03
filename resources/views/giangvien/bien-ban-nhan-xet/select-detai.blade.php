@extends('components.giangvien.app')

@section('content')
<div class="container">
    <h2>Chọn đề tài để nhập biên bản nhận xét</h2>
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Mã đề tài</th>
                    <th>Tên đề tài</th>
                    <th>Nhóm SV</th>
                    <th>GVHD</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($deTais as $deTai)
                <tr>
                    <td>{{ $deTai->id }}</td>
                    <td>{{ $deTai->ma_de_tai }}</td>
                    <td>{{ $deTai->ten_de_tai }}</td>
                    <td>
                        @php $nhom = $deTai->nhoms->first(); @endphp
                        @if($nhom && $nhom->sinhViens->count() > 0)
                            @foreach($nhom->sinhViens as $sv)
                                <div>{{ $sv->ten }} ({{ $sv->mssv }})</div>
                            @endforeach
                        @else
                            <span>Chưa có sinh viên</span>
                        @endif
                    </td>
                    <td>{{ $deTai->giangVien->ten ?? 'N/A' }}</td>
                    <td>
                        <a href="{{ route('giangvien.bien-ban-nhan-xet.create', $deTai->id) }}" class="btn btn-primary btn-sm">Nhập biên bản</a>
                        @php
                            $bienBan = \App\Models\BienBanNhanXet::where('ma_de_tai', $deTai->ma_de_tai)->first();
                        @endphp
                        @if($bienBan)
                            <a href="{{ route('giangvien.bien-ban-nhan-xet.show', $deTai->id) }}" class="btn btn-info btn-sm">Xem biên bản</a>
                            <a href="{{ route('giangvien.bien-ban-nhan-xet.edit', $deTai->id) }}" class="btn btn-warning btn-sm">Sửa biên bản</a>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center">Không có đề tài nào thuộc hội đồng bạn làm Thư ký.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection 