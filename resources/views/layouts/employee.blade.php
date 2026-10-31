@include('layouts.header')
<body>
    <div class="main-wrapper">
        @include('layouts.navigations.header-navigation')
        <div class="sidebar" id="sidebar">
            <div class="sidebar-inner slimscroll"><div id="sidebar-menu" class="sidebar-menu"><ul class="sidebar-vertical">
                <li class="menu-title"><span>Employee</span></li>
                <li><a class="{{ ($active_link ?? '') === 'my_dtr' ? 'active' : '' }}" href="{{ route('my-dtr.index') }}"><i class="la la-calendar-check-o"></i><span> My DTR</span></a></li>
                <li><a class="{{ ($active_link ?? '') === 'my_payroll' ? 'active' : '' }}" href="{{ route('my-payroll.index') }}"><i class="la la-money"></i><span> My Payroll</span></a></li>
                <li><a class="{{ ($active_link ?? '') === 'my_updates' ? 'active' : '' }}" href="{{ route('my-updates.index') }}"><i class="la la-bell"></i><span> My Updates</span></a></li>
                <li><a class="{{ ($active_link ?? '') === 'my_profile' ? 'active' : '' }}" href="{{ route('my-profile.index') }}"><i class="la la-user"></i><span> My Profile</span></a></li>
            </ul></div></div>
        </div>
        <div class="page-wrapper"><div class="content container-fluid">@yield('content')</div></div>
    </div>
    @include('layouts.footer')
