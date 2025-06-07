@extends('components.giangvien.app')
@section('title', 'Thống kê')
@section('content')
    <div class="container py-4">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header" style="background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);">
                        <h4 class="mb-0 text-white">Thống kê</h4>
                    </div>
                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        <!-- Thống kê chính -->
                        <div class="row">
                            <div class="col-md-3 mb-4">
                                <div class="card" style="background: linear-gradient(135deg, #36b9cc 0%, #1a8997 100%); border: none; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
                                    <div class="card-body text-white">
                                        <h5 class="card-title" style="font-size: 1.1rem; font-weight: 500;">Giảng Viên</h5>
                                        <p class="card-text display-4" style="font-size: 2.5rem; font-weight: 600; margin: 0;">{{ $totalLecturers ?? 0 }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3 mb-4">
                                <div class="card" style="background: linear-gradient(135deg, #1cc88a 0%, #13855c 100%); border: none; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
                                    <div class="card-body text-white">
                                        <h5 class="card-title" style="font-size: 1.1rem; font-weight: 500;">Sinh Viên</h5>
                                        <p class="card-text display-4" style="font-size: 2.5rem; font-weight: 600; margin: 0;">{{ $totalStudents ?? 0 }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3 mb-4">
                                <div class="card" style="background: linear-gradient(135deg, #4e73df 0%, #224abe 100%); border: none; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
                                    <div class="card-body text-white">
                                        <h5 class="card-title" style="font-size: 1.1rem; font-weight: 500;">Đã Duyệt</h5>
                                        <p class="card-text display-4" style="font-size: 2.5rem; font-weight: 600; margin: 0;">{{ $approvedRegistrations ?? 0 }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3 mb-4">
                                <div class="card" style="background: linear-gradient(135deg, #f6c23e 0%, #dda20a 100%); border: none; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
                                    <div class="card-body text-white">
                                        <h5 class="card-title" style="font-size: 1.1rem; font-weight: 500;">Tổng Đăng Ký</h5>
                                        <p class="card-text display-4" style="font-size: 2.5rem; font-weight: 600; margin: 0;">{{ $totalRegistrations ?? 0 }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Thống kê đăng ký và sinh viên -->
                        {{-- <div class="row mt-4">
                            <div class="col-md-6">
                                <div class="card" style="box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
                                    <div class="card-header" style="background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);">
                                        <h5 class="mb-0 text-white">Thống Kê Đăng Ký Hướng Dẫn</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <thead class="table-light">
                                                <tr>
                                                    <th>Trạng thái</th>
                                                    <th class="text-center">Số lượng</th>
                                                    <th class="text-center">Tỷ lệ</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @php
                                                    $approvedPercent = ($totalRegistrations > 0) ? round(($approvedRegistrations / $totalRegistrations) * 100) : 0;
                                                    $rejectedPercent = ($totalRegistrations > 0) ? round(($rejectedRegistrations / $totalRegistrations) * 100) : 0;
                                                @endphp
                                                <tr>
                                                    <td><span class="badge bg-success">Đã duyệt</span></td>
                                                    <td class="text-center">{{ $approvedRegistrations ?? 0 }}</td>
                                                    <td class="text-center">{{ $approvedPercent }}%</td>
                                                </tr>
                                                <tr>
                                                    <td><span class="badge bg-danger">Từ chối</span></td>
                                                    <td class="text-center">{{ $rejectedRegistrations ?? 0 }}</td>
                                                    <td class="text-center">{{ $rejectedPercent }}%</td>
                                                </tr>
                                                <tr class="table-active">
                                                    <td><strong>Tổng cộng</strong></td>
                                                    <td class="text-center"><strong>{{ $totalRegistrations ?? 0 }}</strong></td> <!-- Hiển thị tổng số lượng -->
                                                    <td class="text-center"><strong>100%</strong></td> <!-- Tỷ lệ tổng là 100% -->
                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="text-center mt-3">
                                            <a href="{{ route('giangvien.dang-ky.index') }}" class="btn btn-primary">
                                                <i class="fas fa-list"></i> Xem tất cả đăng ký
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header bg-success text-white">
                                        <h5 class="mb-0">Thống Kê Sinh Viên</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <thead class="table-light">
                                                <tr>
                                                    <th>Trạng thái</th>
                                                    <th class="text-center">Số lượng SV</th>
                                                    <th class="text-center">Tỷ lệ</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @php
                                                    $totalPercentage = 0;
                                                @endphp
                                                @forelse($studentsByStatus ?? [] as $status)
                                                    @php
                                                        $percentage = ($totalStudents > 0) ? round(($status->count / $totalStudents) * 100) : 0;
                                                        $totalPercentage += $percentage;
                                                    @endphp
                                                    <tr>
                                                        <td>{{ $status->name }}</td>
                                                        <td class="text-center">{{ $status->count }}</td>
                                                        <td class="text-center">
                                                            {{ $percentage }}%
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="3" class="text-center">Không có dữ liệu</td>
                                                    </tr>
                                                @endforelse
                                                @if(isset($studentsByStatus) && count($studentsByStatus) > 0)
                                                    <tr class="table-active">
                                                        <td><strong>Tổng cộng</strong></td>
                                                        <td class="text-center"><strong>{{ $totalStudents ?? 0 }}</strong></td>
                                                        <td class="text-center"><strong>{{ $totalPercentage }}%</strong></td>
                                                    </tr>
                                                @endif
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="text-center mt-3">
                                            <a href="{{ route('giangvien.sinh-vien.index') }}" class="btn btn-success">
                                                <i class="fas fa-user-graduate"></i> Xem tất cả sinh viên
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> --}}

                        <!-- Thống kê chi tiết -->
                        {{-- <div class="row mt-4">
                            @if(isset($latestRegistrations) && count($latestRegistrations) > 0)
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header bg-secondary text-white">
                                            <h5 class="mb-0">Đăng Ký Gần Đây</h5>
                                        </div>
                                        <div class="card-body">
                                            <ul class="list-group">
                                                @foreach($latestRegistrations as $registration)
                                                    <li class="list-group-item">
                                                        <div class="d-flex justify-content-between">
                                                            <div>
                                                                @if($registration->status == 'da_duyet')
                                                                    <i class="fas fa-check-circle text-success"></i>
                                                                @elseif($registration->status == 'cho_duyet')
                                                                    <i class="fas fa-clock text-warning"></i>
                                                                @else
                                                                    <i class="fas fa-times-circle text-danger"></i>
                                                                @endif
                                                                {{ $registration->description }}
                                                            </div>
                                                            <small class="text-muted">{{ $registration->created_at->diffForHumans() }}</small>
                                                        </div>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div> --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
