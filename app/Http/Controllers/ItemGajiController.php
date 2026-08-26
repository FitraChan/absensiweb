<?php

namespace App\Http\Controllers;

use App\Models\ItemGaji;
use App\Models\KategoriItem;
use Illuminate\Http\Request;

class ItemGajiController extends Controller
{
    public function index()
    {
        $rows = ItemGaji::with('kategoriItems')
        ->orderBy('no_urut')
        ->get();

        $kategori = KategoriItem::orderBy('id')
            ->get();

        return view('admin.item-gaji', compact('rows', 'kategori'))->with([
            'cekNav2' => 'item-gaji'
        ]);
    }

    public function create()
    {
        $kategori = KategoriItem::orderBy('id')->get();

        return view('admin.item-gaji.create', compact('kategori'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_item_gaji' => 'required|string|max:255',
            'kategori_item_id' => 'required|integer',
            'no_urut' => 'required|integer|min:1',
        ]);

        ItemGaji::create([
            'nama_item_gaji' => $request->nama_item_gaji,
            'kategori_item_id' => $request->kategori_item_id,
            'no_urut' => $request->no_urut,
        ]);

        return redirect()
            ->route('item-gaji.index')
            ->with('success', 'Item gaji berhasil ditambahkan.');
    }

    public function show(ItemGaji $itemGaji)
    {
        return view('admin.item-gaji.show', compact('itemGaji'));
    }

    public function edit(ItemGaji $itemGaji)
    {
        $kategori = KategoriItem::orderBy('id')->get();

        return view(
            'admin.item-gaji.edit',
            compact('itemGaji', 'kategori')
        );
    }

    public function update(Request $request, ItemGaji $itemGaji)
    {
        $request->validate([
            'nama_item_gaji' => 'required|string|max:255',
            'kategori_item_id' => 'required|integer',
            'no_urut' => 'required|integer|min:1',
        ]);

        $itemGaji->update([
            'nama_item_gaji' => $request->nama_item_gaji,
            'kategori_item_id' => $request->kategori_item_id,
            'no_urut' => $request->no_urut,
        ]);

        return redirect()
            ->route('item-gaji.index')
            ->with('success', 'Item gaji berhasil diperbarui.');
    }

    public function destroy(ItemGaji $itemGaji)
    {
        $itemGaji->delete();

        return redirect()
            ->route('item-gaji.index')
            ->with('success', 'Item gaji berhasil dihapus.');
    }
}