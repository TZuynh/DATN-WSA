@extends('admin.layout')

@section('title', 'Thêm đợt báo cáo mới')

@section('content')   
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/vi.js"></script>

    <div style="padding: 20px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h1 style="color: #2d3748; font-weight: 700;">Thêm đợt báo cáo mới</h1>
            <a href="{{ route('admin.dot-bao-cao.index') }}" style="padding: 10px 20px; background-color: #718096; color: white; border: none; border-radius: 4px; text-decoration: none;">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
        </div>

        @if($errors->any())
            <div style="background-color: #f56565; color: white; padding: 10px; border-radius: 4px; margin-bottom: 20px;">
                <ul style="margin: 0; padding-left: 20px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 8px rgb(0 0 0 / 0.1);">
            <form action="{{ route('admin.dot-bao-cao.store') }}" method="POST" id="createForm">
                @csrf
                
                <div style="margin-bottom: 20px;">
                    <label for="nam_hoc" style="display: block; margin-bottom: 5px; color: #4a5568;">Năm học</label>
                    <input type="number" name="nam_hoc" id="nam_hoc" value="{{ date('Y') }}"
                        style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; background-color: #f7fafc;"
                        readonly>
                    <small style="color: #718096; font-size: 0.875rem;">Năm học được tự động cập nhật theo năm hiện tại</small>
                </div>

                <div style="margin-bottom: 20px;">
                    <label for="ngay_bat_dau" style="display: block; margin-bottom: 5px; color: #4a5568;">Ngày bắt đầu</label>
                    <input type="text" name="ngay_bat_dau" id="ngay_bat_dau" value="{{ old('ngay_bat_dau') }}"
                        style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;"
                        placeholder="Chọn ngày bắt đầu" required>
                </div>

                <div style="margin-bottom: 20px;">
                    <label for="ngay_ket_thuc" style="display: block; margin-bottom: 5px; color: #4a5568;">Ngày kết thúc</label>
                    <input type="text" name="ngay_ket_thuc" id="ngay_ket_thuc" value="{{ old('ngay_ket_thuc') }}"
                        style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;"
                        placeholder="Chọn ngày kết thúc" required>
                </div>

                <div style="text-align: right;">
                    <button type="submit" style="padding: 10px 20px; background-color: #4299e1; color: white; border: none; border-radius: 4px; cursor: pointer;">
                        <i class="fas fa-save"></i> Lưu
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Cấu hình Flatpickr cho ngày bắt đầu
            const ngayBatDau = flatpickr("#ngay_bat_dau", {
                locale: "vi",
                dateFormat: "Y-m-d",
                minDate: "today",
                onChange: function(selectedDates, dateStr) {
                    // Cập nhật minDate của ngày kết thúc
                    ngayKetThuc.set("minDate", dateStr);
                }
            });

            // Cấu hình Flatpickr cho ngày kết thúc
            const ngayKetThuc = flatpickr("#ngay_ket_thuc", {
                locale: "vi",
                dateFormat: "Y-m-d",
                minDate: "today"
            });

            // Validate form trước khi submit
            document.getElementById('createForm').addEventListener('submit', function(e) {
                const namHoc = document.getElementById('nam_hoc').value;
                const ngayBatDau = document.getElementById('ngay_bat_dau').value;
                const ngayKetThuc = document.getElementById('ngay_ket_thuc').value;

                if (!ngayBatDau || !ngayKetThuc) {
                    e.preventDefault();
                    alert('Vui lòng điền đầy đủ thông tin!');
                    return;
                }

                const batDau = new Date(ngayBatDau);
                const ketThuc = new Date(ngayKetThuc);

                if (batDau >= ketThuc) {
                    e.preventDefault();
                    alert('Ngày kết thúc phải sau ngày bắt đầu!');
                    return;
                }

                // Kiểm tra năm học có khớp với ngày bắt đầu không
                const namBatDau = batDau.getFullYear();
                if (namBatDau != namHoc) {
                    e.preventDefault();
                    alert('Ngày bắt đầu phải thuộc năm hiện tại!');
                    return;
                }
            });
        });
    </script>
@endsection 