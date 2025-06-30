@extends('admin.layout')

@section('title', 'Thêm phân công hội đồng')

@section('content')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <div style="max-width: 800px; margin: 0 auto;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h1 style="color: #2d3748; font-weight: 700;">Thêm phân công hội đồng</h1>
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
            <form action="{{ route('admin.phan-cong-hoi-dong.store') }}" method="POST">
                @csrf
                
                <div style="margin-bottom: 20px;">
                    <label for="hoi_dong_id" style="display: block; margin-bottom: 5px; color: #4a5568;">Hội đồng <span style="color: red">*</span></label>
                    <select name="hoi_dong_id" id="hoi_dong_id" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;" placeholder="Chọn hội đồng">
                        <option value="">Chọn hội đồng</option>
                        @foreach($hoiDongs as $hoiDong)
                            <option value="{{ $hoiDong->id }}" {{ old('hoi_dong_id', $selectedHoiDong ?? '') == $hoiDong->id ? 'selected' : '' }}>
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
                                // Nếu giảng viên đã được phân công trong hội đồng thì disable
                                if (in_array($taiKhoan->id, $giangViensDaPhanCong ?? [])) $disable = true;
                            @endphp
                            <option value="{{ $taiKhoan->id }}" {{ $disable ? 'disabled' : '' }}>
                                {{ $taiKhoan->ten }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div style="margin-bottom: 20px;">
                    <label for="vai_tro_id" style="display: block; margin-bottom: 5px; color: #4a5568;">Vai trò <span style="color: red">*</span></label>
                    <select name="vai_tro_id" id="vai_tro_id" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;" placeholder="Chọn vai trò">
                        <option value="">Chọn vai trò</option>
                        @foreach($vaiTros as $vaiTro)
                            @if($vaiTro->ten !== 'Giảng viên')
                                @php
                                    $disable = false;
                                    if (in_array($vaiTro->id, $vaiTrosDaPhanCong ?? []) && in_array($vaiTro->id, [$truongTieuBanId, $thuKyId])) $disable = true;
                                @endphp
                                <option value="{{ $vaiTro->id }}" {{ $disable ? 'disabled' : '' }}>
                                    {{ $vaiTro->ten }}
                                </option>
                            @endif
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
        document.getElementById('hoi_dong_id').addEventListener('change', function() {
            var hoiDongId = this.value;
            window.location.href = '?hoi_dong_id=' + hoiDongId;
        });
    </script>
@endsection 