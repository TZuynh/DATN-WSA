@extends('admin.layout')

@section('title', 'Quản lý lịch chấm')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Quản lý lịch chấm</h1>
        <div>
            <a href="{{ route('admin.lich-cham.create') }}" class="btn btn-primary me-2">
                <i class="fas fa-plus me-2"></i> Thêm lịch chấm
            </a>
            <a href="{{ route('admin.lich-cham.export-pdf') }}" class="btn btn-danger">
                <i class="fas fa-file-pdf me-2"></i> Xuất PDF
            </a>
        </div>
    </div>

    <div class="alert alert-info mb-4">
        <i class="fas fa-info-circle me-2"></i>
        Bạn có thể kéo thả các dòng để sắp xếp lại thứ tự danh sách. Thứ tự này sẽ được áp dụng khi xuất PDF.
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="lichChamTable">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 50px;">STT</th>
                            <th>Hội đồng</th>
                            <th>Đợt báo cáo</th>
                            <th>Nhóm</th>
                            <th>Đề tài</th>
                            <th>Thời gian</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody id="sortable">
                        @forelse($lichChams as $lichCham)
                            <tr data-id="{{ $lichCham->id }}" class="sortable-row">
                                <td>
                                    <i class="fas fa-grip-vertical me-2 text-muted"></i>
                                    {{ $lichCham->thu_tu }}
                                </td>
                                <td>{{ $lichCham->hoiDong->ten }}</td>
                                <td>{{ $lichCham->dotBaoCao->nam_hoc }}</td>
                                <td>{{ $lichCham->nhom->ten }}</td>
                                <td>{{ $lichCham->deTai->ten_de_tai }}</td>
                                <td>{{ \Carbon\Carbon::parse($lichCham->lich_tao)->format('d/m/Y H:i') }}</td>
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
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">Không có lịch chấm nào</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-end mt-3">
                {{ $lichChams->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const tableBody = document.getElementById('sortable');

        new Sortable(tableBody, {
            animation: 150,
            onEnd: function () {
                let orders = [];
                tableBody.querySelectorAll('tr').forEach((row, index) => {
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
                    body: JSON.stringify({ orders })  // 👉 phải là `orders`
                }).then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload(); // Reload để hiển thị STT mới
                    } else {
                        alert("Cập nhật thất bại: " + data.message);
                    }
                });
            }
        });
    });
</script>

@endpush

@push('styles')
<link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<link href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css" rel="stylesheet">
<style>
    .sortable-row {
        cursor: move;
    }
    .sortable-ghost {
        opacity: 0.4;
        background: #F0F0F0 !important;
    }
    .sortable-drag {
        background: #fff !important;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        cursor: move;
    }
    tbody tr {
        transition: all 0.3s ease;
    }
    tbody tr:hover {
        background-color: #f8f9fa;
    }
</style>
@endpush 