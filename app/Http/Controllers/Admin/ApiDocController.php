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
                'title' => 'Authentication API (project.test)',
                'description' => 'Các API xác thực chung cho toàn bộ hệ thống',
                'endpoints' => [
                    [
                        'method' => 'POST',
                        'url' => 'http://project.test/api/auth/login',
                        'description' => 'Đăng nhập vào hệ thống và lấy token',
                        'params' => [
                            'email' => 'Email của tài khoản',
                            'mat_khau' => 'Mật khẩu của tài khoản'
                        ],
                        'response' => [
                            'success' => [
                                'token_access' => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...',
                                'data' => [
                                    'id' => 1,
                                    'ten' => 'Nguyễn Văn A',
                                    'email' => 'nguyenvana@example.com',
                                    'vai_tro' => 'admin',
                                    'created_at' => '2024-03-15T08:00:00.000000Z',
                                    'updated_at' => '2024-03-15T08:00:00.000000Z'
                                ]
                            ],
                            'error' => [
                                'message' => 'Thông tin đăng nhập không chính xác.',
                                'errors' => [
                                    'email' => ['Thông tin đăng nhập không chính xác.']
                                ]
                            ]
                        ]
                    ],
                    [
                        'method' => 'POST',
                        'url' => 'http://project.test/api/auth/logout',
                        'description' => 'Đăng xuất khỏi hệ thống',
                        'headers' => [
                            'Authorization' => 'Bearer {token}'
                        ],
                        'response' => [
                            'success' => [
                                'message' => 'Đăng xuất thành công'
                            ],
                            'error' => [
                                'message' => 'Unauthenticated.'
                            ]
                        ]
                    ],
                    [
                        'method' => 'GET',
                        'url' => 'http://project.test/api/auth/user',
                        'description' => 'Lấy thông tin tài khoản hiện tại',
                        'headers' => [
                            'Authorization' => 'Bearer {token}'
                        ],
                        'response' => [
                            'success' => [
                                'id' => 1,
                                'ten' => 'Nguyễn Văn A',
                                'email' => 'nguyenvana@example.com',
                                'vai_tro' => 'admin',
                                'created_at' => '2024-03-15T08:00:00.000000Z',
                                'updated_at' => '2024-03-15T08:00:00.000000Z'
                            ],
                            'error' => [
                                'message' => 'Unauthenticated.'
                            ]
                        ]
                    ]
                ]
            ],
            'admin' => [
                'title' => 'Admin API (admin.project.test)',
                'description' => 'Các API dành riêng cho quản trị viên',
                'endpoints' => [
                    // Thêm các endpoint API cho admin ở đây
                ]
            ],
            'giangvien' => [
                'title' => 'Giảng viên API (giangvien.project.test)',
                'description' => 'Các API dành riêng cho giảng viên',
                'endpoints' => [
                    // Thêm các endpoint API cho giảng viên ở đây
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