<?php

namespace App\Http\Requests\GiangVien;

use Illuminate\Foundation\Http\FormRequest;

class StoreBaoCaoQuaTrinhRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'nhom_id' => 'required|exists:nhoms,id',
            'dot_bao_cao_id' => 'required|exists:dot_bao_caos,id',
            'noi_dung_bao_cao' => 'required|string',
            'ngay_bao_cao' => 'required|date_format:Y-m-d',
        ];
    }
} 