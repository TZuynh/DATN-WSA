<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Đăng ký đề tài tốt nghiệp</title>
    <style>
        body {
            font-family: 'DejaVu Sans', 'Times New Roman', serif;
            font-size: 14px;
            line-height: 1.5;
            color: #000;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            min-height: 100vh;
            box-sizing: border-box;
        }
        @page {
            margin: 0;
            padding: 0;
        }
        .container {
            width: 760px;
            margin: 40px auto;
            padding: 0 20px 0 30px;
            box-sizing: border-box;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            position: relative;
            width: 100%;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        .header-table td {
            width: 30%;
            font-size: 16px;
            padding: 0;
        }
        .header-table td:first-child {
            padding-right: 20px;
        }
        .header-table td:last-child {
            padding-left: 20px;
        }
        .header-title {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .header-subtitle {
            text-align: center;
            font-size: 14px;
            margin-bottom: 20px;
        }
        .content-section {
            margin-bottom: 20px;
            width: 100%;
        }
        .content-table {
            width: 100%;
            border-collapse: collapse;
        }
        .content-table td {
            padding: 0;
            vertical-align: top;
        }
        .section-title {
            width: 10px;
            white-space: nowrap;
            padding-right: 10px;
        }
        .section-content {
            font-weight: bold;
            text-transform: uppercase;
            white-space: nowrap;
        }
        .student-list {
            margin: 10px 0 0 0;
            padding: 0;
            list-style: none;
        }
        .student-item {
            margin-bottom: 10px;
            padding: 0;
        }
        .student-info {
            display: inline-block;
            margin-right: 20px;
        }
        .student-name {
            font-weight: bold;
            margin-right: 10px;
        }
        .student-detail {
            font-weight: normal;
            margin-right: 10px;
        }
        .topic-title {
            text-transform: uppercase;
        }
        .topic-content {
            margin: 20px 0;
            min-height: 200px;
        }
        .topic-content-description {
            font-style: italic;
            font-weight: normal;
            text-transform: none;
            white-space: pre-wrap;
            display: block;
            margin-top: 10px;
        }
        .content-table td.section-content.topic-content-description {
            display: block;
            width: 100%;
        }
        .signature-section {
            margin-top: 50px;
            width: 100%;
        }
        .signature-table {
            width: 100%;
        }
        .signature-table td {
            width: 33.33%;
            text-align: center;
            vertical-align: top;
            padding: 0 10px;
        }
        .signature-title {
            margin-bottom: 50px;
        }
        .signature-line {
            margin-top: -50px;
            text-align: center;
        }
        .date-text {
            text-transform: lowercase;
            font-weight: normal;
        }
        .header-content {
            display: flex;
            flex-direction: row;
            justify-content: space-between;
            width: 100%;
        }
        .content-table td.section-content .student-list {
            display: block;
            width: 100%;
        }
        .content-text {
            font-weight: normal;
            text-transform: none;
            white-space: pre-wrap;
            display: block;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <table class="header-table">
                <tr>
                    <td>Trường CĐ Kỹ Thuật Cao Thắng</td>
                    <td style="text-align: center;">Cộng hòa xã hội chủ nghĩa Việt Nam</td>
                </tr>
                <tr>
                    <td>Khoa Công Nghệ Thông Tin</td>
                    <td style="text-align: center;">Độc lập - Tự do - Hạnh phúc</td>
                </tr>
            </table>
            <div class="header-title">ĐĂNG KÝ ĐỀ TÀI TỐT NGHIỆP</div>
            <div class="header-subtitle">Niên khóa: 2022 - 2025</div>
        </div>

        <div class="content-section">
            <table class="content-table">
                <tr>
                    <td class="section-title">GIẢNG VIÊN HƯỚNG DẪN:</td>
                    <td class="section-content">{{ $deTai->giangVien->ten ?? 'Chưa có giảng viên' }}</td>
                </tr>
            </table>
        </div>

        <div class="content-section">
            <table class="content-table">
                <tr>
                    <td class="section-title">SINH VIÊN THỰC HIỆN:</td>
                    <td class="section-content">
                        @if($deTai->nhom && $deTai->nhom->sinhViens->count() > 0)
                            <ul class="student-list">
                                @foreach($deTai->nhom->sinhViens as $index => $sinhVien)
                                    <li class="student-item">
                                        {{ $index + 1 }}. 
                                        <span class="student-name">{{ $sinhVien->ten }}</span>
                                        <span class="student-detail">MSSV: {{ $sinhVien->mssv }}</span>
                                        <span class="student-detail">Lớp: {{ $sinhVien->lop->ten_lop ?? 'N/A' }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <div class="student-item">Chưa có sinh viên thực hiện</div>
                        @endif
                    </td>
                </tr>
            </table>
        </div>

        <div class="content-section">
            <table class="content-table">
                <tr>
                    <td class="section-title">TÊN ĐỀ TÀI:</td>
                    <td class="section-content topic-title">{{ $deTai->ten_de_tai }}</td>
                </tr>
            </table>
        </div>

        <div class="content-section">
            <table class="content-table">
                <tr>
                    <td class="section-title">NỘI DUNG YÊU CẦU CỦA ĐỀ TÀI:</td>
                </tr>
                <tr>
                    <td colspan="2">
                        <div class="content-text">{{ $deTai->mo_ta ?? 'Chưa có nội dung yêu cầu' }}</div>
                    </td>
                </tr>
            </table>
        </div>

        <div class="content-section">
            <table class="content-table">
                <tr>
                    <td class="section-title" style="font-size: 16px;">Thời gian thực hiện đề tài:</td>
                    <td class="section-content">
                        <span class="date-text">từ ngày</span> {{ $deTai->ngay_bat_dau ? $deTai->ngay_bat_dau->format('d/m/Y') : 'N/A' }} 
                        <span class="date-text">đến ngày</span> {{ $deTai->ngay_ket_thuc ? $deTai->ngay_ket_thuc->format('d/m/Y') : 'N/A' }}
                    </td>
                </tr>
            </table>
        </div>

        <div class="content-section">
            <table class="content-table">
                <tr>
                    <td class="section-title">Ý KIẾN CỦA GIẢNG VIÊN HƯỚNG DẪN:</td>
                </tr>
                <tr>
                    <td colspan="2">
                        <div class="content-text">{{ $deTai->y_kien_giang_vien ?? 'Chưa có ý kiến' }}</div>
                    </td>
                </tr>
            </table>
        </div>

        <div class="signature-section">
            <table class="signature-table">
                <tr>
                    <td>
                        <div class="signature-title">Giám Hiệu</div>
                        <div class="signature-line"></div>
                    </td>
                    <td>
                        <div class="signature-title">Khoa Công Nghệ Thông Tin</div>
                        <div class="signature-line"></div>
                    </td>
                    <td>
                        <div class="signature-title">GV Hướng dẫn</div>
                        <div class="signature-line">(Ký và ghi rõ họ tên)</div>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html> 