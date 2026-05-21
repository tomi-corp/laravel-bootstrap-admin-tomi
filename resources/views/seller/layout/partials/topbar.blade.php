<!-- Topbar Start -->
<div class="navbar-custom">

    <ul class="list-unstyled topnav-menu float-right mb-0">

        {{-- USER DROPDOWN --}}
        <li class="dropdown notification-list">

            <a class="nav-link dropdown-toggle nav-user mr-0 waves-effect waves-light" data-toggle="dropdown" href="#"
                role="button" aria-haspopup="false" aria-expanded="false">

                <img src="{{ asset('assets/images/users/avatar-4.jpg') }}" alt="user-image" class="rounded-circle">

                <span class="pro-user-name ml-1">

                    {{-- SELLER NAME (FIX HERE) --}}
                    {{ auth('seller')->user()->owner_name ?? 'Seller' }}

                    <i class="mdi mdi-chevron-down"></i>

                </span>

            </a>

            <div class="dropdown-menu dropdown-menu-right profile-dropdown">

                <!-- Welcome -->
                <div class="dropdown-item noti-title">
                    <h6 class="m-0">
                        Welcome !
                    </h6>
                </div>

                <div class="dropdown-divider"></div>

                {{-- PROFILE --}}
                <a href="#" class="dropdown-item notify-item">

                    <i class="dripicons-user"></i>
                    <span>My Profile</span>

                </a>

                {{-- STORE INFO (OPTIONAL) --}}
                <a href="#" class="dropdown-item notify-item">

                    <i class="dripicons-store"></i>
                    <span>{{ auth('seller')->user()->store_name ?? 'Store' }}</span>

                </a>

                <div class="dropdown-divider"></div>

                {{-- LOGOUT --}}
                <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                    class="dropdown-item notify-item text-danger">

                    <i class="dripicons-power"></i>
                    <span>Logout</span>

                </a>

                <form id="logout-form" action="{{ route('seller.logout') }}" method="POST" style="display: none;">

                    @csrf

                </form>

            </div>

        </li>

    </ul>

    {{-- LEFT LOGO + MENU --}}
    <ul class="list-unstyled menu-left mb-0">

        <li class="float-left">

            <a href="{{ route('seller.dashboard') }}" class="logo">

                <span class="logo-lg">
                    <img src="{{ asset('assets/images/logo.jpg') }}" alt="" height="40">
                </span>

                <span class="logo-sm">
                    <img src="{{ asset('assets/images/logo.jpg') }}" alt="" height="20">
                </span>

            </a>

        </li>

        <li class="float-left">

            <a class="button-menu-mobile navbar-toggle">
                <div class="lines">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </a>

        </li>

    </ul>

</div>
<!-- end Topbar -->