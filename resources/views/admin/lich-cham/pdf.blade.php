<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
        }
        .header {
            text-align: center;
            margin-bottom: 0px;
            line-height: 1;
        }
        .title {
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 0px;
            line-height: 1;
        }
        .info {
            margin-bottom: 0px;
            line-height: 1;
        }
        .info p {
            margin: 0;
            padding: 0;
            line-height: 1;
            font-size: 9px;
        }
        .info .thoi-gian {
            font-size: 8px;
            color: red;
            text-align: right;
            margin: 0;
            padding: 0;
            line-height: 1;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            margin-left: auto;
            margin-right: auto;
            font-size: 9px;
        }
        th, td {
            border: 1px solid #000;
            padding: 3px;
            text-align: center;
            word-wrap: break-word;
            white-space: nowrap;
        }
        th {
            background-color: #b9b9b9;
            font-weight: bold;
            font-size: 9px;
        }
        /* Điều chỉnh width các cột */
        td:nth-child(1) { /* STT */
            width: 5%;
            text-align: center;
        }
        td:nth-child(2) { /* MSSV */
            width: 10%;
            text-align: left;
        }
        td:nth-child(3) { /* Họ và Tên */
            width: 15%;
            text-align: left;
        }
        td:nth-child(4) { /* Lớp */
            width: 8%;
            text-align: left;
        }
        td:nth-child(5) { /* Đề Tài */
            width: 25%;
            text-align: left;
        }
        td:nth-child(6) { /* GVHD */
            width: 15%;
            text-align: left;
        }
        td:nth-child(7) { /* GVPB */
            width: 15%;
            text-align: left;
        }
        td:nth-child(8) { /* HĐ */
            width: 7%;
            text-align: cen;
        }
        /* Căn trái cho các cột có nội dung dài */
        td:nth-child(3),
        td:nth-child(5),
        td:nth-child(6),
        td:nth-child(7) {
            text-align: left;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .footer {
            margin-top: 20px;
            text-align: right;
            font-size: 9px;
        }
        .signature {
            width: 80px;
            height: 40px;
            border-bottom: 1px solid #000;
            margin: 0 auto;
        }
        /* Thêm style cho page break */
        .page-break {
            page-break-after: always;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    @foreach($groupedData as $group)
        @php
            $hoiDong = $group['hoiDong'];
            $phong = $hoiDong->phong ?? null;
            $truongTieuBan = $group['truongTieuBan'];
            $thuKy = $group['thuKy'];
            $lichChams = $group['lichChams']->sortBy('thu_tu');
        @endphp
        <div class="header">
            <div class="title">DANH SÁCH NHÓM BẢO VỆ ĐATN - {{ $hoiDong->ten ?? 'Chưa có hội đồng' }} - {{ $phong->ten_phong ?? 'Chưa có phòng' }}</div>
            <div class="info">
                <p style="font-weight: bold;">Trưởng tiểu ban: {{ $truongTieuBan }}</p>
                <p style="font-weight: bold;">Thư ký: {{ $thuKy }}</p>
                <p class="thoi-gian">Thời gian: {{ $group['lichTao'] ?? 'Chưa có thời gian' }}</p>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>STT</th>
                    <th>MSSV</th>
                    <th>Họ và Tên</th>
                    <th>Lớp</th>
                    <th>Đề Tài</th>
                    <th>GVHD</th>
                    <th>GVPB</th>
                    <th>HĐ</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $stt = 1;
                @endphp
                @foreach($lichChams as $lichCham)
                    @foreach($lichCham->nhom->sinhViens as $index => $sinhVien)
                    <tr>
                        @if($index === 0)
                            <td rowspan="{{ $lichCham->nhom->sinhViens->count() }}">{{ $stt }}</td>
                            <td>{{ $sinhVien->mssv }}</td>
                            <td>{{ $sinhVien->ten }}</td>
                            <td>{{ $sinhVien->lop->ten_lop }}</td>
                            <td>{{ $lichCham->nhom->deTai->ten_de_tai ?? '' }}</td>
                            <td>{{ $lichCham->nhom->giangVien->ten ?? '' }}</td>
                            <td>{{ $lichCham->phanCongCham->giangVienPhanBien->ten ?? '' }}</td>
                            <td rowspan="{{ $lichCham->nhom->sinhViens->count() }}">{{ preg_replace('/[^0-9]/', '', $lichCham->hoiDong->ten ?? '') }}</td>
                        @else
                            <td>{{ $sinhVien->mssv }}</td>
                            <td>{{ $sinhVien->ten }}</td>
                            <td>{{ $sinhVien->lop->ten_lop }}</td>
                            <td></td>
                            <td></td>
                            <td></td>
                        @endif
                    </tr>
                    @endforeach
                    @php
                        $stt++;
                    @endphp
                @endforeach
            </tbody>
        </table>
        <div class="page-break"></div>
    @endforeach
</body>
</html>