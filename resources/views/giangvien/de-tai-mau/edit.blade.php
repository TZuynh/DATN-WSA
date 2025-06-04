@extends('components.giangvien.app')

@section('title', 'Sửa mẫu đề tài')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Sửa mẫu đề tài</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('giangvien.de-tai-mau.update', $deTaiMau) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="form-group mb-3">
                            <label for="ten">Tên mẫu</label>
                            <input type="text" class="form-control @error('ten') is-invalid @enderror" id="ten" name="ten" value="{{ old('ten', $deTaiMau->ten) }}" placeholder="Nhập tên mẫu đề tài" required>
                            @error('ten')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Cập nhật</button>
                            <a href="{{ route('giangvien.de-tai-mau.index') }}" class="btn btn-secondary">Hủy</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 