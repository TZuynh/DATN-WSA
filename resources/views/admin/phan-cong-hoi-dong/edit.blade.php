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
                    <label for="hoi_dong_id" style="display: block; margin-bottom: 5px; color: #4a5568;">Hội đồng</label>
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
                    <label for="tai_khoan_id" style="display: block; margin-bottom: 5px; color: #4a5568;">Giảng viên</label>
                    <select name="tai_khoan_id" id="tai_khoan_id" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;" placeholder="Chọn giảng viên">
                        <option value="">Chọn giảng viên</option>
                        @foreach($taiKhoans as $taiKhoan)
                            <option value="{{ $taiKhoan->id }}" {{ old('tai_khoan_id', $phanCongVaiTro->tai_khoan_id) == $taiKhoan->id ? 'selected' : '' }}>
                                {{ $taiKhoan->ten }}
                                @if(in_array($taiKhoan->id, $giangVienCoDeTai))
                                    (Đang có đề tài)
                                @endif
                            </option>
                        @endforeach
                    </select>
                </div>

                <div style="margin-bottom: 20px;">
                    <label for="vai_tro_id" style="display: block; margin-bottom: 5px; color: #4a5568;">Vai trò</label>
                    <select name="vai_tro_id" id="vai_tro_id" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;" placeholder="Chọn vai trò">
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

                <div style="text-align: right;">
                    <button type="submit" style="padding: 10px 20px; background-color: #4299e1; color: white; border: none; border-radius: 4px; cursor: pointer;">
                        <i class="fas fa-save"></i> Lưu
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('tai_khoan_id').addEventListener('change', function() {
            const selectedGiangVienId = this.value;
            const vaiTroSelect = document.getElementById('vai_tro_id');
            const giangVienCoDeTai = @json($giangVienCoDeTai);
            const vaiTroKhongDuocPhanCong = @json($vaiTroKhongDuocPhanCong);
            const currentVaiTroId = {{ $phanCongVaiTro->vai_tro_id }};

            // Enable/disable các vai trò dựa trên giảng viên được chọn
            Array.from(vaiTroSelect.options).forEach(option => {
                if (option.value === '') return; // Bỏ qua option mặc định

                if (in_array(parseInt(option.value), vaiTroKhongDuocPhanCong) && 
                    in_array(parseInt(selectedGiangVienId), giangVienCoDeTai)) {
                    option.disabled = true;
                    // Nếu vai trò hiện tại bị disable, reset về giá trị mặc định
                    if (parseInt(option.value) === currentVaiTroId) {
                        vaiTroSelect.value = '';
                    }
                } else {
                    option.disabled = false;
                }
            });
        });

        // Helper function để kiểm tra giá trị trong mảng
        function in_array(needle, haystack) {
            return haystack.indexOf(needle) !== -1;
        }
    </script>
@endsection 