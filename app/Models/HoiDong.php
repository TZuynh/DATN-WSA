<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HoiDong extends Model
{
    use HasFactory;

    protected $table = 'hoi_dongs';

    protected $fillable = [
        'ma_hoi_dong',
        'ten',
        'dot_bao_cao_id',
        'phong_id',
        'thoi_gian_bat_dau'
    ];

    protected $casts = [
        'thoi_gian_bat_dau' => 'datetime'
    ];

    /**
     * Tạo mã hội đồng tự động
     */
    public static function taoMaHoiDong($dotBaoCaoId)
    {
        $dotBaoCao = DotBaoCao::findOrFail($dotBaoCaoId);
        $count = self::where('dot_bao_cao_id', $dotBaoCaoId)->count() + 1;
        return 'HD' . $dotBaoCao->nam_hoc . str_pad($count, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Lấy đợt báo cáo của hội đồng
     */
    public function dotBaoCao()
    {
        return $this->belongsTo(DotBaoCao::class);
    }

    /**
     * Lấy danh sách chi tiết báo cáo của hội đồng
     */
    public function chiTietBaoCaos()
    {
        return $this->hasMany(ChiTietDeTaiBaoCao::class);
    }

    /**
     * Lấy danh sách lịch chấm của hội đồng
     */
    public function lichChams()
    {
        return $this->hasMany(LichCham::class);
    }

    /**
     * Lấy danh sách phân công vai trò của hội đồng
     */
    public function phanCongVaiTros()
    {
        return $this->hasMany(\App\Models\PhanCongVaiTro::class, 'hoi_dong_id');
    }

    /**
     * Lấy phòng của hội đồng
     */
    public function phong()
    {
        return $this->belongsTo(Phong::class);
    }

    /**
     * Thêm đề tài của giảng viên vào hội đồng
     * @param int $giangVienId ID của giảng viên
     * @return void
     */
    public function themDeTaiCuaGiangVien($giangVienId)
    {
        // Lấy danh sách đề tài của giảng viên trong đợt báo cáo này
        $deTais = DeTai::where('giang_vien_id', $giangVienId)
            ->where('dot_bao_cao_id', $this->dot_bao_cao_id)
            ->get();

        // Thêm từng đề tài vào chi tiết báo cáo của hội đồng
        foreach ($deTais as $deTai) {
            ChiTietDeTaiBaoCao::create([
                'hoi_dong_id' => $this->id,
                'de_tai_id' => $deTai->id,
                'dot_bao_cao_id' => $this->dot_bao_cao_id,
                'trang_thai' => 0 // Trạng thái mặc định là chờ duyệt
            ]);
        }
    }

    /**
     * Thêm đề tài trực tiếp vào hội đồng
     * @param array $deTaiData Dữ liệu đề tài
     * @return DeTai
     */
    public function themDeTaiTrucTiep($deTaiData)
    {
        // Tạo đề tài mới
        $deTai = DeTai::create([
            'ten_de_tai' => $deTaiData['ten_de_tai'],
            'mo_ta' => $deTaiData['mo_ta'] ?? null,
            'giang_vien_id' => $deTaiData['giang_vien_id'],
            'dot_bao_cao_id' => $this->dot_bao_cao_id,
            'nhom_id' => $deTaiData['nhom_id'] ?? null,
            'y_kien_giang_vien' => $deTaiData['y_kien_giang_vien'] ?? null,
            'trang_thai' => DeTai::TRANG_THAI_CHO_DUYET
        ]);

        // Tạo chi tiết đề tài báo cáo
        ChiTietDeTaiBaoCao::create([
            'hoi_dong_id' => $this->id,
            'de_tai_id' => $deTai->id,
            'dot_bao_cao_id' => $this->dot_bao_cao_id,
            'trang_thai' => 0 // Trạng thái mặc định là chờ duyệt
        ]);

        // Cập nhật nhóm nếu có
        if (!empty($deTaiData['nhom_id'])) {
            $nhom = Nhom::find($deTaiData['nhom_id']);
            if ($nhom) {
                $nhom->update(['de_tai_id' => $deTai->id]);
            }
        }

        return $deTai;
    }

    /**
     * Lấy danh sách đề tài của hội đồng
     */
    public function getDeTais()
    {
        return $this->chiTietBaoCaos()
            ->with('deTai.nhom', 'deTai.giangVien')
            ->get()
            ->pluck('deTai')
            ->filter();
    }

    /**
     * Lấy danh sách giảng viên trong hội đồng
     */
    public function getGiangViens()
    {
        return $this->phanCongVaiTros()
            ->with('taiKhoan', 'vaiTro')
            ->get()
            ->pluck('taiKhoan')
            ->filter();
    }
}

