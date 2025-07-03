@extends('components.giangvien.app')

@push('styles')
<style>
    .report-detail-container {
        max-width: 900px;
        margin: 40px auto 0 auto;
    }
    .label-title {
        font-weight: bold;
        font-size: 1.12rem;
        color: #2d2d2d;
        margin-bottom: 0.4rem;
        display: block;
    }
    .value-box {
        margin-bottom: 1.2rem;
        font-size: 1.04rem;
    }
    .word-paper {
        background: #fff;
        margin: 18px 0;
        padding: 40px 50px;
        min-height: 350px;
        border-radius: 9px;
        box-shadow: 0 2px 32px 0 rgba(80,80,80,0.10);
        font-family: "Times New Roman", Times, serif;
        font-size: 1.11rem;
        line-height: 1.85;
        color: #232323;
        border: 1.5px solid #f1f1f1;
        word-break: break-word;
    }
    .word-paper p {
        margin: 1.0em 0;
        text-align: justify;
    }
    .word-paper ul, .word-paper ol {
        margin-left: 2em;
        padding-left: 1.3em;
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
    @media (max-width: 600px) {
        .word-paper { padding: 10px 2px; }
        .report-detail-container { padding: 2px; }
    }
</style>
@endpush

@section('content')
<div class="container report-detail-container">
    <h1 class="mb-4" style="font-family: 'Times New Roman', Times, serif;">Chi tiết báo cáo quá trình</h1>
    <div class="mb-3 value-box">
        <span class="label-title">Nhóm:</span>
        <span>{{ $baoCao->nhom->ten_nhom ?? $baoCao->nhom_id }}</span>
    </div>
    <div class="mb-3 value-box">
        <span class="label-title">Đợt báo cáo:</span>
        <span>{{ $baoCao->dotBaoCao->ten_dot ?? $baoCao->dot_bao_cao_id }}</span>
    </div>
    <div class="mb-3 value-box">
        <span class="label-title">Nội dung báo cáo:</span>
        <div class="word-paper">
            {!! $baoCao->noi_dung_bao_cao !!}
        </div>
    </div>
    <a href="{{ route('giangvien.bao-cao-qua-trinh.index') }}" class="btn btn-secondary mt-3">Quay lại</a>
</div>
@endsection
