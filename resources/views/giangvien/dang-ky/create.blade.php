@extends('components.giangvien.app')
@section('title', 'Thêm đăng ký giảng viên')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Thêm đăng ký hướng dẫn</h3>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    @if (session('error'))
                         <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form action="{{ route('giangvien.dang-ky.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="sinh_vien_ids" class="form-label">Chọn sinh viên</label>
                            <select name="sinh_vien_ids[]" id="sinh_vien_ids"
                            class="form-select @error('sinh_vien_ids') is-invalid @enderror @error('sinh_vien_ids.*') is-invalid @enderror"
                            multiple size="10" required>
                            @foreach($sinhViens as $sinhVien)
                                <option value="{{ $sinhVien->id }}" {{ in_array($sinhVien->id, old('sinh_vien_ids', [])) ? 'selected' : '' }}>
                                    {{ $sinhVien->ten }} - {{ $sinhVien->mssv }}
                                </option>
                            @endforeach
                        </select>
                            @error('sinh_vien_ids')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                             @error('sinh_vien_ids.*')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="giang_vien_id" class="form-label">Chọn giảng viên</label>
                            <select name="giang_vien_id" id="giang_vien_id" class="form-select @error('giang_vien_id') is-invalid @enderror" required>
                                <option value="">-- Chọn giảng viên --</option>
                                @foreach($giangViens as $giangVien)
                                    <option value="{{ $giangVien->id }}" {{ old('giang_vien_id') == $giangVien->id ? 'selected' : '' }}>
                                        {{ $giangVien->ten }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Lưu
                            </button>
                            <a href="{{ route('giangvien.dang-ky.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Quay lại
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
