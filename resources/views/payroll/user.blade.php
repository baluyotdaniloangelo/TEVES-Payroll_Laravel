@extends('layouts.layout')  
@section('content')  
			<!-- Page Wrapper -->
            <div class="page-wrapper">
			
				<!-- Page Content -->
                <div class="content container-fluid">
				
					<!-- Page Header -->
					<div class="page-header">
						<div class="row align-items-center">
							<div class="col">
								<h3 class="page-title">User</h3>
								<!--<ul class="breadcrumb">
									<li class="breadcrumb-item"><a href="#">Dashboard</a></li>
									<li class="breadcrumb-item active">User</li>
								</ul>-->
							</div>
							<div class="col-auto float-end ms-auto">
								<a href="#" class="btn add-btn" data-bs-toggle="modal" data-bs-target="#user_details_modal" onclick="ResetUserForm()"><i class="fa-solid fa-plus"></i> Add User</a>
								
							</div>
						</div>
					</div>
					
					</style>
					<div class="row">
						<div class="col-md-12">
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
				<!-- /Page Content -->
				<!-- Branch Modal Modal  -->
				<div id="user_details_modal" class="modal custom-modal fade" data-bs-backdrop="static" role="dialog">
					<div class="modal-dialog modal-dialog-centered modal-lg" role="document">
						<div class="modal-content">
							<div class="modal-header">
								<h5 class="modal-title" id="modal_title_action_user">Add User</h5>
								<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" onclick="ResetFormUser()">
									<span aria-hidden="true">&times;</span>
								</button>
							</div>
							<div class="modal-body">
								 <form class="g-2 needs-validation" id="UserForm">
								
									<div class="row">
									
										<div class="col-sm-12">
											<div class="input-block mb-2">
												<label class="col-form-label">Name <span class="text-danger">*</span></label>
												<input class="form-control" type="text" id="user_real_name" name="user_real_name" required>
												<span class="valid-feedback" id="user_real_nameError" title="Required"></span>
											</div>
										</div>

                                        <div class="col-sm-12">
											<div class="input-block mb-2">
												<label class="col-form-label">Job Title <span class="text-danger">*</span></label>
												<input class="form-control" type="text" id="user_job_title" name="user_job_title" required>
												<span class="valid-feedback" id="user_job_titleError" title="Required"></span>
											</div>
										</div>

                                       

                                        <div class="col-sm-12">
											<div class="input-block mb-2">
												<label class="col-form-label">Email Address <span class="text-danger">*</span></label>
												<input class="form-control" type="text" id="user_email_address" name="user_email_address">
												<span class="valid-feedback" id="user_email_addressError" title="Required"></span>
											</div>
										</div>

                                         <div class="col-sm-6">
											<div class="input-block mb-2">
												<label class="col-form-label">User Name <span class="text-danger">*</span></label>
												<input class="form-control" type="text" id="user_name" name="user_name" required>
												<span class="valid-feedback" id="user_nameError" title="Required"></span>
											</div>
										</div>

                                        <div class="col-sm-6">
											<div class="input-block mb-2">
												<label class="col-form-label">Password <span class="text-danger">*</span></label>
												<input class="form-control" type="password" id="user_password" name="user_password">
												<span class="valid-feedback" id="user_passwordError" title="Required"></span>
											</div>
										</div>
										
										<div class="col-sm-12">
											<div class="input-block mb-2">
												<label class="col-form-label">User Type <span class="text-danger">*</span></label>
													<select class="form-select form-control" required="" name="user_type" id="user_type" onchange="ChangeAccessType_Add()">
								                        <option selected="" disabled="" value="">Choose...</option>
								                        <option value="Admin">Admin</option>
								                        <option value="Supervisor">Supervisor</option>
								                        <option value="Accounting_Staff">Accounting Staff</option>
								                        <option value="Encoder">Encoder</option>
								                    </select>
												<span class="valid-feedback" id="user_typeError" title="Required"></span>
											</div>
										</div>

                                        <div class="col-sm-12">
											<div class="input-block mb-2">
												<label class="col-form-label">User Access <span class="text-danger">*</span></label>
													<select class="form-select form-control" required="" name="user_access" id="user_access">
								                        <option value="BYBRANCH" selected>Assign by Branch</option>
								                        <option value="ALL">All</option>
								                    </select>
												<span class="valid-feedback" id="user_accessError" title="Required"></span>
											</div>
										</div>


									</div>	
										
									<div class="submit-section">
										<button class="btn btn-primary submit-btn" id="save-user" value="0">Submit</button>
									</div>
									
								</form>
							</div>
						</div>
					</div>
				</div>
               
				<!-- Branch Modal  -->		
				
				
				
				<!-- Delete Branch Modal -->
				<div class="modal custom-modal fade" id="UserDeleteModal"  data-bs-backdrop="static" role="dialog">
					<div class="modal-dialog modal-dialog-centered">
						<div class="modal-content">
							<div class="modal-body">
								<div class="form-header">
									<h3>Delete User</h3>
									<p>Are you sure want to delete?</p>
								</div>
								<div class="modal-btn delete-action">
									<div class="row">
									<div class="card">
								<div class="card-body pt-1">
                                            <div class="card ribbone-card">
												<div class="arrow-ribbone-left bg-secondary">User</div>
												<div class="card-body">
													<h6 class="card-subtitle mb-2 text-dark fw-bold text-end">&nbsp;</h6>
													<p class="card-text"><b>Name:</b> <span id="user_real_name_info_delete">G-T PETROLEUM PRODUCTS RETAILING</span></p>
													<p class="card-text"><b>Username:</b> <span id="user_name_info_delete">GT</span></p>
													<p class="card-text"><b>Usertype:</b> <span id="user_type_info_delete">GT</span></p>
												</div>
											</div>

								</div>
							</div>
									</div>
									
									<div class="row">
										<button type="submit" class="col-6 btn btn-primary continue-btn" id="deleteUserConfirmed">Delete</button>
										<div class="col-6">
											<a href="javascript:void(0);" data-bs-dismiss="modal" class="btn btn-primary cancel-btn">Cancel</a>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				
				<!-- /Delete Branch Modal -->
				
				<!-- Successful Action -->
				<div id="success-alert-modal" class="modal fade" tabindex="-1" role="dialog" >
					<div class="modal-dialog modal-lg">
						<div class="modal-content modal-filled bg-success">
							<div class="modal-body p-4">
								<div class="text-center">
									<i class="dripicons-checkmark h1 text-white"></i>
										<h4 class="mt-2 text-white" id="modal_success_details">!</h4>
											<!--<p class="mt-3 text-white">Cras mattis consectetur purus sit amet fermentum. Cras justo odio, dapibus ac facilisis in, egestas eget quam.</p>-->
											<button type="button" class="btn btn-light my-2" data-bs-dismiss="modal">Continue</button>
									</div>
								</div>
								</div><!-- /.modal-content -->
					</div><!-- /.modal-dialog -->
				</div>
				
				<!-- Succeful Action End -->
				
				</div>
				<!-- User Access Modal -->
				<div id="BranchUserAccessModal" class="modal custom-modal fade" data-bs-backdrop="static" role="dialog">
					<div class="modal-dialog modal-dialog-centered modal-xl" role="document">
						<div class="modal-content">
							<div class="modal-header">
								<h5 class="modal-title" id="modal_title_branch_department_management"></h5>
								<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
									<span aria-hidden="true">&times;</span>
								</button>
							</div>
							<div class="modal-body">


									<div class="row">
									
										<div class="col-md-4">
											<div class="card ribbone-card">
												<div class="arrow-ribbone-left bg-secondary">User Information</div>
												<div class="card-body">
													<h6 class="card-subtitle mb-2 text-dark fw-bold text-end">&nbsp;</h6>
													<p class="card-text"><b>Name:</b> <span id="user_real_name_info_branch_access"></span></p>
													<p class="card-text"><b>User Name:</b> <span id="user_name_info_branch_access"></span></p>
													<p class="card-text"><b>User Type:</b> <span id="user_type_info_branch_access"></span></p>

                                                    </div>
											</div>
											
										</div>
										
										
										<div class="col-sm-8">
										
											<!--table for department-->
											<div class="row mb-3">
											<div class="table-responsive">
                                            <table class="table table-bordered dataTable" id="UserBranchAccessList" width="100%" cellspacing="0">
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
									
									<!-- -->
                                    <div class="submit-section">
										<button class="btn btn-primary submit-btn" id="update-user-branch-access" value="0">Submit</button>
									</div>

							</div>
						</div>
					</div>
				</div>
				<!-- User Access Modal -->								
				
				
			<!-- /Page Wrapper -->
			


@endsection
