@extends('admin.layout')
@section('title', 'Chỉnh sửa thông tin sinh viên')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Chỉnh sửa thông tin sinh viên</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.sinh-vien.index') }}" class="btn btn-default btn-sm">
                            <i class="fas fa-arrow-left"></i> Quay lại
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('admin.sinh-vien.update', $sinhVien) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label for="mssv">Mã số sinh viên <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('mssv') is-invalid @enderror" 
                                   id="mssv" name="mssv" value="{{ old('mssv', $sinhVien->mssv) }}" required>
                            <small class="form-text text-muted">
                                Mã số sinh viên phải bắt đầu bằng 0306 và có đủ 10 chữ số
                            </small>
                        </div>

                        <div class="form-group">
                            <label for="ten">Tên sinh viên <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('ten') is-invalid @enderror" 
                                   id="ten" name="ten" value="{{ old('ten', $sinhVien->ten) }}" required>
                        </div>

                        <div class="form-group">
                            <label for="lop_id">Lớp <span class="text-danger">*</span></label>
                            <select class="form-control @error('lop_id') is-invalid @enderror" 
                                    id="lop_id" name="lop_id" required>
                                <option value="">Chọn lớp</option>
                                @foreach($lops as $lop)
                                    <option value="{{ $lop->id }}" 
                                        {{ old('lop_id', $sinhVien->lop_id) == $lop->id ? 'selected' : '' }}>
                                        {{ $lop->ten_lop }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Cập nhật
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 