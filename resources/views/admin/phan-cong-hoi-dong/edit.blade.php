@extends('admin.layout')

@section('title', 'Sửa phân công hội đồng')

@section('content')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <div style="max-width: 800px; margin: 0 auto;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h1 style="color: #2d3748; font-weight: 700;">Sửa phân công hội đồng</h1>
            <a href="{{ route('admin.phan-cong-hoi-dong.index') }}" style="padding: 10px 20px; background-color: #718096; color: white; border: none; border-radius: 4px; text-decoration: none;">
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
            <form action="{{ route('admin.phan-cong-hoi-dong.update', $phanCongVaiTro->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div style="margin-bottom: 20px;">
                    <label for="hoi_dong_id" style="display: block; margin-bottom: 5px; color: #4a5568;">Hội đồng <span style="color: red">*</span></label>
                    <select name="hoi_dong_id" id="hoi_dong_id" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;" placeholder="Chọn hội đồng">
                        <option value="">Chọn hội đồng</option>
                        @foreach($hoiDongs as $hoiDong)
                            <option value="{{ $hoiDong->id }}" {{ old('hoi_dong_id', $phanCongVaiTro->hoi_dong_id) == $hoiDong->id ? 'selected' : '' }}>
                                {{ $hoiDong->ten }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div style="margin-bottom: 20px;">
                    <label for="tai_khoan_id" style="display: block; margin-bottom: 5px; color: #4a5568;">Giảng viên <span style="color: red">*</span></label>
                    <select name="tai_khoan_id" id="tai_khoan_id" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;" placeholder="Chọn giảng viên">
                        <option value="">Chọn giảng viên</option>
                        @foreach($taiKhoans as $taiKhoan)
                            @php
                                $disable = false;
                                // Disable nếu giảng viên đã được phân công trong hội đồng này (trừ chính bản ghi đang sửa)
                                if (in_array($taiKhoan->id, $giangViensDaPhanCong ?? [])) $disable = true;
                            @endphp
                            <option value="{{ $taiKhoan->id }}" 
                                {{ old('tai_khoan_id', $phanCongVaiTro->tai_khoan_id) == $taiKhoan->id ? 'selected' : '' }}
                                {{ $disable ? 'disabled' : '' }}>
                                {{ $taiKhoan->ten }}
                                @if(in_array($taiKhoan->id, $giangVienCoDeTai))
                                    (Đang có đề tài)
                                @endif
                            </option>
                        @endforeach
                    </select>
                </div>

                <div style="margin-bottom: 20px;">
                    <label for="vai_tro_id" style="display: block; margin-bottom: 5px; color: #4a5568;">Vai trò <span style="color: red">*</span></label>
                    <select name="vai_tro_id" id="vai_tro_id" class="vai-tro-select" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;" placeholder="Chọn vai trò">
                        <option value="">Chọn vai trò</option>
                        @foreach($vaiTros as $vaiTro)
                            <option value="{{ $vaiTro->id }}" 
                                {{ old('vai_tro_id', $phanCongVaiTro->vai_tro_id) == $vaiTro->id ? 'selected' : '' }}
                                @if(in_array($vaiTro->id, $vaiTroKhongDuocPhanCong) && in_array(old('tai_khoan_id', $phanCongVaiTro->tai_khoan_id), $giangVienCoDeTai))
                                    disabled
                                @endif
                            >
                                {{ $vaiTro->ten }}
                                @if(in_array($vaiTro->id, $vaiTroKhongDuocPhanCong))
                                    (Không áp dụng cho giảng viên có đề tài)
                                @endif
                            </option>
                        @endforeach
                    </select>
                </div>

                <div id="loai_giang_vien_field" style="margin-bottom: 20px; display: none;">
                    <label for="loai_giang_vien" style="display: block; margin-bottom: 5px; color: #4a5568;">Loại giảng viên <span style="color: red">*</span></label>
                    <select name="loai_giang_vien" id="loai_giang_vien" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                        <option value="Giảng Viên Phản Biện" {{ old('loai_giang_vien', $phanCongVaiTro->loai_giang_vien) == 'Giảng Viên Phản Biện' ? 'selected' : '' }}>Giảng Viên Phản Biện</option>
                        <option value="Giảng Viên Khác" {{ old('loai_giang_vien', $phanCongVaiTro->loai_giang_vien) == 'Giảng Viên Khác' ? 'selected' : '' }}>Giảng Viên Khác</option>
                    </select>
                </div>
                <div id="loai_giang_vien_text" style="margin-bottom: 20px; display: none;">
                    <label style="display: block; margin-bottom: 5px; color: #4a5568;">Loại giảng viên</label>
                    <input type="text" value="Giảng Viên Hướng Dẫn" class="form-control" disabled>
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
        // Lấy id các vai trò đặc biệt từ backend
        const TRUONG_TIEU_BAN_ID = @json($vaiTros->where('ten', 'Trưởng tiểu ban')->first()->id ?? null);
        const THU_KY_ID = @json($vaiTros->where('ten', 'Thư ký')->first()->id ?? null);

        // Hàm cập nhật dropdown vai trò (nâng cao, dùng cho nhiều dòng nếu có)
        function updateVaiTroDropdowns() {
            let selectedSpecialRoles = [];
            document.querySelectorAll('.vai-tro-select').forEach(function(select) {
                let val = select.value;
                if (val && (val == TRUONG_TIEU_BAN_ID || val == THU_KY_ID)) {
                    selectedSpecialRoles.push(val);
                }
            });
            document.querySelectorAll('.vai-tro-select').forEach(function(select) {
                Array.from(select.options).forEach(function(option) {
                    if ((option.value == TRUONG_TIEU_BAN_ID || option.value == THU_KY_ID) &&
                        selectedSpecialRoles.includes(option.value) && select.value != option.value) {
                        option.style.display = 'none';
                    } else {
                        option.style.display = '';
                    }
                });
            });
        }
        // Nếu chỉ có 1 dropdown thì vẫn hoạt động bình thường
        document.querySelectorAll('.vai-tro-select').forEach(function(select) {
            select.addEventListener('change', updateVaiTroDropdowns);
        });
        // Gọi khi load trang
        updateVaiTroDropdowns();

        function updateLoaiGiangVienField() {
            var vaiTroSelect = document.getElementById('vai_tro_id');
            var giangVienSelect = document.getElementById('tai_khoan_id');
            var loaiGiangVienField = document.getElementById('loai_giang_vien_field');
            var loaiGiangVienText = document.getElementById('loai_giang_vien_text');
            var giangVienCoDeTai = @json($giangVienCoDeTai);
            var thanhVienId = @json($vaiTros->where('ten', 'Thành viên')->first()->id ?? null);

            if (vaiTroSelect.value == thanhVienId && giangVienSelect.value) {
                if (giangVienCoDeTai.includes(parseInt(giangVienSelect.value))) {
                    loaiGiangVienField.style.display = 'none';
                    loaiGiangVienText.style.display = 'block';
                } else {
                    loaiGiangVienField.style.display = 'block';
                    loaiGiangVienText.style.display = 'none';
                }
            } else {
                loaiGiangVienField.style.display = 'none';
                loaiGiangVienText.style.display = 'none';
            }
        }
        document.getElementById('vai_tro_id').addEventListener('change', updateLoaiGiangVienField);
        document.getElementById('tai_khoan_id').addEventListener('change', updateLoaiGiangVienField);
        window.addEventListener('DOMContentLoaded', updateLoaiGiangVienField);
    </script>
@endsection 