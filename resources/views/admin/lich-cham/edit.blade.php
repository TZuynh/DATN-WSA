@extends('admin.layout')

@section('title', 'Sửa lịch bảo vệ')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/vi.js"></script>
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Sửa lịch bảo vệ</h1>
        <a href="{{ route('admin.lich-cham.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Quay lại
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route('admin.lich-cham.update', $lichCham) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="hoi_dong_id" class="form-label">Hội đồng <span class="text-danger">*</span></label>
                        <select class="form-select @error('hoi_dong_id') is-invalid @enderror" 
                                id="hoi_dong_id" 
                                name="hoi_dong_id" 
                                required>
                            <option value="">Chọn hội đồng</option>
                            @foreach($hoiDongs as $hoiDong)
                                <option value="{{ $hoiDong->id }}" 
                                    {{ (old('hoi_dong_id', $lichCham->hoi_dong_id) == $hoiDong->id) ? 'selected' : '' }}>
                                    {{ $hoiDong->ten }}
                                </option>
                            @endforeach
                        </select>
                        @error('hoi_dong_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="dot_bao_cao_id" class="form-label">Đợt báo cáo <span class="text-danger">*</span></label>
                        <select class="form-select @error('dot_bao_cao_id') is-invalid @enderror" 
                                id="dot_bao_cao_id" 
                                name="dot_bao_cao_id" 
                                required>
                            <option value="">Chọn đợt báo cáo</option>
                            @foreach($dotBaoCaos as $dotBaoCao)
                                <option value="{{ $dotBaoCao->id }}"
                                    {{ (old('dot_bao_cao_id', $lichCham->dot_bao_cao_id) == $dotBaoCao->id) ? 'selected' : '' }}>
                                    {{ $dotBaoCao->nam_hoc }} - {{ $dotBaoCao->hocKy->ten }}
                                </option>
                            @endforeach
                        </select>
                        @error('dot_bao_cao_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="nhom_id" class="form-label">Nhóm <span class="text-danger">*</span></label>
                        <select class="form-select @error('nhom_id') is-invalid @enderror" 
                                id="nhom_id" 
                                name="nhom_id" 
                                required>
                            <option value="">Chọn nhóm</option>
                            @foreach($nhoms as $nhom)
                                <option value="{{ $nhom->id }}"
                                    data-de-tai="{{ $nhom->deTais->first()->ten_de_tai ?? '' }}"
                                    {{ (old('nhom_id', $lichCham->nhom_id) == $nhom->id) ? 'selected' : '' }}>
                                    {{ $nhom->ma_nhom }} - {{ $nhom->ten }} (GV: {{ $nhom->giangVien->ten }})
                                </option>
                            @endforeach
                        </select>
                        @error('nhom_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="ten_de_tai" class="form-label">Tên đề tài</label>
                        <input type="text" 
                               class="form-control" 
                               id="ten_de_tai" 
                               value="{{ $lichCham->deTai->ten_de_tai ?? '' }}"
                               disabled>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="lich_tao" class="form-label">Thời gian <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control @error('lich_tao') is-invalid @enderror" 
                               id="lich_tao" 
                               name="lich_tao" 
                               value="{{ old('lich_tao', isset($lichCham) ? \Carbon\Carbon::parse($lichCham->lich_tao)->format('Y-m-d H:i') : '') }}"
                               placeholder="Chọn thời gian"
                               required>
                        @error('lich_tao')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Cập nhật
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Cập nhật tên đề tài khi chọn nhóm
        document.getElementById('nhom_id').addEventListener('change', function(e) {
            const selectedOption = e.target.options[e.target.selectedIndex];
            const tenDeTai = selectedOption.getAttribute('data-de-tai');
            document.getElementById('ten_de_tai').value = tenDeTai || '';
        });

        // Cấu hình Flatpickr cho thời gian
        const lichTao = flatpickr("#lich_tao", {
            locale: "vi",
            enableTime: true,
            dateFormat: "Y-m-d H:i",
            minDate: "today",
            time_24hr: true,
            minuteIncrement: 1
        });

        // Validate form trước khi submit
        document.querySelector('form').addEventListener('submit', function(e) {
            const lichTaoValue = document.getElementById('lich_tao').value;

            if (!lichTaoValue) {
                e.preventDefault();
                alert('Vui lòng chọn thời gian!');
                return;
            }

            const selectedDate = new Date(lichTaoValue);
            const now = new Date();

            if (selectedDate < now) {
                e.preventDefault();
                alert('Thời gian không được nhỏ hơn thời gian hiện tại!');
                return;
            }
        });
    });
</script>
@endpush