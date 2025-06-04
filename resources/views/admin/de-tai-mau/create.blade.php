@extends('admin.layout')

@section('title', 'Thêm đề tài mẫu mới')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Thêm đề tài mẫu mới</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.de-tai-mau.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="ten">Tên mẫu</label>
                            <input type="text" class="form-control @error('ten') is-invalid @enderror" id="ten" name="ten" value="{{ old('ten') }}" placeholder="Nhập tên mẫu đề tài" required>
                            @error('ten')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary">Tạo mẫu</button>
                        <a href="{{ route('admin.de-tai-mau.index') }}" class="btn btn-secondary">Hủy</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 