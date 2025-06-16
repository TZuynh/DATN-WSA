@extends('admin.layout')

@section('title', 'Thêm lịch chấm')

@section('content')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/vi.js"></script>
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Thêm lịch chấm</h1>
        <a href="{{ route('admin.lich-cham.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Quay lại
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route('admin.lich-cham.store') }}" method="POST">
                @csrf
                
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
                                    {{ old('hoi_dong_id') == $hoiDong->id ? 'selected' : '' }}>
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
                                    {{ old('dot_bao_cao_id') == $dotBaoCao->id ? 'selected' : '' }}>
                                    {{ $dotBaoCao->nam_hoc }}
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
                                    {{ old('nhom_id') == $nhom->id ? 'selected' : '' }}>
                                    {{ $nhom->ma_nhom }} - {{ $nhom->ten }} (GV: {{ $nhom->giangVien->ten }})
                                </option>
                            @endforeach
                        </select>
                        @error('nhom_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

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
                        <i class="fas fa-save"></i> Lưu
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
        console.log('Khởi tạo form tạo lịch chấm');

        // Cấu hình Flatpickr cho thời gian
        const lichTao = flatpickr("#lich_tao", {
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

        // Log khi thay đổi hội đồng
        document.getElementById('hoi_dong_id').addEventListener('change', function(e) {
            console.log('Đã chọn hội đồng:', {
                id: e.target.value,
                text: e.target.options[e.target.selectedIndex].text
            });
        });

        // Log khi thay đổi đợt báo cáo
        document.getElementById('dot_bao_cao_id').addEventListener('change', function(e) {
            console.log('Đã chọn đợt báo cáo:', {
                id: e.target.value,
                text: e.target.options[e.target.selectedIndex].text
            });
        });

        // Log khi thay đổi nhóm
        document.getElementById('nhom_id').addEventListener('change', function(e) {
            console.log('Đã chọn nhóm:', {
                id: e.target.value,
                text: e.target.options[e.target.selectedIndex].text
            });
        });

        // Validate form trước khi submit
        document.querySelector('form').addEventListener('submit', function(e) {
            console.log('Bắt đầu validate form');
            
            const formData = new FormData(this);
            const formDataObj = {};
            formData.forEach((value, key) => formDataObj[key] = value);
            console.log('Dữ liệu form:', formDataObj);

            const lichTaoValue = document.getElementById('lich_tao').value;
            console.log('Giá trị thời gian:', lichTaoValue);

            if (!lichTaoValue) {
                console.error('Lỗi: Chưa chọn thời gian');
                e.preventDefault();
                alert('Vui lòng chọn thời gian!');
                return;
            }

            const selectedDate = new Date(lichTaoValue);
            const now = new Date();
            console.log('So sánh thời gian:', {
                selected: selectedDate,
                now: now,
                isPast: selectedDate < now
            });

            if (selectedDate < now) {
                console.error('Lỗi: Thời gian đã chọn trong quá khứ');
                e.preventDefault();
                alert('Thời gian không được nhỏ hơn thời gian hiện tại!');
                return;
            }

            console.log('Form hợp lệ, đang gửi dữ liệu...');
        });

        // Log lỗi validation nếu có
        @if($errors->any())
            console.error('Lỗi validation:', @json($errors->all()));
        @endif

        // Log thông báo thành công nếu có
        @if(session('success'))
            console.log('Thông báo thành công:', @json(session('success')));
        @endif

        // Log thông báo lỗi nếu có
        @if(session('error'))
            console.error('Thông báo lỗi:', @json(session('error')));
        @endif
    });
</script>
@endpush 