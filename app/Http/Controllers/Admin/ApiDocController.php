<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ApiDocController extends Controller
{
    public function index()
    {
        $apis = [
            'auth' => [
                'title' => 'Xác thực',
                'endpoints' => [
                    [
                        'method' => 'POST',
                        'url' => '/api/auth/login',
                        'description' => 'Đăng nhập vào hệ thống',
                        'params' => [
                            'email' => 'Email người dùng',
                            'password' => 'Mật khẩu'
                        ],
                        'response' => [
                            'success' => [
                                'token' => 'JWT token',
                                'user' => 'Thông tin người dùng'
                            ],
                            'error' => [
                                'message' => 'Thông báo lỗi'
                            ]
                        ]
                    ],
                    [
                        'method' => 'POST',
                        'url' => '/api/auth/logout',
                        'description' => 'Đăng xuất khỏi hệ thống',
                        'headers' => [
                            'Authorization' => 'Bearer {token}'
                        ],
                        'response' => [
                            'success' => [
                                'message' => 'Đăng xuất thành công'
                            ]
                        ]
                    ]
                ]
            ],
            'de-tai' => [
                'title' => 'Quản lý đề tài',
                'endpoints' => [
                    [
                        'method' => 'GET',
                        'url' => '/api/de-tai',
                        'description' => 'Lấy danh sách đề tài',
                        'params' => [
                            'page' => 'Số trang',
                            'per_page' => 'Số lượng mỗi trang',
                            'search' => 'Từ khóa tìm kiếm'
                        ],
                        'response' => [
                            'data' => 'Danh sách đề tài',
                            'meta' => 'Thông tin phân trang'
                        ]
                    ],
                    [
                        'method' => 'POST',
                        'url' => '/api/de-tai',
                        'description' => 'Tạo đề tài mới',
                        'params' => [
                            'ten_de_tai' => 'Tên đề tài',
                            'mo_ta' => 'Mô tả',
                            'ngay_bat_dau' => 'Ngày bắt đầu',
                            'ngay_ket_thuc' => 'Ngày kết thúc',
                            'nhom_id' => 'ID nhóm',
                            'giang_vien_id' => 'ID giảng viên'
                        ],
                        'response' => [
                            'success' => [
                                'message' => 'Tạo đề tài thành công',
                                'data' => 'Thông tin đề tài'
                            ],
                            'error' => [
                                'message' => 'Thông báo lỗi'
                            ]
                        ]
                    ]
                ]
            ],
            'hoi-dong' => [
                'title' => 'Quản lý hội đồng',
                'endpoints' => [
                    [
                        'method' => 'GET',
                        'url' => '/api/hoi-dong',
                        'description' => 'Lấy danh sách hội đồng',
                        'params' => [
                            'page' => 'Số trang',
                            'per_page' => 'Số lượng mỗi trang'
                        ],
                        'response' => [
                            'data' => 'Danh sách hội đồng',
                            'meta' => 'Thông tin phân trang'
                        ]
                    ],
                    [
                        'method' => 'POST',
                        'url' => '/api/hoi-dong',
                        'description' => 'Tạo hội đồng mới',
                        'params' => [
                            'ten' => 'Tên hội đồng',
                            'mo_ta' => 'Mô tả',
                            'ngay_hop' => 'Ngày họp',
                            'dia_diem' => 'Địa điểm'
                        ],
                        'response' => [
                            'success' => [
                                'message' => 'Tạo hội đồng thành công',
                                'data' => 'Thông tin hội đồng'
                            ],
                            'error' => [
                                'message' => 'Thông báo lỗi'
                            ]
                        ]
                    ]
                ]
            ]
        ];

        return view('admin.api-doc.index', compact('apis'));
    }
} 