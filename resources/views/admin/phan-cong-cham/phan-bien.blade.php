@extends('admin.layout')

@section('content')
<div class="container">
    <h2>Phân công giảng viên phản biện cho đề tài</h2>

    @if($deTais->isEmpty())
        <div class="alert alert-info">
            Không có đề tài nào cần phân công phản biện. 
            <br>
            Lưu ý: Chỉ những đề tài đã được giảng viên hướng dẫn đồng ý mới có thể phân công phản biện.
        </div>
    @else
        <form method="POST" action="{{ route('admin.phan-cong-cham.phan-bien.store') }}">
            @csrf
            <div class="form-group">
                <label for="de_tai_id">Chọn đề tài:</label>
                <select id="de_tai_id" name="de_tai_id" class="form-control" required>
                    <option value="">-- Chọn đề tài --</option>
                    @foreach($deTais as $deTai)
                        <option value="{{ $deTai->id }}">
                            {{ $deTai->ma_de_tai ?? '' }} - {{ $deTai->ten_de_tai }}
                            (GVHD: {{ optional($deTai->giangVien)->ten }})
                        </option>
                    @endforeach
                </select>
                <small class="form-text text-muted">
                    Chỉ hiển thị các đề tài đã được giảng viên hướng dẫn đồng ý và chưa có giảng viên phản biện
                </small>
            </div>

            <div class="form-group mt-3">
                <label for="giang_vien_id">Chọn giảng viên phản biện:</label>
                <select id="giang_vien_id" name="giang_vien_id" class="form-control" required>
                    <option value="">-- Chọn giảng viên --</option>
                    @foreach($giangViens as $gv)
                        <option value="{{ $gv->id }}">{{ $gv->ten }}</option>
                    @endforeach
                </select>
                <small class="form-text text-muted">Lưu ý: Giảng viên hướng dẫn không thể phản biện đề tài của chính mình</small>
            </div>

            <div class="form-group mt-3">
                <div id="thongTinDeTai" class="card d-none">
                    <div class="card-body">
                        <h5 class="card-title">Thông tin đề tài</h5>
                        <div id="chiTietDeTai"></div>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary mt-3">Phân công</button>
        </form>
    @endif
</div>

<script>
document.getElementById('de_tai_id')?.addEventListener('change', function() {
    var deTaiId = this.value;
    var giangVienSelect = document.getElementById('giang_vien_id');
    var thongTinDeTai = document.getElementById('thongTinDeTai');
    var chiTietDeTai = document.getElementById('chiTietDeTai');

    if (deTaiId) {
        // Lấy danh sách giảng viên có thể phản biện
        fetch('/admin/phan-cong-cham/giang-vien-hoi-dong/' + deTaiId)
            .then(response => response.json())
            .then(data => {
                // Cập nhật select giảng viên
                giangVienSelect.innerHTML = '<option value="">-- Chọn giảng viên --</option>';
                data.forEach(gv => {
                    giangVienSelect.innerHTML += `<option value="${gv.id}">${gv.ten}</option>`;
                });

                // Hiển thị thông tin đề tài
                const selectedDeTai = Array.from(this.options).find(option => option.value === deTaiId);
                if (selectedDeTai) {
                    thongTinDeTai.classList.remove('d-none');
                    chiTietDeTai.innerHTML = `<p><strong>Đề tài:</strong> ${selectedDeTai.text}</p>`;
                }
            })
            .catch(error => {
                console.error('Lỗi:', error);
                giangVienSelect.innerHTML = '<option value="">-- Lỗi khi tải danh sách giảng viên --</option>';
            });
    } else {
        giangVienSelect.innerHTML = '<option value="">-- Chọn giảng viên --</option>';
        thongTinDeTai.classList.add('d-none');
    }
});
</script>
@endsection 