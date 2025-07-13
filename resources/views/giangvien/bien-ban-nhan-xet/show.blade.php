@extends('components.giangvien.app')

@section('title', 'Xem biên bản nhận xét')

@section('content')
<div class="container mt-4">
    <div class="card shadow-lg border-0">
        <div class="card-header bg-primary text-white d-flex align-items-center">
            <i class="fas fa-file-alt fa-lg me-2"></i>
            <h4 class="mb-0">Biên bản nhận xét - Đề tài: <span class="text-warning">{{ $deTai->ten_de_tai ?? '' }}</span></h4>
        </div>
        <div class="card-body p-4">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <div class="border rounded-3 p-3 bg-light mb-3">
                        <div class="fw-bold mb-1"><i class="fas fa-eye me-1 text-primary"></i> Nhận xét về hình thức:</div>
                        <div class="text-secondary">{{ $bienBan->hinh_thuc }}</div>
                    </div>
                    <div class="border rounded-3 p-3 bg-light mb-3">
                        <div class="fw-bold mb-1"><i class="fas fa-bolt me-1 text-warning"></i> Tính cấp thiết của đề tài:</div>
                        <div class="text-secondary">{{ $bienBan->cap_thiet }}</div>
                    </div>
                    <div class="border rounded-3 p-3 bg-light mb-3">
                        <div class="fw-bold mb-1"><i class="fas fa-bullseye me-1 text-success"></i> Mục tiêu và nội dung:</div>
                        <div class="text-secondary">{{ $bienBan->muc_tieu }}</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="border rounded-3 p-3 bg-light mb-3">
                        <div class="fw-bold mb-1"><i class="fas fa-book me-1 text-info"></i> Tổng quan tài liệu và tài liệu tham khảo:</div>
                        <div class="text-secondary">{{ $bienBan->tai_lieu }}</div>
                    </div>
                    <div class="border rounded-3 p-3 bg-light mb-3">
                        <div class="fw-bold mb-1"><i class="fas fa-flask me-1 text-danger"></i> Phương pháp nghiên cứu:</div>
                        <div class="text-secondary">{{ $bienBan->phuong_phap }}</div>
                    </div>
                </div>
            </div>
            <div class="mb-4">
                <div class="fw-bold mb-2"><i class="fas fa-question-circle text-primary me-1"></i> Câu hỏi phản biện:</div>
                <ol class="list-group list-group-numbered list-group-flush">
                    @forelse($bienBan->cauTraLois as $cauHoi)
                        <li class="list-group-item ps-4">{{ $cauHoi->cau_hoi }}</li>
                    @empty
                        <li class="list-group-item ps-4 text-muted">Không có câu hỏi phản biện.</li>
                    @endforelse
                </ol>
            </div>
            <div class="mb-4 text-center">
                <label class="fw-bold fs-5 mb-2"><i class="fas fa-award me-2 text-success"></i>Kết quả đạt được:</label><br>
                @if($bienBan->ket_qua === 'Đạt')
                    <span class="badge bg-success fs-4 py-2 px-4"><i class="fas fa-check-circle me-2"></i>Đạt</span>
                @elseif($bienBan->ket_qua === 'Không đạt')
                    <span class="badge bg-danger fs-4 py-2 px-4"><i class="fas fa-times-circle me-2"></i>Không đạt</span>
                @else
                    <span class="badge bg-secondary fs-4 py-2 px-4">Chưa xác định</span>
                @endif
            </div>
            <div class="d-flex justify-content-end gap-2 mt-3">
                <a href="{{ route('giangvien.bien-ban-nhan-xet.edit', $deTai->id) }}" class="btn btn-warning"><i class="fas fa-edit me-1"></i> Sửa biên bản</a>
                <a href="{{ route('giangvien.bien-ban-nhan-xet.select-detai') }}" class="btn btn-secondary"><i class="fas fa-arrow-left me-1"></i> Quay lại</a>
            </div>
        </div>
    </div>
</div>
@endsection 