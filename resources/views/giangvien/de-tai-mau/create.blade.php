@extends('components.giangvien.app')

@section('title', 'Thêm mẫu đề tài mới')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Thêm mẫu đề tài mới</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('giangvien.de-tai-mau.store') }}" method="POST">
                        @csrf
                        <div class="form-group mb-3">
                            <label for="ten">Tên mẫu</label>
                            <input type="text" class="form-control @error('ten') is-invalid @enderror" id="ten" name="ten" value="{{ old('ten') }}" placeholder="Nhập tên mẫu đề tài" required>
                            @error('ten')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Tạo mẫu</button>
                            <a href="{{ route('giangvien.de-tai-mau.index') }}" class="btn btn-secondary">Hủy</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 