@extends('template.master')

@section('content')
    @php
        $img = function ($path) {
            return $path ? asset('storage/' . $path) : null;
        };
    @endphp

    <div class="container-fluid">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4 class="mb-0">Setting Toko</h4>
                <small class="text-muted">Atur identitas toko & upload logo</small>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('settings.toko.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row g-3">

                {{-- Identitas --}}
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="fw-semibold mb-2">Identitas Toko</div>

                            <div class="mb-3">
                                <label class="form-label">Nama Toko</label>
                                <input type="text" class="form-control" name="store_name"
                                    value="{{ old('store_name', $setting->store_name) }}" required>
                            </div>

                            <div class="row g-2">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Telepon</label>
                                    <input type="text" class="form-control" name="phone"
                                        value="{{ old('phone', $setting->phone) }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" class="form-control" name="email"
                                        value="{{ old('email', $setting->email) }}">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Alamat</label>
                                <textarea class="form-control" name="address"
                                    rows="3">{{ old('address', $setting->address) }}</textarea>
                            </div>

                            

                        </div>
                    </div>
                </div>

                {{-- Struk --}}
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="fw-semibold mb-2">Pengaturan Struk</div>

                            <div class="mb-3">
                                <label class="form-label">Header Struk (opsional)</label>
                                <input type="text" class="form-control" name="receipt_header"
                                    value="{{ old('receipt_header', $setting->receipt_header) }}"
                                    placeholder="Contoh: Terima kasih sudah berbelanja">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Footer Struk (opsional)</label>
                                <textarea class="form-control" name="receipt_footer" rows="4"
                                    placeholder="Contoh: Barang yang sudah dibeli tidak dapat dikembalikan.">{{ old('receipt_footer', $setting->receipt_footer) }}</textarea>
                            </div>

                            <div class="alert alert-info mb-0">
                                Tips: untuk printer thermal, gunakan logo yang sederhana (hitam/putih).
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Logo Upload --}}
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="fw-semibold mb-2">Upload Logo</div>
                            <small class="text-muted d-block mb-3">
                                Format: PNG/JPG/WEBP, max 2MB. Disarankan: PNG transparan.
                            </small>

                            <div class="row g-3">

                                @php
                                    $items = [
                                        ['key' => 'logo_app_dark', 'title' => 'Logo App (Dark)', 'hint' => 'Dipakai di sidebar/topbar tema gelap', 'rec' => '800x800 / transparan'],
                                        ['key' => 'logo_app_light', 'title' => 'Logo App (Light)', 'hint' => 'Dipakai di Struk', 'rec' => '800x800 / transparan'],
                                        ['key' => 'logo_doc', 'title' => 'Logo Dokumen (PDF/Laporan)', 'hint' => 'Untuk laporan PDF/invoice', 'rec' => '800x800 / putih/hitam'],
                                       
                                    ];
                                  @endphp

                                @foreach($items as $it)
                                    @php $path = $setting->{$it['key']}; @endphp
                                    <div class="col-md-6 col-lg-4">
                                        <div class="border rounded p-3 h-100">
                                            <div class="fw-semibold">{{ $it['title'] }}</div>
                                            <div class="text-muted" style="font-size:12px;">{{ $it['hint'] }}</div>
                                            <div class="text-muted mb-2" style="font-size:12px;">Rekomendasi: {{ $it['rec'] }}
                                            </div>

                                            <div class="mb-2">
                                                <input class="form-control" type="file" name="{{ $it['key'] }}" accept="image/*"
                                                    onchange="previewLogo(this, 'prev_{{ $it['key'] }}')">
                                            </div>

                                            <div class="d-flex gap-2 align-items-center">
                                                <div
                                                    style="width:72px;height:72px;border:1px dashed #cbd5e1;border-radius:12px;display:flex;align-items:center;justify-content:center;overflow:hidden;background:#f8fafc;">
                                                    <img id="prev_{{ $it['key'] }}" src="{{ $img($path) }}"
                                                        style="max-width:100%;max-height:100%;{{ $path ? '' : 'display:none;' }}">
                                                    <span id="ph_{{ $it['key'] }}" class="text-muted"
                                                        style="font-size:12px;{{ $path ? 'display:none;' : '' }}">Preview</span>
                                                </div>

                                                @if($path)
                                                    <small class="text-muted" style="word-break:break-all;">
                                                        {{ $path }}
                                                    </small>
                                                @endif
                                            </div>

                                        </div>
                                    </div>
                                @endforeach

                            </div>

                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <button class="btn btn-primary px-4">Simpan</button>
                </div>

            </div>
        </form>
    </div>

    <script>
        function previewLogo(input, imgId) {
            const img = document.getElementById(imgId);
            const ph = document.getElementById('ph_' + imgId.replace('prev_', ''));
            if (input.files && input.files[0]) {
                img.src = URL.createObjectURL(input.files[0]);
                img.style.display = 'block';
                if (ph) ph.style.display = 'none';
            }
        }
    </script>
@endsection