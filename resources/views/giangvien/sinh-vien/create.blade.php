@extends('components.giangvien.app')
@section('title', 'Thêm sinh viên mới')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h3 class="card-title mb-0 text-primary">
                        <i class="fas fa-user-plus me-2"></i>Thêm sinh viên mới
                    </h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('giangvien.sinh-vien.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="mssv" class="form-label">Mã số sinh viên (<span class="text-danger">*</span>)</label>
                                    <input type="text" class="form-control @error('mssv') is-invalid @enderror" id="mssv" name="mssv" value="{{ old('mssv') }}" required placeholder="Nhập mã số sinh viên (VD: 0306xxxxxx)">
                                    @error('mssv')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="ten" class="form-label">Họ tên (<span class="text-danger">*</span>)</label>
                                    <input type="text" class="form-control @error('ten') is-invalid @enderror" id="ten" name="ten" value="{{ old('ten') }}" required placeholder="Nhập họ và tên sinh viên">
                                    @error('ten')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="lop" class="form-label">Lớp</label>
                                    <input type="text" class="form-control @error('lop') is-invalid @enderror" id="lop" name="lop" value="{{ old('lop') }}" placeholder="Nhập tên lớp (VD: DCT1234)">
                                    @error('lop')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="nganh" class="form-label">Ngành</label>
                                    <input type="text" class="form-control @error('nganh') is-invalid @enderror" id="nganh" name="nganh" value="{{ old('nganh') }}" placeholder="Nhập tên ngành học">
                                    @error('nganh')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="khoa_hoc" class="form-label">Khóa học</label>
                                    <input type="text" class="form-control @error('khoa_hoc') is-invalid @enderror" id="khoa_hoc" name="khoa_hoc" value="{{ old('khoa_hoc') }}" placeholder="Nhập khóa học (VD: 2020-2024)">
                                    @error('khoa_hoc')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="fas fa-save me-2"></i>Lưu
                            </button>
                            <a href="{{ route('giangvien.sinh-vien.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Hủy
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
