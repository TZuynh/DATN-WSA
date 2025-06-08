@extends('components.giangvien.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Sửa thông tin lớp</h5>
                </div>

                <div class="card-body">
                    <form action="{{ route('giangvien.lop.update', $lop) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label for="ten_lop">Tên lớp <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('ten_lop') is-invalid @enderror" 
                                id="ten_lop" name="ten_lop" value="{{ old('ten_lop', $lop->ten_lop) }}" 
                                placeholder="Nhập tên lớp" required>
                            @error('ten_lop')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mt-3">
                            <button type="submit" class="btn btn-primary">Cập nhật</button>
                            <a href="{{ route('giangvien.lop.index') }}" class="btn btn-secondary">Quay lại</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 