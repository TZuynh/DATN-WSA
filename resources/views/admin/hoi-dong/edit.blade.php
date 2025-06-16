@extends('admin.layout')

@section('title', 'Chỉnh sửa hội đồng')

@section('content')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/vi.js"></script>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Sửa hội đồng</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.hoi-dong.update', $hoiDong->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label for="ma_hoi_dong">Mã hội đồng <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('ma_hoi_dong') is-invalid @enderror" 
                                id="ma_hoi_dong" name="ma_hoi_dong" value="{{ old('ma_hoi_dong', $hoiDong->ma_hoi_dong) }}" required>
                            @error('ma_hoi_dong')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="ten">Tên hội đồng <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('ten') is-invalid @enderror" 
                                id="ten" name="ten" value="{{ old('ten', $hoiDong->ten) }}" required>
                            @error('ten')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="dot_bao_cao_id">Đợt báo cáo <span class="text-danger">*</span></label>
                            <select class="form-control @error('dot_bao_cao_id') is-invalid @enderror" 
                                id="dot_bao_cao_id" name="dot_bao_cao_id" required>
                                <option value="">Chọn đợt báo cáo</option>
                                @foreach($dotBaoCaos as $dotBaoCao)
                                    <option value="{{ $dotBaoCao->id }}" {{ old('dot_bao_cao_id', $hoiDong->dot_bao_cao_id) == $dotBaoCao->id ? 'selected' : '' }}>
                                        {{ $dotBaoCao->nam_hoc }}
                                    </option>
                                @endforeach
                            </select>
                            @error('dot_bao_cao_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="phong_id">Phòng <span class="text-danger">*</span></label>
                            <select class="form-control @error('phong_id') is-invalid @enderror" 
                                id="phong_id" name="phong_id" required>
                                <option value="">Chọn phòng</option>
                                @foreach($phongs as $phong)
                                    <option value="{{ $phong->id }}" {{ old('phong_id', $hoiDong->phong_id) == $phong->id ? 'selected' : '' }}>
                                        {{ $phong->ten_phong }}
                                    </option>
                                @endforeach
                            </select>
                            @error('phong_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="thoi_gian_bat_dau">Thời gian bắt đầu</label>
                            <input type="text" 
                                   class="form-control @error('thoi_gian_bat_dau') is-invalid @enderror" 
                                   id="thoi_gian_bat_dau" 
                                   name="thoi_gian_bat_dau" 
                                   value="{{ old('thoi_gian_bat_dau', $hoiDong->thoi_gian_bat_dau ? $hoiDong->thoi_gian_bat_dau->format('Y-m-d H:i') : '') }}"
                                   placeholder="Chọn thời gian">
                            @error('thoi_gian_bat_dau')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Cập nhật
                            </button>
                            <a href="{{ route('admin.hoi-dong.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Quay lại
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Cấu hình Flatpickr cho thời gian
        const thoiGianBatDau = flatpickr("#thoi_gian_bat_dau", {
            locale: "vi",
            enableTime: true,
            dateFormat: "Y-m-d H:i",
            minDate: "today",
            time_24hr: true,
            minuteIncrement: 1,
            onChange: function(selectedDates, dateStr) {
                console.log('Đã chọn thời gian:', dateStr);
            }
        });

        // Validate form trước khi submit
        document.querySelector('form').addEventListener('submit', function(e) {
            const thoiGianBatDauValue = document.getElementById('thoi_gian_bat_dau').value;
            
            if (thoiGianBatDauValue) {
                const selectedDate = new Date(thoiGianBatDauValue);
                const now = new Date();

                if (selectedDate < now) {
                    e.preventDefault();
                    alert('Thời gian không được nhỏ hơn thời gian hiện tại!');
                    return;
                }
            }
        });
    });
</script>
@endpush 