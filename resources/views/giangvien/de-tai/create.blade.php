@extends('components.giangvien.app')

@section('title', 'Thêm đề tài mới')

@section('content')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/vi.js"></script>

    <div style="padding: 20px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h1 style="color: #2d3748; font-weight: 700;">Thêm đề tài mới</h1>
            <a href="{{ route('giangvien.de-tai.index') }}" style="padding: 10px 20px; background-color: #718096; color: white; border: none; border-radius: 4px; text-decoration: none;">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
        </div>

        @if($errors->any())
            <div style="background-color: #f56565; color: white; padding: 10px; border-radius: 4px; margin-bottom: 20px;">
                <ul style="margin: 0; padding-left: 20px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 8px rgb(0 0 0 / 0.1);">
            <form action="{{ route('giangvien.de-tai.store') }}" method="POST" id="createForm">
                @csrf
                
                <div style="margin-bottom: 20px;">
                    <label for="ma_de_tai" style="display: block; margin-bottom: 5px; color: #4a5568;">Mã đề tài</label>
                    <input type="text" class="form-control @error('ma_de_tai') is-invalid @enderror" 
                        id="ma_de_tai" name="ma_de_tai" 
                        value="{{ old('ma_de_tai') }}" 
                        placeholder="Nhập mã đề tài" required
                        style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                    @error('ma_de_tai')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div style="margin-bottom: 20px;">
                    <label for="ten_de_tai" style="display: block; margin-bottom: 5px; color: #4a5568;">Tên đề tài</label>
                    <input type="text" class="form-control @error('ten_de_tai') is-invalid @enderror" 
                        id="ten_de_tai" name="ten_de_tai" 
                        value="{{ old('ten_de_tai') }}" 
                        placeholder="Nhập tên đề tài" required
                        style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                    @error('ten_de_tai')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div style="margin-bottom: 20px;">
                    <label for="mo_ta" style="display: block; margin-bottom: 5px; color: #4a5568;">Mô tả</label>
                    <textarea class="form-control @error('mo_ta') is-invalid @enderror" 
                        id="mo_ta" name="mo_ta" rows="3" 
                        placeholder="Nhập mô tả đề tài"
                        style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">{{ old('mo_ta') }}</textarea>
                    @error('mo_ta')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div style="margin-bottom: 20px;">
                    <label for="y_kien_giang_vien" style="display: block; margin-bottom: 5px; color: #4a5568;">Ý kiến giảng viên</label>
                    <textarea class="form-control @error('y_kien_giang_vien') is-invalid @enderror" 
                        id="y_kien_giang_vien" name="y_kien_giang_vien" rows="3" 
                        placeholder="Nhập ý kiến giảng viên"
                        style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">{{ old('y_kien_giang_vien') }}</textarea>
                    @error('y_kien_giang_vien')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div style="margin-bottom: 20px;">
                    <label for="ngay_bat_dau" style="display: block; margin-bottom: 5px; color: #4a5568;">Ngày bắt đầu</label>
                    <input type="text" class="form-control @error('ngay_bat_dau') is-invalid @enderror" 
                        id="ngay_bat_dau" name="ngay_bat_dau" 
                        value="{{ old('ngay_bat_dau') }}"
                        placeholder="Chọn ngày bắt đầu" required
                        style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                    @error('ngay_bat_dau')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div style="margin-bottom: 20px;">
                    <label for="ngay_ket_thuc" style="display: block; margin-bottom: 5px; color: #4a5568;">Ngày kết thúc</label>
                    <input type="text" class="form-control @error('ngay_ket_thuc') is-invalid @enderror" 
                        id="ngay_ket_thuc" name="ngay_ket_thuc" 
                        value="{{ old('ngay_ket_thuc') }}"
                        placeholder="Chọn ngày kết thúc" required
                        style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                    @error('ngay_ket_thuc')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div style="margin-bottom: 20px;">
                    <label for="nhom_id" style="display: block; margin-bottom: 5px; color: #4a5568;">Chọn nhóm</label>
                    <select name="nhom_id" id="nhom_id" 
                        class="form-control @error('nhom_id') is-invalid @enderror"
                        style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                        <option value="">-- Chọn nhóm --</option>
                        @foreach($nhoms as $nhom)
                        <option value="{{ $nhom->id }}" {{ old('nhom_id') == $nhom->id ? 'selected' : '' }}>
                            {{ $nhom->ten }}
                        </option>
                        @endforeach
                    </select>
                    @error('nhom_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div style="text-align: right;">
                    <button type="submit" style="padding: 10px 20px; background-color: #4299e1; color: white; border: none; border-radius: 4px; cursor: pointer;">
                        <i class="fas fa-save"></i> Lưu
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Cấu hình Flatpickr cho ngày bắt đầu
            const ngayBatDau = flatpickr("#ngay_bat_dau", {
                locale: "vi",
                dateFormat: "Y-m-d",
                minDate: "today",
                onChange: function(selectedDates, dateStr) {
                    // Cập nhật minDate của ngày kết thúc
                    ngayKetThuc.set("minDate", dateStr);
                }
            });

            // Cấu hình Flatpickr cho ngày kết thúc
            const ngayKetThuc = flatpickr("#ngay_ket_thuc", {
                locale: "vi",
                dateFormat: "Y-m-d",
                minDate: "today"
            });

            // Validate form trước khi submit
            document.getElementById('createForm').addEventListener('submit', function(e) {
                const ngayBatDau = document.getElementById('ngay_bat_dau').value;
                const ngayKetThuc = document.getElementById('ngay_ket_thuc').value;

                if (!ngayBatDau || !ngayKetThuc) {
                    e.preventDefault();
                    alert('Vui lòng điền đầy đủ thông tin!');
                    return;
                }

                const batDau = new Date(ngayBatDau);
                const ketThuc = new Date(ngayKetThuc);

                if (batDau >= ketThuc) {
                    e.preventDefault();
                    alert('Ngày kết thúc phải sau ngày bắt đầu!');
                    return;
                }
            });
        });
    </script>
@endsection 