@extends('admin.layout')
@section('title', 'Thêm mới phòng')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Thêm phòng mới</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.phong.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="ten_phong">Tên phòng <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('ten_phong') is-invalid @enderror" 
                                id="ten_phong" name="ten_phong" value="{{ old('ten_phong') }}" placeholder="Nhập tên phòng" required>
                            @error('ten_phong')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Thêm mới</button>
                            <a href="{{ route('admin.phong.index') }}" class="btn btn-secondary">Quay lại</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 