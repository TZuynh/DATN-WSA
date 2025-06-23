@extends('components.giangvien.app')

@section('title', 'Sửa đề tài')

@section('content')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/vi.js"></script>
    <script src="https://cdn.ckeditor.com/ckeditor5/40.1.0/classic/ckeditor.js"></script>

    <div style="padding: 20px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h1 style="color: #2d3748; font-weight: 700;">Sửa đề tài</h1>
            <a href="{{ route('giangvien.de-tai.index') }}" style="padding: 10px 20px; background-color: #718096; color: white; border: none; border-radius: 4px; text-decoration: none;">
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
            <form action="{{ route('giangvien.de-tai.update', $deTai) }}" method="POST" id="editForm">
                @csrf
                @method('PUT')

                <div style="margin-bottom: 20px;">
                    <label for="ma_de_tai" style="display: block; margin-bottom: 5px; color: #4a5568;">Mã đề tài</label>
                    <input type="text" class="form-control @error('ma_de_tai') is-invalid @enderror"
                        id="ma_de_tai" name="ma_de_tai"
                        value="{{ old('ma_de_tai', $deTai->ma_de_tai) }}"
                        placeholder="Nhập mã đề tài" required
                        style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;" readonly>
                    @error('ma_de_tai')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div style="margin-bottom: 20px;">
                    <label for="ten_de_tai" style="display: block; margin-bottom: 5px; color: #4a5568;">Tên đề tài</label>
                    <input type="text" class="form-control @error('ten_de_tai') is-invalid @enderror"
                        id="ten_de_tai" name="ten_de_tai"
                        value="{{ old('ten_de_tai', $deTai->ten_de_tai) }}"
                        placeholder="Nhập tên đề tài" required
                        style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                    @error('ten_de_tai')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div style="margin-bottom: 20px;">
                    <label for="y_kien_giang_vien" style="display: block; margin-bottom: 5px; color: #4a5568;">Ý kiến giảng viên</label>
                    <textarea class="form-control @error('y_kien_giang_vien') is-invalid @enderror"
                        id="y_kien_giang_vien" name="y_kien_giang_vien"
                        placeholder="Nhập ý kiến của giảng viên">{{ old('y_kien_giang_vien', $deTai->y_kien_giang_vien) }}</textarea>
                    @error('y_kien_giang_vien')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div style="margin-bottom: 20px;">
                    <label for="mo_ta" style="display: block; margin-bottom: 5px; color: #4a5568;">Mô tả</label>
                    <textarea class="form-control @error('mo_ta') is-invalid @enderror"
                        id="mo_ta" name="mo_ta"
                        placeholder="Nhập mô tả đề tài">{{ old('mo_ta', $deTai->mo_ta) }}</textarea>
                    @error('mo_ta')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div style="margin-bottom: 20px;">
                    <label for="dot_bao_cao_id" style="display: block; margin-bottom: 5px; color: #4a5568;">Đợt báo cáo <span class="text-danger">*</span></label>
                    <select name="dot_bao_cao_id" id="dot_bao_cao_id"
                        class="form-control @error('dot_bao_cao_id') is-invalid @enderror" required
                        style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                        @foreach($dotBaoCaos as $dotBaoCao)
                            <option value="{{ $dotBaoCao->id }}" {{ old('dot_bao_cao_id', $deTai->dot_bao_cao_id) == $dotBaoCao->id ? 'selected' : '' }}>
                                {{ $dotBaoCao->nam_hoc }} - {{ optional($dotBaoCao->hocKy)->ten }}
                            </option>
                        @endforeach
                    </select>
                    @error('dot_bao_cao_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                @php
                    $nhomDangGiu = $nhoms->firstWhere('de_tai_id', $deTai->id);
                @endphp
                <div style="margin-bottom: 20px;">
                    <label for="nhom_id" style="display: block; margin-bottom: 5px; color: #4a5568;">Chọn nhóm</label>
                    <select name="nhom_id" id="nhom_id"
                        class="form-control @error('nhom_id') is-invalid @enderror"
                        style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                        <option value="">-- Chọn nhóm --</option>
                        @foreach($nhoms as $nhom)
                            <option value="{{ $nhom->id }}"
                                {{ old('nhom_id', optional($nhomDangGiu)->id) == $nhom->id ? 'selected' : '' }}>
                                {{ $nhom->ten }}
                            </option>
                        @endforeach
                    </select>
                    @error('nhom_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div style="margin-bottom: 20px;">
                    <label for="trang_thai" style="display: block; margin-bottom: 5px; color: #4a5568;">Trạng thái</label>
                    <select name="trang_thai" id="trang_thai"
                        class="form-control @error('trang_thai') is-invalid @enderror" required
                        style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;"
                        {{ $daCoLichCham ? 'disabled' : '' }}>
                        @if(!$daPhanCongCham)
                        <option value="0" {{ old('trang_thai', $deTai->trang_thai) == 0 ? 'selected' : '' }}>Đang chờ duyệt</option>
                        @endif
                        <option value="1" {{ old('trang_thai', $deTai->trang_thai) == 1 ? 'selected' : '' }}>Đang thực hiện (giảng viên hướng dẫn đồng ý báo cáo)</option>
                        @if(!$daPhanCongCham)
                        <option value="3" {{ old('trang_thai', $deTai->trang_thai) == 3 ? 'selected' : '' }}>Không xảy ra (giảng viên hướng dẫn không đồng ý)</option>
                        @endif
                    </select>
                    @if($daCoLichCham)
                    <small style="color: #e53e3e; display: block; margin-top: 5px;">Không thể thay đổi trạng thái vì đề tài đã có trong lịch bảo vệ</small>
                    @endif
                    @error('trang_thai')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
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
            // Cấu hình CKEditor
            ClassicEditor
                .create(document.querySelector('#y_kien_giang_vien'), {
                    toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', '|', 'outdent', 'indent', '|', 'blockQuote', 'insertTable', 'undo', 'redo'],
                    language: 'vi',
                    table: {
                        contentToolbar: ['tableColumn', 'tableRow', 'mergeTableCells']
                    }
                })
                .catch(error => {
                    console.error(error);
                });

            ClassicEditor
                .create(document.querySelector('#mo_ta'), {
                    toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', '|', 'outdent', 'indent', '|', 'blockQuote', 'insertTable', 'undo', 'redo'],
                    language: 'vi',
                    table: {
                        contentToolbar: ['tableColumn', 'tableRow', 'mergeTableCells']
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
            document.getElementById('editForm').addEventListener('submit', function(e) {

            });
        });
    </script>
@endsection
