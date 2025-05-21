<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DotBaoCao;
use Illuminate\Http\Request;

class DotBaoCaoController extends Controller
{
    public function index()
    {
        $dotBaoCaos = DotBaoCao::orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('admin.dot-bao-cao.index', compact('dotBaoCaos'));
    }

    public function create()
    {
        return view('admin.dot-bao-cao.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nam_hoc' => 'required|integer|min:2000|max:2100'
        ]);

        DotBaoCao::create($request->all());

        return redirect()->route('admin.dot-bao-cao.index')
            ->with('success', 'Thêm đợt báo cáo thành công.');
    }

    public function edit(DotBaoCao $dotBaoCao)
    {
        return view('admin.dot-bao-cao.edit', compact('dotBaoCao'));
    }

    public function update(Request $request, DotBaoCao $dotBaoCao)
    {
        $request->validate([
            'nam_hoc' => 'required|integer|min:2000|max:2100'
        ]);

        $dotBaoCao->update($request->all());

        return redirect()->route('admin.dot-bao-cao.index')
            ->with('success', 'Cập nhật đợt báo cáo thành công.');
    }

    public function destroy(DotBaoCao $dotBaoCao)
    {
        $dotBaoCao->delete();

        return redirect()->route('admin.dot-bao-cao.index')
            ->with('success', 'Xóa đợt báo cáo thành công.');
    }
} 