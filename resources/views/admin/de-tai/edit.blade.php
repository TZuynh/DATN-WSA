@extends('admin.layout')

@section('title', 'Chỉnh sửa đề tài')

@section('content')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/vi.js"></script>

    <div style="padding: 20px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h1 style="color: #2d3748; font-weight: 700;">Chỉnh sửa đề tài</h1>
            <a href="{{ route('admin.de-tai.index') }}" style="padding: 10px 20px; background-color: #718096; color: white; border: none; border-radius: 4px; text-decoration: none;">
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
            <form action="{{ route('admin.de-tai.update', $deTai->id) }}" method="POST" id="editForm">
                @csrf
                @method('PUT')
                
                <input type="hidden" name="ngay_bat_dau" value="{{ old('ngay_bat_dau', $deTai->ngay_bat_dau ? $deTai->ngay_bat_dau->format('Y-m-d') : '') }}">
                <input type="hidden" name="ngay_ket_thuc" value="{{ old('ngay_ket_thuc', $deTai->ngay_ket_thuc ? $deTai->ngay_ket_thuc->format('Y-m-d') : '') }}">
                <input type="hidden" name="giang_vien_id" value="{{ old('giang_vien_id', $deTai->giang_vien_id) }}">
                <input type="hidden" name="trang_thai" value="{{ old('trang_thai', $deTai->trang_thai) }}">
                <input type="hidden" name="mo_ta" value="{{ old('mo_ta', $deTai->mo_ta) }}">
                <input type="hidden" name="y_kien_giang_vien" value="{{ old('y_kien_giang_vien', $deTai->y_kien_giang_vien) }}">
                <input type="hidden" name="nhom_id" value="{{ old('nhom_id', $deTai->nhom_id) }}">
                <input type="hidden" name="ma_de_tai" value="{{ old('ma_de_tai', $deTai->ma_de_tai) }}">

                <div style="margin-bottom: 20px;">
                    <label for="ma_de_tai" style="display: block; margin-bottom: 5px; color: #4a5568;">Mã đề tài</label>
                    <input type="text" class="form-control @error('ma_de_tai') is-invalid @enderror" 
                        id="ma_de_tai" name="ma_de_tai" 
                        value="{{ old('ma_de_tai', $deTai->ma_de_tai) }}" 
                        placeholder="Nhập mã đề tài" required
                        style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; background-color: #f3f4f6;" disabled>
                    @error('ma_de_tai')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div style="margin-bottom: 20px;">
                    <label for="ten_de_tai" style="display: block; margin-bottom: 5px; color: #4a5568;">Tên đề tài</label>
                    <input type="text" class="form-control @error('ten_de_tai') is-invalid @enderror" 
                        id="ten_de_tai" name="ten_de_tai" 
                        value="{{ old('ten_de_tai', $deTai->ten_de_tai) }}" 
                        placeholder="Nhập tên đề tài" required
                        style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                    @error('ten_de_tai')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div style="margin-bottom: 20px;">
                    <label for="mo_ta" style="display: block; margin-bottom: 5px; color: #4a5568;">Mô tả</label>
                    <div class="form-control" 
                        style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; background-color: #f3f4f6; overflow-y: auto; max-height: 200px; min-height: 100px;">
                        {{ strip_tags(old('mo_ta', $deTai->mo_ta)) }}
                    </div>
                    @error('mo_ta')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div style="margin-bottom: 20px;">
                    <label for="y_kien_giang_vien" style="display: block; margin-bottom: 5px; color: #4a5568;">Ý kiến giảng viên</label>
                    <div class="form-control" 
                        style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; background-color: #f3f4f6; overflow-y: auto; max-height: 200px; min-height: 100px;">
                        {{ strip_tags(old('y_kien_giang_vien', $deTai->y_kien_giang_vien)) }}
                    </div>
                    @error('y_kien_giang_vien')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div style="margin-bottom: 20px;">
                    <label for="ngay_bat_dau" style="display: block; margin-bottom: 5px; color: #4a5568;">Ngày bắt đầu <span style="color: red;">*</span></label>
                    <input type="text" class="form-control @error('ngay_bat_dau') is-invalid @enderror" 
                        id="ngay_bat_dau" name="ngay_bat_dau" 
                        value="{{ old('ngay_bat_dau', $deTai->ngay_bat_dau ? $deTai->ngay_bat_dau->format('Y-m-d') : '') }}"
                        placeholder="Chọn ngày bắt đầu" required
                        style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; background-color: #f3f4f6;" disabled>
                    @error('ngay_bat_dau')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div style="margin-bottom: 20px;">
                    <label for="ngay_ket_thuc" style="display: block; margin-bottom: 5px; color: #4a5568;">Ngày kết thúc <span style="color: red;">*</span></label>
                    <input type="text" class="form-control @error('ngay_ket_thuc') is-invalid @enderror" 
                        id="ngay_ket_thuc" name="ngay_ket_thuc" 
                        value="{{ old('ngay_ket_thuc', $deTai->ngay_ket_thuc ? $deTai->ngay_ket_thuc->format('Y-m-d') : '') }}"
                        placeholder="Chọn ngày kết thúc" required
                        style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; background-color: #f3f4f6;" disabled>
                    @error('ngay_ket_thuc')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div style="margin-bottom: 20px;">
                    <label for="nhom_id" style="display: block; margin-bottom: 5px; color: #4a5568;">Chọn nhóm</label>
                    <select name="nhom_id" id="nhom_id" 
                        class="form-control @error('nhom_id') is-invalid @enderror"
                        style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; background-color: #f3f4f6;" disabled>
                        <option value="">-- Chọn nhóm --</option>
                        @foreach($nhoms as $nhom)
                        <option value="{{ $nhom->id }}" {{ old('nhom_id', $deTai->nhom_id) == $nhom->id ? 'selected' : '' }}>
                            {{ $nhom->ten }}
                        </option>
                        @endforeach
                    </select>
                    @error('nhom_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div style="margin-bottom: 20px;">
                    <label for="giang_vien_id" style="display: block; margin-bottom: 5px; color: #4a5568;">Chọn giảng viên <span style="color: red;">*</span></label>
                    <select name="giang_vien_id" id="giang_vien_id" 
                        class="form-control @error('giang_vien_id') is-invalid @enderror" required
                        style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; background-color: #f3f4f6;" disabled>
                        <option value="">-- Chọn giảng viên --</option>
                        @foreach($giangViens as $giangVien)
                        <option value="{{ $giangVien->id }}" {{ old('giang_vien_id', $deTai->giang_vien_id) == $giangVien->id ? 'selected' : '' }}>
                            {{ $giangVien->ten }}
                        </option>
                        @endforeach
                    </select>
                    @error('giang_vien_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div style="margin-bottom: 20px;">
                    <label for="trang_thai" style="display: block; margin-bottom: 5px; color: #4a5568;">Trạng thái <span style="color: red;">*</span></label>
                    <select name="trang_thai" id="trang_thai" 
                        class="form-control @error('trang_thai') is-invalid @enderror" required
                        style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; background-color: #f3f4f6;" disabled>
                        <option value="">-- Chọn trạng thái --</option>
                        <option value="0" {{ old('trang_thai', $deTai->trang_thai) == 0 ? 'selected' : '' }}>Đang chờ duyệt</option>
                        <option value="1" {{ old('trang_thai', $deTai->trang_thai) == 1 ? 'selected' : '' }}>Đang thực hiện (giảng viên hướng dẫn đồng ý báo cáo)</option>
                        <option value="2" {{ old('trang_thai', $deTai->trang_thai) == 2 ? 'selected' : '' }}>Đang thực hiện (gi viên phản biện đồng ý báo cáo)</option>
                        <option value="3" {{ old('trang_thai', $deTai->trang_thai) == 3 ? 'selected' : '' }}>Không xảy ra (giảng viên hướng dẫn không đồng ý)</option>
                        <option value="4" {{ old('trang_thai', $deTai->trang_thai) == 4 ? 'selected' : '' }}>Không xảy ra (giảng viên phản biện không đồng ý)</option>
                    </select>
                    @error('trang_thai')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div style="text-align: right;">
                    <button type="submit" style="padding: 10px 20px; background-color: #4299e1; color: white; border: none; border-radius: 4px; cursor: pointer;">
                        <i class="fas fa-save"></i> Lưu thay đổi
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
            document.getElementById('editForm').addEventListener('submit', function(e) {
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