<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Chi Tiết Đề Tài</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #333;
            background-color: #fff;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 40px;
            position: relative;
            padding: 20px 0;
            border-bottom: 2px solid #1a237e;
        }
        .header-content {
            text-align: center;
            margin-top: 20px;
        }
        .header h1 {
            font-size: 24px;
            margin-bottom: 8px;
            color: #1a237e;
            text-transform: uppercase;
            font-weight: bold;
        }
        .header h2 {
            font-size: 18px;
            margin: 10px 0;
            color: #0d47a1;
            font-weight: bold;
        }
        .header p {
            font-size: 14px;
            margin: 5px 0;
            color: #424242;
        }
        .logo-container {
            position: absolute;
            top: 0;
            width: 100px;
            height: 100px;
        }
        .logo-left {
            left: 0;
        }
        .logo-right {
            right: 0;
        }
        .logo-container img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }
        .info-section {
            margin-bottom: 30px;
            background-color: #f5f5f5;
            padding: 15px;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .info-section h3 {
            font-size: 16px;
            margin-bottom: 15px;
            color: #1a237e;
            border-bottom: 2px solid #1a237e;
            padding-bottom: 8px;
            font-weight: bold;
        }
        .info-row {
            margin-bottom: 12px;
            display: flex;
            align-items: flex-start;
            border-bottom: 1px dashed #e0e0e0;
            padding-bottom: 8px;
        }
        .info-row:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        .info-label {
            width: 180px;
            font-weight: bold;
            display: inline-block;
            vertical-align: top;
            color: #424242;
        }
        .info-value {
            flex: 1;
            display: inline-block;
            vertical-align: top;
            padding-left: 15px;
            color: #212121;
        }
        .status-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: bold;
            color: #fff;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .status-0 { background-color: #ffa000; } /* Đang thực hiện */
        .status-1 { background-color: #43a047; } /* GV đồng ý */
        .status-2 { background-color: #1e88e5; } /* GVPB đồng ý */
        .status-3 { background-color: #e53935; } /* Không được GVHD đồng ý */
        .status-4 { background-color: #d32f2f; } /* Không được GVPB đồng ý */
        .footer {
            margin-top: 50px;
            text-align: right;
            border-top: 2px solid #1a237e;
            padding-top: 15px;
            color: #424242;
        }
        .footer p {
            margin: 5px 0;
            font-size: 12px;
        }
        .footer p:last-child {
            font-weight: bold;
            color: #1a237e;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo-container logo-left">
            <img src="data:image/jpeg;base64,{{ base64_encode(file_get_contents(public_path('images/logo.jpg'))) }}" alt="Logo trái">
        </div>
        <div class="logo-container logo-right">
            <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('images/logo-caothang.png'))) }}" alt="Logo phải">
        </div>
        <div class="header-content">
            <h1>MẪU ĐĂNG KÝ</h1>
            <h2>TRƯỜNG CAO ĐẲNG KỸ THUẬT CAO THẮNG</h2>
            <p>KHOA CÔNG NGHỆ THÔNG TIN</p>
        </div>
    </div>

    <div class="info-section">
        <h3>Thông tin đề tài</h3>
        <div class="info-row">
            <div class="info-label">Mã đề tài:</div>
            <div class="info-value">{{ $deTai->ma_de_tai }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Tên đề tài:</div>
            <div class="info-value">{{ $deTai->ten_de_tai }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Mô tả:</div>
            <div class="info-value">{{ $deTai->mo_ta ?? 'Chưa có mô tả' }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Ý kiến giảng viên:</div>
            <div class="info-value">{{ $deTai->y_kien_giang_vien ?? 'Chưa có ý kiến giảng viên' }}</div>
        </div>
    </div>

    <div class="info-section">
        <h3>Thông tin thời gian</h3>
        <div class="info-row">
            <div class="info-label">Ngày bắt đầu:</div>
            <div class="info-value">{{ $deTai->ngay_bat_dau ? $deTai->ngay_bat_dau->format('d/m/Y') : 'N/A' }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Ngày kết thúc:</div>
            <div class="info-value">{{ $deTai->ngay_ket_thuc ? $deTai->ngay_ket_thuc->format('d/m/Y') : 'N/A' }}</div>
        </div>
    </div>

    <div class="info-section">
        <h3>Thông tin nhóm thực hiện</h3>
        <div class="info-row">
            <div class="info-label">Mã nhóm:</div>
            <div class="info-value">{{ $deTai->nhom->ma_nhom ?? 'N/A' }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Tên nhóm:</div>
            <div class="info-value">{{ $deTai->nhom->ten ?? 'N/A' }}</div>
        </div>
    </div>

    <div class="info-section">
        <h3>Trạng thái đề tài</h3>
        <div class="info-row">
            <div class="info-label">Trạng thái:</div>
            <div class="info-value">
                <span class="status-badge status-{{ $deTai->trang_thai }}">
                    @switch($deTai->trang_thai)
                        @case(0)
                            Đang thực hiện
                            @break
                        @case(1)
                            Giảng viên đồng ý báo cáo
                            @break
                        @case(2)
                            Giảng viên phản biện đồng ý báo cáo
                            @break
                        @case(3)
                            Không được giảng viên hướng dẫn đồng ý
                            @break
                        @case(4)
                            Không được giảng viên phản biện đồng ý
                            @break
                    @endswitch
                </span>
            </div>
        </div>
    </div>

    <div class="footer">
        <p>Ngày xuất: {{ now()->format('d/m/Y H:i:s') }}</p>
        <p>Giảng viên: {{ auth()->user()->ten }}</p>
    </div>
</body>
</html> 