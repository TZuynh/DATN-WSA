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
        $query = \App\Models\BangDiem::with(['sinhVien', 'dotBaoCao.hocKy']);
        if ($this->dotBaoCaoId) {
            $query->where('dot_bao_cao_id', $this->dotBaoCaoId);
        }
        $bangDiems = $query->orderBy('created_at', 'desc')->get();
        // Gom nhóm theo sinh viên
        $grouped = $bangDiems->groupBy('sinh_vien_id')->map(function($items) {
            $sinhVien = $items->first()->sinhVien;
            $dotBaoCao = $items->first()->dotBaoCao;
            $diem_bao_cao_tb = $items->pluck('diem_bao_cao')->filter(function($v){ return $v !== null; })->avg();
            $tong_ket = $items->map(function($bd) {
                return ($bd->diem_thuyet_trinh ?? 0) + ($bd->diem_demo ?? 0) + ($bd->diem_cau_hoi ?? 0) + ($bd->diem_cong ?? 0);
            })->avg();
            $diem_tong_ket = $diem_bao_cao_tb !== null && $tong_ket !== null ? round($diem_bao_cao_tb * 0.2 + $tong_ket * 0.8, 2) : null;
            return [
                'mssv' => $sinhVien->mssv ?? 'N/A',
                'ten' => $sinhVien->ten ?? 'N/A',
                'dot_bao_cao' => ($dotBaoCao->nam_hoc ?? 'N/A') . ' - ' . ($dotBaoCao->hocKy->ten ?? 'N/A'),
                'diem_bao_cao_tb' => $diem_bao_cao_tb !== null ? round($diem_bao_cao_tb, 2) : '-',
                'tong_ket' => $tong_ket !== null ? round($tong_ket, 2) : '-',
                'diem_tong_ket' => $diem_tong_ket !== null ? number_format(min($diem_tong_ket, 10), 2) : '-',
            ];
        })->values();
        return $grouped;
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