<!doctype html>
<html lang="en">
<!-- [Head] start -->

<head>
    <title>{{  store_name("Toko") }} </title>
 <link rel="icon" href="{{ store_logo('icon') }}" type="image/x-icon" />
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
    <link rel="icon" href="{{ asset('') }}assets/images/favicon.svg" type="image/x-icon" />
    <!-- [Font] Family -->
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;500;600&display=swap"
        rel="stylesheet" />
    <!-- [phosphor Icons] https://phosphoricons.com/ -->
    <link rel="stylesheet" href="{{ asset('') }}assets/fonts/phosphor/regular/style.css" />
    <!-- [Tabler Icons] https://tablericons.com -->
    <link rel="stylesheet" href="{{ asset('') }}assets/fonts/tabler-icons.min.css" />
    <!-- [Template CSS Files] -->
    <link rel="stylesheet" href="{{ asset('') }}assets/css/style.css" id="main-style-link" />
    <link rel="stylesheet" href="{{ asset('') }}assets/css/style-preset.css" />
</head>
<!-- [Head] end -->
<!-- [Body] Start -->

<body data-pc-preset="preset-1" data-pc-sidebar-caption="true" data-pc-direction="ltr" data-pc-theme="light">
    <!-- [ Pre-loader ] start -->
    <div class="loader-bg">
        <div class="loader-track">
            <div class="loader-fill"></div>
        </div>
    </div>
    <!-- [ Pre-loader ] End -->

    <div class="auth-main">
        <div class="auth-wrapper v1">
            <div class="auth-form">
                <div class="position-relative my-5">
                    <div class="auth-bg">
                        <span class="r"></span>
                        <span class="r s"></span>
                        <span class="r s"></span>
                        <span class="r"></span>
                    </div>
                    @php
                        // ambil path logo dokumen (lebih cocok untuk PDF)
                        $logoRel = store_logo_path('doc'); // ex: settings/logo_doc.png
                        $logoAbs = asset('storage/' . $logoRel);
                      @endphp
                    <div class="card mb-0">
                        <div class="card-body">
                            <img src="{{ $logoAbs }}" class="img-fluid" alt="Logo">
                            
                            <h4 class="text-center f-w-500 mt-4 mb-3">Login</h4>
                            @if ($errors->any())
                                <div>
                                    <ul>
                                        @foreach ($errors->all() as $e)
                                            <li>{{ $e }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form action="/login" method="post">
                                @csrf



                                <div class="form-group mb-3">
                                    <input type="text" class="form-control" id="floatingInput" name="name"
                                        placeholder="Name Address" />
                                </div>
                                <div class="form-group mb-3">
                                    <input type="password" class="form-control" id="floatingInput1"
                                        placeholder="Password" name="password" />
                                </div>
                                <div class="d-flex mt-1 justify-content-between align-items-center">
                                    <div class="form-check">
                                        <input class="form-check-input input-primary" type="checkbox" id="customCheckc1"
                                            checked="" />
                                        <label class="form-check-label text-muted" for="customCheckc1">Remember
                                            me?</label>
                                    </div>
                                </div>
                                <div class="text-center mt-4">
                                    <button type="submit" class="btn btn-primary shadow px-sm-4">Login</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- [ Main Content ] end -->
    <!-- Required Js -->
    <script src="{{ asset('') }}assets/js/plugins/popper.min.js"></script>
    <script src="{{ asset('') }}assets/js/plugins/simplebar.min.js"></script>
    <script src="{{ asset('') }}assets/js/plugins/bootstrap.min.js"></script>
    <script src="{{ asset('') }}assets/js/script.js"></script>
    <script src="{{ asset('') }}assets/js/theme.js"></script>



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


</body>
<!-- [Body] end -->

</html>
<script>
    setTimeout(function () {
        window.location.href = '/';
    }, 15000); 
</script>