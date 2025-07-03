@extends('components.giangvien.app')
@section('title', content: 'Tạo báo cáo quá trình')

@section('content')
<div class="container">
    <h1>Tạo báo cáo quá trình</h1>
    {{-- HIỂN THỊ LỖI VALIDATION (nếu có) --}}
    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form action="{{ route('giangvien.bao-cao-qua-trinh.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="nhom_id" class="form-label">Nhóm</label>
            <select name="nhom_id" id="nhom_id" class="form-control" required>
                <option value="">-- Chọn nhóm --</option>
                @foreach($nhoms as $nhom)
                    <option value="{{ $nhom->id }}" {{ old('nhom_id') == $nhom->id ? 'selected' : '' }}>
                        {{ $nhom->ten ?? $nhom->id }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label for="dot_bao_cao_id" class="form-label">Đợt báo cáo</label>
            <select name="dot_bao_cao_id" id="dot_bao_cao_id" class="form-control" required>
                <option value="">-- Chọn đợt báo cáo --</option>
                @foreach($dotBaoCaos as $dot)
                    <option value="{{ $dot->id }}" {{ old('dot_bao_cao_id') == $dot->id ? 'selected' : '' }}>
                        {{ $dot->nam_hoc ?? $dot->id }} - {{ $dot->hocKy->ten ?? 'N/A' }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label for="ngay_bao_cao" class="form-label">Ngày báo cáo</label>
            <input 
                type="text" 
                name="ngay_bao_cao" 
                id="ngay_bao_cao" 
                class="form-control @error('ngay_bao_cao') is-invalid @enderror" 
                value="{{ old('ngay_bao_cao') }}" 
                required 
                autocomplete="off"
                placeholder="Chọn ngày báo cáo">
            @error('ngay_bao_cao')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>        
        <div class="mb-3">
            <label for="noi_dung_bao_cao" class="form-label">Nội dung báo cáo</label>
            <textarea name="noi_dung_bao_cao" id="noi_dung_bao_cao" class="form-control">{{ old('noi_dung_bao_cao') }}</textarea>
        </div>
        <button type="submit" class="btn btn-success">Lưu</button>
        <a href="{{ route('giangvien.bao-cao-qua-trinh.index') }}" class="btn btn-secondary">Quay lại</a>
    </form>
</div>
@endsection

@push('scripts')
<!-- CKEditor -->
<script src="https://cdn.ckeditor.com/ckeditor5/40.1.0/classic/ckeditor.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        ClassicEditor.create(document.querySelector('#noi_dung_bao_cao'), {
            toolbar: {
                items: [
                    'heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', '|', 'blockQuote', 'insertTable', 'undo', 'redo'
                ]
            },
            language: 'vi',
            table: {
                contentToolbar: [ 'tableColumn', 'tableRow', 'mergeTableCells' ]
            }
        }).catch(error => { console.error(error); });

        // Flatpickr cho ngày giờ
        flatpickr('#ngay_bao_cao', {
            locale: 'vi',
            enableTime: false,
            dateFormat: 'Y-m-d',
            minDate: 'today'
        });
    });
</script>
@endpush
