<div class="navbar-custom">
    <ul class="list-unstyled topnav-menu float-right mb-0">
        <li class="dropdown notification-list">
            <a class="nav-link dropdown-toggle nav-user mr-0 waves-effect waves-light" data-toggle="dropdown" href="#">
                <i class="dripicons-user"></i>
                <span class="pro-user-name ml-1">Lender <i class="mdi mdi-chevron-down"></i></span>
            </a>
            <div class="dropdown-menu dropdown-menu-right profile-dropdown">
                <a href="{{ route('lender.settings.index') }}" class="dropdown-item notify-item">
                    <i class="dripicons-gear"></i><span>Pengaturan</span>
                </a>
            </div>
        </li>
    </ul>
    <button class="button-menu-mobile open-left waves-effect">
        <i class="fe-menu"></i>
    </button>
</div>
