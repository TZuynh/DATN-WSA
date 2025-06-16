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
            <a href="{{ route('admin.lich-cham.export-pdf') }}" class="btn btn-primary">
                <i class="fas fa-file-pdf me-2"></i> Xuất PDF
            </a>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="lichChamTable">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Hội đồng</th>
                            <th>Đợt báo cáo</th>
                            <th>Nhóm</th>
                            <th>Thời gian</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($lichChams as $lichCham)
                            <tr>
                                <td>{{ $lichCham->id }}</td>
                                <td>{{ $lichCham->hoiDong->ten }}</td>
                                <td>{{ $lichCham->dotBaoCao->nam_hoc }}</td>
                                <td>{{ $lichCham->nhom->ten }}</td>
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
<script>
    $(document).ready(function() {
        $('#lichChamTable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/vi.json',
            },
            pageLength: 10,
            ordering: true,
            responsive: true,
            dom: 'Bfrtip',
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print'
            ]
        });
    });
</script>
@endpush

@push('styles')
<link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<link href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css" rel="stylesheet">
@endpush 