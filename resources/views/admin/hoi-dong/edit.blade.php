@extends('admin.layout')

@section('title', 'Chỉnh sửa hội đồng')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Sửa hội đồng</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.hoi-dong.update', $hoiDong->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label for="ten">Tên hội đồng <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('ten') is-invalid @enderror" 
                                id="ten" name="ten" value="{{ old('ten', $hoiDong->ten) }}" required>
                            @error('ten')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="dot_bao_cao_id">Đợt báo cáo <span class="text-danger">*</span></label>
                            <select class="form-control @error('dot_bao_cao_id') is-invalid @enderror" 
                                id="dot_bao_cao_id" name="dot_bao_cao_id" required>
                                <option value="">Chọn đợt báo cáo</option>
                                @foreach($dotBaoCaos as $dotBaoCao)
                                    <option value="{{ $dotBaoCao->id }}" {{ old('dot_bao_cao_id', $hoiDong->dot_bao_cao_id) == $dotBaoCao->id ? 'selected' : '' }}>
                                        {{ $dotBaoCao->nam_hoc }}
                                    </option>
                                @endforeach
                            </select>
                            @error('dot_bao_cao_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="phong_id">Phòng <span class="text-danger">*</span></label>
                            <select class="form-control @error('phong_id') is-invalid @enderror" 
                                id="phong_id" name="phong_id" required>
                                <option value="">Chọn phòng</option>
                                @foreach($phongs as $phong)
                                    <option value="{{ $phong->id }}" {{ old('phong_id', $hoiDong->phong_id) == $phong->id ? 'selected' : '' }}>
                                        {{ $phong->ten_phong }}
                                    </option>
                                @endforeach
                            </select>
                            @error('phong_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary">Cập nhật</button>
                        <a href="{{ route('admin.hoi-dong.index') }}" class="btn btn-secondary">Quay lại</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 