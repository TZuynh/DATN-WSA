@extends('admin.layout')

@section('title', 'Thêm hội đồng mới')

@section('content')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/vi.js"></script>

    <style>
        .de-tai-item {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6 !important;
            transition: all 0.3s ease;
        }
        
        .de-tai-item:hover {
            background-color: #e9ecef;
            border-color: #adb5bd !important;
        }
        
        .de-tai-item .card-header {
            background-color: #e3f2fd;
            border-bottom: 1px solid #dee2e6;
        }
        
        .remove-de-tai {
            transition: all 0.2s ease;
        }
        
        .remove-de-tai:hover {
            transform: scale(1.1);
        }
        
        .form-group label {
            font-weight: 500;
            color: #495057;
        }
        
        .alert-info {
            background-color: #d1ecf1;
            border-color: #bee5eb;
            color: #0c5460;
        }
        
        .btn-success {
            background-color: #28a745;
            border-color: #28a745;
        }
        
        .btn-success:hover {
            background-color: #218838;
            border-color: #1e7e34;
        }
    </style>

<div class="container-fluid">
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Thêm hội đồng mới</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.hoi-dong.store') }}" method="POST" id="hoiDongForm">
                        @csrf
                        
                        <!-- Thông tin hội đồng -->
                        <div class="row">
                            <div class="col-md-8">
                                <h5 class="mb-3">Thông tin hội đồng</h5>
                                
                                <div class="form-group mb-3">
                                    <label for="ten">Tên hội đồng <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('ten') is-invalid @enderror" 
                                        id="ten" name="ten" value="{{ old('ten') }}" placeholder="Nhập tên hội đồng" required>
                                    @error('ten')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label for="dot_bao_cao_id">Đợt báo cáo <span class="text-danger">*</span></label>
                                    <select class="form-control @error('dot_bao_cao_id') is-invalid @enderror" 
                                        id="dot_bao_cao_id" name="dot_bao_cao_id" required>
                                        <option value="">Chọn đợt báo cáo</option>
                                        @foreach($dotBaoCaos as $dotBaoCao)
                                            <option value="{{ $dotBaoCao->id }}" {{ old('dot_bao_cao_id') == $dotBaoCao->id ? 'selected' : '' }}>
                                                {{ $dotBaoCao->nam_hoc }} - {{ optional($dotBaoCao->hocKy)->ten }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('dot_bao_cao_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label for="phong_id">Phòng</label>
                                    <select name="phong_id" id="phong_id" class="form-control @error('phong_id') is-invalid @enderror">
                                        <option value="">Chọn phòng</option>
                                        @foreach($phongs as $phong)
                                            <option value="{{ $phong->id }}" {{ old('phong_id') == $phong->id ? 'selected' : '' }}>
                                                {{ $phong->ten_phong }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('phong_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Mỗi phòng chỉ được sử dụng cho một hội đồng trong cùng đợt báo cáo
                                    </small>
                                </div>

                                <div class="form-group mb-3">
                                    <label for="thoi_gian_bat_dau">Thời gian bắt đầu <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('thoi_gian_bat_dau') is-invalid @enderror" 
                                           id="thoi_gian_bat_dau" 
                                           name="thoi_gian_bat_dau" 
                                           value="{{ old('thoi_gian_bat_dau') }}"
                                           placeholder="Chọn thời gian" required>
                                    @error('thoi_gian_bat_dau')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Thêm đề tài đơn giản -->
                            <div class="col-md-4">
                                <h5 class="mb-3">Thêm đề tài</h5>
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i>
                                    Bạn có thể thêm đề tài cơ bản. Giáo viên sẽ được phân công sau.
                                </div>

                                <button type="button" class="btn btn-success btn-sm mb-3" onclick="toggleDeTaiForm()">
                                    <i class="fas fa-plus"></i> Thêm đề tài
                                </button>

                                <div id="deTaiForm" style="display: none;">
                                    <div class="border rounded p-3 bg-light">
                                        <div class="form-group mb-2">
                                            <label for="ten_de_tai">Tên đề tài <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" 
                                                   id="ten_de_tai" 
                                                   name="ten_de_tai" 
                                                   placeholder="Nhập tên đề tài">
                                        </div>

                                        <div class="form-group mb-2">
                                            <label for="dot_bao_cao_de_tai">Đợt báo cáo <span class="text-danger">*</span></label>
                                            <select class="form-control" 
                                                    id="dot_bao_cao_de_tai" 
                                                    name="dot_bao_cao_de_tai">
                                                <option value="">Chọn đợt báo cáo</option>
                                                @foreach($dotBaoCaos as $dotBaoCao)
                                                    <option value="{{ $dotBaoCao->id }}">
                                                        {{ $dotBaoCao->nam_hoc }} - {{ optional($dotBaoCao->hocKy)->ten }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="form-group mb-2">
                                            <label for="nhom_id">Nhóm (tùy chọn)</label>
                                            <select class="form-control" 
                                                    id="nhom_id" 
                                                    name="nhom_id">
                                                <option value="">Chọn nhóm</option>
                                                @foreach($nhoms as $nhom)
                                                    <option value="{{ $nhom->id }}">
                                                        {{ $nhom->ten }} ({{ $nhom->ma_nhom }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="d-flex gap-2">
                                            <button type="button" class="btn btn-primary btn-sm" onclick="saveDeTai()">
                                                <i class="fas fa-save"></i> Lưu đề tài
                                            </button>
                                            <button type="button" class="btn btn-secondary btn-sm" onclick="cancelDeTai()">
                                                <i class="fas fa-times"></i> Hủy
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Danh sách đề tài đã thêm -->
                                <div id="deTaiList" class="mt-3">
                                    <!-- Các đề tài sẽ được hiển thị ở đây -->
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">Thêm mới</button>
                                <a href="{{ route('admin.hoi-dong.index') }}" class="btn btn-secondary">Quay lại</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let deTaiList = [];

    document.addEventListener('DOMContentLoaded', function() {
        // Cấu hình Flatpickr cho thời gian
        const thoiGianBatDau = flatpickr("#thoi_gian_bat_dau", {
            locale: "vi",
            enableTime: true,
            dateFormat: "Y-m-d H:i",
            minDate: "today",
            time_24hr: true,
            minuteIncrement: 1,
            onChange: function(selectedDates, dateStr) {
                console.log('Đã chọn thời gian:', dateStr);
            }
        });

        // Validate form trước khi submit
        document.querySelector('#hoiDongForm').addEventListener('submit', function(e) {
            const thoiGianBatDauValue = document.getElementById('thoi_gian_bat_dau').value;
            
            if (thoiGianBatDauValue) {
                const selectedDate = new Date(thoiGianBatDauValue);
                const now = new Date();

                if (selectedDate < now) {
                    e.preventDefault();
                    alert('Thời gian không được nhỏ hơn thời gian hiện tại!');
                    return;
                }
            }

            // Thêm dữ liệu đề tài vào form nếu có
            if (deTaiList.length > 0) {
                const firstDeTai = deTaiList[0]; // Chỉ lấy đề tài đầu tiên
                
                // Tạo input ẩn cho đề tài
                const tenDeTaiInput = document.createElement('input');
                tenDeTaiInput.type = 'hidden';
                tenDeTaiInput.name = 'ten_de_tai';
                tenDeTaiInput.value = firstDeTai.ten_de_tai;
                this.appendChild(tenDeTaiInput);

                const dotBaoCaoInput = document.createElement('input');
                dotBaoCaoInput.type = 'hidden';
                dotBaoCaoInput.name = 'dot_bao_cao_de_tai';
                dotBaoCaoInput.value = firstDeTai.dot_bao_cao_id;
                this.appendChild(dotBaoCaoInput);

                if (firstDeTai.nhom_id) {
                    const nhomInput = document.createElement('input');
                    nhomInput.type = 'hidden';
                    nhomInput.name = 'nhom_id';
                    nhomInput.value = firstDeTai.nhom_id;
                    this.appendChild(nhomInput);
                }
            }
        });
    });

    function toggleDeTaiForm() {
        const form = document.getElementById('deTaiForm');
        if (form.style.display === 'none') {
            form.style.display = 'block';
        } else {
            form.style.display = 'none';
        }
    }

    function saveDeTai() {
        const tenDeTai = document.getElementById('ten_de_tai').value;
        const dotBaoCao = document.getElementById('dot_bao_cao_de_tai').value;
        const nhom = document.getElementById('nhom_id').value;

        if (!tenDeTai || !dotBaoCao) {
            alert('Vui lòng điền đầy đủ thông tin bắt buộc!');
            return;
        }

        // Thêm vào danh sách
        const deTai = {
            ten_de_tai: tenDeTai,
            dot_bao_cao_id: dotBaoCao,
            nhom_id: nhom || null
        };

        deTaiList.push(deTai);
        updateDeTaiList();
        clearDeTaiForm();
        toggleDeTaiForm();
    }

    function cancelDeTai() {
        clearDeTaiForm();
        toggleDeTaiForm();
    }

    function clearDeTaiForm() {
        document.getElementById('ten_de_tai').value = '';
        document.getElementById('dot_bao_cao_de_tai').value = '';
        document.getElementById('nhom_id').value = '';
    }

    function updateDeTaiList() {
        const container = document.getElementById('deTaiList');
        if (deTaiList.length === 0) {
            container.innerHTML = '<p class="text-muted">Chưa có đề tài nào</p>';
            return;
        }

        let html = '<h6>Danh sách đề tài:</h6>';
        deTaiList.forEach((deTai, index) => {
            html += `
                <div class="border rounded p-2 mb-2 bg-white">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <strong>${deTai.ten_de_tai}</strong><br>
                            <small class="text-muted">Đợt báo cáo: ${getDotBaoCaoName(deTai.dot_bao_cao_id)}</small>
                            ${deTai.nhom_id ? `<br><small class="text-info">Nhóm: ${getNhomName(deTai.nhom_id)}</small>` : ''}
                        </div>
                        <button type="button" class="btn btn-sm btn-danger" onclick="removeDeTai(${index})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            `;
        });

        container.innerHTML = html;
    }

    function removeDeTai(index) {
        deTaiList.splice(index, 1);
        updateDeTaiList();
    }

    function getDotBaoCaoName(id) {
        const select = document.getElementById('dot_bao_cao_de_tai');
        const option = select.querySelector(`option[value="${id}"]`);
        return option ? option.textContent : 'N/A';
    }

    function getNhomName(id) {
        const select = document.getElementById('nhom_id');
        const option = select.querySelector(`option[value="${id}"]`);
        return option ? option.textContent : 'N/A';
    }
</script>
@endpush 