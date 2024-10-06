
  <!-- ======= Header ======= -->
  <header id="header" class="header fixed-top d-flex align-items-center navbar_header_bg">

    <div class="d-flex align-items-center justify-content-between">
	<i class="bi bi-list toggle-sidebar-btn toggle_sidebar"></i>
      <div class="logo d-flex align-items-center">
		<img src="{{asset('client_logo/logo-2.png')}}" class="rounded" alt="..." style="width:100px;">
        <span class="d-lg-block navbar_header_title">Billing System</span>
      </div>
      
    </div><!-- End Logo -->

    <nav class="header-nav ms-auto">
      <ul class="d-flex align-items-center">


        <li class="nav-item dropdown pe-3">

          <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
            <!--<img src="{{asset('NiceAdmin-pro/assets/img/profile-img.jpg')}}" alt="Profile" class="rounded-circle">-->
			<span class="bi bi-person-circle rounded-circle" style="color: #c0ff00 !important; font-size: 30px;"></span>			
            <span class="d-none d-md-block dropdown-toggle ps-2 navbar_white_text">{{$data->user_real_name}}</span>
          </a><!-- End Profile Iamge Icon -->

          <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
            <li class="dropdown-header">
              <h6>{{$data->user_name}}</h6>
              <span>{{$data->user_type}}</span>
            </li>
           
            <li>
              <hr class="dropdown-divider">
            </li>

            <li>
              <a class="dropdown-item d-flex align-items-center" id="accountUser" style="cursor: pointer;">
                <i class="bi bi-gear"></i>
                <span>Account Settings</span>
              </a>
            </li>
           
            <li>
              <hr class="dropdown-divider">
            </li>

            <li>
			
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

	   <li class="nav-item">
        <a class="nav-link collapsed navbar_bg" data-bs-target="#billing-nav" data-bs-toggle="collapse" href="#" title="Manage Product, Client and System User Account">
          <i class="bi bi-file-spreadsheet navbar_icon"></i><span title="Billing">Billing</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="billing-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
          <li>
		
            <a class="nav-link navbar_bg sidebar_li_a" href="{{ route('create_so_billing') }}" title="Create Billing Transaction">
          <i class="bi bi-file-spreadsheet navbar_icon"></i>
          <span title="Create SO/Billing">Create</span>
        </a>

		<a class="nav-link navbar_bg sidebar_li_a" href="{{ route('so') }}" title="SO List">
          <i class="bi bi-file-spreadsheet navbar_icon"></i>
          <span title="New Billing Transaction History">SO List</span>
        </a>
		
		<a class="nav-link navbar_bg sidebar_li_a" href="{{ route('billing') }}" title="Billing Transaction">
          <i class="bi bi-file-spreadsheet navbar_icon"></i>
          <span title="iew Billing Transaction History">Billing Transaction</span>
        </a>

          </li>
        </ul>
      </li>
	  
	  <li class="nav-item ">
        <a class="nav-link navbar_bg" href="{{ route('receivables') }}" title="Receivable List">
          <i class="bi bi-file-spreadsheet navbar_icon"></i>
          <span title="Create Billing Transaction">Receivable</span>
        </a>
      </li>
	  
	  <?php if($data->user_type=="Admin"){ ?>
	   <li class="nav-item ">
        <a class="nav-link navbar_bg" href="{{ route('salesorder') }}" title="Create Sales Order">
          <i class="bi bi-file-spreadsheet navbar_icon"></i>
          <span title="Create Sales Order">Sales Order</span>
        </a>
      </li>
		
	  
		
	  <li class="nav-item ">
        <a class="nav-link navbar_bg" href="{{ route('purchaseorder_v2') }}" title="Create Sales Order">
          <i class="bi bi-file-spreadsheet navbar_icon"></i>
          <span title="Create Purchases Order Order">Purchase Order</span>
        </a>
      </li>
		<?php } ?>
		
		<li class="nav-item ">
        <a class="nav-link navbar_bg" href="{{ route('cashierReport') }}" title="Create Sales Order">
          <i class="bi bi-file-spreadsheet navbar_icon"></i>
          <span title="Create Sales Order">Cashier's Report</span>
        </a>
      </li>
	
	  <li class="nav-item ">
        <!--<a class="nav-link navbar_bg" href="#" title="Report">-->
		<a class="nav-link collapsed navbar_bg" data-bs-target="#components-nav-report" data-bs-toggle="collapse" href="#" title="Manage Product, Client and System User Account">
          <i class="bi bi-graph-up navbar_icon"></i>
          <span title="Generate Billing History">Report</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
		<ul id="components-nav-report" class="nav-content collapse " data-bs-parent="#sidebar-nav-report">
          <li>
			<a href="{{ route('report') }}" class="sidebar_li_a" title="Billing History">
              <i class="bi bi-cart navbar_icon" title="Manage Product list"></i><span> Billing History</span>
            </a>
			<!---->
			<a href="{{ route('soa_summary_history') }}" class="sidebar_li_a" title="Statement of Account Summary">
              <i class="bi bi-cart navbar_icon" title="Msnage Product list"></i><span> SOA Summary</span>
            </a>
			
		  </li>
		</ul>
      </li>
	  
      <li class="nav-item">
        <a class="nav-link collapsed navbar_bg" data-bs-target="#components-nav" data-bs-toggle="collapse" href="#" title="Manage Product, Client and System User Account">
          <i class="bi bi-gear navbar_icon"></i><span title="Manage Product, Client and System User Account">Maintenance</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="components-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
          <li>
		
          
			
			<?php if($data->user_type=="Admin"){ ?>
			<a href="{{ route('product') }}" class="sidebar_li_a" title="Manage Product list">
              <i class="bi bi-cart navbar_icon" title="Msnage Product list"></i><span>Product</span>
            </a>
			
			<a href="{{ route('branch') }}" class="sidebar_li_a" title="Manage Branch list">
              <i class="bi bi-building navbar_icon" title="Manage Client list"></i><span>Branch</span>
            </a>
			
			<a href="{{ route('client') }}" class="sidebar_li_a" title="Manage Client list">
              <i class="bi bi-file-person navbar_icon" title="Manage Client list"></i><span>Client</span>
            </a>
			
			<a href="{{ route('supplier') }}" class="sidebar_li_a" title="Manage Supplier list">
              <i class="bi bi-file-person navbar_icon" title="Manage Supplier list"></i><span>Supplier</span>
            </a>
			
			<a href="{{ route('user') }}" class="sidebar_li_a" title="Manage System User">
              <i class="bi bi-people navbar_icon" title="Manage System User"></i><span>User's Account</span>
            </a>
			
			<?php } ?>

          </li>
        </ul>
      </li><!-- End Components Nav -->

	  <!-- End Charts Nav -->

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
							<input type="email" class="form-control " name="user_email_address" id="user_email_address" value="" placeholder="Email Address">
							<span class="valid-feedback" id="user_email_addressError" title="Required"></span>
						  </div>
						</div>

						<div class="row mb-2">
						  <label for="account_user_password" class="col-sm-3 col-form-label">Password:</label>
						  <div class="col-sm-9">
							<input type="password" placeholder="Optional" class="form-control " name="account_user_password" id="account_user_password" value="">
							<span class="valid-feedback" id="account_user_passwordError"></span>
						  </div>
						</div>
						
						</div>
						
                    <div class="modal-footer modal-footer_form">
						
						  <button type="submit" class="btn btn-success bi bi-save-fill navbar_icon form_button_icon" id="account-user"> Submit</button>
						  <button type="reset" class="btn btn-primary bi bi-backspace-fill navbar_icon  form_button_icon" id="clear-user-account"> Clear Form</button>
						  
					</div>
					</form><!-- End Multi Columns Form -->
                  </div>
                </div>
    </div>