@extends('components.giangvien.app')

@section('title', content: 'Chấm điểm')

@section('content')
@if(isset($isTruongTieuBan) && $isTruongTieuBan)
    <div class="mb-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Danh sách điểm hội đồng (Trưởng tiểu ban)</h3>
            </div>
            <div class="card-body">
                @if($bangDiemBySinhVienHoiDong->isEmpty())
                    <div class="alert alert-info">Không có dữ liệu điểm nào trong các hội đồng bạn phụ trách.</div>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>STT</th>
                                    <th>MSSV</th>
                                    <th>Tên sinh viên</th>
                                    <th>Đợt báo cáo</th>
                                    <th>Điểm trung bình báo cáo</th>
                                    <th>Tổng điểm trung bình</th>
                                    <th>Điểm tổng kết</th>
                                    <th>Giảng viên chấm</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $stt = 1; @endphp
                                @foreach($bangDiemBySinhVienHoiDong as $sinhVienId => $group)
                                    @php
                                        $bangDiemList = $group['list'];
                                        $diem_bao_cao_tb = $group['diem_bao_cao_tb'];
                                        $tong_ket = $group['tong_ket'];
                                        $diem_tong_ket = $group['diem_tong_ket'];
                                        $sinhVien = $bangDiemList->first()->sinhVien ?? null;
                                        $dotBaoCao = $bangDiemList->first()->dotBaoCao ?? null;
                                        $giangVienChams = $bangDiemList->pluck('giangVien.ten')->unique()->filter()->implode(', ');
                                    @endphp
                                    <tr>
                                        <td>{{ $stt++ }}</td>
                                        <td>{{ $sinhVien->mssv ?? '' }}</td>
                                        <td>{{ $sinhVien->ten ?? '' }}</td>
                                        <td>{{ ($dotBaoCao->nam_hoc ?? 'N/A') . ' - ' . ($dotBaoCao->hocKy->ten ?? 'N/A') }}</td>
                                        <td>{{ $diem_bao_cao_tb !== null ? number_format($diem_bao_cao_tb, 2) : '-' }}</td>
                                        <td>{{ $tong_ket !== null ? number_format($tong_ket, 2) : '-' }}</td>
                                        <td>{{ $diem_tong_ket !== null ? number_format(min($diem_tong_ket, 10), 2) : '-' }}</td>
                                        <td>{{ $giangVienChams }}</td>
                                        <td>
                                            @if($bangDiemList->first())
                                                <a href="{{ route('giangvien.bang-diem.show', $bangDiemList->first()->id) }}" class="btn btn-sm btn-info" title="Xem chi tiết">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endif
