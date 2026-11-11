<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller {
    public function index()
    {
        // Hanya order COMPLETED yang dihitung sebagai penjualan.
        // ACTIVE belum dibayar, CANCELLED batal.
        $completed = Order::where('status', 'completed');

        $totalPenjualan = (int) (clone $completed)->sum('total');
        $jumlahOrderSelesai = (int) (clone $completed)->count();

        // Profit = (harga jual snapshot di order - modal Product) x qty.
        // Kalau kolom di tbl_produk masih bernama price, ganti p.modal -> p.price.
        $totalProfit = (int) DB::table('tbl_order_detail as od')
            ->join('tbl_order as o', 'o.id', '=', 'od.order_id')
            ->join('tbl_menu_detail as md', 'md.id', '=', 'od.menu_detail_id')
            ->join('tbl_produk as p', 'p.id', '=', 'md.id_product')
            ->where('o.status', 'completed')
            ->sum(DB::raw('(od.harga - p.modal) * od.qty'));

        return view('dashboard', [
            'jumlahProduk'       => Product::count(),
            'jumlahMenu'         => Menu::count(),
            'totalPenjualan'     => $totalPenjualan,
            'totalProfit'        => $totalProfit,
            'jumlahOrderSelesai' => $jumlahOrderSelesai,
        ]);
    }
}