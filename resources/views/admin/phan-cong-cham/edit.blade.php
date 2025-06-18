@extends('admin.layout')

@section('title', 'Chỉnh sửa phản biện')

@section('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    .required-field::after {
        content: " *";
        color: red;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Chỉnh sửa phản biện</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.phan-cong-cham.update', $phanCongCham) }}" method="POST" id="formPhanCongCham">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="giang_vien_huong_dan_id" id="giang_vien_huong_dan_id" value="{{ $phanCongCham->giang_vien_huong_dan_id }}">

                        <div class="form-group">
                            <label for="de_tai_id" class="required-field">Đề tài</label>
                            @if($deTaiCoLichCham)
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <strong>Cảnh báo:</strong> Đề tài này đã có lịch chấm. Việc thay đổi phản biện có thể ảnh hưởng đến lịch chấm hiện tại.
                                </div>
                            @endif
                            <select name="de_tai_id" id="de_tai_id" class="form-control @error('de_tai_id') is-invalid @enderror" required {{ $deTaiCoLichCham ? 'disabled' : '' }}>
                                <option value="">Chọn đề tài</option>
                                @foreach($deTais as $deTai)
                                    <option value="{{ $deTai->id }}" 
                                            data-giang-vien-id="{{ $deTai->giang_vien_id }}" 
                                            {{ old('de_tai_id', $phanCongCham->de_tai_id) == $deTai->id ? 'selected' : '' }}>
                                        {{ $deTai->ma_de_tai }} - {{ $deTai->ten_de_tai }}
                                    </option>
                                @endforeach
                            </select>
                            @if($deTaiCoLichCham)
                                <small class="form-text text-muted">
                                    Để thay đổi đề tài, vui lòng xóa lịch chấm hiện tại trước.
                                </small>
                            @endif
                            @error('de_tai_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Giảng viên hướng dẫn</label>
                            <input type="text" class="form-control" id="giang_vien_huong_dan" value="{{ $phanCongCham->giangVienHuongDan->ten }}" readonly>
                        </div>

                        <div class="form-group">
                            <label for="giang_vien_phan_bien_id" class="required-field">Giảng viên phản biện</label>
                            <select name="giang_vien_phan_bien_id" id="giang_vien_phan_bien_id" class="form-control @error('giang_vien_phan_bien_id') is-invalid @enderror" required>
                                <option value="">Chọn giảng viên phản biện</option>
                                @foreach($giangViens as $giangVien)
                                    <option value="{{ $giangVien->id }}" data-ten="{{ $giangVien->ten }}" {{ $giangVien->id == $phanCongCham->giang_vien_phan_bien_id ? 'selected' : '' }}>
                                        {{ $giangVien->ten }}
                                    </option>
                                @endforeach
                            </select>
                            @error('giang_vien_phan_bien_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="giang_vien_khac_id" class="required-field">Giảng viên khác</label>
                            <select name="giang_vien_khac_id" id="giang_vien_khac_id" class="form-control @error('giang_vien_khac_id') is-invalid @enderror" required>
                                <option value="">Chọn giảng viên khác</option>
                                @foreach($giangViens as $giangVien)
                                    <option value="{{ $giangVien->id }}" data-ten="{{ $giangVien->ten }}" {{ $giangVien->id == $phanCongCham->giang_vien_khac_id ? 'selected' : '' }}>
                                        {{ $giangVien->ten }}
                                    </option>
                                @endforeach
                            </select>
                            @error('giang_vien_khac_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="lich_cham" class="required-field">Lịch chấm</label>
                            <input type="text" name="lich_cham" id="lich_cham" 
                                   class="form-control @error('lich_cham') is-invalid @enderror" 
                                   placeholder="Chọn lịch chấm"
                                   value="{{ \Carbon\Carbon::parse($phanCongCham->lich_cham)->format('Y-m-d H:i') }}" required>
                            @error('lich_cham')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary" id="btnSubmit">Cập nhật</button>
                            <a href="{{ route('admin.phan-cong-cham.index') }}" class="btn btn-secondary">Quay lại</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/vi.js"></script>
<script>
    $(document).ready(function() {
        // Debug: Kiểm tra dữ liệu
        console.log('PhanCongCham ID:', {{ $phanCongCham->id }});
        console.log('DeTai ID:', {{ $phanCongCham->de_tai_id }});
        console.log('Selected option value:', $('#de_tai_id').val());
        console.log('Selected option text:', $('#de_tai_id option:selected').text());

        // Cấu hình Flatpickr cho lịch chấm
        const lichCham = flatpickr("#lich_cham", {
            locale: "vi",
            dateFormat: "Y-m-d H:i",
            enableTime: true,
            time_24hr: true,
            minDate: "today",
            minTime: "08:00",
            maxTime: "18:00",
            placeholder: "Chọn lịch chấm",
            allowInput: true
        });

        var giangVienHuongDanId = {{ $phanCongCham->giang_vien_huong_dan_id }};

        // Khi chọn đề tài, tự động điền giảng viên hướng dẫn
        $('#de_tai_id').change(function() {
            // Kiểm tra nếu select box bị disabled
            if ($(this).prop('disabled')) {
                return;
            }
            
            var selectedOption = $(this).find('option:selected');
            var newGiangVienHuongDanId = selectedOption.data('giang-vien-id');
            
            console.log('Change event triggered');
            console.log('Selected option:', selectedOption.text());
            console.log('New giangVienHuongDanId:', newGiangVienHuongDanId);
            
            if (newGiangVienHuongDanId) {
                // Lấy tên giảng viên từ danh sách giảng viên
                var giangVienTen = $('#giang_vien_phan_bien_id option[value="' + newGiangVienHuongDanId + '"]').data('ten');
                $('#giang_vien_huong_dan').val(giangVienTen);
                $('#giang_vien_huong_dan_id').val(newGiangVienHuongDanId);
                console.log('Giảng viên hướng dẫn mới ID:', newGiangVienHuongDanId); // Debug
                
                // Kiểm tra giảng viên phản biện và giảng viên khác
                var giangVienPhanBienId = $('#giang_vien_phan_bien_id').val();
                var giangVienKhacId = $('#giang_vien_khac_id').val();

                // Nếu giảng viên phản biện hoặc giảng viên khác trùng với giảng viên hướng dẫn mới
                if (giangVienPhanBienId == newGiangVienHuongDanId) {
                    alert('Giảng viên phản biện không thể là giảng viên hướng dẫn mới');
                    $('#giang_vien_phan_bien_id').val('');
                }
                if (giangVienKhacId == newGiangVienHuongDanId) {
                    alert('Giảng viên khác không thể là giảng viên hướng dẫn mới');
                    $('#giang_vien_khac_id').val('');
                }
                
                // Vô hiệu hóa giảng viên hướng dẫn trong các select box khác
                $('#giang_vien_phan_bien_id option, #giang_vien_khac_id option').each(function() {
                    if ($(this).val() == newGiangVienHuongDanId) {
                        $(this).prop('disabled', true);
                    } else {
                        $(this).prop('disabled', false);
                    }
                });

                // Cập nhật biến giangVienHuongDanId
                giangVienHuongDanId = newGiangVienHuongDanId;
            } else {
                // Reset khi không chọn đề tài
                giangVienHuongDanId = null;
                $('#giang_vien_huong_dan').val('');
                $('#giang_vien_huong_dan_id').val('');
                $('#giang_vien_phan_bien_id option, #giang_vien_khac_id option').prop('disabled', false);
            }
        });

        // Vô hiệu hóa giảng viên hướng dẫn trong các select box
        if (giangVienHuongDanId) {
            $('#giang_vien_phan_bien_id option[value="' + giangVienHuongDanId + '"], #giang_vien_khac_id option[value="' + giangVienHuongDanId + '"]').prop('disabled', true);
        }

        // Khi chọn giảng viên
        $('#giang_vien_phan_bien_id, #giang_vien_khac_id').change(function() {
            var selectedValue = $(this).val();
            var currentSelect = $(this).attr('id');
            
            console.log('Selected value:', selectedValue); // Debug
            console.log('Current select:', currentSelect); // Debug
            console.log('Selected option:', $(this).find('option:selected').text()); // Debug
            
            // Kiểm tra nếu chọn trùng với giảng viên hướng dẫn
            if (selectedValue == giangVienHuongDanId) {
                alert('Không thể chọn giảng viên hướng dẫn');
                $(this).val('');
                return;
            }
            
            // Reset trạng thái disabled của tất cả các option
            $('#giang_vien_phan_bien_id option, #giang_vien_khac_id option').prop('disabled', false);
            
            // Vô hiệu hóa giảng viên hướng dẫn trong tất cả các select box
            if (giangVienHuongDanId) {
                $('#giang_vien_phan_bien_id option[value="' + giangVienHuongDanId + '"], #giang_vien_khac_id option[value="' + giangVienHuongDanId + '"]').prop('disabled', true);
            }
            
            // Vô hiệu hóa giảng viên đã chọn trong các select box khác
            $('#giang_vien_phan_bien_id, #giang_vien_khac_id').each(function() {
                var value = $(this).val();
                if (value) {
                    // Vô hiệu hóa option trong tất cả các select box khác
                    $('#giang_vien_phan_bien_id option[value="' + value + '"], #giang_vien_khac_id option[value="' + value + '"]').each(function() {
                        if ($(this).parent().attr('id') != currentSelect) {
                            $(this).prop('disabled', true);
                        }
                    });
                }
            });

            // Kiểm tra trùng lặp
            var selectedValues = [];
            $('#giang_vien_phan_bien_id, #giang_vien_khac_id').each(function() {
                var value = $(this).val();
                if (value) {
                    if (selectedValues.includes(value)) {
                        alert('Các giảng viên không được trùng nhau');
                        $(this).val('');
                    } else {
                        selectedValues.push(value);
                    }
                }
            });
        });

        // Kiểm tra form trước khi submit
        $('#formPhanCongCham').submit(function(e) {
            e.preventDefault(); // Ngăn form submit mặc định

            // Lấy giá trị từ select box
            var deTai = $('#de_tai_id').val();
            var giangVienPhanBien = $('#giang_vien_phan_bien_id option:selected').val();
            var giangVienKhac = $('#giang_vien_khac_id option:selected').val();
            var lichCham = $('#lich_cham').val();
            var giangVienHuongDan = $('#giang_vien_huong_dan_id').val();

            // Debug chi tiết
            console.log('Select box values:', {
                deTai: {
                    val: $('#de_tai_id').val(),
                    selected: $('#de_tai_id option:selected').val(),
                    text: $('#de_tai_id option:selected').text()
                },
                giangVienPhanBien: {
                    val: $('#giang_vien_phan_bien_id').val(),
                    selected: $('#giang_vien_phan_bien_id option:selected').val(),
                    text: $('#giang_vien_phan_bien_id option:selected').text()
                },
                giangVienKhac: {
                    val: $('#giang_vien_khac_id').val(),
                    selected: $('#giang_vien_khac_id option:selected').val(),
                    text: $('#giang_vien_khac_id option:selected').text()
                }
            });

            var isValid = true;

            if (!deTai) {
                alert('Vui lòng chọn đề tài');
                isValid = false;
            }

            if (!giangVienPhanBien) {
                alert('Vui lòng chọn giảng viên phản biện');
                isValid = false;
            }

            if (!giangVienKhac) {
                alert('Vui lòng chọn giảng viên khác');
                isValid = false;
            }

            if (!lichCham) {
                alert('Vui lòng chọn lịch chấm');
                isValid = false;
            }

            if (!giangVienHuongDan) {
                alert('Vui lòng chọn đề tài để lấy giảng viên hướng dẫn');
                isValid = false;
            }

            if (isValid) {
                // Nếu tất cả đều hợp lệ, submit form
                var form = $(this);
                form.off('submit');
                
                // Đảm bảo giá trị được set đúng
                $('#giang_vien_phan_bien_id').val(giangVienPhanBien);
                $('#giang_vien_khac_id').val(giangVienKhac);
                
                // Submit form bằng cách tạo form mới
                var newForm = $('<form>', {
                    'method': 'POST',
                    'action': form.attr('action')
                });

                // Thêm CSRF token
                newForm.append($('<input>', {
                    'type': 'hidden',
                    'name': '_token',
                    'value': $('meta[name="csrf-token"]').attr('content')
                }));

                // Thêm method PUT
                newForm.append($('<input>', {
                    'type': 'hidden',
                    'name': '_method',
                    'value': 'PUT'
                }));

                // Thêm các trường dữ liệu
                newForm.append($('<input>', {
                    'type': 'hidden',
                    'name': 'de_tai_id',
                    'value': deTai
                }));

                newForm.append($('<input>', {
                    'type': 'hidden',
                    'name': 'giang_vien_phan_bien_id',
                    'value': giangVienPhanBien
                }));

                newForm.append($('<input>', {
                    'type': 'hidden',
                    'name': 'giang_vien_khac_id',
                    'value': giangVienKhac
                }));

                newForm.append($('<input>', {
                    'type': 'hidden',
                    'name': 'lich_cham',
                    'value': lichCham
                }));

                newForm.append($('<input>', {
                    'type': 'hidden',
                    'name': 'giang_vien_huong_dan_id',
                    'value': giangVienHuongDan
                }));

                // Debug form mới
                console.log('New form data:', {
                    de_tai_id: deTai,
                    giang_vien_phan_bien_id: giangVienPhanBien,
                    giang_vien_khac_id: giangVienKhac,
                    lich_cham: lichCham,
                    giang_vien_huong_dan_id: giangVienHuongDan
                });

                // Thêm form vào body và submit
                $('body').append(newForm);
                newForm.submit();
            }
        });

        // Trigger change event khi trang load để tự động điền giảng viên hướng dẫn
        if ($('#de_tai_id').val()) {
            console.log('Triggering change event for de_tai_id');
            $('#de_tai_id').trigger('change');
        }
    });
</script>
@endpush
@endsection 