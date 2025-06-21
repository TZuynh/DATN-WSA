@extends('admin.layout')

@section('title', 'Chỉnh sửa đợt báo cáo')

@section('content')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/vi.js"></script>

    <div style="padding: 20px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h1 style="font-size: 24px; color: #2d3748;">Chỉnh sửa đợt báo cáo</h1>
            <a href="{{ route('admin.dot-bao-cao.index') }}" 
               style="padding: 10px 20px; background-color: #718096; color: white; border-radius: 4px; text-decoration: none;">
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

        <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <form action="{{ route('admin.dot-bao-cao.update', $dotBaoCao->id) }}" method="POST" id="editForm">
                @csrf
                @method('PUT')
                
                <div style="display: flex; gap: 20px; margin-bottom: 20px;">
                    <div style="flex: 1;">
                        <label for="nam_hoc" style="display: block; margin-bottom: 5px; color: #4a5568;">Năm học</label>
                        <input type="number" name="nam_hoc" id="nam_hoc" value="{{ old('nam_hoc', $dotBaoCao->nam_hoc) }}"
                            style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; background-color: #f7fafc;"
                            readonly>
                        <small style="color: #718096; font-size: 0.875rem;">Năm học được tự động cập nhật theo năm hiện tại</small>
                    </div>
                    <div style="flex: 1;">
                        <label for="hoc_ky_id" style="display: block; margin-bottom: 5px; color: #4a5568;">Học kỳ</label>
                        <select name="hoc_ky_id" id="hoc_ky_id" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; background-color: #f7fafc;" required>
                            <option value="">-- Chọn học kỳ --</option>
                            @foreach($hocKys as $hocKy)
                                <option value="{{ $hocKy->id }}" {{ old('hoc_ky_id', $dotBaoCao->hoc_ky_id) == $hocKy->id ? 'selected' : '' }}>{{ $hocKy->ten }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div style="margin-bottom: 20px;">
                    <label for="ngay_bat_dau" style="display: block; margin-bottom: 5px; color: #4a5568;">Ngày bắt đầu</label>
                    <input type="text" name="ngay_bat_dau" id="ngay_bat_dau" 
                        value="{{ old('ngay_bat_dau', $dotBaoCao->ngay_bat_dau ? date('Y-m-d', strtotime($dotBaoCao->ngay_bat_dau)) : '') }}"
                        style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;"
                        placeholder="Chọn ngày bắt đầu" required>
                    @error('ngay_bat_dau')
                        <small style="color: #e53e3e; font-size: 0.875rem;">{{ $message }}</small>
                    @enderror
                </div>

                <div style="margin-bottom: 20px;">
                    <label for="ngay_ket_thuc" style="display: block; margin-bottom: 5px; color: #4a5568;">Ngày kết thúc</label>
                    <input type="text" name="ngay_ket_thuc" id="ngay_ket_thuc" 
                        value="{{ old('ngay_ket_thuc', $dotBaoCao->ngay_ket_thuc ? date('Y-m-d', strtotime($dotBaoCao->ngay_ket_thuc)) : '') }}"
                        style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;"
                        placeholder="Chọn ngày kết thúc" required>
                    @error('ngay_ket_thuc')
                        <small style="color: #e53e3e; font-size: 0.875rem;">{{ $message }}</small>
                    @enderror
                </div>

                <div style="margin-bottom: 20px;">
                    <label for="mo_ta" style="display: block; margin-bottom: 5px; color: #4a5568;">Mô tả</label>
                    <textarea name="mo_ta" id="mo_ta" rows="4" 
                        style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;"
                        placeholder="Nhập mô tả ngắn về đợt báo cáo">{{ old('mo_ta', $dotBaoCao->mo_ta) }}</textarea>
                    @error('mo_ta')
                        <small style="color: #e53e3e; font-size: 0.875rem;">{{ $message }}</small>
                    @enderror
                </div>

                <div style="margin-bottom: 20px;">
                    <label for="trang_thai" style="display: block; margin-bottom: 5px; color: #4a5568;">Trạng thái</label>
                    <select name="trang_thai" id="trang_thai" 
                        style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;"
                        {{ $dotBaoCao->trang_thai === 'da_ket_thuc' ? 'disabled' : '' }}>
                        <option value="chua_bat_dau" {{ $dotBaoCao->trang_thai === 'chua_bat_dau' ? 'selected' : '' }}>Chưa bắt đầu</option>
                        <option value="dang_dien_ra" {{ $dotBaoCao->trang_thai === 'dang_dien_ra' ? 'selected' : '' }}>Đang diễn ra</option>
                        <option value="da_ket_thuc" {{ $dotBaoCao->trang_thai === 'da_ket_thuc' ? 'selected' : '' }}>Đã kết thúc</option>
                        <option value="da_huy" {{ $dotBaoCao->trang_thai === 'da_huy' ? 'selected' : '' }}>Đã hủy</option>
                    </select>
                    @error('trang_thai')
                        <small style="color: #e53e3e; font-size: 0.875rem;">{{ $message }}</small>
                    @enderror
                    @if($dotBaoCao->trang_thai === 'da_ket_thuc')
                        <small style="color: #718096; font-size: 0.875rem;">Không thể thay đổi trạng thái của đợt báo cáo đã kết thúc</small>
                    @endif
                </div>

                <div style="display: flex; gap: 10px;">
                    <button type="submit" 
                        style="padding: 10px 20px; background-color: #4299e1; color: white; border: none; border-radius: 4px; cursor: pointer;">
                        <i class="fas fa-save"></i> Lưu thay đổi
                    </button>
                    <a href="{{ route('admin.dot-bao-cao.index') }}" 
                       style="padding: 10px 20px; background-color: #718096; color: white; border-radius: 4px; text-decoration: none;">
                        Hủy
                    </a>
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
                onChange: function(selectedDates, dateStr) {
                    // Cập nhật minDate của ngày kết thúc
                    ngayKetThuc.set("minDate", dateStr);
                    
                    // Cập nhật năm học theo năm của ngày bắt đầu
                    const namBatDau = selectedDates[0].getFullYear();
                    document.getElementById('nam_hoc').value = namBatDau;
                }
            });

            // Cấu hình Flatpickr cho ngày kết thúc
            const ngayKetThuc = flatpickr("#ngay_ket_thuc", {
                locale: "vi",
                dateFormat: "Y-m-d"
            });

            // Validate form trước khi submit
            document.getElementById('editForm').addEventListener('submit', function(e) {
                const ngayBatDau = document.getElementById('ngay_bat_dau').value;
                const ngayKetThuc = document.getElementById('ngay_ket_thuc').value;
                
                if (!ngayBatDau || !ngayKetThuc) {
                    e.preventDefault();
                    alert('Vui lòng chọn đầy đủ ngày bắt đầu và ngày kết thúc.');
                    return;
                }

                if (new Date(ngayKetThuc) <= new Date(ngayBatDau)) {
                    e.preventDefault();
                    alert('Ngày kết thúc phải sau ngày bắt đầu.');
                    return;
                }
            });
        });
    </script>
@endsection 