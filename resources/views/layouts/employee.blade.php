@include('layouts.header')
<body>
    <div class="main-wrapper">
        @include('layouts.navigations.header-navigation')
        <div class="sidebar" id="sidebar">
            <div class="sidebar-inner slimscroll"><div id="sidebar-menu" class="sidebar-menu"><ul class="sidebar-vertical">
                <li class="menu-title"><span>Employee</span></li>
                <li class="submenu"><a class="active" href="{{ route('my-dtr.index') }}"><i class="la la-calendar-check-o"></i><span> My DTR</span></a></li>
            </ul></div></div>
        </div>
        <div class="page-wrapper"><div class="content container-fluid">@yield('content')</div></div>
    </div>
    @include('layouts.footer')
