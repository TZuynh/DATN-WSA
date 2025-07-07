<?php

namespace App\Exports;

use App\Models\BangDiem;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use App\Models\SinhVien;
use App\Models\DotBaoCao;

class BangDiemExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $dotBaoCaoId;

    public function __construct($dotBaoCaoId = null)
    {
        $this->dotBaoCaoId = $dotBaoCaoId;
    }

    public function collection()
    {
        $query = BangDiem::with(['sinhVien','dotBaoCao.hocKy']);
        if ($this->dotBaoCaoId) {
            $query->where('dot_bao_cao_id', $this->dotBaoCaoId);
        }
        $all = $query->orderBy('created_at','desc')->get();
    
        // Gom nhóm theo sinh viên, nhưng với $valid lọc tổng > 0
        return $all->groupBy('sinh_vien_id')
            ->map(function($items) {
                // Lọc bỏ lượt chấm tổng = 0
                $valid = $items->filter(function($bd){
                    return (
                        ($bd->diem_thuyet_trinh ?? 0)
                      + ($bd->diem_demo          ?? 0)
                      + ($bd->diem_cau_hoi       ?? 0)
                      + ($bd->diem_cong          ?? 0)
                    ) > 0;
                });
    
                $sv     = $items->first()->sinhVien;
                $dot    = $items->first()->dotBaoCao;
                $bcTB   = $valid->avg('diem_bao_cao');
                $tkTB   = $valid->map(fn($bd)=>
                        ($bd->diem_thuyet_trinh ?? 0)
                      + ($bd->diem_demo          ?? 0)
                      + ($bd->diem_cau_hoi       ?? 0)
                      + ($bd->diem_cong          ?? 0)
                    )->avg();
                $dtk    = null;
                if (!is_null($bcTB) && !is_null($tkTB)) {
                    $dtk = min(round($bcTB*0.2 + $tkTB*0.8,2),10);
                }
    
                return [
                    'mssv'            => $sv->mssv,
                    'ten'             => $sv->ten,
                    'dot_bao_cao'     => ($dot->nam_hoc ?? '').' - '.($dot->hocKy->ten ?? ''),
                    'diem_bao_cao_tb' => round($bcTB,2),
                    'tong_ket'        => round($tkTB,2),
                    'diem_tong_ket'   => $dtk,
                ];
            })->values();
    }    

    public function headings(): array
    {
        return [
            'STT',
            'Mã sinh viên',
            'Tên sinh viên',
            'Đợt báo cáo',
            'Điểm trung bình báo cáo',
            'Tổng điểm trung bình',
            'Điểm tổng kết',
        ];
    }

    public function map($row): array
    {
        static $stt = 1;
        return [
            $stt++,
            $row['mssv'],
            $row['ten'],
            $row['dot_bao_cao'],
            $row['diem_bao_cao_tb'],
            $row['tong_ket'],
            $row['diem_tong_ket'],
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E2EFDA']
                ]
            ]
        ];
    }
}