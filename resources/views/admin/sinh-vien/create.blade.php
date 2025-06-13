@extends('admin.layout')
@section('title', 'Thêm sinh viên mới')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Thêm sinh viên mới</h3>
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

                    <form action="{{ route('admin.sinh-vien.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="mssv">Mã số sinh viên <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('mssv') is-invalid @enderror" 
                                   id="mssv" name="mssv" value="{{ old('mssv') }}" 
                                   placeholder="Ví dụ: 03062" required>
                            @error('mssv')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="ten">Tên sinh viên <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('ten') is-invalid @enderror" 
                                   id="ten" name="ten" value="{{ old('ten') }}" placeholder="Nhập tên sinh viên" required>
                        </div>

                        <div class="form-group">
                            <label for="lop_id">Lớp <span class="text-danger">*</span></label>
                            <select class="form-control @error('lop_id') is-invalid @enderror" 
                                    id="lop_id" name="lop_id" required>
                                <option value="">Chọn lớp</option>
                                @foreach($lops as $lop)
                                    <option value="{{ $lop->id }}" {{ old('lop_id') == $lop->id ? 'selected' : '' }}>
                                        {{ $lop->ten_lop }}
                                    </option>
                                @endforeach
                            </select>
                            @error('lop_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Lưu
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 