@extends('components.giangvien.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Danh sách lớp</h5>
                    <a href="{{ route('giangvien.lop.create') }}" class="btn btn-primary">Thêm lớp mới</a>
                </div>

                <div class="card-body">
                    <form action="{{ route('giangvien.lop.bulk-delete') }}" method="POST" id="bulk-delete-form">
                        @csrf
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Tên lớp</th>
                                        <th>Số lượng sinh viên</th>
                                        <th width="150">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($lops as $index => $lop)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $lop->ten_lop }}</td>
                                            <td>{{ $lop->sinh_viens_count }}</td>
                                            <td>
                                                <a href="{{ route('giangvien.lop.edit', $lop) }}" class="btn btn-sm btn-primary">Sửa</a>
                                                <form action="{{ route('giangvien.lop.destroy', $lop) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc chắn muốn xóa lớp này?')">Xóa</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center">Không có dữ liệu</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </form>

                    <div class="mt-3">
                        {{ $lops->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 