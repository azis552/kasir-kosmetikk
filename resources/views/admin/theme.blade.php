@extends('template.master')

@section('content')
    <div class="container py-4">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-4">🎨 Pengaturan Tema Aplikasi</h4>

                <div class="alert alert-info">
                    Pengaturan warna ini akan diterapkan ke seluruh aplikasi (Dashboard, Kasir, Laporan, dll).
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.theme.update') }}" method="POST">
                    @csrf

                    <div class="row g-4">

                        <!-- PRIMARY -->
                        <div class="col-md-4">
                            <label class="fw-semibold">Primary Color</label>
                            <small class="text-muted d-block mb-2">
                                Digunakan untuk: Button utama (Simpan, Bayar, Tambah), menu aktif, highlight penting.
                            </small>
                            <input type="color" name="primary_color" class="form-control form-control-color"
                                value="{{ $theme->primary_color }}">
                        </div>

                        <!-- SECONDARY -->
                        <div class="col-md-4">
                            <label class="fw-semibold">Secondary Color</label>
                            <small class="text-muted d-block mb-2">
                                Digunakan untuk: Button sekunder, badge, elemen tambahan.
                            </small>
                            <input type="color" name="secondary_color" class="form-control form-control-color"
                                value="{{ $theme->secondary_color }}">
                        </div>

                        <!-- SIDEBAR -->
                        <div class="col-md-4">
                            <label class="fw-semibold">Sidebar Color</label>
                            <small class="text-muted d-block mb-2">
                                Digunakan untuk: Background menu sidebar kiri.
                            </small>
                            <input type="color" name="sidebar_color" class="form-control form-control-color"
                                value="{{ $theme->sidebar_color }}">
                        </div>

                        <!-- BACKGROUND -->
                        <div class="col-md-4">
                            <label class="fw-semibold">Background Color</label>
                            <small class="text-muted d-block mb-2">
                                Digunakan untuk: Background utama halaman.
                            </small>
                            <input type="color" name="background_color" class="form-control form-control-color"
                                value="{{ $theme->background_color }}">
                        </div>

                        <!-- TEXT -->
                        <div class="col-md-4">
                            <label class="fw-semibold">Text Color</label>
                            <small class="text-muted d-block mb-2">
                                Digunakan untuk: Warna teks sidebar.
                            </small>
                            <input type="color" name="text_color" class="form-control form-control-color"
                                value="{{ $theme->text_color }}">
                        </div>

                    </div>

                    <button class="btn btn-success mt-4">
                        💾 Simpan Pengaturan Tema
                    </button>
                </form>
            </div>
        </div>



    </div>
@endsection