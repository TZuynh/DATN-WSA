@extends('admin.layout')

@section('title', 'Quản lý lịch bảo vệ')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Quản lý lịch bảo vệ</h1>
        <div>
            {{-- <a href="{{ route('admin.lich-cham.create') }}" class="btn btn-primary me-2">
                <i class="fas fa-plus me-2"></i> Thêm lịch bảo vệ
            </a> --}}
            <a href="{{ route('admin.lich-cham.export-pdf') }}" class="btn btn-danger">
                <i class="fas fa-file-pdf me-2"></i> Xuất PDF
            </a>
        </div>
    </div>

    <div class="alert alert-info mb-4">
        <i class="fas fa-info-circle me-2"></i>
        Bạn có thể kéo thả các dòng trong cùng một hội đồng để sắp xếp lại thứ tự danh sách.
    </div>

    @foreach($lichChams as $hoiDongId => $lichChamsHoiDong)
        <div class="card shadow mb-4">
            <div class="card-header">
                <h4 class="mb-0">{{ $lichChamsHoiDong->first()->hoiDong->ten }}</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 50px;">STT</th>
                                <th>Đợt báo cáo</th>
                                <th>Nhóm</th>
                                <th>Đề tài</th>
                                <th>Thời gian</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody class="sortable" data-hoi-dong-id="{{ $hoiDongId }}">
                            @foreach($lichChamsHoiDong as $index => $lichCham)
                                <tr data-id="{{ $lichCham->id }}" class="sortable-row">
                                    <td>
                                        <i class="fas fa-grip-vertical me-2 text-muted"></i>
                                        {{ $lichCham->thu_tu }}
                                    </td>
                                    <td>{{ $lichCham->dotBaoCao->nam_hoc }} - {{ $lichCham->dotBaoCao->hocKy->ten }}</td>
                                    <td>{{ $lichCham->nhom->ten }}</td>
                                    <td>{{ $lichCham->deTai->ten_de_tai }}</td>
                                    <td>{{ \Carbon\Carbon::parse($lichCham->lich_tao)->format('d/m/Y') }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.lich-cham.edit', $lichCham) }}" 
                                               class="btn btn-sm btn-warning" 
                                               title="Sửa">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.lich-cham.destroy', $lichCham) }}" 
                                                  method="POST" 
                                                  class="d-inline"
                                                  onsubmit="return confirm('Bạn có chắc chắn muốn xóa lịch chấm này?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" title="Xóa">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                            @if($index > 0 && count($lichChamsHoiDong) > 1)
                                                <button type="button" class="btn btn-sm btn-info btn-len-top" 
                                                        title="Đưa lên đầu" data-lich-cham-id="{{ $lichCham->id }}">
                                                    <i class="fas fa-arrow-up"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endforeach
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Kéo thả
        document.querySelectorAll('.sortable').forEach(function(el) {
            new Sortable(el, {
                animation: 150,
                group: el.dataset.hoiDongId,
                onEnd: function () {
                    let orders = [];
                    el.querySelectorAll('tr').forEach((row, index) => {
                        orders.push({
                            id: row.dataset.id,
                            new_order: index + 1
                        });
                    });

                    fetch("{{ route('admin.lich-cham.update-order') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ orders })
                    }).then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert("Cập nhật thất bại: " + data.message);
                        }
                    });
                }
            });
        });

        // Ẩn nút lên top mặc định
        document.querySelectorAll('.btn-len-top').forEach(function(btn) {
            btn.style.display = 'none';
        });

        // Hiện nút khi hover
        document.querySelectorAll('.sortable-row').forEach(function(row) {
            row.addEventListener('mouseenter', function() {
                let btn = row.querySelector('.btn-len-top');
                if (btn) btn.style.display = 'inline-block';
            });
            row.addEventListener('mouseleave', function() {
                let btn = row.querySelector('.btn-len-top');
                if (btn) btn.style.display = 'none';
            });
        });

        // Xử lý click lên top
        document.querySelectorAll('.btn-len-top').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var lichChamId = btn.dataset.lichChamId;
                var tbody = btn.closest('tbody');
                var rows = tbody.querySelectorAll('tr');
                var orders = [];
                // Đưa lịch chấm này lên đầu
                orders.push({ id: lichChamId, new_order: 1 });
                var order = 2;
                rows.forEach(function(row) {
                    var id = row.dataset.id;
                    if (id != lichChamId) {
                        orders.push({ id: id, new_order: order });
                        order++;
                    }
                });
                fetch("{{ route('admin.lich-cham.update-order') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ orders })
                }).then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert("Cập nhật thất bại: " + data.message);
                    }
                });
            });
        });
    });
</script>
@endpush

@push('styles')
<style>
    .sortable-row { cursor: move; }
    .sortable-ghost { opacity: 0.4; background: #F0F0F0 !important; }
    .sortable-drag { background: #fff !important; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
    tbody tr { transition: all 0.3s ease; }
    tbody tr:hover { background-color: #f8f9fa; }
    .btn-len-top { display: none; }
</style>
@endpush 