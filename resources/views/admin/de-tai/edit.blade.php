@extends('admin.layout')

@section('title', 'Sửa đề tài')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Sửa đề tài</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.de-tai.update', $deTai) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label for="ma_de_tai">Mã đề tài</label>
                            <input type="text" class="form-control @error('ma_de_tai') is-invalid @enderror" id="ma_de_tai" name="ma_de_tai" value="{{ old('ma_de_tai', $deTai->ma_de_tai) }}" placeholder="Nhập mã đề tài" required>
                            @error('ma_de_tai')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="ten_de_tai">Tên đề tài</label>
                            <input type="text" class="form-control @error('ten_de_tai') is-invalid @enderror" id="ten_de_tai" name="ten_de_tai" value="{{ old('ten_de_tai', $deTai->ten_de_tai) }}" placeholder="Nhập tên đề tài" required>
                            @error('ten_de_tai')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="mo_ta">Mô tả</label>
                            <textarea class="form-control @error('mo_ta') is-invalid @enderror" id="mo_ta" name="mo_ta" rows="3" placeholder="Nhập mô tả đề tài">{{ old('mo_ta', $deTai->mo_ta) }}</textarea>
                            @error('mo_ta')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
 
                        <div class="form-group">
                            <label for="y_kien_giang_vien">Ý kiến giảng viên</label>
                            <textarea class="form-control @error('y_kien_giang_vien') is-invalid @enderror" id="y_kien_giang_vien" name="y_kien_giang_vien" rows="3" placeholder="Nhập ý kiến giảng viên">{{ old('y_kien_giang_vien', $deTai->y_kien_giang_vien) }}</textarea>
                            @error('y_kien_giang_vien')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="ngay_bat_dau">Ngày bắt đầu</label>
                            <input type="date" class="form-control @error('ngay_bat_dau') is-invalid @enderror" id="ngay_bat_dau" name="ngay_bat_dau" value="{{ old('ngay_bat_dau', $deTai->ngay_bat_dau ? $deTai->ngay_bat_dau->format('Y-m-d') : '') }}" onfocus="this.showPicker()">
                            @error('ngay_bat_dau')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="ngay_ket_thuc">Ngày kết thúc</label>
                            <input type="date" class="form-control @error('ngay_ket_thuc') is-invalid @enderror" id="ngay_ket_thuc" name="ngay_ket_thuc" value="{{ old('ngay_ket_thuc', $deTai->ngay_ket_thuc ? $deTai->ngay_ket_thuc->format('Y-m-d') : '') }}" onfocus="this.showPicker()">
                            @error('ngay_ket_thuc')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="nhom_id">Chọn nhóm</label>
                            <select name="nhom_id" id="nhom_id" class="form-control @error('nhom_id') is-invalid @enderror">
                                <option value="">-- Chọn nhóm --</option>
                                @foreach($nhoms as $nhom)
                                <option value="{{ $nhom->id }}" {{ old('nhom_id', $deTai->nhom_id) == $nhom->id ? 'selected' : '' }}>
                                    {{ $nhom->ten }}
                                </option>
                                @endforeach
                            </select>
                            @error('nhom_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="giang_vien_id">Chọn giảng viên</label>
                            <select name="giang_vien_id" id="giang_vien_id" class="form-control @error('giang_vien_id') is-invalid @enderror">
                                <option value="">-- Chọn giảng viên --</option>
                                @foreach($giangViens as $giangVien)
                                <option value="{{ $giangVien->id }}" {{ old('giang_vien_id', $deTai->giang_vien_id) == $giangVien->id ? 'selected' : '' }}>
                                    {{ $giangVien->ten }}
                                </option>
                                @endforeach
                            </select>
                            @error('giang_vien_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="trang_thai">Trạng thái</label>
                            <select name="trang_thai" id="trang_thai" class="form-control @error('trang_thai') is-invalid @enderror" required>
                                <option value="0" {{ old('trang_thai', $deTai->trang_thai) == 0 ? 'selected' : '' }}>Chưa bắt đầu</option>
                                <option value="1" {{ old('trang_thai', $deTai->trang_thai) == 1 ? 'selected' : '' }}>Đang diễn ra</option>
                                <option value="2" {{ old('trang_thai', $deTai->trang_thai) == 2 ? 'selected' : '' }}>Đã kết thúc</option>
                                <option value="3" {{ old('trang_thai', $deTai->trang_thai) == 3 ? 'selected' : '' }}>Đã hủy</option>
                                <option value="4" {{ old('trang_thai', $deTai->trang_thai) == 4 ? 'selected' : '' }}>Đang chờ duyệt</option>
                            </select>
                            @error('trang_thai')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary">Cập nhật</button>
                        <a href="{{ route('admin.de-tai.index') }}" class="btn btn-secondary">Hủy</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 