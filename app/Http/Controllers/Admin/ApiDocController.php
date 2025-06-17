<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DeTai;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\TaiKhoan;
use Illuminate\Support\Facades\Hash;

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
                'title' => 'Admin API',
                'description' => 'Các API dành riêng cho quản trị viên',
                'endpoints' => [
                    [
                        'method' => 'GET',
                        'url' => 'http://project.test/api/admin/de-tai',
                        'description' => 'Lấy danh sách đề tài (Admin)',
                        'params' => [
                            'page' => 'Số trang',
                            'per_page' => 'Số lượng mỗi trang',
                            'search' => 'Từ khóa tìm kiếm',
                            'trang_thai' => 'Trạng thái đề tài (Đang thực hiện, Hoàn thành, Đã hủy)',
                            'giang_vien_id' => 'ID giảng viên hướng dẫn',
                            'nhom_id' => 'ID nhóm thực hiện',
                            'ngay_bat_dau' => 'Ngày bắt đầu (YYYY-MM-DD)',
                            'ngay_ket_thuc' => 'Ngày kết thúc (YYYY-MM-DD)'
                        ],
                        'headers' => [
                            'Authorization' => 'Bearer {token}'
                        ],
                        'response' => [
                            'success' => [
                                'data' => [
                                    [
                                        'id' => 1,
                                        'ten_de_tai' => 'Tên đề tài',
                                        'mo_ta' => 'Mô tả đề tài',
                                        'trang_thai' => 'Đang thực hiện',
                                        'ngay_bat_dau' => '2024-03-15',
                                        'ngay_ket_thuc' => '2024-06-15',
                                        'diem' => 8.5,
                                        'nhom' => [
                                            'id' => 1,
                                            'ten_nhom' => 'Nhóm 1',
                                            'sinh_vien' => [
                                                [
                                                    'id' => 1,
                                                    'ten' => 'Nguyễn Văn A',
                                                    'mssv' => '20200001'
                                                ]
                                            ]
                                        ],
                                        'giang_vien' => [
                                            'id' => 1,
                                            'ten' => 'Nguyễn Văn B',
                                            'email' => 'nguyenvanb@example.com'
                                        ],
                                        'hoi_dong' => [
                                            'id' => 1,
                                            'ten' => 'Hội đồng 1',
                                            'ngay_hop' => '2024-06-15'
                                        ],
                                        'bao_cao' => [
                                            [
                                                'id' => 1,
                                                'tieu_de' => 'Báo cáo tuần 1',
                                                'trang_thai' => 'Đã duyệt'
                                            ]
                                        ],
                                        'created_at' => '2024-03-15T08:00:00.000000Z',
                                        'updated_at' => '2024-03-15T08:00:00.000000Z'
                                    ]
                                ],
                                'meta' => [
                                    'current_page' => 1,
                                    'per_page' => 10,
                                    'total' => 50,
                                    'last_page' => 5
                                ]
                            ],
                            'error' => [
                                'message' => 'Unauthorized',
                                'errors' => [
                                    'auth' => ['Bạn không có quyền truy cập']
                                ]
                            ]
                        ]
                    ]
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
                'description' => 'Các API quản lý đề tài và báo cáo',
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
                            'data' => [
                                'id' => 1,
                                'ten_de_tai' => 'Tên đề tài',
                                'mo_ta' => 'Mô tả đề tài',
                                'trang_thai' => 'Đang thực hiện',
                                'ngay_bat_dau' => '2024-03-15',
                                'ngay_ket_thuc' => '2024-06-15',
                                'nhom' => [
                                    'id' => 1,
                                    'ten_nhom' => 'Nhóm 1'
                                ],
                                'giang_vien' => [
                                    'id' => 1,
                                    'ten' => 'Nguyễn Văn A'
                                ]
                            ],
                            'meta' => [
                                'current_page' => 1,
                                'total' => 50
                            ]
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
                                'data' => [
                                    'id' => 1,
                                    'ten_de_tai' => 'Tên đề tài',
                                    'mo_ta' => 'Mô tả đề tài',
                                    'trang_thai' => 'Đang thực hiện',
                                    'ngay_bat_dau' => '2024-03-15',
                                    'ngay_ket_thuc' => '2024-06-15'
                                ]
                            ],
                            'error' => [
                                'message' => 'Thông báo lỗi',
                                'errors' => [
                                    'ten_de_tai' => ['Tên đề tài không được để trống']
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            'bao-cao' => [
                'title' => 'Quản lý báo cáo',
                'description' => 'Các API quản lý báo cáo quá trình',
                'endpoints' => [
                    [
                        'method' => 'GET',
                        'url' => '/api/bao-cao',
                        'description' => 'Lấy danh sách báo cáo',
                        'params' => [
                            'page' => 'Số trang',
                            'per_page' => 'Số lượng mỗi trang',
                            'de_tai_id' => 'ID đề tài'
                        ],
                        'response' => [
                            'data' => [
                                'id' => 1,
                                'tieu_de' => 'Báo cáo tuần 1',
                                'noi_dung' => 'Nội dung báo cáo',
                                'file_dinh_kem' => 'path/to/file.pdf',
                                'trang_thai' => 'Đã duyệt',
                                'de_tai' => [
                                    'id' => 1,
                                    'ten_de_tai' => 'Tên đề tài'
                                ],
                                'dot_bao_cao' => [
                                    'id' => 1,
                                    'ten' => 'Đợt 1'
                                ]
                            ],
                            'meta' => [
                                'current_page' => 1,
                                'total' => 20
                            ]
                        ]
                    ],
                    [
                        'method' => 'POST',
                        'url' => '/api/bao-cao',
                        'description' => 'Tạo báo cáo mới',
                        'params' => [
                            'tieu_de' => 'Tiêu đề báo cáo',
                            'noi_dung' => 'Nội dung báo cáo',
                            'file_dinh_kem' => 'File đính kèm',
                            'de_tai_id' => 'ID đề tài',
                            'dot_bao_cao_id' => 'ID đợt báo cáo'
                        ],
                        'response' => [
                            'success' => [
                                'message' => 'Tạo báo cáo thành công',
                                'data' => [
                                    'id' => 1,
                                    'tieu_de' => 'Báo cáo tuần 1',
                                    'noi_dung' => 'Nội dung báo cáo',
                                    'file_dinh_kem' => 'path/to/file.pdf',
                                    'trang_thai' => 'Chờ duyệt'
                                ]
                            ],
                            'error' => [
                                'message' => 'Thông báo lỗi',
                                'errors' => [
                                    'tieu_de' => ['Tiêu đề không được để trống']
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            'hoi-dong' => [
                'title' => 'Quản lý hội đồng',
                'description' => 'Các API quản lý hội đồng chấm điểm',
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
                            'data' => [
                                'id' => 1,
                                'ten' => 'Hội đồng 1',
                                'mo_ta' => 'Mô tả hội đồng',
                                'ngay_hop' => '2024-06-15',
                                'dia_diem' => 'Phòng A101',
                                'trang_thai' => 'Đang hoạt động',
                                'thanh_vien' => [
                                    [
                                        'id' => 1,
                                        'ten' => 'Nguyễn Văn A',
                                        'vai_tro' => 'Chủ tịch'
                                    ]
                                ]
                            ],
                            'meta' => [
                                'current_page' => 1,
                                'total' => 10
                            ]
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
                            'dia_diem' => 'Địa điểm',
                            'thanh_vien' => [
                                [
                                    'giang_vien_id' => 'ID giảng viên',
                                    'vai_tro_id' => 'ID vai trò'
                                ]
                            ]
                        ],
                        'response' => [
                            'success' => [
                                'message' => 'Tạo hội đồng thành công',
                                'data' => [
                                    'id' => 1,
                                    'ten' => 'Hội đồng 1',
                                    'mo_ta' => 'Mô tả hội đồng',
                                    'ngay_hop' => '2024-06-15',
                                    'dia_diem' => 'Phòng A101',
                                    'trang_thai' => 'Đang hoạt động'
                                ]
                            ],
                            'error' => [
                                'message' => 'Thông báo lỗi',
                                'errors' => [
                                    'ten' => ['Tên hội đồng không được để trống']
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            'nhan-xet' => [
                'title' => 'Quản lý nhận xét',
                'description' => 'Các API quản lý nhận xét và đánh giá',
                'endpoints' => [
                    [
                        'method' => 'GET',
                        'url' => '/api/nhan-xet',
                        'description' => 'Lấy danh sách nhận xét',
                        'params' => [
                            'page' => 'Số trang',
                            'per_page' => 'Số lượng mỗi trang',
                            'de_tai_id' => 'ID đề tài'
                        ],
                        'response' => [
                            'data' => [
                                'id' => 1,
                                'noi_dung' => 'Nội dung nhận xét',
                                'diem' => 8.5,
                                'de_tai' => [
                                    'id' => 1,
                                    'ten_de_tai' => 'Tên đề tài'
                                ],
                                'giang_vien' => [
                                    'id' => 1,
                                    'ten' => 'Nguyễn Văn A'
                                ]
                            ],
                            'meta' => [
                                'current_page' => 1,
                                'total' => 15
                            ]
                        ]
                    ],
                    [
                        'method' => 'POST',
                        'url' => '/api/nhan-xet',
                        'description' => 'Tạo nhận xét mới',
                        'params' => [
                            'noi_dung' => 'Nội dung nhận xét',
                            'diem' => 'Điểm đánh giá',
                            'de_tai_id' => 'ID đề tài'
                        ],
                        'response' => [
                            'success' => [
                                'message' => 'Tạo nhận xét thành công',
                                'data' => [
                                    'id' => 1,
                                    'noi_dung' => 'Nội dung nhận xét',
                                    'diem' => 8.5,
                                    'created_at' => '2024-03-15T08:00:00.000000Z'
                                ]
                            ],
                            'error' => [
                                'message' => 'Thông báo lỗi',
                                'errors' => [
                                    'noi_dung' => ['Nội dung không được để trống']
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];

        return view('admin.api-doc.index', compact('apis'));
    }

    /**
     * Lấy danh sách đề tài
     */
    public function getDeTai(Request $request)
    {
        $query = DeTai::with(['nhom.sinhViens', 'giangVien', 'chiTietBaoCaos'])
            ->when($request->search, function ($q) use ($request) {
                $q->where('ten_de_tai', 'like', '%' . $request->search . '%')
                    ->orWhere('mo_ta', 'like', '%' . $request->search . '%');
            })
            ->when($request->trang_thai, function ($q) use ($request) {
                $q->where('trang_thai', $request->trang_thai);
            })
            ->when($request->giang_vien_id, function ($q) use ($request) {
                $q->where('giang_vien_id', $request->giang_vien_id);
            })
            ->when($request->nhom_id, function ($q) use ($request) {
                $q->where('nhom_id', $request->nhom_id);
            })
            ->when($request->ngay_bat_dau, function ($q) use ($request) {
                $q->where('ngay_bat_dau', '>=', $request->ngay_bat_dau);
            })
            ->when($request->ngay_ket_thuc, function ($q) use ($request) {
                $q->where('ngay_ket_thuc', '<=', $request->ngay_ket_thuc);
            });

        $deTais = $query->paginate($request->per_page ?? 10);

        return response()->json([
            'data' => $deTais->items(),
            'meta' => [
                'current_page' => $deTais->currentPage(),
                'per_page' => $deTais->perPage(),
                'total' => $deTais->total(),
                'last_page' => $deTais->lastPage()
            ]
        ]);
    }

    /**
     * Lấy chi tiết đề tài
     */
    public function showDeTai($id)
    {
        $deTai = DeTai::with(['nhom.sinhViens', 'giangVien', 'chiTietBaoCaos'])
            ->findOrFail($id);

        return response()->json([
            'data' => $deTai
        ]);
    }

    /**
     * Tạo đề tài mới
     */
    public function storeDeTai(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ten_de_tai' => 'required|string|max:255',
            'mo_ta' => 'nullable|string',
            'y_kien_giang_vien' => 'nullable|string',
            'ngay_bat_dau' => 'required|date',
            'ngay_ket_thuc' => 'required|date|after:ngay_bat_dau',
            'nhom_id' => 'nullable|exists:nhoms,id',
            'giang_vien_id' => 'required|exists:tai_khoans,id',
            'dot_bao_cao_id' => 'nullable|exists:dot_bao_caos,id',
            'vai_tro_id' => 'nullable|exists:vai_tros,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $deTai = DeTai::create([
                'ten_de_tai' => $request->ten_de_tai,
                'mo_ta' => $request->mo_ta ?? null,
                'y_kien_giang_vien' => $request->y_kien_giang_vien ?? null,
                'ngay_bat_dau' => $request->ngay_bat_dau,
                'ngay_ket_thuc' => $request->ngay_ket_thuc,
                'nhom_id' => $request->nhom_id ?? null,
                'giang_vien_id' => $request->giang_vien_id,
                'dot_bao_cao_id' => $request->dot_bao_cao_id ?? null,
                'vai_tro_id' => $request->vai_tro_id ?? null
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Tạo đề tài thành công',
                'data' => $deTai
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Có lỗi xảy ra',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cập nhật đề tài
     */
    public function updateDeTai(Request $request, $id)
    {
        $request->validate([
            'ten_de_tai' => 'required|string',
            'mo_ta' => 'nullable|string',
            'y_kien_giang_vien' => 'nullable|string',
            'ngay_bat_dau' => 'required|date',
            'ngay_ket_thuc' => 'required|date|after:ngay_bat_dau',
            'nhom_id' => 'nullable|exists:nhoms,id',
            'giang_vien_id' => 'required|exists:tai_khoans,id',
            'dot_bao_cao_id' => 'nullable|exists:dot_bao_caos,id',
            'vai_tro_id' => 'nullable|exists:vai_tros,id',
            'trang_thai' => 'required|integer|in:0,1,2,3,4'
        ]);

        try {
            $deTai = DeTai::findOrFail($id);
            $deTai->update([
                'ten_de_tai' => $request->ten_de_tai,
                'mo_ta' => $request->mo_ta,
                'y_kien_giang_vien' => $request->y_kien_giang_vien,
                'ngay_bat_dau' => $request->ngay_bat_dau,
                'ngay_ket_thuc' => $request->ngay_ket_thuc,
                'nhom_id' => $request->nhom_id,
                'giang_vien_id' => $request->giang_vien_id,
                'dot_bao_cao_id' => $request->dot_bao_cao_id ?? null,
                'vai_tro_id' => $request->vai_tro_id ?? null,
                'trang_thai' => $request->trang_thai
            ]);

            return response()->json([
                'message' => 'Cập nhật đề tài thành công',
                'data' => $deTai
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Có lỗi xảy ra khi cập nhật đề tài',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Xóa đề tài
     */
    public function destroyDeTai($id)
    {
        $deTai = DeTai::findOrFail($id);

        try {
            DB::beginTransaction();

            $deTai->delete();

            DB::commit();

            return response()->json([
                'message' => 'Xóa đề tài thành công'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Có lỗi xảy ra',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lấy danh sách tài khoản
     */
    public function getTaiKhoan(Request $request)
    {
        try {
            $query = TaiKhoan::query();
            
            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('ten', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            }

            if ($request->has('vai_tro')) {
                $query->where('vai_tro', $request->vai_tro);
            }

            $taiKhoans = $query->paginate(10);
            
            return response()->json([
                'success' => true,
                'data' => $taiKhoans
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi lấy danh sách tài khoản: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lấy chi tiết tài khoản
     */
    public function showTaiKhoan($id)
    {
        try {
            $taiKhoan = TaiKhoan::findOrFail($id);
            
            return response()->json([
                'success' => true,
                'data' => $taiKhoan
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi lấy thông tin tài khoản: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Tạo tài khoản mới
     */
    public function storeTaiKhoan(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ten' => 'required|string|max:255',
            'email' => 'required|email|unique:tai_khoans,email',
            'mat_khau' => 'required|string|min:6',
            'vai_tro' => 'required|string|in:admin,giang_vien,sinh_vien'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $taiKhoan = TaiKhoan::create([
                'ten' => $request->ten,
                'email' => $request->email,
                'mat_khau' => Hash::make($request->mat_khau),
                'vai_tro' => $request->vai_tro
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Tạo tài khoản thành công',
                'data' => $taiKhoan
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Có lỗi xảy ra',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cập nhật tài khoản
     */
    public function updateTaiKhoan(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'ten' => 'required|string|max:255',
            'email' => 'required|email|unique:tai_khoans,email,' . $id,
            'vai_tro' => 'required|string|in:admin,giang_vien,sinh_vien',
            'mat_khau' => 'nullable|string|min:6'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $taiKhoan = TaiKhoan::findOrFail($id);
            
            $data = [
                'ten' => $request->ten,
                'email' => $request->email,
                'vai_tro' => $request->vai_tro
            ];

            if ($request->filled('mat_khau')) {
                $data['mat_khau'] = Hash::make($request->mat_khau);
            }

            $taiKhoan->update($data);

            return response()->json([
                'message' => 'Cập nhật tài khoản thành công',
                'data' => $taiKhoan
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Có lỗi xảy ra khi cập nhật tài khoản',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Xóa tài khoản
     */
    public function destroyTaiKhoan($id)
    {
        $taiKhoan = TaiKhoan::findOrFail($id);

        try {
            DB::beginTransaction();

            $taiKhoan->delete();

            DB::commit();

            return response()->json([
                'message' => 'Xóa tài khoản thành công'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Có lỗi xảy ra',
                'error' => $e->getMessage()
            ], 500);
        }
    }
} 