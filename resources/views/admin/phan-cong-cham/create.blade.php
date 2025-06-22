@extends('admin.layout')

@section('title', 'Thêm phân công chấm')

@section('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    .required-field::after { content: " *"; color: red; }
    #hoi-dong-info { display: none; margin-top: 20px; }
    .table-sm th, .table-sm td { padding: 0.4rem; }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Thêm phân công chấm</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.phan-cong-cham.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="de_tai_id" class="required-field">Đề tài</label>
                            <select name="de_tai_id" id="de_tai_id" class="form-control @error('de_tai_id') is-invalid @enderror" required>
                                <option value="">Chọn đề tài</option>
                                @foreach($deTais as $deTai)
                                    <option value="{{ $deTai->id }}" {{ old('de_tai_id') == $deTai->id ? 'selected' : '' }}>
                                        {{ $deTai->ma_de_tai }} - {{ $deTai->ten_de_tai }}
                                    </option>
                                @endforeach
                            </select>
                            @error('de_tai_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div id="hoi-dong-info">
                            <h5 id="hoi-dong-ten"></h5>
                            <table class="table table-bordered table-sm">
                                <thead>
                                    <tr>
                                        <th>Tên giảng viên</th>
                                        <th>Vai trò trong hội đồng</th>
                                        <th>Loại giảng viên</th>
                                    </tr>
                                </thead>
                                <tbody id="hoi-dong-members">
                                </tbody>
                            </table>
                        </div>

                        <div class="form-group">
                            <label for="lich_cham" class="required-field">Lịch chấm</label>
                            <input type="text" name="lich_cham" id="lich_cham" class="form-control @error('lich_cham') is-invalid @enderror" placeholder="Chọn lịch chấm" value="{{ old('lich_cham') }}" required>
                            @error('lich_cham')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Thêm mới</button>
                            <a href="{{ route('admin.phan-cong-cham.index') }}" class="btn btn-secondary">Quay lại</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/vi.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        flatpickr("#lich_cham", {
            locale: "vi",
            dateFormat: "Y-m-d H:i",
            enableTime: true,
            time_24hr: true,
            minDate: "today",
            placeholder: "Chọn lịch chấm",
        });

        const deTaiSelect = document.getElementById('de_tai_id');
        const hoiDongInfoDiv = document.getElementById('hoi-dong-info');
        const hoiDongTenEl = document.getElementById('hoi-dong-ten');
        const hoiDongMembersTbody = document.getElementById('hoi-dong-members');
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || document.querySelector('input[name="_token"]')?.value;

        function fetchHoiDongInfo(deTaiId) {
            if (!deTaiId) {
                hoiDongInfoDiv.style.display = 'none';
                return;
            }

            fetch('{{ route("admin.phan-cong-cham.getHoiDongInfo") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ de_tai_id: deTaiId })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.error) {
                    alert(data.error);
                    hoiDongInfoDiv.style.display = 'none';
                    return;
                }
                
                hoiDongTenEl.textContent = 'Hội đồng: ' + data.ten_hoi_dong;
                let membersHtml = '';
                data.members.forEach(member => {
                    membersHtml += `
                        <tr>
                            <td>${member.ten}</td>
                            <td>${member.vai_tro}</td>
                            <td>${member.loai_giang_vien}</td>
                        </tr>
                    `;
                });
                hoiDongMembersTbody.innerHTML = membersHtml;
                hoiDongInfoDiv.style.display = 'block';
            })
            .catch(error => {
                console.error('Error fetching council info:', error);
                alert('Có lỗi xảy ra khi tải thông tin hội đồng.');
                hoiDongInfoDiv.style.display = 'none';
            });
        }

        deTaiSelect.addEventListener('change', function() {
            fetchHoiDongInfo(this.value);
        });

        // Trigger on page load if a de_tai is already selected (e.g., from old input)
        if (deTaiSelect.value) {
            fetchHoiDongInfo(deTaiSelect.value);
        }
    });
</script>
@endpush 