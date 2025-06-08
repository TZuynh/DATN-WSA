@extends('components.giangvien.app')
@section('title', 'Sửa thông tin sinh viên')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h3 class="card-title mb-0 text-primary">
                        <i class="fas fa-user-edit me-2"></i>Sửa thông tin sinh viên
                    </h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('giangvien.sinh-vien.update', $sinhVien) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="mssv" class="form-label">Mã số sinh viên (<span class="text-danger">*</span>)</label>
                                    <input type="text" class="form-control bg-light @error('mssv') is-invalid @enderror" id="mssv" name="mssv" value="{{ old('mssv', $sinhVien->mssv) }}" required readonly style="cursor: not-allowed;" placeholder="Mã số sinh viên">
                                    @error('mssv')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="ten" class="form-label">Họ tên (<span class="text-danger">*</span>)</label>
                                    <input type="text" class="form-control @error('ten') is-invalid @enderror" id="ten" name="ten" value="{{ old('ten', $sinhVien->ten) }}" required placeholder="Nhập họ và tên sinh viên">
                                    @error('ten')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="lop_id" class="form-label">Lớp (<span class="text-danger">*</span>)</label>
                                    <select class="form-select @error('lop_id') is-invalid @enderror" id="lop_id" name="lop_id" required>
                                        <option value="">Chọn lớp</option>
                                        @foreach($lops as $lop)
                                            <option value="{{ $lop->id }}" {{ old('lop_id', $sinhVien->lop_id) == $lop->id ? 'selected' : '' }}>
                                                {{ $lop->ten_lop }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('lop_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="fas fa-save me-2"></i>Cập nhật
                            </button>
                            <a href="{{ route('giangvien.sinh-vien.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Hủy
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 