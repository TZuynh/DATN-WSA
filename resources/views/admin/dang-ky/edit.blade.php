@extends('admin.layout')
@section('title', 'Chỉnh sửa đăng ký hướng dẫn')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Chỉnh sửa đăng ký hướng dẫn</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.dang-ky.update', $dangKy->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label for="sinh_vien_id" class="form-label">Sinh viên</label>
                            <select name="sinh_vien_id" id="sinh_vien_id" class="form-select @error('sinh_vien_id') is-invalid @enderror">
                                <option value="">Chọn sinh viên</option>
                                @foreach($sinhViens as $sinhVien)
                                    <option value="{{ $sinhVien->id }}" {{ (old('sinh_vien_id', $dangKy->sinh_vien_id) == $sinhVien->id) ? 'selected' : '' }}>
                                        {{ $sinhVien->mssv }} - {{ $sinhVien->ten }}
                                    </option>
                                @endforeach
                            </select>
                            @error('sinh_vien_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="giang_vien_id" class="form-label">Giảng viên hướng dẫn</label>
                            <select name="giang_vien_id" id="giang_vien_id" class="form-select @error('giang_vien_id') is-invalid @enderror">
                                <option value="">Chọn giảng viên</option>
                                @foreach($giangViens as $giangVien)
                                    <option value="{{ $giangVien->id }}" {{ (old('giang_vien_id', $dangKy->giang_vien_id) == $giangVien->id) ? 'selected' : '' }}>
                                        {{ $giangVien->ten }}
                                    </option>
                                @endforeach
                            </select>
                            @error('giang_vien_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="trang_thai" class="form-label">Trạng thái</label>
                            <select name="trang_thai" id="trang_thai" class="form-select @error('trang_thai') is-invalid @enderror">
                                @foreach($trangThais as $value => $label)
                                    <option value="{{ $value }}" {{ (old('trang_thai', $dangKy->trang_thai) == $value) ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('trang_thai')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary">Cập nhật</button>
                        <a href="{{ route('admin.dang-ky.index') }}" class="btn btn-secondary">Hủy</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 