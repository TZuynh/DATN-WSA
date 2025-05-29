@extends('components.giangvien.app')
@section('title', 'Chỉnh sửa đăng ký giảng viên')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Chỉnh sửa đăng ký hướng dẫn</h3>
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

                    <form action="{{ route('giangvien.dang-ky.update', $dangKy) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="giang_vien_id" class="form-label">Giảng viên hướng dẫn</label>
                            <input type="text" readonly class="form-control" value="{{ auth()->user()->ten }} - {{ auth()->user()->ma_gv ?? '' }}">
                        </div>


                        <div class="mb-3">
                            <label for="sinh_vien_id" class="form-label">Sinh viên</label>
                            <select name="sinh_vien_id" id="sinh_vien_id" class="form-select @error('sinh_vien_id') is-invalid @enderror">
                                <option value="">Chọn sinh viên</option>
                                @foreach($sinhViens as $sinhVien)
                                    <option value="{{ $sinhVien->id }}" {{ old('sinh_vien_id', $dangKy->sinh_vien_id) == $sinhVien->id ? 'selected' : '' }}>
                                        {{ $sinhVien->ten }} - {{ $sinhVien->mssv }}
                                    </option>
                                @endforeach
                            </select>
                            @error('sinh_vien_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="trang_thai" class="form-label">Trạng thái</label>
                            <select name="trang_thai" id="trang_thai" class="form-select @error('trang_thai') is-invalid @enderror">
                                @foreach($trangThais as $value => $label)
                                    <option value="{{ $value }}" {{ old('trang_thai', $dangKy->trang_thai) == $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('trang_thai')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
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
