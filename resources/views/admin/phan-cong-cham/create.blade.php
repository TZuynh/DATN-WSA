@extends('admin.layout')

@section('title', 'Thêm phân công chấm')

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
                    <h3 class="card-title">Thêm phân công chấm</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.phan-cong-cham.store') }}" method="POST" id="formPhanCongCham">
                        @csrf
                        <input type="hidden" name="giang_vien_huong_dan_id" id="giang_vien_huong_dan_id" value="{{ old('giang_vien_huong_dan_id') }}">
                        <div class="form-group">
                            <label for="de_tai_id" class="required-field">Đề tài</label>
                            <select name="de_tai_id" id="de_tai_id" class="form-control @error('de_tai_id') is-invalid @enderror" required>
                                <option value="">Chọn đề tài</option>
                                @foreach($deTais as $deTai)
                                    <option value="{{ $deTai->id }}" data-giang-vien-id="{{ $deTai->giang_vien_id }}" {{ old('de_tai_id') == $deTai->id ? 'selected' : '' }}>
                                        {{ $deTai->ma_de_tai }} - {{ $deTai->ten_de_tai }}
                                    </option>
                                @endforeach
                            </select>
                            @error('de_tai_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="giang_vien_huong_dan">Giảng viên hướng dẫn</label>
                            <input type="text" class="form-control" id="giang_vien_huong_dan" placeholder="Tự động chọn giảng viên hướng dẫn khi chọn đề tài" readonly>
                        </div>

                        <div class="form-group">
                            <label for="giang_vien_phan_bien_id" class="required-field">Giảng viên phản biện</label>
                            <select name="giang_vien_phan_bien_id" id="giang_vien_phan_bien_id" class="form-control @error('giang_vien_phan_bien_id') is-invalid @enderror" required>
                                <option value="">Chọn giảng viên phản biện</option>
                                @foreach($giangViens as $giangVien)
                                    <option value="{{ $giangVien->id }}" data-ten="{{ $giangVien->ten }}" {{ old('giang_vien_phan_bien_id') == $giangVien->id ? 'selected' : '' }}>
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
                                    <option value="{{ $giangVien->id }}" data-ten="{{ $giangVien->ten }}" {{ old('giang_vien_khac_id') == $giangVien->id ? 'selected' : '' }}>
                                        {{ $giangVien->ten }}
                                    </option>
                                @endforeach
                            </select>
                            @error('giang_vien_khac_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="ngay_phan_cong" class="required-field">Ngày phân công</label>
                            <input type="text" name="ngay_phan_cong" id="ngay_phan_cong" class="form-control @error('ngay_phan_cong') is-invalid @enderror" placeholder="Chọn ngày phân công" value="{{ old('ngay_phan_cong') }}" required>
                            @error('ngay_phan_cong')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary" id="btnSubmit">Thêm mới</button>
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
        // Cấu hình Flatpickr cho ngày phân công
        const ngayPhanCong = flatpickr("#ngay_phan_cong", {
            locale: "vi",
            dateFormat: "Y-m-d",
            minDate: "today",
            placeholder: "Chọn ngày phân công",
            allowInput: true,
            defaultDate: "{{ old('ngay_phan_cong') }}"
        });

        var giangVienHuongDanId = null;

        // Khi chọn đề tài, tự động điền giảng viên hướng dẫn
        $('#de_tai_id').change(function() {
            var selectedOption = $(this).find('option:selected');
            giangVienHuongDanId = selectedOption.data('giang-vien-id');
            
            if (giangVienHuongDanId) {
                // Lấy tên giảng viên từ danh sách giảng viên
                var giangVienTen = $('#giang_vien_phan_bien_id option[value="' + giangVienHuongDanId + '"]').data('ten');
                $('#giang_vien_huong_dan').val(giangVienTen);
                $('#giang_vien_huong_dan_id').val(giangVienHuongDanId);
                console.log('Giảng viên hướng dẫn ID:', giangVienHuongDanId); // Debug
                
                // Vô hiệu hóa giảng viên hướng dẫn trong các select box khác
                $('#giang_vien_phan_bien_id option, #giang_vien_khac_id option').each(function() {
                    if ($(this).val() == giangVienHuongDanId) {
                        $(this).prop('disabled', true);
                    } else {
                        $(this).prop('disabled', false);
                    }
                });
            } else {
                // Reset khi không chọn đề tài
                giangVienHuongDanId = null;
                $('#giang_vien_huong_dan').val('');
                $('#giang_vien_huong_dan_id').val('');
                $('#giang_vien_phan_bien_id option, #giang_vien_khac_id option').prop('disabled', false);
            }
        });

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
            var ngayPhanCong = $('#ngay_phan_cong').val();
            var giangVienHuongDan = $('#giang_vien_huong_dan_id').val();

            // Debug chi tiết
            console.log('Select box values:', {
                deTai: {
                    val: $('#de_tai_id').val(),
                    selected: $('#de_tai_id option:selected').val()
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

            if (!ngayPhanCong) {
                alert('Vui lòng chọn ngày phân công');
                isValid = false;
            }

            if (!giangVienHuongDan) {
                alert('Vui lòng chọn đề tài để lấy giảng viên hướng dẫn');
                isValid = false;
            }

            // Đảm bảo giảng viên hướng dẫn được gửi đi
            if (giangVienHuongDanId) {
                $('#giang_vien_huong_dan_id').val(giangVienHuongDanId);
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
                    'name': 'ngay_phan_cong',
                    'value': ngayPhanCong
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
                    ngay_phan_cong: ngayPhanCong,
                    giang_vien_huong_dan_id: giangVienHuongDan
                });

                // Thêm form vào body và submit
                $('body').append(newForm);
                newForm.submit();
            }
        });

        // Trigger change event nếu có giá trị cũ
        if ($('#de_tai_id').val()) {
            $('#de_tai_id').trigger('change');
        }
    });
</script>
@endpush
@endsection 