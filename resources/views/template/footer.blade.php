<!-- [ Main Content ] end -->
<footer class="pc-footer">
    <div class="footer-wrapper container-fluid">
        <div class="row">
            <div class="col my-1">
                <p class="m-0">Copyright &copy; {{ date('Y') }} <a href="" target="_blank">{{ store_name('full') }}</a>
                </p>
            </div>
        </div>
    </div>
</footer>
<!-- Required Js -->
<script src="{{ asset('assets/js/plugins/popper.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/simplebar.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/bootstrap.min.js') }}"></script>
<script src="{{ asset('assets/js/script.js') }}"></script>
<script src="{{ asset('assets/js/theme.js') }}"></script>
<!-- Tambahkan jQuery -->




<script>
    layout_change('light');
</script>

<script>
    change_box_container('false');
</script>

<script>
    layout_caption_change('true');
</script>

<script>
    layout_rtl_change('false');
</script>

<script>
    preset_change('preset-1');
</script>

<script>
    layout_theme_sidebar_change('false');
</script>
<script>
    document.addEventListener('DOMContentLoaded', function () {

        document.querySelectorAll('input').forEach(function (input) {
            input.setAttribute('autocomplete', 'off');
            input.setAttribute('autocorrect', 'off');
            input.setAttribute('autocapitalize', 'off');
            input.setAttribute('spellcheck', 'false');



            // Hindari name umum yang sering dipakai autofill
            if (['amount', 'price', 'total', 'bayar', 'dibayar'].includes(input.name)) {
                input.setAttribute('name', input.name + '_noauto');
            }
        });

    });
</script>

<script>
    $(document).on('click', '#btnReload', function () {
        location.reload();
    });
    $(document).on('click', '#btnDashboard', function () {

        let transactionId = $('#batal').data('transaksi');

        if (!transactionId) {
            redirectDashboard();
            return;
        }

        Swal.fire({
            title: 'Batalkan transaksi?',
            text: 'Transaksi yang sedang berjalan akan dibatalkan.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Batalkan',
            cancelButtonText: 'Tidak'
        }).then((result) => {

            if (result.isConfirmed) {

                $.ajax({
                    url: "{{ route('kasir.batal') }}",
                    method: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        transactionId: transactionId
                    },
                    success: function () {
                        redirectDashboard();
                    },
                    error: function (xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal membatalkan transaksi',
                            text: xhr.responseJSON?.message || 'Terjadi kesalahan.'
                        });
                    }
                });

            }
        });

    });


    function redirectDashboard() {

        @role('admin')
        window.location.href = "{{ route('dashboard.admin') }}";
        @endrole

        @role('kasir')
        window.location.href = "{{ route('dashboard.kasir') }}";
        @endrole
    }


</script>

<script>
$(document).on('keydown', function (e) {

    const key = e.key.toLowerCase();
    const isAdmin = @json(auth()->user()->hasRole('admin'));
    const isKasir = @json(auth()->user()->hasRole('kasir'));
    const currentRoute = "{{ Route::currentRouteName() }}";

    // ===============================
    // DASHBOARD
    // ALT + D
    // ===============================
    if (e.altKey && key === 'd') {
        e.preventDefault();
        $('#btnDashboard').click();
    }

    // ===============================
    // MENU KASIR GLOBAL
    // ===============================
    if (e.altKey && key === 't') {
        e.preventDefault();
        window.location.href = "{{ route('kasir.index') }}";
    }

    if (e.altKey && key === 'r') {
        e.preventDefault();
        window.location.href = "{{ route('transaksis.riwayat') }}";
    }

    // ===============================
    // SHORTCUT KHUSUS HALAMAN KASIR
    // ===============================
    if (currentRoute === 'kasir.index') {

        if (e.altKey && key === 'n') {
            e.preventDefault();
            $('#barcode').focus().select();
        }

        // GANTI ALT + V → ALT + J (voucher input)
        if (e.altKey && key === 'j') {
            e.preventDefault();
            $('#voucherCode').focus().select();
        }

        if (e.altKey && key === 'c') {
            e.preventDefault();
            $('#btnApplyVoucher').click();
        }

        // GANTI ALT + B → ALT + Q (bayar input)
        if (e.altKey && key === 'q') {
            e.preventDefault();
            $('#paid').focus().select();
        }

        if (e.altKey && key === 'm') {
            e.preventDefault();
            $('#paymentMethod').focus();
        }

        if (e.key === 'F9') {
            e.preventDefault();
            $('#bayar').click();
        }
    }

    // ===============================
    // MENU ADMIN
    // ===============================
    if (isAdmin) {

        if (e.altKey && key === 'u') {
            e.preventDefault();
            window.location.href = "{{ route('users.index') }}";
        }

        if (e.altKey && key === 'o') {
            e.preventDefault();
            window.location.href = "{{ route('roles.index') }}";
        }

        if (e.altKey && key === 'i') {
            e.preventDefault();
            window.location.href = "{{ route('products.index') }}";
        }

        if (e.altKey && key === 'k') {
            e.preventDefault();
            window.location.href = "{{ route('product_categories.index') }}";
        }

        if (e.altKey && key === 'v') {
            e.preventDefault();
            window.location.href = "{{ route('vouchers.index') }}";
        }

        if (e.altKey && key === 'x') {
            e.preventDefault();
            window.location.href = "{{ route('taxes.index') }}";
        }

        if (e.altKey && key === 'h') {
            e.preventDefault();
            window.location.href = "{{ route('laporan.laporan_harian') }}";
        }

        if (e.altKey && key === 'b') {
            e.preventDefault();
            window.location.href = "{{ route('laporan.laporan_bulanan') }}";
        }

        if (e.altKey && key === 'y') {
            e.preventDefault();
            window.location.href = "{{ route('laporan.laporan_tahunan') }}";
        }

        if (e.altKey && key === 'l') {
            e.preventDefault();
            window.location.href = "{{ route('laporan.laba_rugi_harian') }}";
        }

        if (e.altKey && key === 'e') {
            e.preventDefault();
            window.location.href = "{{ route('users.edit', auth()->user()->id) }}";
        }

        if (e.altKey && key === 's') {
            e.preventDefault();
            window.location.href = "{{ route('settings.toko') }}";
        }

        if (e.altKey && key === 'a') {
            e.preventDefault();
            window.location.href = "{{ route('admin.theme') }}";
        }
    }

    // ===============================
    // LOGOUT
    // F10
    // ===============================
    if (e.key === 'F10') {
        e.preventDefault();
        $('form[action="{{ route('logout') }}"]').submit();
    }

});
</script>
<script>
    // Klik tombol ?
    $('#btnShortcutInfo').on('click', function () {
        $('#shortcutModal').fadeIn();
    });

    // Tombol tutup
    $('#closeShortcutModal').on('click', function () {
        $('#shortcutModal').fadeOut();
    });

    // Klik area gelap untuk tutup
    $('#shortcutModal').on('click', function (e) {
        if (e.target === this) {
            $(this).fadeOut();
        }
    });

    // F1 untuk buka info
    $(document).on('keydown', function (e) {
        if (e.key === 'F1') {
            e.preventDefault();
            $('#shortcutModal').fadeIn();
        }
    });
</script>


</body>
<!-- [Body] end -->


</html>