@extends('components.giangvien.app')

@section('content')
<div class="container mt-4">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Biên bản nhận xét - Đề tài: {{ $deTai->ten_de_tai ?? '' }}</h4>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            <div class="mb-3">
                <label class="fw-bold">1. Nhận xét về hình thức:</label>
                <textarea class="form-control bg-light" rows="2" readonly>{{ $bienBan->hinh_thuc }}</textarea>
            </div>
            <div class="mb-3">
                <label class="fw-bold">2.2. Tính cấp thiết của đề tài:</label>
                <textarea class="form-control bg-light" rows="2" readonly>{{ $bienBan->cap_thiet }}</textarea>
            </div>
            <div class="mb-3">
                <label class="fw-bold">2.3. Mục tiêu và nội dung:</label>
                <textarea class="form-control bg-light" rows="2" readonly>{{ $bienBan->muc_tieu }}</textarea>
            </div>
            <div class="mb-3">
                <label class="fw-bold">2.4. Tổng quan tài liệu và tài liệu tham khảo:</label>
                <textarea class="form-control bg-light" rows="2" readonly>{{ $bienBan->tai_lieu }}</textarea>
            </div>
            <div class="mb-3">
                <label class="fw-bold">2.5. Phương pháp nghiên cứu:</label>
                <textarea class="form-control bg-light" rows="2" readonly>{{ $bienBan->phuong_phap }}</textarea>
            </div>
            <div class="mb-3">
                <label class="fw-bold">2.6. Kết quả đạt được:</label>
                <textarea class="form-control bg-light" rows="2" readonly>{{ $bienBan->ket_qua }}</textarea>
            </div>
            <div class="mb-3">
                <label class="fw-bold">Quá trình hoạt động đề tài</label>
                <textarea class="form-control bg-light" rows="2" readonly>{{ $bienBan->qua_trinh_hoat_dong }}</textarea>
            </div>
            <div class="mb-3">
                <h5 class="fw-bold">Câu hỏi phản biện</h5>
                <ul class="list-group list-group-flush">
                    @forelse($bienBan->cauTraLois as $cauHoi)
                        <li class="list-group-item ps-4">{{ $cauHoi->cau_hoi }}</li>
                    @empty
                        <li class="list-group-item ps-4 text-muted">Không có câu hỏi phản biện.</li>
                    @endforelse
                </ul>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('giangvien.bien-ban-nhan-xet.edit', $deTai->id) }}" class="btn btn-warning">Sửa biên bản</a>
                <a href="{{ route('giangvien.bien-ban-nhan-xet.select-detai') }}" class="btn btn-secondary">Quay lại</a>
            </div>
        </div>
    </div>
</div>
@endsection 