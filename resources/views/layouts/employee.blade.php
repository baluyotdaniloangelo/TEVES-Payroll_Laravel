@include('layouts.header')
<body class="fsm-theme fsm-employee">
    <div class="main-wrapper">
        @include('layouts.navigations.sidebar-navigation', ['fsmEmployee' => true])
        <div id="main-shell">
            @include('layouts.navigations.header-navigation', ['fsmEmployee' => true])
            <div class="page-wrapper"><div class="content container-fluid">@yield('content')</div></div>
        </div>
    </div>
    @include('layouts.footer')
    <script src="{{ asset('fsm/js/payroll-shell.js') }}" defer></script>
</body>
</html>
