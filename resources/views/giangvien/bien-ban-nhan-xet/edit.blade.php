@extends('components.giangvien.app')

@section('content')
<div class="container mt-4">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Sửa biên bản nhận xét - Đề tài: {{ $deTai->ten_de_tai ?? '' }}</h4>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            <form method="POST" action="{{ route('giangvien.bien-ban-nhan-xet.update', $deTai->id) }}">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label class="fw-bold">1. Nhận xét về hình thức:</label>
                    <textarea name="hinh_thuc" class="form-control" rows="2">{{ old('hinh_thuc', $bienBan->hinh_thuc) }}</textarea>
                </div>
                <div class="mb-3">
                    <label class="fw-bold">2.2. Tính cấp thiết của đề tài:</label>
                    <textarea name="cap_thiet" class="form-control" rows="2">{{ old('cap_thiet', $bienBan->cap_thiet) }}</textarea>
                </div>
                <div class="mb-3">
                    <label class="fw-bold">2.3. Mục tiêu và nội dung:</label>
                    <textarea name="muc_tieu" class="form-control" rows="2">{{ old('muc_tieu', $bienBan->muc_tieu) }}</textarea>
                </div>
                <div class="mb-3">
                    <label class="fw-bold">2.4. Tổng quan tài liệu và tài liệu tham khảo:</label>
                    <textarea name="tai_lieu" class="form-control" rows="2">{{ old('tai_lieu', $bienBan->tai_lieu) }}</textarea>
                </div>
                <div class="mb-3">
                    <label class="fw-bold">2.5. Phương pháp nghiên cứu:</label>
                    <textarea name="phuong_phap" class="form-control" rows="2">{{ old('phuong_phap', $bienBan->phuong_phap) }}</textarea>
                </div>
                <div class="mb-3">
                    <label class="fw-bold">2.6. Kết quả đạt được:</label>
                    <textarea name="ket_qua" class="form-control" rows="2">{{ old('ket_qua', $bienBan->ket_qua) }}</textarea>
                </div>
                <div class="mb-3">
                    <label class="fw-bold">Quá trình hoạt động đề tài</label>
                    <textarea name="qua_trinh_hoat_dong" class="form-control" rows="2">{{ old('qua_trinh_hoat_dong', $bienBan->qua_trinh_hoat_dong) }}</textarea>
                </div>
                <div class="mb-3">
                    <h5 class="fw-bold">Câu hỏi phản biện</h5>
                    <table class="table table-bordered align-middle" id="cau-hoi-table">
                        <thead class="table-light">
                            <tr>
                                <th width="90%">Câu hỏi</th>
                                <th width="10%"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(old('cau_hoi'))
                                @foreach(old('cau_hoi') as $i => $cauHoi)
                                    <tr>
                                        <td><textarea name="cau_hoi[]" class="form-control" required>{{ $cauHoi }}</textarea></td>
                                        <td class="text-center"><button type="button" class="btn btn-danger btn-sm remove-row">X</button></td>
                                    </tr>
                                @endforeach
                            @else
                                @foreach($bienBan->cauTraLois as $cauHoi)
                                    <tr>
                                        <td><textarea name="cau_hoi[]" class="form-control" required>{{ $cauHoi->cau_hoi }}</textarea></td>
                                        <td class="text-center"><button type="button" class="btn btn-danger btn-sm remove-row">X</button></td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                    <button type="button" class="btn btn-success btn-sm mb-3" id="add-row">Thêm câu hỏi</button>
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Cập nhật</button>
                    <a href="{{ route('giangvien.bien-ban-nhan-xet.show', $deTai->id) }}" class="btn btn-secondary">Quay lại</a>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    document.getElementById('add-row').onclick = function() {
        var table = document.getElementById('cau-hoi-table').getElementsByTagName('tbody')[0];
        var newRow = table.insertRow();
        var cell1 = newRow.insertCell(0);
        var cell2 = newRow.insertCell(1);
        cell1.innerHTML = '<textarea name="cau_hoi[]" class="form-control" required></textarea>';
        cell2.innerHTML = '<button type="button" class="btn btn-danger btn-sm remove-row">X</button>';
    };
    document.addEventListener('click', function(e) {
        if(e.target && e.target.classList.contains('remove-row')) {
            e.target.closest('tr').remove();
        }
    });
</script>
@endsection 