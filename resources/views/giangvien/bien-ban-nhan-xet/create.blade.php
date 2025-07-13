@extends('components.giangvien.app')

@section('content')
<div class="container mt-4">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Biên bản nhận xét - Đề tài: {{ $deTai->ten_de_tai ?? '' }}</h4>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            <form method="POST" action="{{ route('giangvien.bien-ban-nhan-xet.store', $deTai->id) }}">
                @csrf
                <div class="mb-3">
                    <label class="fw-bold">Nhận xét về hình thức:</label>
                    <textarea name="hinh_thuc" class="form-control" rows="2" placeholder="Nhập nhận xét về hình thức...">{{ old('hinh_thuc') }}</textarea>
                </div>
                <div class="mb-3">
                    <label class="fw-bold">Tính cấp thiết của đề tài:</label>
                    <textarea name="cap_thiet" class="form-control" rows="2" placeholder="Nhập tính cấp thiết của đề tài...">{{ old('cap_thiet') }}</textarea>
                </div>
                <div class="mb-3">
                    <label class="fw-bold">Mục tiêu và nội dung:</label>
                    <textarea name="muc_tieu" class="form-control" rows="2" placeholder="Nhập mục tiêu và nội dung...">{{ old('muc_tieu') }}</textarea>
                </div>
                <div class="mb-3">
                    <label class="fw-bold">Tổng quan tài liệu và tài liệu tham khảo:</label>
                    <textarea name="tai_lieu" class="form-control" rows="2" placeholder="Nhập tổng quan tài liệu và tài liệu tham khảo...">{{ old('tai_lieu') }}</textarea>
                </div>
                <div class="mb-3">
                    <label class="fw-bold">Phương pháp nghiên cứu:</label>
                    <textarea name="phuong_phap" class="form-control" rows="2" placeholder="Nhập phương pháp nghiên cứu...">{{ old('phuong_phap') }}</textarea>
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
                                        <td><textarea name="cau_hoi[]" class="form-control" required placeholder="Nhập câu hỏi phản biện...">{{ $cauHoi }}</textarea></td>
                                        <td class="text-center">
                                            @if(count(old('cau_hoi')) > 1)
                                                <button type="button" class="btn btn-danger btn-sm remove-row">X</button>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td><textarea name="cau_hoi[]" class="form-control" required placeholder="Nhập câu hỏi phản biện..."></textarea></td>
                                    <td class="text-center"></td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                    <button type="button" class="btn btn-success btn-sm mb-3" id="add-row">Thêm câu hỏi</button>
                </div>
                <div class="mb-3">
                    <label class="fw-bold">Kết quả đạt được:</label>
                    <select name="ket_qua" class="form-control" required>
                        <option value="">-- Chọn kết quả --</option>
                        <option value="Đạt" {{ old('ket_qua') == 'Đạt' ? 'selected' : '' }}>Đạt</option>
                        <option value="Không đạt" {{ old('ket_qua') == 'Không đạt' ? 'selected' : '' }}>Không đạt</option>
                    </select>
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Lưu biên bản</button>
                    <a href="{{ route('giangvien.bien-ban-nhan-xet.select-detai') }}" class="btn btn-secondary">Quay lại</a>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    function updateRemoveButtons() {
        var rows = document.querySelectorAll('#cau-hoi-table tbody tr');
        var removeBtns = document.querySelectorAll('#cau-hoi-table .remove-row');
        if (rows.length <= 1) {
            removeBtns.forEach(btn => btn.style.display = 'none');
        } else {
            removeBtns.forEach(btn => btn.style.display = '');
        }
    }
    document.getElementById('add-row').onclick = function() {
        var table = document.getElementById('cau-hoi-table').getElementsByTagName('tbody')[0];
        var newRow = table.insertRow();
        var cell1 = newRow.insertCell(0);
        var cell2 = newRow.insertCell(1);
        cell1.innerHTML = '<textarea name="cau_hoi[]" class="form-control" required placeholder="Nhập câu hỏi phản biện..."></textarea>';
        cell2.innerHTML = '<button type="button" class="btn btn-danger btn-sm remove-row">X</button>';
        updateRemoveButtons();
    };
    document.addEventListener('click', function(e) {
        if(e.target && e.target.classList.contains('remove-row')) {
            e.target.closest('tr').remove();
            updateRemoveButtons();
        }
    });
    // Gọi khi load trang
    updateRemoveButtons();
</script>
@endsection 