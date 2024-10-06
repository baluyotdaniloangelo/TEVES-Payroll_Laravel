@extends('layouts.layout')  
@section('content')  

<main id="main" class="main">	
    <section class="section">
	
          <div class="card">
		  
			  <div class="card card-12-btm">
			  
				<div class="card-header" style="text-align:center;">
                             
				  
				  <div class="row">
					
						  <div class="col-sm-12">
						  
						  <h5 class="card-title bi bi-person-lines-fill">&nbsp;{{ $title }}</h5>    
						  <!--OPTIONS HERE-->
							<div class="d-flex justify-content-end" id="user_option"></div>
						  </div>
						  
						  
						</div>
				  
				  </div>
				</div>			  
		 
            <div class="card-body">
				
				<div class="p-d3">
									<div class="table-responsive">
										<table class="table dataTable display nowrap cell-border" id="userList" width="100%" cellspacing="0">
											<thead>
												<tr>
													<th class="all">#</th>
													<th class="all" >Name</th>
													<th class="">Job Title</th>
													<th  class="all">User Name</th>
													<th>User Type</th>
													<th>Email Address</th>
													<th class="none">Date Created:</th>
													<th class="none">Date Updated:</th>	
													<th class="all">Action</th>
												</tr>
											</thead>				
											
											<tbody>
												
											</tbody>
											
											
										</table>
									</div>		
				</div>									
                   
            </div>
          </div>

	<!-- Switch Delete Modal-->
    <div class="modal fade" id="UserDeleteModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header header_modal_bg">
                    <h5 class="modal-title" id="exampleModalLabel"></h5>
 					<div class="btn-sm btn-warning btn-circle bi bi-exclamation-circle btn_icon_modal"></div>
                </div>
                <div class="modal-body warning_modal_bg" id="modal-body">
				Are you sure you want to Delete this User?</div>
				<div style="margin:10px;">
				User Real Name: <span id="user_real_name_info_confirm"></span><br>
				Username: <span id="user_name_info_confirm"></span><br>
				User Type: <span id="user_type_info_confirm"></span><br>
				</div>
                <div class="modal-footer footer_modal_bg">         
					<button type="button" class="btn btn-danger" data-bs-dismiss="modal" id="deleteUserConfirmed" value=""><i class="bi bi-trash3 form_button_icon"></i>Delete</button>
					<button type="button" class="btn btn-primary" data-bs-dismiss="modal"><i class="bi bi-x-circle form_button_icon"></i>Cancel</button>
                </div>
            </div>
        </div>
    </div>	

	<!--Modal to Create User-->
	<div class="modal fade" id="CreateUserModal" tabindex="-1">
           <div class="modal-dialog modal-lg">
                  <div class="modal-content">
                    <div class="modal-header modal-header_form">
                      <h5 class="modal-title">Create User</h5>
					  <div class="btn-group" role="group" aria-label="Basic outlined example">		
						<button type="button" class="btn btn-danger bi bi-x-circle form_button_icon" data-bs-dismiss="modal"></button>
					  </div>
                    </div>
                    <div class="modal-body">

					  <form class="g-2 needs-validation" id="CreateUserform">
					  
						<div class="row mb-2">
						  <label for="user_real_name" class="col-sm-3 col-form-label" title="Switch Name">Name:</label>
						  <div class="col-sm-9">
							<input type="text" class="form-control" name="user_real_name" id="user_real_name" value="" required>
							<span class="valid-feedback" id="user_real_nameError" title="Required"></span>
						  </div>
						</div>
						
						<div class="row mb-2">
						  <label for="user_job_title" class="col-sm-3 col-form-label">Job Title:</label>
						  <div class="col-sm-9">
							<input type="text" class="form-control " name="user_job_title" id="user_job_title" value="">
							<span class="valid-feedback" id="user_job_titleError"></span>
						  </div>
						</div>
						
						<div class="row mb-2">
						  <label for="user_name" class="col-sm-3 col-form-label">User Name:</label>
						  <div class="col-sm-9">
							<input type="text" class="form-control " name="user_name" id="user_name" value="" required>
							<span class="valid-feedback" id="user_nameError"></span>
						  </div>
						</div>
						
						<div class="row mb-2">
						  <label for="user_name" class="col-sm-3 col-form-label">Email Address:</label>
						  <div class="col-sm-9">
							<input type="email" class="form-control " name="user_email_address_management" id="user_email_address_management" value="" required>
							<span class="valid-feedback" id="user_email_address_managementError"></span>
						  </div>
						</div>

						<div class="row mb-2">
						  <label for="user_password" class="col-sm-3 col-form-label">Password:</label>
						  <div class="col-sm-9">
							<input type="password" class="form-control " name="user_password" id="user_password" value="" required>
							<span class="valid-feedback" id="user_passwordError"></span>
						  </div>
						</div>
					
						<div class="row mb-2">
						  <label for="user_type" class="col-sm-3 col-form-label">User Type:</label>
						  <div class="col-sm-9">
								<select class="form-select form-control" required="" name="user_type" id="user_type" onchange="ChangeAccessType_Add()">
								<option selected="" disabled="" value="">Choose...</option>
								<option value="Admin">Admin</option>
								<option value="Supervisor">Supervisor</option>
								<option value="Accounting_Staff">Accounting Staff</option>
								<option value="Encoder">Encoder</option>
								</select>
							<span class="valid-feedback" id="user_typeError"></span>
						  </div>
						</div>	
						
						<div class="row mb-2">
						  <label for="user_access" class="col-sm-3 col-form-label">User Access:</label>
						  <div class="col-sm-9">
								<select class="form-select form-control" required="" name="user_access" id="user_access">
								<option value="BYBRANCH" selected>Assign by Branch</option>
								<option value="ALL">All</option>
								</select>
							<span class="valid-feedback" id="user_accessError"></span>
						  </div>
						</div>
						
						</div>
						
                    <div class="modal-footer modal-footer_form">
						
						  <button type="submit" class="btn btn-success btn-sm bi bi-save-fill form_button_icon" id="save-user"> Submit</button>
						  <button type="reset" class="btn btn-primary btn-sm bi bi-backspace-fill form_button_icon" id="clear-user"> Clear Form</button>
						  
					</div>
					</form><!-- End Multi Columns Form -->
                  </div>
                </div>
                  </div>
	
	<!--Modal to Create User-->
	<div class="modal fade" id="UpdateUserModal" tabindex="-1">
           <div class="modal-dialog modal-lg">
                  <div class="modal-content">
                    <div class="modal-header modal-header_form">
                      <h5 class="modal-title">Update User</h5>
					  <div class="btn-group" role="group" aria-label="Basic outlined example">		
						<button type="button" class="btn btn-danger bi bi-x-circle form_button_icon" data-bs-dismiss="modal"></button>
					  </div>
                    </div>
                    <div class="modal-body">

					  <form class="g-2 needs-validation" id="UpdateUserform">
					  
						<div class="row mb-2">
						  <label for="update_user_real_name" class="col-sm-3 col-form-label" title="Switch Name">Name:</label>
						  <div class="col-sm-9">
							<input type="text" class="form-control" name="update_user_real_name" id="update_user_real_name" value="" required>
							<span class="valid-feedback" id="update_user_real_nameError" title="Required"></span>
						  </div>
						</div>
						
						<div class="row mb-2">
						  <label for="update_user_job_title" class="col-sm-3 col-form-label">Job Title:</label>
						  <div class="col-sm-9">
							<input type="text" class="form-control " name="update_user_job_title" id="update_user_job_title" value="">
							<span class="valid-feedback" id="update_user_job_titleError"></span>
						  </div>
						</div>
						
						<div class="row mb-2">
						  <label for="update_user_name" class="col-sm-3 col-form-label">User Name:</label>
						  <div class="col-sm-9">
							<input type="text" class="form-control " name="update_user_name" id="update_user_name" value="" required>
							<span class="valid-feedback" id="update_user_nameError"></span>
						  </div>
						</div>
						
						<div class="row mb-2">
						  <label for="user_name" class="col-sm-3 col-form-label">Email Address:</label>
						  <div class="col-sm-9">
							<input type="email" class="form-control " name="update_user_email_address_management" id="update_user_email_address_management" value="" required>
							<span class="valid-feedback" id="update_user_email_address_managementError"></span>
						  </div>
						</div>

						<div class="row mb-2">
						  <label for="update_user_password" class="col-sm-3 col-form-label">Password:</label>
						  <div class="col-sm-9">
							<input type="password" placeholder="Optional" class="form-control " name="update_user_password" id="update_user_password" value="">
							<span class="valid-feedback" id="update_user_passwordError"></span>
						  </div>
						</div>
					
						<div class="row mb-2">
						  <label for="update_user_type" class="col-sm-3 col-form-label">User Type:</label>
						  <div class="col-sm-9">
								<select class="form-select form-control" required="" name="update_user_type" id="update_user_type" onchange="ChangeAccessType_Update()">
								<option selected="" disabled="" value="">Choose...</option>
								<option value="Admin">Admin</option>
								<option value="Accounting_Staff">Accounting Staff</option>
								<option value="Encoder">Encoder</option>
								</select>
							<span class="valid-feedback" id="update_user_typeError"></span>
						  </div>
						</div>	
						
						<div class="row mb-2">
						  <label for="update_user_access" class="col-sm-3 col-form-label">User Access:</label>
						  <div class="col-sm-9">
								<select class="form-select form-control" required="" name="update_user_access" id="update_user_access">
								<option value="BYBRANCH" selected>Assign by Branch</option>
								<option value="ALL">All</option>
								</select>
							<span class="valid-feedback" id="update_user_accessError"></span>
						  </div>
						</div>
						
						</div>
						
                    <div class="modal-footer modal-footer_form">
						
						  <button type="submit" class="btn btn-success btn-sm bi bi-save-fill form_button_icon" id="update-user"> Submit</button>
						  
					</div>
					</form><!-- End Multi Columns Form -->
                  </div>
                </div>
                  </div>
	
	
			<!--Modal to Create User-->
	<div class="modal fade" id="SiteUserAccessModal" tabindex="-1">
           <div class="modal-dialog modal-xl">
                  <div class="modal-content">
				  
                    <div class="modal-header modal-header_form">
                      <h5 class="modal-title">User Site Access</h5>
					  
					  <div class="btn-group" role="group" aria-label="Basic outlined example">		
						<button type="button" class="btn btn-danger bi bi-x-circle form_button_icon" data-bs-dismiss="modal"></button>
					  </div>
					  
                    </div>
					
                    <div class="modal-body">
						<div class="row mb-3">
						
							<div class="col-sm-4">
							
									<ol class="list-group list-group-numbered">
									<li class="list-group-item d-flex justify-content-between align-items-start">
									  <div class="ms-2 me-auto">
										<div class="fw-bold">Name</div>
										<span id="user_real_name_info_site_access"></span>
									  </div>
									 
									</li>
									<li class="list-group-item d-flex justify-content-between align-items-start">
									  <div class="ms-2 me-auto">
										<div class="fw-bold">Username</div>
										<span id="user_name_info_site_access"></span>
									  </div>
									 
									</li>
									<li class="list-group-item d-flex justify-content-between align-items-start">
									  <div class="ms-2 me-auto">
										<div class="fw-bold">User Type</div>
										<span id="user_type_info_site_access"></span>
									  </div>
									  
									</li>
								  </ol>
							  
							</div>
							
							<div class="col-sm-8">
							
									<div class="table-responsive">
										<table class="table table-bordered dataTable" id="UserSiteAccessList" width="100%" cellspacing="0">
											<thead>
												<tr>
													<th class="all"></th>
													<th class="all">#</th>
													<th class="all">Branch Code</th>
													<th class="all">Branch Name</th>
												</tr>
											</thead>				
											
											<tbody>
												
											</tbody>
											
											<tfoot>
												<tr>
													<th class="all"></th>
													<th class="all">#</th>
													<th class="all">Branch Code</th>
													<th class="all">Branch Name</th>
												</tr>
											</tfoot>
											
										</table>
										
									</div>
							</div>		
	
                  </div>
				  
					
					
                </div>
				
					<div class="modal-footer modal-footer_form">
						
						  <button type="submit" class="btn btn-success btn-sm bi bi-save-fill form_button_icon" id="update-user-site-access"> Submit</button>
						  <!--<button type="reset" class="btn btn-primary btn-sm bi bi-backspace-fill form_button_icon" id="update-clear-user"> Clear Form</button>-->
						  
				    </div>
            </div>
			
    </section>
</main>


@endsection

