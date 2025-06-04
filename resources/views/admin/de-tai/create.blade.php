@extends('admin.layout')

@section('title', 'Thêm đề tài mới')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Thêm đề tài mới</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.de-tai.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="ma_de_tai">Mã đề tài</label>
                            <input type="text" class="form-control @error('ma_de_tai') is-invalid @enderror" id="ma_de_tai" name="ma_de_tai" value="{{ old('ma_de_tai') }}" placeholder="Nhập mã đề tài" required>
                            @error('ma_de_tai')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="de_tai_mau_id">Chọn mẫu đề tài</label>
                            <select name="de_tai_mau_id" id="de_tai_mau_id" class="form-control @error('de_tai_mau_id') is-invalid @enderror" required>
                                <option value="">-- Chọn mẫu đề tài --</option>
                                @foreach($deTaiMaus as $deTaiMau)
                                <option value="{{ $deTaiMau->id }}" {{ old('de_tai_mau_id') == $deTaiMau->id ? 'selected' : '' }}>
                                    {{ $deTaiMau->ten }}
                                </option>
                                @endforeach
                            </select>
                            @error('de_tai_mau_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="mo_ta">Mô tả</label>
                            <textarea class="form-control @error('mo_ta') is-invalid @enderror" id="mo_ta" name="mo_ta" rows="3" placeholder="Nhập mô tả đề tài">{{ old('mo_ta') }}</textarea>
                            @error('mo_ta')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="ngay_bat_dau">Ngày bắt đầu</label>
                            <input type="date" class="form-control @error('ngay_bat_dau') is-invalid @enderror" id="ngay_bat_dau" name="ngay_bat_dau" value="{{ old('ngay_bat_dau') }}" onfocus="this.showPicker()">
                            @error('ngay_bat_dau')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="ngay_ket_thuc">Ngày kết thúc</label>
                            <input type="date" class="form-control @error('ngay_ket_thuc') is-invalid @enderror" id="ngay_ket_thuc" name="ngay_ket_thuc" value="{{ old('ngay_ket_thuc') }}" onfocus="this.showPicker()">
                            @error('ngay_ket_thuc')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="nhom_id">Chọn nhóm</label>
                            <select name="nhom_id" id="nhom_id" class="form-control @error('nhom_id') is-invalid @enderror">
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

                        <div class="form-group">
                            <label for="giang_vien_id">Chọn giảng viên</label>
                            <select name="giang_vien_id" id="giang_vien_id" class="form-control @error('giang_vien_id') is-invalid @enderror">
                                <option value="">-- Chọn giảng viên --</option>
                                @foreach($giangViens as $giangVien)
                                <option value="{{ $giangVien->id }}" {{ old('giang_vien_id') == $giangVien->id ? 'selected' : '' }}>
                                    {{ $giangVien->ten }}
                                </option>
                                @endforeach
                            </select>
                            @error('giang_vien_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary">Tạo đề tài</button>
                        <a href="{{ route('admin.de-tai.index') }}" class="btn btn-secondary">Hủy</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 