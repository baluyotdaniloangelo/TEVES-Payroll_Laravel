@include('layouts.header');
    <body>
	
		<!-- Main Wrapper -->
        <div class="main-wrapper">
			@include('layouts.navigations.header-navigation')     
			@include('layouts.navigations.sidebar-navigation') 
			@include('layouts.navigations.two-col-bar')		
				@yield('content')
			@include('layouts.navigations.layout-settings')	
       </div>
		<!-- /Main Wrapper --> 
		
		
@include('layouts.footer')
<?php

if (Request::is('site')){

?>
@include('layouts.site_script')
<?php

}


?>
	</body>
</html>