<!doctype html>
<html lang="en">
<!-- [Head] start -->

<head>
    <title>{{  store_name("Toko") }} </title>
    <!-- [Meta] -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="description"
        content="Datta able is trending dashboard template made using Bootstrap 5 design framework. Datta able is available in Bootstrap, React, CodeIgniter, Angular,  and .net Technologies." />
    <meta name="keywords"
        content="Bootstrap admin template, Dashboard UI Kit, Dashboard Template, Backend Panel, react dashboard, angular dashboard" />
    <meta name="author" content="Codedthemes" />

    <!-- [Favicon] icon -->
    <link rel="icon" href="{{ store_logo('icon') }}" type="image/x-icon" />
    <!-- [Font] Family -->
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;500;600&display=swap"
        rel="stylesheet" />
    <!-- [phosphor Icons] https://phosphoricons.com/ -->
    <link rel="stylesheet" href="{{ asset('assets/fonts/phosphor/regular/style.css') }}" />
    <!-- [Tabler Icons] https://tablericons.com -->
    <link rel="stylesheet" href="{{ asset('assets/fonts/tabler-icons.min.css') }}" />
    <!-- [Template CSS Files] -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" id="main-style-link" />
    <link rel="stylesheet" href="{{ asset('assets/css/style-preset.css') }}" />
    <script src="{{ asset('assets/js/plugins/jquery-3.6.0.min.js') }}"></script>


    <!-- Load Inputmask (ensure it's placed after jQuery) -->
    <script src="{{ asset('assets/js/plugins/inputmask.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-14K1GBX9FG"></script>
    <style>
        .disabled-link {
            pointer-events: none;
            opacity: .6;
            cursor: not-allowed;
        }
    </style>
    <style>
        /* =========================
       HIDE SIDEBAR (POS MODE)
    ========================== */

        body.hide-sidebar .pc-sidebar {
            width: 0 !important;
            min-width: 0 !important;
            --pc-sidebar-border: none;
        }

        body.hide-sidebar .pc-header {
            left: 0 !important;
        }

        body.hide-sidebar .pc-container,
        body.hide-sidebar .pc-footer {
            margin-left: 0 !important;
        }

        /* =========================
       POS MODE CLEAN HEADER
    ========================== */

        body.pos-mode .page-header {
            display: none !important;
        }

        /* Optional: sembunyikan tombol sidebar */
        body.pos-mode .pc-header .pc-sidebar-collapse,
        body.pos-mode .pc-header .pc-sidebar-popup {
            display: none !important;
        }
    </style>


    @php
        $theme = \App\Models\AppSetting::first();
    @endphp

    <style>
        :root {
            --primary-color:
                {{ $theme->primary_color ?? '#28a745' }}
            ;
            --secondary-color:
                {{ $theme->secondary_color ?? '#6c757d' }}
            ;
            --sidebar-color:
                {{ $theme->sidebar_color ?? '#212529' }}
            ;
            --background-color:
                {{ $theme->background_color ?? '#f6f7fb' }}
            ;
            --text-color:
                {{ $theme->text_color ?? '#000000' }}
            ;
        }

        body {
            background: var(--background-color) !important;
            color: var(--text-color) !important;
        }

        /* BUTTON PRIMARY */
        .btn-success,
        .btn-primary {
            background-color: var(--primary-color) !important;
            border-color: var(--primary-color) !important;
        }

        /* SIDEBAR */
        .pc-sidebar {
            background-color: var(--sidebar-color) !important;
        }

        .pc-sidebar .pc-link {
            color: var(--text-color) !important;
        }

        .pc-sidebar .pc-caption {
            color: var(--text-color) !important;
        }

        .pc-sidebar .pc-item.active>.pc-link {
            background-color: var(--primary-color) !important;
        }

        /* HEADER PAGE */
        .page-header {
            background-color: var(--background-color) !important;
        }

        /* ================= HEADER ================= */

        .pc-header,
        .header-wrapper {
            background: var(--primary-color) !important;
        }

        /* Icon & text header */
        .pc-header .pc-head-link,
        .pc-header .pc-head-link i,
        .pc-header .pc-head-link svg {
            color: #ffffff !important;
        }


        /* HILANGKAN shadow/overlay bawaan */
        .pc-header::before {
            display: none !important;
        }

        /* ================= DROPDOWN PROFILE ================= */

        /* Override Bootstrap bg-primary */
        .bg-primary {
            background-color: var(--primary-color) !important;
        }

        /* Supaya dropdown header ikut theme */
        .dropdown-header {
            background-color: var(--primary-color) !important;
        }
    </style>



    <style>
        /* Styling untuk header tabel (thead) dengan warna abu-abu soft */
        .table thead tr {
            background-color: #f8f9fa;
            /* Warna abu-abu muda untuk header tabel */
            color: #495057;
            /* Warna teks gelap untuk kontras */
        }

        /* Styling untuk baris total keseluruhan (tfoot) */
        .table tfoot tr.total-row {
            background-color: #f1f3f5;
            /* Warna putih krem soft untuk footer */
            font-weight: bold;
            color: #495057;
            /* Warna teks gelap untuk kontras */
        }

        /* Styling untuk kolom total di footer */
        .table tfoot th {
            background-color: #e9ecef;
            /* Warna abu-abu lebih gelap untuk footer */
            color: #495057;
            /* Warna teks gelap untuk kontras */
        }

        /* Styling untuk baris normal */
        .table tbody tr {
            background-color: #ffffff;
            /* Warna latar belakang baris biasa */
        }

        .table tbody tr:hover {
            background-color: #f8f9fa;
            /* Warna latar belakang saat hover */
        }
    </style>



</head>
<!-- [Head] end -->
<!-- [Body] Start -->

<body data-pc-preset="preset-1" data-pc-sidebar-caption="true" data-pc-direction="ltr" data-pc-theme="light">
    <!-- MODAL INFO -->
<!-- MODAL INFO -->
<div id="shortcutModal"
     style="
        display:none;
        position:fixed;
        top:0;
        left:0;
        width:100%;
        height:100%;
        background:rgba(0,0,0,0.6);
        z-index:9998;
     ">

    <div style="
        background:#ffffff;
        width:600px;
        max-width:95%;
        margin:5% auto;
        padding:25px;
        border-radius:10px;
        max-height:85%;
        overflow-y:auto;
        color:#212529;
        font-weight:500;
    ">

        <h4 style="color:#000;">📋 Shortcut Keyboard</h4>
        <hr>

        <h6 style="color:#000;">Global</h6>
        <ul style="color:#000;">
            <li><b>ALT + D</b> → Dashboard</li>
            <li><b>ALT + T</b> → Transaksi</li>
            <li><b>ALT + R</b> → Riwayat</li>
            <li><b>F10</b> → Logout</li>
        </ul>

        <h6 style="color:#000;">Kasir</h6>
        <ul style="color:#000;">
            <li><b>ALT + N</b> → Fokus Barcode</li>
            <li><b>ALT + J</b> → Fokus Voucher</li>
            <li><b>ALT + C</b> → Apply Voucher</li>
            <li><b>ALT + Q</b> → Fokus Bayar</li>
            <li><b>ALT + M</b> → Metode Pembayaran</li>
            <li><b>F9</b> → Proses Bayar</li>
        </ul>

        <h6 style="color:#000;">Berpindah menu khusus admin</h6>
        <ul style="color:#000;">
            <li><b>ALT + U</b> → Users</li>
            <li><b>ALT + O</b> → Roles</li>
            <li><b>ALT + I</b> → Produk</li>
            <li><b>ALT + K</b> → Kategori</li>
            <li><b>ALT + V</b> → Voucher</li>
            <li><b>ALT + H</b> → Laporan Harian</li>
            <li><b>ALT + B</b> → Laporan Bulanan</li>
            <li><b>ALT + Y</b> → Laporan Tahunan</li>
            <li><b>ALT + L</b> → Laba Rugi</li>
        </ul>

        <div style="text-align:right;">
            <button id="closeShortcutModal"
                    style="padding:6px 15px;background:#dc3545;color:white;border:none;border-radius:5px;">
                Tutup
            </button>
        </div>

    </div>
</div>
    <!-- [ Pre-loader ] start -->
    <div class="loader-bg">
        <div class="loader-track">
            <div class="loader-fill"></div>
        </div>
    </div>
    <!-- [ Pre-loader ] End -->
    <!-- [ Sidebar Menu ] start -->
    <nav class="pc-sidebar">
        <div class="navbar-wrapper">
            <div class="m-header">
                <a href="" class="b-brand text-primary">
                    <!-- ========   Change your logo from here   ============ -->
                    <img src="{{ store_logo('app_dark') }}" class="img-fluid" width="50px" alt="logo" />
                    <strong class="pc-link" for="">{{ store_name('full') }}</strong>
                </a>
            </div>
            <div class="navbar-content">
                <ul class="pc-navbar">

                    {{-- ===== DASHBOARD (SELALU MUNCUL) ===== --}}
                    @role('admin')
                    <li class="pc-item {{ request()->is('dashboard/admin*') ? 'active' : '' }}">
                        <a href="{{ route('dashboard.admin') }}" class="pc-link">
                            <span class="pc-micon"><i class="ph ph-gauge"></i></span>
                            <span class="pc-mtext">Dashboard</span>
                        </a>
                    </li>
                    @endrole

                    @role('kasir')
                    <li class="pc-item {{ request()->is('dashboard/kasir*') ? 'active' : '' }}">
                        <a href="{{ route('dashboard.kasir') }}" class="pc-link">
                            <span class="pc-micon"><i class="ph ph-gauge"></i></span>
                            <span class="pc-mtext">Dashboard</span>
                        </a>
                    </li>
                    @endrole

                    {{-- ===== MENU KASIR (ADMIN & KASIR BOLEH LIHAT) ===== --}}
                    @php
                        $kasirOpen = request()->is('kasir*') || request()->is('transaksis*');
                    @endphp

                    <li class="pc-item pc-caption">
                        <label data-i18n="UI Components">Menu Kasir</label>
                        <i class="ph ph-pencil-ruler"></i>
                    </li>

                    <li class="pc-item pc-hasmenu {{ $kasirOpen ? 'pc-trigger active' : '' }}">
                        <a href="javascript:void(0)" class="pc-link">
                            <span class="pc-micon"><i class="ph ph-cash-register"></i></span>
                            <span class="pc-mtext">Kasir</span>
                            <span class="pc-arrow"><i class="ph ph-caret-down"></i></span>
                        </a>

                        <ul class="pc-submenu" style="{{ $kasirOpen ? 'display:block;' : '' }}">
                            <li class="pc-item {{ request()->is('kasir*') ? 'active' : '' }}">
                                <a href="{{ route('kasir.index') }}" class="pc-link" id="kasir-trigger">
                                    <span class="pc-micon"><i class="ph ph-cash-register"></i></span>
                                    <span class="pc-mtext">Transaksi</span>
                                </a>
                            </li>


                            <li class="pc-item {{ request()->is('transaksis*') ? 'active' : '' }}">
                                <a href="{{ route('transaksis.riwayat') }}" class="pc-link">
                                    <span class="pc-micon"><i class="ph ph-address-book"></i></span>
                                    <span class="pc-mtext">Riwayat Transaksi</span>
                                </a>
                            </li>
                        </ul>
                    </li>

                    {{-- ===== SEMUA MENU DI BAWAH INI KHUSUS ADMIN ===== --}}
                    @role('admin')

                    {{-- Manajemen User --}}
                    @php $userOpen = request()->is('roles*') || request()->is('users*'); @endphp
                    <li class="pc-item pc-caption">
                        <label data-i18n="UI Components">Manajemen User</label>
                        <i class="ph ph-pencil-ruler"></i>
                    </li>

                    <li class="pc-item pc-hasmenu {{ $userOpen ? 'pc-trigger active' : '' }}">
                        <a href="javascript:void(0)" class="pc-link">
                            <span class="pc-micon"><i class="ph ph-users"></i></span>
                            <span class="pc-mtext">Manajemen User</span>
                            <span class="pc-arrow"><i class="ph ph-caret-down"></i></span>
                        </a>

                        <ul class="pc-submenu" style="{{ $userOpen ? 'display:block;' : '' }}">
                            <li class="pc-item {{ request()->is('roles*') ? 'active' : '' }}">
                                <a href="{{ route('roles.index') }}" class="pc-link">
                                    <span class="pc-micon"><i class="ph ph-video-conference"></i></span>
                                    <span class="pc-mtext">Role</span>
                                </a>
                            </li>

                            <li class="pc-item {{ request()->is('users*') ? 'active' : '' }}">
                                <a href="{{ route('users.index') }}" class="pc-link">
                                    <span class="pc-micon"><i class="ph ph-user-gear"></i></span>
                                    <span class="pc-mtext">Akun Karyawan</span>
                                </a>
                            </li>
                        </ul>
                    </li>

                    {{-- Manajemen Produk --}}
                    @php
                        $produkOpen =
                            request()->is('product_categories*') ||
                            request()->is('products*') ||
                            request()->is('diskon*') ||
                            request()->is('vouchers*') ||
                            request()->is('taxes*');
                      @endphp

                    <li class="pc-item pc-caption">
                        <label data-i18n="UI Components">Manajemen Produk</label>
                        <i class="ph ph-pencil-ruler"></i>
                    </li>

                    <li class="pc-item pc-hasmenu {{ $produkOpen ? 'pc-trigger active' : '' }}">
                        <a href="javascript:void(0)" class="pc-link">
                            <span class="pc-micon"><i class="ph ph-package"></i></span>
                            <span class="pc-mtext">Manajemen Produk</span>
                            <span class="pc-arrow"><i class="ph ph-caret-down"></i></span>
                        </a>

                        <ul class="pc-submenu" style="{{ $produkOpen ? 'display:block;' : '' }}">
                            <li class="pc-item {{ request()->is('product_categories*') ? 'active' : '' }}">
                                <a href="{{ route('product_categories.index') }}" class="pc-link">
                                    <span class="pc-micon"><i class="ph ph-squares-four"></i></span>
                                    <span class="pc-mtext">Kategori Produk</span>
                                </a>
                            </li>

                            <li class="pc-item {{ request()->is('products*') ? 'active' : '' }}">
                                <a href="{{ route('products.index') }}" class="pc-link">
                                    <span class="pc-micon"><i class="ph ph-swatches"></i></span>
                                    <span class="pc-mtext">Produk</span>
                                </a>
                            </li>

                            <li class="pc-item {{ request()->is('diskon*') ? 'active' : '' }}">
                                <a href="{{ route('diskon.index') }}" class="pc-link">
                                    <span class="pc-micon"><i class="ph ph-tag"></i></span>
                                    <span class="pc-mtext">Diskon Produk</span>
                                </a>
                            </li>

                            <li class="pc-item {{ request()->is('vouchers*') ? 'active' : '' }}">
                                <a href="{{ route('vouchers.index') }}" class="pc-link">
                                    <span class="pc-micon"><i class="ph ph-ticket"></i></span>
                                    <span class="pc-mtext">Voucher</span>
                                </a>
                            </li>

                            <li class="pc-item {{ request()->is('taxes*') ? 'active' : '' }}">
                                <a href="{{ route('taxes.index') }}" class="pc-link">
                                    <span class="pc-micon"><i class="ph ph-calculator"></i></span>
                                    <span class="pc-mtext">Pajak</span>
                                </a>
                            </li>
                        </ul>
                    </li>

                    {{-- Laporan --}}
                    @php
                        $laporanOpen = request()->is('laporan_harian*')
                            || request()->is('laporan_bulanan*')
                            || request()->is('laporan_tahunan*');
                      @endphp

                    <li class="pc-item pc-caption">
                        <label data-i18n="UI Components">Laporan</label>
                        <i class="ph ph-pencil-ruler"></i>
                    </li>

                    <li class="pc-item pc-hasmenu {{ $laporanOpen ? 'pc-trigger active' : '' }}">
                        <a href="javascript:void(0)" class="pc-link">
                            <span class="pc-micon"><i class="ph ph-note"></i></span>
                            <span class="pc-mtext">Laporan</span>
                            <span class="pc-arrow"><i class="ph ph-caret-down"></i></span>
                        </a>

                        <ul class="pc-submenu" style="{{ $laporanOpen ? 'display:block;' : '' }}">
                            <li class="pc-item {{ request()->is('laporan_harian*') ? 'active' : '' }}">
                                <a href="{{ route('laporan.laporan_harian') }}" class="pc-link">
                                    <span class="pc-micon"><i class="ph ph-calendar-dot"></i></span>
                                    <span class="pc-mtext">Harian</span>
                                </a>
                            </li>

                            <li class="pc-item {{ request()->is('laporan_bulanan*') ? 'active' : '' }}">
                                <a href="{{ route('laporan.laporan_bulanan') }}" class="pc-link">
                                    <span class="pc-micon"><i class="ph ph-calendar-dots"></i></span>
                                    <span class="pc-mtext">Bulanan</span>
                                </a>
                            </li>

                            <li class="pc-item {{ request()->is('laporan_tahunan*') ? 'active' : '' }}">
                                <a href="{{ route('laporan.laporan_tahunan') }}" class="pc-link">
                                    <span class="pc-micon"><i class="ph ph-calendar"></i></span>
                                    <span class="pc-mtext">Tahunan</span>
                                </a>
                            </li>
                        </ul>
                    </li>

                    {{-- Laba Rugi --}}
                    @php $lrOpen = request()->is('laporan/laba-rugi*'); @endphp

                    <li class="pc-item pc-hasmenu {{ $lrOpen ? 'pc-trigger active' : '' }}">
                        <a href="javascript:void(0)" class="pc-link">
                            <span class="pc-micon"><i class="ph ph-chart-line-up"></i></span>
                            <span class="pc-mtext">Laba Rugi</span>
                            <span class="pc-arrow"><i class="ph ph-caret-down"></i></span>
                        </a>

                        <ul class="pc-submenu" style="{{ $lrOpen ? 'display:block;' : '' }}">
                            <li class="pc-item {{ request()->is('laporan/laba-rugi/harian*') ? 'active' : '' }}">
                                <a href="{{ route('laporan.laba_rugi_harian') }}" class="pc-link">
                                    <span class="pc-micon"><i class="ph ph-calendar-dot"></i></span>
                                    <span class="pc-mtext">Harian</span>
                                </a>
                            </li>

                            <li class="pc-item {{ request()->is('laporan/laba-rugi/bulanan*') ? 'active' : '' }}">
                                <a href="{{ route('laporan.laba_rugi_bulanan') }}" class="pc-link">
                                    <span class="pc-micon"><i class="ph ph-calendar-dots"></i></span>
                                    <span class="pc-mtext">Bulanan</span>
                                </a>
                            </li>

                            <li class="pc-item {{ request()->is('laporan/laba-rugi/tahunan*') ? 'active' : '' }}">
                                <a href="{{ route('laporan.laba_rugi_tahunan') }}" class="pc-link">
                                    <span class="pc-micon"><i class="ph ph-calendar"></i></span>
                                    <span class="pc-mtext">Tahunan</span>
                                </a>
                            </li>
                        </ul>
                    </li>

                    @endrole

                </ul>
            </div>

        </div>
    </nav>