<!-- [ Main Content ] end -->
    <footer class="pc-footer">
        <div class="footer-wrapper container-fluid">
            <div class="row">
                <div class="col my-1">
                    <p class="m-0">Copyright &copy; {{ date('Y') }} <a href=""
                            target="_blank">{{ store_name('full') }}</a></p>
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

@role('admin')
    <script>

        document.addEventListener('DOMContentLoaded', function () {

            /* 🔄 Reload halaman kasir */
            document.getElementById('btnReload')?.addEventListener('click', function () {
                location.reload();
            });

            /* 🏠 Kembali ke Dashboard */
            document.getElementById('btnDashboard')?.addEventListener('click', function () {

                // hapus mode POS
                sessionStorage.removeItem('pos_mode');

                // keluar fullscreen (kalau ada)
                if (document.fullscreenElement) {
                    document.exitFullscreen().catch(() => { });
                }



                // redirect ke dashboard
                window.location.href = "{{ route('dashboard.admin') }}";
            });

        });
    </script>
    @endrole
    @role('kasir')
    <script>

        document.addEventListener('DOMContentLoaded', function () {

            /* 🔄 Reload halaman kasir */
            document.getElementById('btnReload')?.addEventListener('click', function () {
                location.reload();
            });

            /* 🏠 Kembali ke Dashboard */
            document.getElementById('btnDashboard')?.addEventListener('click', function () {

                // hapus mode POS
                sessionStorage.removeItem('pos_mode');

                // keluar fullscreen (kalau ada)
                if (document.fullscreenElement) {
                    document.exitFullscreen().catch(() => { });
                }



                // redirect ke dashboard
                window.location.href = "{{ route('dashboard.kasir') }}";
            });

        });
    </script>
    @endrole


</body>
<!-- [Body] end -->


</html>