@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Chỉnh sửa đề tài mẫu</h3>
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

                    <form action="{{ route('admin.de-tai-mau.update', $deTaiMau) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label for="ten_de_tai">Tên đề tài <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('ten_de_tai') is-invalid @enderror" 
                                id="ten_de_tai" name="ten_de_tai" value="{{ old('ten_de_tai', $deTaiMau->ten_de_tai) }}" required>
                            @error('ten_de_tai')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="mo_ta">Mô tả <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('mo_ta') is-invalid @enderror" 
                                id="mo_ta" name="mo_ta" rows="3" required>{{ old('mo_ta', $deTaiMau->mo_ta) }}</textarea>
                            @error('mo_ta')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="yeu_cau">Yêu cầu <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('yeu_cau') is-invalid @enderror" 
                                id="yeu_cau" name="yeu_cau" rows="3" required>{{ old('yeu_cau', $deTaiMau->yeu_cau) }}</textarea>
                            @error('yeu_cau')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="tai_lieu_tham_khao">Tài liệu tham khảo <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('tai_lieu_tham_khao') is-invalid @enderror" 
                                id="tai_lieu_tham_khao" name="tai_lieu_tham_khao" rows="3" required>{{ old('tai_lieu_tham_khao', $deTaiMau->tai_lieu_tham_khao) }}</textarea>
                            @error('tai_lieu_tham_khao')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="so_luong_sinh_vien">Số lượng sinh viên <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('so_luong_sinh_vien') is-invalid @enderror" 
                                id="so_luong_sinh_vien" name="so_luong_sinh_vien" 
                                value="{{ old('so_luong_sinh_vien', $deTaiMau->so_luong_sinh_vien) }}" 
                                min="1" required>
                            @error('so_luong_sinh_vien')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="trang_thai">Trạng thái <span class="text-danger">*</span></label>
                            <select class="form-control @error('trang_thai') is-invalid @enderror" 
                                id="trang_thai" name="trang_thai" required>
                                <option value="active" {{ old('trang_thai', $deTaiMau->trang_thai) == 'active' ? 'selected' : '' }}>Hoạt động</option>
                                <option value="inactive" {{ old('trang_thai', $deTaiMau->trang_thai) == 'inactive' ? 'selected' : '' }}>Không hoạt động</option>
                            </select>
                            @error('trang_thai')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Cập nhật</button>
                            <a href="{{ route('admin.de-tai-mau.index') }}" class="btn btn-secondary">Hủy</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 