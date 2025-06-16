@extends('components.giangvien.app')

@section('title', 'Thêm mới đề tài')

@section('content')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/vi.js"></script>
    <script src="https://cdn.ckeditor.com/ckeditor5/40.1.0/classic/ckeditor.js"></script>

    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Tạo đề tài mới</h3>
                    </div>
                    <div class="card-body">
                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('giangvien.de-tai.store') }}" method="POST" id="createForm">
                            @csrf
                            <div class="form-group mb-4">
                                <label for="ten_de_tai" class="form-label">Tên đề tài <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('ten_de_tai') is-invalid @enderror" 
                                    id="ten_de_tai" name="ten_de_tai" value="{{ old('ten_de_tai') }}" 
                                    placeholder="Nhập tên đề tài">
                                @error('ten_de_tai')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group mb-4">
                                <label for="mo_ta" class="form-label">Mô tả</label>
                                <textarea class="form-control @error('mo_ta') is-invalid @enderror" 
                                    id="mo_ta" name="mo_ta" rows="3" 
                                    placeholder="Nhập mô tả đề tài">{{ old('mo_ta') }}</textarea>
                                @error('mo_ta')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group mb-4">
                                <label for="y_kien_giang_vien" class="form-label">Ý kiến giảng viên</label>
                                <textarea class="form-control @error('y_kien_giang_vien') is-invalid @enderror" 
                                    id="y_kien_giang_vien" name="y_kien_giang_vien" rows="3" 
                                    placeholder="Nhập ý kiến của giảng viên">{{ old('y_kien_giang_vien') }}</textarea>
                                @error('y_kien_giang_vien')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group mb-4">
                                <label for="ngay_bat_dau" class="form-label">Ngày bắt đầu <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('ngay_bat_dau') is-invalid @enderror" 
                                    id="ngay_bat_dau" name="ngay_bat_dau" value="{{ old('ngay_bat_dau') }}"
                                    placeholder="Chọn ngày bắt đầu">
                                @error('ngay_bat_dau')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group mb-4">
                                <label for="ngay_ket_thuc" class="form-label">Ngày kết thúc <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('ngay_ket_thuc') is-invalid @enderror" 
                                    id="ngay_ket_thuc" name="ngay_ket_thuc" value="{{ old('ngay_ket_thuc') }}"
                                    placeholder="Chọn ngày kết thúc">
                                @error('ngay_ket_thuc')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group mb-4">
                                <label for="nhom_id" class="form-label">Chọn nhóm</label>
                                <select class="form-control @error('nhom_id') is-invalid @enderror" 
                                    id="nhom_id" name="nhom_id">
                                    <option value="">-- Chọn nhóm --</option>
                                    @forelse($nhoms as $nhom)
                                        <option value="{{ $nhom->id }}" {{ old('nhom_id') == $nhom->id ? 'selected' : '' }}>
                                            {{ $nhom->ten }}
                                        </option>
                                    @empty
                                        <option value="" disabled>Không có nhóm nào khả dụng</option>
                                    @endforelse
                                </select>
                                @error('nhom_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                @if($nhoms->isEmpty())
                                    <div class="text-danger mt-2">
                                        <i class="fas fa-exclamation-triangle"></i> 
                                        Tất cả các nhóm đã có đề tài. Vui lòng tạo nhóm mới hoặc chọn nhóm khác.
                                    </div>
                                @endif
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">Lưu</button>
                                <a href="{{ route('giangvien.de-tai.index') }}" class="btn btn-secondary">Hủy</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Khởi tạo CKEditor cho các trường văn bản
            ClassicEditor
                .create(document.querySelector('#mo_ta'), {
                    toolbar: {
                        items: [
                            'heading',
                            '|',
                            'bold',
                            'italic',
                            'link',
                            'bulletedList',
                            'numberedList',
                            '|',
                            'blockQuote',
                            'insertTable',
                            'undo',
                            'redo'
                        ]
                    },
                    language: 'vi',
                    table: {
                        contentToolbar: [
                            'tableColumn',
                            'tableRow',
                            'mergeTableCells'
                        ]
                    }
                })
                .catch(error => {
                    console.error(error);
                });

            ClassicEditor
                .create(document.querySelector('#y_kien_giang_vien'), {
                    toolbar: {
                        items: [
                            'heading',
                            '|',
                            'bold',
                            'italic',
                            'link',
                            'bulletedList',
                            'numberedList',
                            '|',
                            'blockQuote',
                            'insertTable',
                            'undo',
                            'redo'
                        ]
                    },
                    language: 'vi',
                    table: {
                        contentToolbar: [
                            'tableColumn',
                            'tableRow',
                            'mergeTableCells'
                        ]
                    }
                })
                .catch(error => {
                    console.error(error);
                });

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
            });
        });
    </script>
@endsection 