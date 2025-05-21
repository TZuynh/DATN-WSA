<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VaiTro;
use Illuminate\Http\Request;

class VaiTroController extends Controller
{
    public function index()
    {
        $vaiTros = VaiTro::orderBy('created_at', 'desc')->paginate(10);
        return view('admin.vai-tro.index', compact('vaiTros'));
    }

    public function create()
    {
        return view('admin.vai-tro.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'ten' => 'required|string|max:255|unique:vai_tros,ten'
        ]);

        VaiTro::create($request->all());

        return redirect()->route('admin.vai-tro.index')
            ->with('success', 'Vai trò đã được tạo thành công.');
    }

    public function edit(VaiTro $vaiTro)
    {
        return view('admin.vai-tro.edit', compact('vaiTro'));
    }

    public function update(Request $request, VaiTro $vaiTro)
    {
        $request->validate([
            'ten' => 'required|string|max:255|unique:vai_tros,ten,' . $vaiTro->id
        ]);

        $vaiTro->update($request->all());

        return redirect()->route('admin.vai-tro.index')
            ->with('success', 'Vai trò đã được cập nhật thành công.');
    }

    public function destroy(VaiTro $vaiTro)
    {
        $vaiTro->delete();

        return redirect()->route('admin.vai-tro.index')
            ->with('success', 'Vai trò đã được xóa thành công.');
    }
} 