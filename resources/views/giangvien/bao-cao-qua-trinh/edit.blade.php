@extends('components.giangvien.app')
@section('title', content: 'Sửa báo cáo quá trình')

@section('content')
<div class="container">
    <h1>Sửa báo cáo quá trình</h1>
    <form action="{{ route('giangvien.bao-cao-qua-trinh.update', $baoCao->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="nhom_id" class="form-label">Nhóm</label>
            <select name="nhom_id" id="nhom_id" class="form-control" required>
                <option value="">-- Chọn nhóm --</option>
                @foreach($nhoms as $nhom)
                    <option value="{{ $nhom->id }}" {{ $baoCao->nhom_id == $nhom->id ? 'selected' : '' }}>{{ $nhom->ten ?? $nhom->id }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label for="dot_bao_cao_id" class="form-label">Đợt báo cáo</label>
            <select name="dot_bao_cao_id" id="dot_bao_cao_id" class="form-control" required>
                <option value="">-- Chọn đợt báo cáo --</option>
                @foreach($dotBaoCaos as $dot)
                    <option value="{{ $dot->id }}" {{ $baoCao->dot_bao_cao_id == $dot->id ? 'selected' : '' }}>
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
                value="{{ old('ngay_bao_cao', isset($baoCao->ngay_bao_cao) ? \Carbon\Carbon::parse($baoCao->ngay_bao_cao)->format('Y-m-d') : '') }}"
                required
                autocomplete="off"
                placeholder="Chọn ngày báo cáo">
            @error('ngay_bao_cao')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <label for="noi_dung_bao_cao" class="form-label">Nội dung báo cáo</label>
            <textarea name="noi_dung_bao_cao" id="noi_dung_bao_cao" class="form-control">{{ old('noi_dung_bao_cao', $baoCao->noi_dung_bao_cao) }}</textarea>
        </div>
        <button type="submit" class="btn btn-success">Cập nhật</button>
        <a href="{{ route('giangvien.bao-cao-qua-trinh.index') }}" class="btn btn-secondary">Quay lại</a>
    </form>
</div>
@endsection

@push('scripts')
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

        flatpickr('#ngay_bao_cao', {
            locale: 'vi',
            enableTime: false,
            dateFormat: 'Y-m-d',
            minDate: 'today'
        });
    });
</script>
@endpush
