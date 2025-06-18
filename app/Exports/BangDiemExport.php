<?php

namespace App\Exports;

use App\Models\BangDiem;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BangDiemExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $dotBaoCaoId;

    public function __construct($dotBaoCaoId = null)
    {
        $this->dotBaoCaoId = $dotBaoCaoId;
    }

    public function collection()
    {
        $query = BangDiem::with(['sinhVien', 'dotBaoCao', 'giangVien']);

        if ($this->dotBaoCaoId) {
            $query->where('dot_bao_cao_id', $this->dotBaoCaoId);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'STT',
            'Mã sinh viên',
            'Tên sinh viên',
            'Đợt báo cáo',
            'Giảng viên chấm',
            'Điểm báo cáo',
            'Điểm thuyết trình',
            'Điểm demo',
            'Điểm câu hỏi',
            'Điểm cộng',
            'Tổng điểm',
            'Bình luận',
            'Ngày chấm'
        ];
    }

    public function map($bangDiem): array
    {
        $tongDiem = $bangDiem->diem_bao_cao + $bangDiem->diem_thuyet_trinh + 
                   $bangDiem->diem_demo + $bangDiem->diem_cau_hoi + $bangDiem->diem_cong;

        return [
            $bangDiem->id,
            $bangDiem->sinhVien->mssv ?? 'N/A',
            $bangDiem->sinhVien->ten ?? 'N/A',
            $bangDiem->dotBaoCao->nam_hoc ?? 'N/A',
            $bangDiem->giangVien->ten ?? 'N/A',
            $bangDiem->diem_bao_cao,
            $bangDiem->diem_thuyet_trinh,
            $bangDiem->diem_demo,
            $bangDiem->diem_cau_hoi,
            $bangDiem->diem_cong,
            $tongDiem,
            $bangDiem->binh_luan ?? '',
            $bangDiem->created_at->format('d/m/Y H:i')
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