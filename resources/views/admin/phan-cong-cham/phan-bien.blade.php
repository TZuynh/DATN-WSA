@extends('admin.layout')

@section('content')
<div class="container">
    <h2>Phân công giảng viên phản biện cho đề tài</h2>
    <form method="POST" action="{{ route('admin.phan-cong-cham.phan-bien.store') }}">
        @csrf
        <div class="form-group">
            <label for="de_tai_id">Chọn đề tài:</label>
            <select id="de_tai_id" name="de_tai_id" class="form-control" required>
                <option value="">-- Chọn đề tài --</option>
                @foreach($deTais as $deTai)
                    <option value="{{ $deTai->id }}">{{ $deTai->ma_de_tai ?? '' }} - {{ $deTai->ten_de_tai }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="giang_vien_id">Chọn giảng viên phản biện:</label>
            <select id="giang_vien_id" name="giang_vien_id" class="form-control" required>
                <option value="">-- Chọn giảng viên --</option>
            </select>
        </div>
        <div class="form-group mt-3">
            <div id="danhSachGiangVien">
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Phân công</button>
    </form>
</div>
<script>
document.getElementById('de_tai_id').addEventListener('change', function() {
    var deTaiId = this.value;
    var giangVienSelect = document.getElementById('giang_vien_id');
    var danhSachDiv = document.getElementById('danhSachGiangVien');
    giangVienSelect.innerHTML = '<option value="">-- Đang tải --</option>';
    danhSachDiv.innerHTML = '<em>Đang tải danh sách giảng viên...</em>';
    if (deTaiId) {
        fetch('/admin/phan-cong-cham/giang-vien-hoi-dong/' + deTaiId)
            .then(response => {
                if (!response.ok) {
                    return response.text().then(text => { throw new Error(text); });
                }
                return response.json();
            })
            .then(data => {
                console.log('Dữ liệu giảng viên hội đồng trả về:', data);
                var members = Array.isArray(data) ? data : (data.members || []);
                giangVienSelect.innerHTML = '<option value="">-- Chọn giảng viên --</option>';
                if (members.length > 0) {
                    let html = '<ul class="list-group">';
                    members.forEach(function(gv) {
                        giangVienSelect.innerHTML += `<option value="${gv.id ?? ''}">${gv.ten} (${gv.vai_tro})</option>`;
                        html += `<li class="list-group-item">${gv.ten} <span class="badge bg-secondary">${gv.vai_tro}</span></li>`;
                    });
                    html += '</ul>';
                    danhSachDiv.innerHTML = html;
                } else {
                    danhSachDiv.innerHTML = '<em>Không có giảng viên trong hội đồng.</em>';
                }
            })
            .catch(error => {
                console.error('Lỗi khi gọi API giảng viên hội đồng:', error);
                danhSachDiv.innerHTML = '<em>Lỗi khi lấy danh sách giảng viên.</em>';
            });
    } else {
        giangVienSelect.innerHTML = '<option value="">-- Chọn giảng viên --</option>';
        danhSachDiv.innerHTML = '<em>Vui lòng chọn đề tài để xem danh sách giảng viên.</em>';
    }
});
</script>
@endsection 