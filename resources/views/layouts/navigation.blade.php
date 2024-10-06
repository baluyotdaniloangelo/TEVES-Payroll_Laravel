
  <!-- ======= Header ======= -->
  <header id="header" class="header fixed-top d-flex align-items-center navbar_header_bg">

    <div class="d-flex align-items-center justify-content-between">
	<i class="bi bi-list toggle-sidebar-btn toggle_sidebar"></i>
      <div class="logo d-flex align-items-center">
		<img src="{{asset('client_logo/logo.png')}}" class="rounded" alt="..." width="100px">
        <span class="d-lg-block navbar_header_title">Centralized Automated Meter Reading</span>
      </div>
      
    </div><!-- End Logo -->
<!-- 
    <div class="search-bar">
      <form class="search-form d-flex align-items-center" method="POST" action="#">
        <input type="text" name="query" placeholder="Search" title="Enter search keyword">
        <button type="submit" title="Search"><i class="bi bi-search"></i></button>
      </form>
    </div>End Search Bar -->

    <nav class="header-nav ms-auto">
      <ul class="d-flex align-items-center">

        <!-- <li class="nav-item d-block d-lg-none">
          <a class="nav-link nav-icon search-bar-toggle " href="#">
            <i class="bi bi-search"></i>
          </a>
        </li>End Search Icon-->

        <li class="nav-item dropdown">
		<!--
          <a class="nav-link nav-icon" href="#" data-bs-toggle="dropdown">
            <i class="bi bi-bell navbar_icon"></i>
            <span class="badge bg-primary badge-number">4</span>
          </a>--><!-- End Notification Icon -->

          <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow notifications">
            <li class="dropdown-header">
              You have 4 new notifications
              <a href="#"><span class="badge rounded-pill bg-primary p-2 ms-2">View all</span></a>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <li class="notification-item">
              <i class="bi bi-exclamation-circle text-warning"></i>
              <div>
                <h4>Lorem Ipsum</h4>
                <p>Quae dolorem earum veritatis oditseno</p>
                <p>30 min. ago</p>
              </div>
            </li>

            <li>
              <hr class="dropdown-divider">
            </li>

            <li class="notification-item">
              <i class="bi bi-x-circle text-danger"></i>
              <div>
                <h4>Atque rerum nesciunt</h4>
                <p>Quae dolorem earum veritatis oditseno</p>
                <p>1 hr. ago</p>
              </div>
            </li>

            <li>
              <hr class="dropdown-divider">
            </li>

            <li class="notification-item">
              <i class="bi bi-check-circle text-success"></i>
              <div>
                <h4>Sit rerum fuga</h4>
                <p>Quae dolorem earum veritatis oditseno</p>
                <p>2 hrs. ago</p>
              </div>
            </li>

            <li>
              <hr class="dropdown-divider">
            </li>

            <li class="notification-item">
              <i class="bi bi-info-circle text-primary"></i>
              <div>
                <h4>Dicta reprehenderit</h4>
                <p>Quae dolorem earum veritatis oditseno</p>
                <p>4 hrs. ago</p>
              </div>
            </li>

            <li>
              <hr class="dropdown-divider">
            </li>
            <li class="dropdown-footer">
              <a href="#">Show all notifications</a>
            </li>

          </ul><!-- End Notification Dropdown Items -->

        </li><!-- End Notification Nav -->


        <li class="nav-item dropdown pe-3">

          <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
            <!--<img src="{{asset('template/assets/img/profile-img.jpg')}}" alt="Profile" class="rounded-circle">-->
			<span class="bi bi-person-circle rounded-circle nav_user_logo"></span>			
            <span class="d-none d-md-block dropdown-toggle ps-2 top_navbar_text">{{$data->user_real_name}}</span>
          </a><!-- End Profile Iamge Icon -->

          <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
            <li class="dropdown-header">
              <h6>{{$data->user_name}}</h6>
              <span>{{$data->user_type}}</span>
            </li>
            <!--<li>
              <hr class="dropdown-divider">
            </li>
			
            <li>
              <a class="dropdown-item d-flex align-items-center" href="users-profile.html">
                <i class="bi bi-person"></i>
                <span>My Profile</span>
              </a>
            </li>
			-->
            <li>
              <hr class="dropdown-divider">
            </li>

            <li>
              <a class="dropdown-item d-flex align-items-center" id="accountUser" style="cursor: pointer;">
                <i class="bi bi-gear"></i>
                <span>Account Settings</span>
              </a>
            </li>
            <!--<li>
              <hr class="dropdown-divider">
            </li>
			
            <li>
              <a class="dropdown-item d-flex align-items-center" href="pages-faq.html">
                <i class="bi bi-question-circle"></i>
                <span>Need Help?</span>
              </a>
            </li>
			-->
            <li>
              <hr class="dropdown-divider">
            </li>

            <li>
			<!--logoutModal
			<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#basicModal"> Basic Modal </button>-->
              <a class="dropdown-item d-flex align-items-center" href="#" data-bs-toggle="modal" data-bs-target="#logoutModal">
                <i class="bi bi-box-arrow-right"></i>
                <span>Sign Out</span>
              </a>
            </li>

          </ul><!-- End Profile Dropdown Items -->
        </li><!-- End Profile Nav -->

      </ul>
    </nav><!-- End Icons Navigation -->

  </header><!-- End Header -->

  <!-- ======= Sidebar ======= -->
  <aside id="sidebar" class="sidebar sidebar_bg">

    <ul class="sidebar-nav" id="sidebar-nav">
