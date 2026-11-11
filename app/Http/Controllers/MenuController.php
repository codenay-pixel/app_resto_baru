<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class MenuController extends Controller {
    public function index()
    {
        return view('menu.index');
    }

    public function create()
    {
        return view('menu.add');
    }

    public function edit($id)
    {
        return view('menu.edit', ['id' => $id]);
    }

    public function data()
    {
        // modal dikirim karena halaman Menu Resto (admin) menampilkan
        // Harga Modal dan Profit. Kalau nanti ada API untuk customer,
        // buat endpoint terpisah tanpa modal.
        $menus = Menu::with([
            'details.product:id,kode,name,deskripsi,stok,modal'
        ])->orderBy('nama')->get();

        return response()->json($menus);
    }

    public function show($id)
    {
        $menu = Menu::with([
            'details.product:id,kode,name,deskripsi,stok,modal'
        ])->findOrFail($id);

        return response()->json($menu);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'kode' => ['required', 'string', 'max:30', 'unique:tbl_menu,kode'],
            'nama' => ['required', 'string', 'max:150'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.id_product' => [
                'required', 'integer', 'exists:tbl_produk,id', 'distinct'
            ],
            'items.*.harga' => ['required', 'integer', 'min:0'],
        ]);

        $menu = DB::transaction(function () use ($data) {
            $menu = Menu::create([
                'kode' => $data['kode'],
                'nama' => $data['nama'],
            ]);

            $details = array_map(function ($item) {
                return [
                    'id_product' => $item['id_product'],
                    'harga' => $item['harga'],
                ];
            }, $data['items']);

            $menu->details()->createMany($details);
            return $menu;
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Menu berhasil disimpan.',
            'data' => $menu->load('details.product:id,kode,name,deskripsi,stok'),
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $menu = Menu::findOrFail($id);

        $data = $request->validate([
            'kode' => [
                'required',
                'string',
                'max:30',
                Rule::unique('tbl_menu', 'kode')->ignore($menu->id),
            ],
            'nama' => ['required', 'string', 'max:150'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.id_product' => [
                'required', 'integer', 'exists:tbl_produk,id', 'distinct'
            ],
            'items.*.harga' => ['required', 'integer', 'min:0'],
        ]);

        DB::transaction(function () use ($menu, $data) {
            $menu->update([
                'kode' => $data['kode'],
                'nama' => $data['nama'],
            ]);

            // Sinkronisasi detail, bukan hapus-lalu-buat-ulang, karena
            // tbl_order_detail merujuk ke tbl_menu_detail (FK RESTRICT).
            $existing = $menu->details()->with('product:id,name')->get()
                ->keyBy('id_product');
            $incoming = collect($data['items'])->keyBy('id_product');

            // 1. Item yang dibuang dari Menu: hapus hanya jika belum pernah dipesan.
            foreach ($existing as $idProduct => $detail) {
                if ($incoming->has($idProduct)) {
                    continue;
                }

                $sudahDipesan = DB::table('tbl_order_detail')
                    ->where('menu_detail_id', $detail->id)
                    ->exists();

                if ($sudahDipesan) {
                    throw ValidationException::withMessages([
                        'items' => 'Product "'
                            . ($detail->product->name ?? $idProduct)
                            . '" sudah pernah dipesan, jadi tidak bisa dihapus dari Menu.'
                    ]);
                }

                $detail->delete();
            }

            // 2. Item lama: update harga. Item baru: buat.
            foreach ($incoming as $idProduct => $item) {
                if ($existing->has($idProduct)) {
                    $existing[$idProduct]->update(['harga' => $item['harga']]);
                } else {
                    $menu->details()->create([
                        'id_product' => $idProduct,
                        'harga' => $item['harga'],
                    ]);
                }
            }
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Menu berhasil diperbarui.',
            'data' => $menu->fresh()->load('details.product:id,kode,name,deskripsi,stok'),
        ]);
    }
}