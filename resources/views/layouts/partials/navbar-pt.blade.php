<div class="app-navbar d-flex justify-content-between">
    <!--begin::Notifications-->
    <div class="app-navbar-item ms-1 ms-md-4">
        <!--end::Menu separator-->
        <div class="me-1">
            <div class="btn btn-icon ps-6 btn-custom btn-icon-muted btn-active-light btn-active-color-primary w-35px h-35px rounded-circle"
                data-kt-menu-trigger="{default: 'click', lg: 'hover'}" data-kt-menu-attach="parent"
                data-kt-menu-placement="bottom-end" id="kt_menu_item_wow">
                <span class="menu-title position-relative">
                    <span class="ms-5 position-absolute translate-middle-y top-50 end-0">
                        <i class="ki-duotone ki-night-day theme-light-show fs-2">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                            <span class="path4"></span>
                            <span class="path5"></span>
                            <span class="path6"></span>
                            <span class="path7"></span>
                            <span class="path8"></span>
                            <span class="path9"></span>
                            <span class="path10"></span>
                        </i>
                        <i class="ki-duotone ki-moon theme-dark-show fs-2">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i> 
                    </span>
                </span>
            </div>
            <!--begin::Menu-->
            <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-title-gray-700 menu-icon-gray-500 menu-active-bg menu-state-color fw-semibold py-4 fs-base w-150px"
                data-kt-menu="true" data-kt-element="theme-mode-menu">
                <!--begin::Menu item-->
                <div class="menu-item px-3 my-0">
                    <a href="#" class="menu-link px-3 py-2" data-kt-element="mode" data-kt-value="light">
                        <span class="menu-icon" data-kt-element="icon">
                            <i class="ki-duotone ki-night-day fs-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                                <span class="path4"></span>
                                <span class="path5"></span>
                                <span class="path6"></span>
                                <span class="path7"></span>
                                <span class="path8"></span>
                                <span class="path9"></span>
                                <span class="path10"></span>
                            </i>
                        </span>
                        <span class="menu-title">Light</span>
                    </a>
                </div>
                <!--end::Menu item-->
                <!--begin::Menu item-->
                <div class="menu-item px-3 my-0">
                    <a href="#" class="menu-link px-3 py-2" data-kt-element="mode" data-kt-value="dark">
                        <span class="menu-icon" data-kt-element="icon">
                            <i class="ki-duotone ki-moon fs-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                        </span>
                        <span class="menu-title">Dark</span>
                    </a>
                </div>
                <!--end::Menu item-->
                <!--begin::Menu item-->
                <div class="menu-item px-3 my-0">
                    <a href="#" class="menu-link px-3 py-2" data-kt-element="mode" data-kt-value="system">
                        <span class="menu-icon" data-kt-element="icon">
                            <i class="ki-duotone ki-screen fs-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                                <span class="path4"></span>
                            </i>
                        </span>
                        <span class="menu-title">System</span>
                    </a>
                </div>
                <!--end::Menu item-->
            </div>
            <!--end::Menu-->
        </div>
        <!--begin::Menu item-->
    </div>
    <!--end::Notifications-->
    <!--begin::User menu-->
    <div class="app-navbar-item ms-1 ms-md-4" id="kt_header_user_menu_toggle">
        <!--begin::Menu wrapper-->
        <div class="cursor-pointer symbol symbol-35px" data-kt-menu-trigger="{default: 'click', lg: 'hover'}"
            data-kt-menu-attach="parent" data-kt-menu-placement="bottom-end">
            <img src="{{ asset(Auth::guard('trainer')->user()->thumbnail ?? 'assets/media/avatars/default.jpg') }}"
                class="rounded-circle" alt="user" />
        </div>
        <!--begin::User account menu-->
        <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg menu-state-color fw-semibold py-4 fs-6 w-275px"
            data-kt-menu="true">
            <!--begin::Menu item-->
            <div class="menu-item px-3">
                <div class="menu-content d-flex flex-column px-3">
                    <!--begin::Avatar-->
                    <div class="symbol symbol-50px me-5">
                        <img alt="Logo"
                            src="{{ asset(Auth::guard('trainer')->user()->thumbnail ?? 'assets/media/avatars/default.jpg') }}" />
                    </div>
                    <!--end::Avatar-->
                    <!--begin::Username-->
                    <div class="d-flex gap-2 flex-column">
                        <div class="fw-bold d-flex align-items-center fs-5">{{ Auth::guard('trainer')->user()->name ??
                            '' }}
                        </div>
                        <div class="fw-bold d-flex align-items-center fs-5">
                            <span class="badge badge-light-success fw-bold fs-8 px-2 py-1">{{
                                Auth::guard('trainer')->user()->email ?? '' }}</span>
                        </div>
                    </div>
                    <!--end::Username-->
                </div>
            </div>
            <!--end::Menu item-->
            <div class="menu-item px-5">
                <a href="{{route('personal-trainer.profile.index')}}" class="menu-link px-5">Profile</a>
            </div>
            <!--end::Menu item-->


            <!--begin::Menu item-->
            <div class="menu-item px-5">
                <a href="{{ route('logout') }}" class="menu-link px-5"
                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Sign Out</a>
                <form id="logout-form" action="{{ route('personal-trainer.logout') }}" method="POST" class="d-none">
                    @csrf</form>
            </div>
            <!--end::Menu item-->
        </div>
        <!--end::User account menu-->
        <!--end::Menu wrapper-->
    </div>
    <!--end::User menu-->
    <!--begin::Header menu toggle-->
    <div class="app-navbar-item d-lg-none ms-2 me-n2" title="Show header menu">
        <div class="btn btn-flex btn-icon btn-active-color-primary w-30px h-30px" id="kt_app_header_menu_toggle">
            <i class="ki-duotone ki-element-4 fs-1">
                <span class="path1"></span>
                <span class="path2"></span>
            </i>
        </div>
    </div>
    <!--end::Header menu toggle-->
    <!--begin::Aside toggle-->
    <!--end::Header menu toggle-->
</div>
