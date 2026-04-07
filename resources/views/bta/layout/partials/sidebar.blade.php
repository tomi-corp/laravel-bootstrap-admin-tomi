{{-- Left Sidebar Start --}}
<div class="left-side-menu">

    <div class="slimscroll-menu">
        <div id="sidebar-menu">
            <ul class="metismenu" id="side-menu">

                <li class="menu-title">Navigation</li>

                <li>
                    <a href="{{ route('bta.dashboard.index') }}">
                        <i class="dripicons-align-center"></i>
                        <span> Dashboard </span>
                    </a>
                </li>

                {{-- MASTER --}}
                @if(optional(auth('bta')->user())->type == 'master')
                <li class="menu-title">Master</li>
                <li>
                    <a href="{{ route('bta.nasabah.index') }}">
                        <i class="dripicons-align-center"></i>
                        <span> Administrator </span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('bta.analyst.index') }}">
                        <i class="dripicons-align-center"></i>
                        <span> Analyst </span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('bta.credam.index') }}">
                        <i class="dripicons-align-center"></i>
                        <span> Credit Administrator </span>
                    </a>
                </li>
                @endif

                {{-- ADMIN --}}
                @if(optional(auth('bta')->user())->type == 'admin')
                <li class="menu-title">Administrator</li>
                <li>
                    <a href="{{ route('bta.nasabah.index') }}">
                        <i class="dripicons-align-center"></i>
                        <span> Administrator </span>
                    </a>
                </li>
                @endif

                {{-- ANALYST --}}
                @if(optional(auth('bta')->user())->type == 'analyst')
                <li class="menu-title">Analyst</li>
                <li>
                    <a href="{{ route('bta.analyst.index') }}">
                        <i class="dripicons-align-center"></i>
                        <span> Analyst </span>
                    </a>
                </li>
                @endif

                {{-- CREDAM --}}
                @if(optional(auth('bta')->user())->type == 'credam')
                <li class="menu-title">Credit Administrator</li>
                <li>
                    <a href="{{ route('bta.credam.index') }}">
                        <i class="dripicons-align-center"></i>
                        <span> Credit Administrator </span>
                    </a>
                </li>
                @endif

            </ul>
        </div>
        <div class="clearfix"></div>
    </div>
</div>