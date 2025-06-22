@extends('components.giangvien.app')

@section('title', 'Chuyển đề tài cho nhóm')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Chuyển đề tài cho nhóm: {{ $nhom->ten }}</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('giangvien.nhom.changeDetai.submit', $nhom->id) }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="de_tai_id">Chọn đề tài mới <span class="text-danger">*</span></label>
                            <select name="de_tai_id" id="de_tai_id" class="form-control @error('de_tai_id') is-invalid @enderror" required>
                                <option value="">-- Chọn đề tài --</option>
                                @foreach($deTais as $deTai)
                                    <option value="{{ $deTai->id }}" {{ $nhom->de_tai_id == $deTai->id ? 'selected' : '' }}>
                                        {{ $deTai->ma_de_tai }} - {{ $deTai->ten_de_tai }}
                                    </option>
                                @endforeach
                            </select>
                            @error('de_tai_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mt-3">
                            <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                            <a href="{{ route('giangvien.nhom.index') }}" class="btn btn-secondary">Hủy</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 