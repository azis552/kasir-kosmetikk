@extends('template.master')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card shadow-sm border-0">
            <div class="card-body text-center py-5">

                <div class="mb-4">
                    <i class="fas fa-cash-register fa-4x text-primary"></i>
                </div>

                <h2 class="mb-3 fw-bold">Selamat Datang di Sistem Kasir</h2>

                <p class="text-muted mb-4">
                    Kelola transaksi penjualan, stok barang, dan laporan dengan mudah dan cepat.
                    Pastikan Anda sudah login untuk mulai melakukan transaksi.
                </p>

                <a href="{{ route('kasir.index') }}" class="btn btn-primary btn-lg px-4">
                    <i class="fas fa-shopping-cart me-2"></i> Mulai Transaksi
                </a>

            </div>
        </div>
    </div>
</div>
@endsection