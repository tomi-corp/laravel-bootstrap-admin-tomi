{{-- Left Sidebar Start --}}
<div class="left-side-menu">

    <div class="slimscroll-menu">
        <div id="sidebar-menu">
            <ul class="metismenu" id="side-menu">

                <li class="menu-title">Navigation</li>

                {{-- DASHBOARD --}}
                <li>
                    <a href="{{ route('bta.dashboard.index') }}">
                        <i class="dripicons-graph-bar"></i>
                        <span> Dashboard </span>
                    </a>
                </li>


                {{-- MASTER --}}
                @if(in_array(optional(auth('bta')->user())->type, ['master', 'super user']))
                <li class="menu-title">Master</li>

                <li>
                    <a href="{{ route('bta.nasabah.index') }}">
                        <i class="dripicons-user-group"></i>
                        <span> Administrator </span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('bta.analyst.index') }}">
                        <i class="dripicons-graph-pie"></i>
                        <span> Analyst </span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('bta.credam.index') }}">
                        <i class="dripicons-wallet"></i>
                        <span> Credit Administrator </span>
                    </a>
                </li>
                @endif

                {{-- ADMIN --}}
                @if(optional(auth('bta')->user())->type == 'admin')
                <li class="menu-title">Administrator</li>

                <li>
                    <a href="{{ route('bta.nasabah.index') }}">
                        <i class="dripicons-user-group"></i>
                        <span> Administrator </span>
                    </a>
                </li>
                @endif

                {{-- ANALYST --}}
                @if(optional(auth('bta')->user())->type == 'analyst')
                <li class="menu-title">Analyst</li>

                <li>
                    <a href="{{ route('bta.analyst.index') }}">
                        <i class="dripicons-graph-pie"></i>
                        <span> Analyst </span>
                    </a>
                </li>
                @endif

                {{-- CREDAM --}}
                @if(optional(auth('bta')->user())->type == 'credam')
                <li class="menu-title">Credit Administrator</li>

                <li>
                    <a href="{{ route('bta.credam.index') }}">
                        <i class="dripicons-wallet"></i>
                        <span> Credit Administrator </span>
                    </a>
                </li>
                @endif

                <li>
                    <a href="{{ route('bta.finance.index') }}">
                        <i class="dripicons-wallet"></i>
                        <span> Finance </span>
                    </a>
                </li>

                {{-- SUPER USER --}}
                @if(optional(auth('bta')->user())->type == 'super user')
                <li class="menu-title">User Management</li>

                <li>
                    <a href="{{ route('bta.users.index') }}" class="d-flex justify-content-between align-items-center">
                        <span>
                            <i class="dripicons-user"></i>
                            Users
                        </span>

                        <span class="badge bg-primary">NEW</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('bta.otp.index') }}" class="d-flex justify-content-between align-items-center">
                        <span>
                            <i class="dripicons-lock"></i>
                            OTP
                        </span>

                        <span class="badge bg-primary">NEW</span>
                    </a>
                </li>
                @endif

            </ul>
        </div>
        <div class="clearfix"></div>
    </div>
</div>
{{-- Left Sidebar End --}}