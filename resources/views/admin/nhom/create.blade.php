@extends('admin.layout')
@section('title', 'Thêm nhóm mới')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Tạo nhóm mới</h3>
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

                    <form action="{{ route('admin.nhom.store') }}" method="POST">
                        @csrf
                        <div class="form-group mb-4">
                            <label for="ten" class="form-label">Tên nhóm <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('ten') is-invalid @enderror" 
                                id="ten" name="ten" value="{{ old('ten') }}" 
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
                                    <option value="{{ $sinhVien->id }}" {{ in_array($sinhVien->id, old('sinh_vien_ids', [])) ? 'selected' : '' }}>
                                        {{ $sinhVien->mssv }} - {{ $sinhVien->ten }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">Tối thiểu: 1 sinh viên, Tối đa: 3 sinh viên</small>
                            @error('sinh_vien_ids')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Tạo nhóm</button>
                            <a href="{{ route('admin.nhom.index') }}" class="btn btn-secondary">Quay lại</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 