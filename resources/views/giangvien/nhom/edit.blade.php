@extends('components.giangvien.app')
@section('title', content: 'Chỉnh sửa nhóm')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Chỉnh sửa nhóm</h3>
                </div>
                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('giangvien.nhom.update', $nhom) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="form-group mb-4">
                            <label for="ma_nhom" class="form-label">Mã nhóm</label>
                            <input type="text" class="form-control bg-light" 
                                id="ma_nhom" value="{{ $nhom->ma_nhom }}" readonly>
                            <small class="form-text text-muted">Mã nhóm không thể thay đổi</small>
                        </div>

                        <div class="form-group mb-4">
                            <label for="ten" class="form-label">Tên nhóm <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('ten') is-invalid @enderror" 
                                id="ten" name="ten" value="{{ old('ten', $nhom->ten) }}" 
                                placeholder="Nhập tên nhóm">
                            @error('ten')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-4">
                            <label for="sinh_vien_ids" class="form-label">Sinh viên <span class="text-danger">*</span></label>
                            <select class="form-control select2 @error('sinh_vien_ids') is-invalid @enderror" 
                                id="sinh_vien_ids" name="sinh_vien_ids[]" multiple="multiple">
                                @foreach($sinhViens as $sinhVien)
                                    <option value="{{ $sinhVien->id }}" 
                                        {{ in_array($sinhVien->id, old('sinh_vien_ids', $sinhVienIds)) ? 'selected' : '' }}>
                                        {{ $sinhVien->mssv }} - {{ $sinhVien->ten }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">Tối thiểu: 1 sinh viên, Tối đa: 3 sinh viên</small>
                            @error('sinh_vien_ids')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-4">
                            <label for="trang_thai" class="form-label">Trạng thái <span class="text-danger">*</span></label>
                            <select class="form-control @error('trang_thai') is-invalid @enderror" 
                                id="trang_thai" name="trang_thai">
                                <option value="hoat_dong" {{ old('trang_thai', $nhom->trang_thai) == 'hoat_dong' ? 'selected' : '' }}>
                                    Hoạt động
                                </option>
                                <option value="khong_hoat_dong" {{ old('trang_thai', $nhom->trang_thai) == 'khong_hoat_dong' ? 'selected' : '' }}>
                                    Không hoạt động
                                </option>
                            </select>
                            @error('trang_thai')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Cập nhật</button>
                            <a href="{{ route('giangvien.nhom.index') }}" class="btn btn-secondary">Quay lại</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 