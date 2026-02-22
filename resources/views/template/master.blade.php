@include('template.sidebar')

@include('template.header')
<!-- FLOATING INFO BUTTON -->
<button id="btnShortcutInfo"
        style="
            position:fixed;
            bottom:20px;
            right:20px;
            width:50px;
            height:50px;
            border-radius:50%;
            background:#0d6efd;
            color:white;
            border:none;
            font-size:20px;
            z-index:9999;
        ">
    ?
</button>
    <!-- [ Main Content ] start -->
    <div class="pc-container">
        <div class="pc-content">
            <!-- [ breadcrumb ] start -->
            <div class="page-header">
                <div class="page-block">
                    <div class="row align-items-center">
                        <div class="col-md-12">
                            <div class="page-header-title">
                                <h5 class="mb-0">{{ $title }}</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
@yield('content')

            <!-- [ Main Content ] end -->
        </div>
    </div>

@include('template.footer')


@yield('script')