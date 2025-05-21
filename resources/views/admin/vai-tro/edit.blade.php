@extends('admin.layout')

@section('title', 'Chỉnh sửa vai trò')

@section('content')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <div style="max-width: 600px; margin: 0 auto;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h1 style="color: #2d3748; font-weight: 700;">Chỉnh sửa vai trò</h1>
            <a href="{{ route('admin.vai-tro.index') }}" style="padding: 8px 16px; background-color: #718096; color: white; border: none; border-radius: 4px; text-decoration: none;">
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

        <form action="{{ route('admin.vai-tro.update', $vaiTro->id) }}" method="POST" style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 8px rgb(0 0 0 / 0.1);">
            @csrf
            @method('PUT')
            <div style="margin-bottom: 20px;">
                <label for="ten" style="display: block; margin-bottom: 8px; color: #4a5568; font-weight: 600;">Tên vai trò</label>
                <input type="text" name="ten" id="ten" value="{{ old('ten', $vaiTro->ten) }}" required
                    style="width: 100%; padding: 8px 12px; border: 1px solid #e2e8f0; border-radius: 4px; font-size: 16px;">
            </div>

            <button type="submit" style="width: 100%; padding: 12px; background-color: #4299e1; color: white; border: none; border-radius: 4px; font-size: 16px; cursor: pointer;">
                Cập nhật vai trò
            </button>
        </form>
    </div>
@endsection 