<!-- 
      <li class="nav-item">
        <a class="nav-link collapsed navbar_bg" href="index.html">
          <i class="bi bi bi-speedometer navbar_icon"></i>
          <span>Dashboard</span>
        </a>
      </li>End Dashboard Nav -->

      <li class="nav-item">
        <a class="nav-link collapsed navbar_bg" data-bs-target="#components-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-gear navbar_icon"></i><span>Maintenance</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="components-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
          <li>
						
            <a href="{{ route('site') }}" class="sidebar_li_a">
              <i class="bi bi-circle"></i><span>Building</span>
            </a>
			
			<?php if($data->user_type=="Admin"){ ?>
			
			<a href="{{ route('division') }}" class="sidebar_li_a">
              <i class="bi bi-circle"></i><span>Division</span>
            </a>
			
			<a href="{{ route('company') }}" class="sidebar_li_a">
              <i class="bi bi-circle"></i><span>Company</span>
            </a>
			
			<a href="{{ route('user') }}" class="sidebar_li_a">
              <i class="bi bi-circle"></i><span>User</span>
            </a>
			<?php } ?>
          </li>
        </ul>
      </li><!-- End Components Nav -->

      <li class="nav-item">
        <a class="nav-link collapsed navbar_bg" data-bs-target="#charts-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-bar-chart navbar_icon"></i><span>Reports</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="charts-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
          <li>
            <a href="{{ route('RAWReport') }}" class="sidebar_li_a" title="Generate Raw Data for Each Meter">
              <i class="bi bi-circle"></i><span>Raw Data</span>
            </a>
          </li>
		  <li>
            <a href="{{ route('DemandReport') }}" class="sidebar_li_a" title="Generate KWh Consumption for Each Meter">
              <i class="bi bi-circle"></i><span>KW Demand</span>
            </a>
          </li>
		  <li>
            <a href="{{ route('ConsumptionReport') }}" class="sidebar_li_a" title="Generate KWh Consumption for Each Meter">
              <i class="bi bi-circle"></i><span>KWh Consumption</span>
            </a>
          </li>
          <li>
            <a href="{{ route('SAPReport') }}" class="sidebar_li_a">
              <i class="bi bi-circle"></i><span>SAP</span>
            </a>
          </li>
		   <li>
            <a href="{{ route('SiteReport') }}" class="sidebar_li_a" title="Generate KWh Consumption for All Meters per Building">
              <i class="bi bi-circle"></i><span>Building</span>
            </a>
          </li>
        </ul>
      </li><!-- End Charts Nav -->

    </ul>

  </aside><!-- End Sidebar-->
<!--Modal to User Profile-->
	<div class="modal fade" id="UserProfileModal" tabindex="-1">
           <div class="modal-dialog modal-lg">
                  <div class="modal-content">
                    <div class="modal-header modal-header_form">
                      <h5 class="modal-title">Account Settings</h5>
					  <div class="btn-group" role="group" aria-label="Basic outlined example">		
						<button type="button" class="btn btn-danger bi bi-x-circle navbar_icon" data-bs-dismiss="modal"></button>
					  </div>
                    </div>
                    <div class="modal-body">

					  <form class="g-2 needs-validation" id="AccountUserform">
					  
						<div class="row mb-2">
						  <label for="account_user_real_name" class="col-sm-3 col-form-label" title="Switch Name">Name:</label>
						  <div class="col-sm-9">
							<input type="text" class="form-control" name="account_user_real_name" id="account_user_real_name" value="" required>
							<span class="valid-feedback" id="account_user_real_nameError" title="Required"></span>
						  </div>
						</div>
						
						<div class="row mb-2">
						  <label for="account_user_name" class="col-sm-3 col-form-label">User Name:</label>
						  <div class="col-sm-9">
							<input type="text" class="form-control " name="account_user_name" id="account_user_name" value="" required>
							<span class="valid-feedback" id="account_user_nameError"></span>
						  </div>
						</div>

						<div class="row mb-2">
						  <label for="user_email_address" class="col-sm-3 col-form-label" title="You Valid Email Address">Email Address:</label>
						  <div class="col-sm-9">
							<input type="email" class="form-control " name="user_email_address" id="user_email_address" value="" placeholder="Email Address" required>
							<span class="valid-feedback" id="user_email_addressError" title="Required"></span>
						  </div>
						</div>

						<div class="row mb-2">
						  <label for="account_user_password" class="col-sm-3 col-form-label">Reset Password:</label>
						  <div class="col-sm-9">
							<input type="password" placeholder="Optional" class="form-control " name="account_user_password" id="account_user_password" value="" minlength="6" maxlength="20">
							<span class="valid-feedback" id="account_user_passwordError"></span>
						  </div>
						</div>
						
						</div>
						
                    <div class="modal-footer modal-footer_form">
						
						  <button type="submit" class="btn btn-success btn-sm bi bi-save-fill navbar_icon" id="account-user"> Submit</button>
						  <button type="reset" class="btn btn-primary btn-sm bi bi-backspace-fill navbar_icon" id="clear-user-account"> Reset</button>
						  
					</div>
					</form><!-- End Multi Columns Form -->
                  </div>
                </div>
    </div>