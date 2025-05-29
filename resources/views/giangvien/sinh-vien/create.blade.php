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
                        <div class="mb-3">
                            <label for="mssv" class="form-label">Mã số sinh viên (<span class="text-danger">*</span>)</label>
                            <input type="text" class="form-control @error('mssv') is-invalid @enderror" id="mssv" name="mssv" value="{{ old('mssv') }}" required>
                            @error('mssv')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="ten" class="form-label">Họ tên (<span class="text-danger">*</span>)</label>
                            <input type="text" class="form-control @error('ten') is-invalid @enderror" id="ten" name="ten" value="{{ old('ten') }}" required>
                            @error('ten')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-save me-2"></i>Lưu
                        </button>
                        <a href="{{ route('giangvien.sinh-vien.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Hủy
                        </a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
