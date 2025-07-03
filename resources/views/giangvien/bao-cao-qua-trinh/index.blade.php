@extends('components.giangvien.app')

@section('title', content: 'Báo cáo quá trình')
{{-- Nếu dùng @push('styles') ở layout --}}
@push('styles')
<style>
    .word-paper {
        background: #fff;
        margin: 0 auto;
        padding: 40px 60px 40px 60px;
        min-height: 400px;
        max-height: 70vh;
        border-radius: 10px;
        box-shadow: 0 0 20px rgba(0,0,0,0.10);
        font-family: "Times New Roman", Times, serif;
        font-size: 1.08rem;
        line-height: 1.8;
        color: #232323;
        overflow-y: auto;
        border: 1.5px solid #f0f0f0;
        width: 100%;
    }
    .word-paper p {
        margin-top: 0.7em;
        margin-bottom: 0.7em;
        text-align: justify;
    }
    .word-paper ul, .word-paper ol {
        margin-left: 2em;
        padding-left: 1.2em;
    }
    .word-paper h1, .word-paper h2, .word-paper h3 {
        font-family: "Times New Roman", Times, serif;
        font-weight: bold;
        margin: 1.5em 0 0.8em 0;
        color: #232323;
    }
    .word-paper table {
        border-collapse: collapse;
        margin: 1em 0;
        width: 100%;
        font-size: 0.98em;
    }
    .word-paper th, .word-paper td {
        border: 1px solid #b6b6b6;
        padding: 7px 12px;
        text-align: left;
    }
    /* Responsive padding on mobile */
    @media (max-width: 600px) {
        .word-paper { padding: 8px; }
    }
</style>
@endpush


@section('content')
<div class="container">
    <h1>Báo cáo quá trình</h1>
    <a href="{{ route('giangvien.bao-cao-qua-trinh.create') }}" class="btn btn-primary mb-3">Tạo báo cáo mới</a>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nhóm</th>
                <th>Đợt báo cáo</th>
                <th>Ngày báo cáo</th>
                <th>Nội dung</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            @forelse($baoCaoQuaTrinhs as $item)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $item->nhom->ten ?? $item->nhom_id }}</td>
                <td>
                    {{ $item->dotBaoCao->nam_hoc ?? $item->dot_bao_cao_id }} -
                    {{ $item->dotBaoCao->hocKy->ten ?? 'N/A' }}
                </td>
                <td>
                    {{ \Carbon\Carbon::parse($item->ngay_bao_cao)->format('d/m/Y') }}
                </td>
                <td>
                    <button type="button" class="btn btn-link p-0" data-bs-toggle="modal" data-bs-target="#modalNoiDung{{ $item->id }}" title="Xem chi tiết">
                        <i class="fa-solid fa-eye"></i>
                    </button>                    
                    <div class="modal fade" id="modalNoiDung{{ $item->id }}" tabindex="-1" aria-labelledby="modalNoiDungLabel{{ $item->id }}" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="modalNoiDungLabel{{ $item->id }}">Nội dung báo cáo</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="word-paper">
                                        {!! $item->noi_dung_bao_cao !!}
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </td>                
                <td>
                    {{-- <a href="{{ route('giangvien.bao-cao-qua-trinh.show', parameters: $item->id) }}" class="btn btn-info btn-sm">Xem</a> --}}
                    <a href="{{ route('giangvien.bao-cao-qua-trinh.edit', $item->id) }}" class="btn btn-warning btn-sm">Sửa</a>
                    <form action="{{ route('giangvien.bao-cao-qua-trinh.destroy', $item->id) }}" method="POST" style="display:inline-block">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc muốn xóa?')">Xóa</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center">Chưa có dữ liệu</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
