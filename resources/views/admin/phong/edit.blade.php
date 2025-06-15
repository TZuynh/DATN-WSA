@extends('admin.layout')
@section('title', 'Chỉnh sửa phòng')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="card-title mb-0 text-primary">
                            <i class="fas fa-edit me-2"></i>Sửa phòng
                        </h3>
                        <a href="{{ route('admin.phong.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Quay lại
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form action="{{ route('admin.phong.update', $phong) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label for="ten_phong">Tên phòng</label>
                            <input type="text" class="form-control @error('ten_phong') is-invalid @enderror" 
                                id="ten_phong" name="ten_phong" value="{{ old('ten_phong', $phong->ten_phong) }}" required>
                            @error('ten_phong')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-primary mt-3">
                            <i class="fas fa-save me-2"></i>Lưu thay đổi
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 