<div class="container-fluid">
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">Danh sách sinh viên cần chấm điểm</h3>
        </div>
        <div class="card-body">
          @if($dsSinhVien->isEmpty())
            @if($phanCongTheoDeTai->isNotEmpty())
              <div class="alert alert-info">
                <p>Có các đề tài bạn được phân công chấm:</p>
              </div>
              <div class="table-responsive">
                <table class="table table-bordered table-striped">
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th>MSV</th>
                      <th>Họ tên</th>
                      <th>Đề tài</th>
                      <th>Nhóm</th>
                      <th>Đợt báo cáo</th>
                      <th>Lịch</th>
                      <th>Vai trò</th>
                      <th>BC</th>
                      <th>TT</th>
                      <th>Demo</th>
                      <th>Q&A</th>
                      <th>Cộng</th>
                      <th>Tổng</th>
                      <th>Thao tác</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($phanCongTheoDeTai as $idx => $pc)
                        @php
                          $deTai   = $pc['de_tai'];
                          $nhom    = $pc['nhom'];
                          $lich    = $pc['lich'];
                          $baoCao  = optional($lich)->dotBaoCao;
                          $role    = $pc['vai_tro_cham'];
                          $dotBcId = optional($baoCao)->id;
                        @endphp
                      @foreach(optional($nhom)->sinhViens ?? [] as $sv)
                        @php
                          $row  = ($idx+1) . '.' . $loop->iteration;
                          $bd   = $bangDiems->where('sinh_vien_id',$sv->id)->where('dot_bao_cao_id',$dotBcId)->first();
                          $chu  = $bangDiems->where('sinh_vien_id',$sv->id)->whereNull('dot_bao_cao_id')->first();
                          $bc   = $bd->diem_bao_cao ?? ($chu->diem_bao_cao ?? '-');
                          $tt   = $bd->diem_thuyet_trinh ?? ($chu->diem_thuyet_trinh ?? '-');
                          $demo = $bd->diem_demo ?? '-';
                          $qa   = $bd->diem_cau_hoi ?? '-';
                          $cong = $bd->diem_cong ?? '-';
                          $tong = (is_numeric($bc) && is_numeric($tt))
                                  ? number_format(($bc * 0.2) + ($demo==='-'?0:$demo) + ($tt==='-'?0:$tt) + ($qa==='-'?0:$qa) + ($cong==='-'?0:$cong),1)
                                  : '-';
                          $pars = [$sv->id];
                          if($dotBcId) $pars[] = $dotBcId;
                        @endphp
                        <tr>
                          <td>{{ $row }}</td>
                          <td>{{ $sv->mssv }}</td>
                          <td>{{ $sv->ten }}</td>
                          <td>{{ $deTai->ma_de_tai }} - {{ $deTai->ten_de_tai }}</td>
                          <td>{{ $nhom->ma_nhom }} - {{ $nhom->ten }}</td>
                          <td>{{ optional($baoCao)->nam_hoc ?? '-' }} - {{ optional($baoCao)->hocKy->ten ?? '' }}</td>
                          <td>{{ optional($lich)->lich_tao ? \Carbon\Carbon::parse(optional($lich)->lich_tao)->format('d/m H:i') : '-' }}</td>
                          <td>
                            <span class="badge {{ $role=='Phản biện'?'bg-primary':'' }} {{ $role=='Hướng dẫn'?'bg-success':'' }} {{ !in_array($role,['Phản biện','Hướng dẫn'])?'bg-secondary':'' }}">
                              {{ $role }}
                            </span>
                          </td>
                          <td>{{ is_numeric($bc) ? number_format($bc, 1) : $bc }}</td>
                          <td>{{ is_numeric($tt) ? number_format($tt, 1) : $tt }}</td>
                          <td>{{ is_numeric($demo) ? number_format($demo, 1) : $demo }}</td>
                          <td>{{ is_numeric($qa) ? number_format($qa, 1) : $qa }}</td>
                          <td>{{ is_numeric($cong) ? number_format($cong, 1) : $cong }}</td>
                          <td><strong>{{ $tong }}</strong></td>
                          <td>
                            <a href="{{ route('giangvien.bang-diem.create', $pars) }}" class="btn btn-sm btn-primary">
                              <i class="fas fa-plus"></i> Chấm
                            </a>
                          </td>
                        </tr>
                      @endforeach
                    @endforeach
                  </tbody>
                </table>
              </div>
            @else
              <div class="alert alert-info">
                <p>Không có sinh viên cần chấm.</p>
              </div>
            @endif
          @else
            {{-- Bảng sinh viên cần chấm --}}
            <div class="table-responsive">
              <table class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>STT</th>
                    <th>MSV</th>
                    <th>Họ tên</th>
                    <th>Đề tài</th>
                    <th>Nhóm</th>
                    <th>Đợt báo cáo</th>
                    <th>Lịch</th>
                    <th>Vai trò</th>
                    <th>BC</th>
                    <th>TT</th>
                    <th>Demo</th>
                    <th>Q&A</th>
                    <th>Cộng</th>
                    <th>Tổng</th>
                    <th>Thao tác</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($dsSinhVien as $item)
                    @php
                      $sv    = $item['sinh_vien'];
                      $nhom  = $item['nhom'];
                      $deTai = $item['de_tai'];
                      $role  = $item['vai_tro_cham']; // Đã đúng chuẩn từ controller
                      $lich  = $deTai->lichCham;
                      $baoCao = optional($lich)->dotBaoCao;
                      $dotBc  = optional($baoCao)->id;
                      $da     = $bangDiems->where('sinh_vien_id',$sv->id)->where('dot_bao_cao_id',$dotBc)->first();
                      $chu    = $bangDiems->where('sinh_vien_id',$sv->id)->whereNull('dot_bao_cao_id')->first();
                      $bc   = floatval($da->diem_bao_cao ?? $chu->diem_bao_cao ?? 0);
                      $tt   = floatval($da->diem_thuyet_trinh ?? $chu->diem_thuyet_trinh ?? 0);
                      $demo = floatval($da->diem_demo ?? 0);
                      $qa   = floatval($da->diem_cau_hoi ?? 0);
                      $cong = floatval($da->diem_cong ?? 0);
                      $tong = ($bc * 0.2) + $tt + $demo + $qa + $cong;
                      $pars   = ['sinhVienId'=>$sv->id]; if($dotBc) $pars['dotBaoCaoId']=$dotBc;
                    @endphp
                      <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $sv->mssv }}</td>
                        <td>{{ $sv->ten }}</td>
                        <td>{{ $deTai->ma_de_tai }} - {{ $deTai->ten_de_tai }}</td>
                        <td>{{ $nhom->ma_nhom }} - {{ $nhom->ten }}</td>
                        <td>{{ optional($baoCao)->nam_hoc ?? '-' }} - {{ optional($baoCao)->hocKy->ten ?? '' }}</td>
                        <td>{{ optional($lich)->lich_tao ? \Carbon\Carbon::parse(optional($lich)->lich_tao)->format('d/m H:i') : '-' }}</td>
                        <td>
                          @php
                            $roleLabel = $role;
                            $loaiGV = $item['phan_cong_vai_tro']->loai_giang_vien ?? null;
                            if (in_array($role, ['Trưởng tiểu ban', 'Thư ký', 'Thành viên']) && in_array($loaiGV, ['Giảng Viên Hướng Dẫn', 'Giảng Viên Phản Biện'])) {
                              $roleLabel .= $loaiGV === 'Giảng Viên Hướng Dẫn' ? ' (Hướng dẫn)' : ' (Phản biện)';
                            }
                          @endphp
                          <span class="badge 
                            {{ $role=='Phản biện' ? 'bg-primary' : '' }} 
                            {{ $role=='Hướng dẫn' ? 'bg-success' : '' }} 
                            {{ !in_array($role,['Phản biện','Hướng dẫn']) ? 'bg-secondary' : '' }}">
                            {{ $roleLabel }}
                          </span>
                        </td>
                        <td>{{ number_format($bc, 1) }}</td>
                        <td>{{ number_format($tt, 1) }}</td>
                        <td>{{ number_format($demo, 1) }}</td>
                        <td>{{ number_format($qa, 1) }}</td>
                        <td>{{ number_format($cong, 1) }}</td>
                        <td><strong>{{ number_format($tong, 1) }}</strong></td>
                        <td>
                          @if($da)
                            <a href="{{ route('giangvien.bang-diem.edit', $da->id) }}" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                          @elseif($chu && $dotBc)
                            <a href="{{ route('giangvien.bang-diem.edit',$chu->id) }}" class="btn btn-sm btn-info"><i class="fas fa-sync"></i></a>
                          @else
                            <a href="{{ route('giangvien.bang-diem.create',$pars) }}" class="btn btn-sm btn-primary"><i class="fas fa-plus"></i> Chấm</a>
                          @endif
                        </td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            @endif
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
