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
                                        <h5 class="card-title" style="font-size: 1.1rem; font-weight: 500;">Tổng sinh viên</h5>
                                        <p class="card-text display-4" style="font-size: 2.5rem; font-weight: 600; margin: 0;">{{ $totalSinhVien ?? 0 }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3 mb-4">
                                <div class="card" style="background: linear-gradient(135deg, #1cc88a 0%, #13855c 100%); border: none; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
                                    <div class="card-body text-white">
                                        <h5 class="card-title" style="font-size: 1.1rem; font-weight: 500;">Tổng giảng viên</h5>
                                        <p class="card-text display-4" style="font-size: 2.5rem; font-weight: 600; margin: 0;">{{ $totalGiangVien ?? 0 }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3 mb-4">
                                <div class="card" style="background: linear-gradient(135deg, #4e73df 0%, #224abe 100%); border: none; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
                                    <div class="card-body text-white">
                                        <h5 class="card-title" style="font-size: 1.1rem; font-weight: 500;">Tổng nhóm</h5>
                                        <p class="card-text display-4" style="font-size: 2.5rem; font-weight: 600; margin: 0;">{{ $totalNhom ?? 0 }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3 mb-4">
                                <div class="card" style="background: linear-gradient(135deg, #f6c23e 0%, #dda20a 100%); border: none; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
                                    <div class="card-body text-white">
                                        <h5 class="card-title" style="font-size: 1.1rem; font-weight: 500;">Tổng đề tài</h5>
                                        <p class="card-text display-4" style="font-size: 2.5rem; font-weight: 600; margin: 0;">{{ $totalDeTai ?? 0 }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
