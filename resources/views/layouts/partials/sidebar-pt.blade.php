<div id="kt_app_sidebar" class="app-sidebar flex-column" data-kt-drawer="true" data-kt-drawer-name="app-sidebar"
    data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true" data-kt-drawer-width="225px"
    data-kt-drawer-direction="start" data-kt-drawer-toggle="#kt_app_sidebar_mobile_toggle">
    <!--begin::Logo-->
    <div class="app-sidebar-logo px-6" id="kt_app_sidebar_logo">
        <!--begin::Logo image-->
        <a href="">
            <img alt="Logo" src="{{ asset('assets/media/logos/logo.svg') }}"
                class="h-40px mx-auto app-sidebar-logo-default" />
            <img alt="Logo" src="{{ asset('assets/media/logos/logo.svg') }}"
                class="h-20px app-sidebar-logo-minimize" />
        </a>
        <div id="kt_app_sidebar_toggle"
            class="app-sidebar-toggle btn btn-icon btn-shadow btn-sm btn-color-muted btn-active-color-primary h-30px w-30px position-absolute top-50 start-100 translate-middle rotate"
            data-kt-toggle="true" data-kt-toggle-state="active" data-kt-toggle-target="body"
            data-kt-toggle-name="app-sidebar-minimize">
            <i class="ki-duotone ki-black-left-line fs-3 rotate-180">
                <span class="path1"></span>
                <span class="path2"></span>
            </i>
        </div>

        <!--end::Sidebar toggle-->
    </div>
    <!--end::Logo-->
    <!--begin::sidebar menu-->
    <div class="app-sidebar-menu overflow-hidden flex-column-fluid">
        <!--begin::Menu wrapper-->
        <div id="kt_app_sidebar_menu_wrapper" class="app-sidebar-wrapper">
            <!--begin::Scroll wrapper-->
            <div id="kt_app_sidebar_menu_scroll" class="scroll-y my-5 mx-3" data-kt-scroll="true"
                data-kt-scroll-activate="true" data-kt-scroll-height="auto"
                data-kt-scroll-dependencies="#kt_app_sidebar_logo, #kt_app_sidebar_footer"
                data-kt-scroll-wrappers="#kt_app_sidebar_menu" data-kt-scroll-offset="5px"
                data-kt-scroll-save-state="true">
                <!--begin::Menu-->
                <div class="menu menu-column menu-rounded menu-sub-indention fw-semibold fs-6" id="#kt_app_sidebar_menu"
                    data-kt-menu="true" data-kt-menu-expand="false">
                    <div class="menu-item mb-2">
                        <a class="menu-link {{ request()->routeIs('personal-trainer.dashboard') ? ' active' : '' }}"
                            href="{{ route('personal-trainer.dashboard') }}">
                            <div class="menu-icon">
                                <img class="icon" src="{{ asset('assets/media/icons/cube.svg') }}" alt="Dashboard">
                            </div>
                            <span class="menu-title">Dashboard</span>
                        </a>
                    </div>
                    <div class="menu-item mb-2">
                        <a class="menu-link {{ request()->routeIs('personal-trainer.schedule.index') ? ' active' : '' }}"
                            href="{{ route('personal-trainer.schedule.index') }}">
                            <div class="menu-icon">
                                <img class="icon" src="{{ asset('assets/media/icons/calendar.svg') }}" alt="Jadwal">
                            </div>
                            <span class="menu-title">Jadwal</span>
                        </a>
                    </div>
                    <div class="menu-item mb-2">
                        <a class="menu-link {{ request()->routeIs('personal-trainer.membership.*') ? ' active' : '' }}"
                            href="{{ route('personal-trainer.membership.index') }}">
                            <div class="menu-icon">
                                <img class="icon" src="{{ asset('assets/media/icons/users.svg') }}" alt="Member">
                            </div>
                            <span class="menu-title">Member</span>
                        </a>
                    </div>
                    <!--<div class="menu-item mb-2">-->
                    <!--    <a class="menu-link {{ request()->routeIs('personal-trainer.booking.*') ? ' active' : '' }}"-->
                    <!--        href="{{ route('personal-trainer.booking.index') }}">-->
                    <!--        <span class="menu-icon">-->
                    <!--            <i class="ki-duotone ki-wallet fs-2">-->
                    <!--                <span class="path1"></span>-->
                    <!--                <span class="path2"></span>-->
                    <!--                <span class="path3"></span>-->
                    <!--            </i>-->
                    <!--        </span>-->
                    <!--        <span class="menu-title">Booking</span>-->
                    <!--    </a>-->
                    <!--</div>-->
                    <div class="menu-item mb-2">
                        <a class="menu-link {{ request()->routeIs('personal-trainer.chat.*') ? ' active' : '' }}"
                            href="{{ route('personal-trainer.chat.index') }}">
                            <div class="menu-icon">
                                <img class="icon" src="{{ asset('assets/media/icons/chat.svg') }}" alt="Chat">
                            </div>
                            <span class="menu-title">Chat</span>
                        </a>
                    </div>
                    <div class="menu-item mb-2">
                        <a class="menu-link {{ request()->routeIs('personal-trainer.profile.*') ? ' active' : '' }}"
                            href="{{ route('personal-trainer.profile.index') }}">
                            <div class="menu-icon">
                                <img class="icon" src="{{ asset('assets/media/icons/profile.svg') }}" alt="Profil">
                            </div>
                            <span class="menu-title">Profil</span>
                        </a>
                    </div>
                    {{-- @canany(['training-program', 'movement', 'category-movement']) --}}
                    <div data-kt-menu-trigger="click"
                        class="menu-item menu-accordion {{ request()->routeIs(['trainer.training-program.*', 'trainer.movement.*', 'trainer.category-movement.*']) ? 'show' : '' }}">
                        <!--begin:Menu link-->
                        <span class="menu-link">
                            <span class="menu-icon">
                                <i class="fas fa-dumbbell fs-2"></i>
                            </span>
                            <span class="menu-title">Program Latihan</span>
                            <span class="menu-arrow"></span>
                        </span>
                        <!--end:Menu link-->
                        <!--begin:Menu sub-->
                        <div class="menu-sub menu-sub-accordion">
                            <!--begin:Menu item-->
                            {{-- @can('training-program') --}}
                            <div class="menu-item ">
                                <!--begin:Menu link-->
                                <a class="menu-link {{ request()->routeIs(['trainer.training-program.index']) ? ' active' : '' }}"
                                    href="{{ route('trainer.training-program.index') }}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title">Program</span>
                                </a>
                                <!--end:Menu link-->
                            </div>
                            {{-- @endcan --}}
                            <!--end:Menu item-->

                            <!--begin:Menu item-->
                            {{-- @can('movement') --}}
                            <div class="menu-item ">
                                <!--begin:Menu link-->
                                <a class="menu-link {{ request()->routeIs(['trainer.movement.index']) ? ' active' : '' }}"
                                    href="{{ route('trainer.movement.index') }}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title">Gerakan</span>
                                </a>
                                <!--end:Menu link-->
                            </div>
                            {{-- @endcan --}}
                            <!--end:Menu item-->

                            <!--begin:Menu item-->
                            {{-- @can('category-movement') --}}
                            <div class="menu-item ">
                                <!--begin:Menu link-->
                                <a class="menu-link {{ request()->routeIs(['trainer.category-movement.index']) ? ' active' : '' }}"
                                    href="{{ route('trainer.category-movement.index') }}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title">Kategori</span>
                                </a>
                                <!--end:Menu link-->
                            </div>
                            {{-- @endcan --}}
                            <!--end:Menu item-->
                        </div>
                        <!--end:Menu sub-->
                    </div>
                    {{-- @endcanany --}}
                </div> 
                <!--end::Menu-->
            </div>
            <!--end::Scroll wrapper-->
        </div>
        <!--end::Menu wrapper-->
    </div>
    <!--end::sidebar menu-->
</div>
