@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-1">Dashboard</h1>
        <p class="text-muted mb-0">
            Ringkasan data aplikasi restoran.
        </p>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-6 col-xl-3">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body">
                <div class="text-muted">Jumlah Product</div>
                <div class="display-5 fw-semibold">
                    {{ $jumlahProduk }}
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-xl-3">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body">
                <div class="text-muted">Jumlah Menu</div>
                <div class="display-5 fw-semibold">
                    {{ $jumlahMenu }}
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-xl-3">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body">
                <div class="text-muted">Total Penjualan</div>
                <div class="h2 fw-semibold mb-1">
                    Rp {{ number_format($totalPenjualan, 0, ',', '.') }}
                </div>
                <small class="text-muted">
                    dari {{ $jumlahOrderSelesai }} order selesai
                </small>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-xl-3">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body">
                <div class="text-muted">Total Profit</div>
                <div class="h2 fw-semibold mb-1 {{ $totalProfit < 0 ? 'text-danger' : 'text-success' }}">
                    Rp {{ number_format($totalProfit, 0, ',', '.') }}
                </div>
                <small class="text-muted">
                    harga jual dikurangi modal
                </small>
            </div>
        </div>
    </div>
</div>
@endsection