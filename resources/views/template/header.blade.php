<!-- [ Sidebar Menu ] end -->
<!-- [ Header Topbar ] start -->
<header class="pc-header">
    <div class="header-wrapper"> <!-- [Mobile Media Block] start -->
        <div class="me-auto pc-mob-drp">
            <ul class="list-unstyled">
                <li class="pc-h-item pc-sidebar-collapse">
                    <a href="#" class="pc-head-link ms-0" id="sidebar-hide">
                        <i class="ph ph-list"></i>
                    </a>
                </li>
                <li class="pc-h-item pc-sidebar-popup">
                    <a href="#" class="pc-head-link ms-0" id="mobile-collapse">
                        <i class="ph ph-list"></i>
                    </a>
                </li>

            </ul>
        </div>
        <!-- [Mobile Media Block end] -->
        <div class="ms-auto">
            <ul class="list-unstyled">

                <li class="dropdown pc-h-item header-user-profile">
                    <button class="btn btn-secondary me-2" id="btnReload" type="button">
                        🔄 Reload
                    </button>

                    <button class="btn btn-warning" id="btnDashboard" type="button">
                        🏠 Dashboard
                    </button>
                    <a class="pc-head-link dropdown-toggle arrow-none me-0" data-bs-toggle="dropdown" href="#"
                        role="button" aria-haspopup="false" data-bs-auto-close="outside" aria-expanded="false">
                        <i class="ph ph-user-circle"></i>
                    </a>
                    <div
                        class="dropdown-menu dropdown-user-profile dropdown-menu-end pc-h-dropdown p-0 overflow-hidden">
                        <div class="dropdown-header d-flex align-items-center justify-content-between bg-primary">
                            <div class="d-flex my-2">
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="text-white mb-1">{{ auth()->user()->name }}</h6>
                                    <span class="text-white text-opacity-75">{{ auth()->user()->email }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="dropdown-body">
                            <div class="profile-notification-scroll position-relative"
                                style="max-height: calc(100vh - 225px)">
                                <a href="{{ route('users.edit', auth()->user()->id) }}" class="dropdown-item">
                                    <span>
                                        <i class="ph ph-user align-middle me-2"></i>
                                        <span>Profil</span>
                                    </span>
                                </a>
                                <a href="{{ route('settings.toko') }}" class="dropdown-item">
                                    <span>
                                        <i class="ph ph-gear align-middle me-2"></i>
                                        <span>Settings</span>
                                    </span>
                                </a>

                                <a href="{{ route('admin.theme') }}" class="dropdown-item">
                                    <span>
                                        <i class="ph ph-palette align-middle me-2"></i>
                                        <span>Tema</span>
                                    </span>
                                </a>

                                <div class="d-grid my-2">
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="btn btn-danger">
                                            <i class="ph ph-sign-out align-middle me-2"></i>
                                            <span>Logout</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</header>
<!-- [ Header ] end -->