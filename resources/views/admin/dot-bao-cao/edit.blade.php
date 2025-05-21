@extends('admin.layout')

@section('title', 'Chỉnh sửa đợt báo cáo')

@section('content')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <div style="max-width: 800px; margin: 0 auto;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h1 style="color: #2d3748; font-weight: 700;">Chỉnh sửa đợt báo cáo</h1>
            <a href="{{ route('admin.dot-bao-cao.index') }}" style="padding: 10px 20px; background-color: #718096; color: white; border: none; border-radius: 4px; text-decoration: none;">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
        </div>

        @if($errors->any())
            <div style="background-color: #f56565; color: white; padding: 10px; border-radius: 4px; margin-bottom: 20px;">
                <ul style="margin: 0; padding-left: 20px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 8px rgb(0 0 0 / 0.1);">
            <form action="{{ route('admin.dot-bao-cao.update', $dotBaoCao->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div style="margin-bottom: 20px;">
                    <label for="nam_hoc" style="display: block; margin-bottom: 5px; color: #4a5568;">Năm học</label>
                    <input type="number" name="nam_hoc" id="nam_hoc" value="{{ old('nam_hoc', $dotBaoCao->nam_hoc) }}"
                        style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;"
                        min="2000" max="2100" placeholder="Ví dụ: 2024">
                </div>

                <div style="text-align: right;">
                    <button type="submit" style="padding: 10px 20px; background-color: #4299e1; color: white; border: none; border-radius: 4px; cursor: pointer;">
                        <i class="fas fa-save"></i> Cập nhật
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection 