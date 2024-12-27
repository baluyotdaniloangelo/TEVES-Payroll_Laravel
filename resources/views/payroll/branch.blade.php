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
								<h3 class="page-title">Branch</h3>
								<ul class="breadcrumb">
									<li class="breadcrumb-item"><a href="#">Dashboard</a></li>
									<li class="breadcrumb-item active">Branch</li>
								</ul>
							</div>
							<div class="col-auto float-end ms-auto">
								<a href="#" class="btn add-btn" data-bs-toggle="modal" data-bs-target="#branch_details_modal" onclick="ResetBranchForm()"><i class="fa-solid fa-plus"></i> Add Branch</a>
								
							</div>
						</div>
					</div>
					
					<!--<style>
					.dt-scroll-body{
						min-height:400px !important;
					}-->
					</style>
					<div class="row">
						<div class="col-md-12">
							<div class="table-responsive">
								<table class="table dataTable display nowrap cell-border" id="getbranchList" width="100%" cellspacing="0">
											<thead>
												<tr>
													<th class="all">#</th>
													<th class="all">Branch Code</th>
													<th class="all">Branch Name</th>
													<th>Branch Initial</th>
													<th class="all">TIN</th>
													<th title='Default Value' class="none">Address:</th>
													<th class="none">Contact Number:</th>
													<th class="none">Owner:</th>
													<th class="none">Owner Position/Title:</th>
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
				<!-- Branch Modal Modal -->
				<div id="branch_details_modal" class="modal custom-modal fade" data-bs-backdrop="static" role="dialog">
					<div class="modal-dialog modal-dialog-centered modal-lg" role="document">
						<div class="modal-content">
							<div class="modal-header">
								<h5 class="modal-title" id="modal_title_action_branch">Add Branch</h5>
								<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
									<span aria-hidden="true">&times;</span>
								</button>
							</div>
							<div class="modal-body">
								 <form class="g-2 needs-validation" id="BranchForm">
								
									<div class="row">
									
										<div class="col-sm-6">
											<div class="input-block mb-2">
												<label class="col-form-label">Branch Code <span class="text-danger">*</span></label>
												<input class="form-control" type="text" id="branch_code" name="branch_code" required>
												<span class="valid-feedback" id="branch_codeError" title="Required"></span>
											</div>
										</div>
										<div class="col-sm-6">
											<div class="input-block mb-2">
												<label class="col-form-label">Branch Initial <span class="text-danger">*</span></label>
												<input class="form-control" type="text" id="branch_initial" name="branch_initial" required>
												<span class="valid-feedback" id="branch_initialError" title="Required"></span>
											</div>
										</div>
										
										<div class="col-sm-12">
											<div class="input-block mb-2">
												<label class="col-form-label">Branch Name <span class="text-danger">*</span></label>
												<input class="form-control" type="text" id="branch_name" name="branch_name" required>
												<span class="valid-feedback" id="branch_nameError" title="Required"></span>
											</div>
										</div>
										
										<div class="col-sm-6">
											<div class="input-block mb-2">
												<label class="col-form-label">TIN </label>
												<input class="form-control" type="text" id="branch_tin" name="branch_tin">
												<span class="valid-feedback" id="branch_tinError"></span>
											</div>
										</div>
										<div class="col-sm-6">
											<div class="input-block mb-2">
												<label class="col-form-label">Contact Number <span class="text-danger"></span></label>
												<input class="form-control" type="number" name="branch_contact_number" id="branch_contact_number" name="branch_contact_number">
											</div>
										</div>
										<div class="col-sm-12">  
											<div class="input-block mb-2">
												<label class="col-form-label">Address <span class="text-danger">*</span></label>
												<input class="form-control" type="text" id="branch_address" name="branch_address">
												<span class="valid-feedback" id="branch_addressError"></span>
											</div>
										</div>
										
										<div class="col-sm-6">
											<div class="input-block mb-2">
												<label class="col-form-label">Owner </label>
												<input class="form-control" type="text" id="branch_owner" name="branch_owner">
											</div>
										</div>
										
										<div class="col-sm-6">  
											<div class="input-block mb-3">
												<label class="col-form-label">Owner's Position/Title </label>
												<input type="text" class="form-control" id="branch_owner_title" name="branch_owner_title">
												<span class="valid-feedback" id="branch_owner_titleError"></span>
											</div>
										</div>
										
									</div>
									
									<div class="submit-section">
										<button class="btn btn-primary submit-btn" id="submit_branch_details" value="0">Submit</button>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
				<!-- Branch Modal  -->		
				
				
				<!-- Branch - Department Modal  -->
				<div id="branch_department_details_modal" class="modal custom-modal fade" data-bs-backdrop="static" role="dialog">
					<div class="modal-dialog modal-dialog-centered modal-xl" role="document">
						<div class="modal-content">
							<div class="modal-header">
								<h5 class="modal-title" id="modal_title_branch_department_management"></h5>
								<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
									<span aria-hidden="true">&times;</span>
								</button>
							</div>
							<div class="modal-body">
								 <form class="g-2 needs-validation" id="BranchDepartmentForm">
								
									<div class="row">
									
										<div class="col-md-4">
											<div class="card ribbone-card">
												<div class="arrow-ribbone-left bg-secondary">Branch</div>
												<div class="card-body">
													<h6 class="card-subtitle mb-2 text-dark fw-bold text-end">&nbsp;</h6>
													<p class="card-text"><b>Name:</b> <span id="branch_name_department_details"></span></p>
													<p class="card-text"><b>Code:</b> <span id="branch_code_department_details"></span></p>
													<p class="card-text"><b>Initial:</b> <span id="branch_initial_department_details"></span></p>
												</div>
											</div>
											
										</div>
										
										
										<div class="col-sm-8">
										
											<div class="input-block mb-3">
												<label class="col-form-label">Department</label>
												<input type="text" class="form-control" name="department_name" id="department_name" required>
												<span class="valid-feedback" id="department_nameError" title="Required"></span>
											</div>
											<div class="text-end mb-3">
											<button type="submit" class="btn btn-primary" id="submit_deparment_details" value="0">Add</button>
											</div>
											
											<!--table for department-->
											<div class="row mb-3">
											<div class="table-responsive">
												<table class="table dataTable display nowrap cell-border" id="departmentlisttable" width="100%" cellspacing="0">
															<thead>
																<tr>
																	<th class="all">#</th>
																	<th class="all">Department</th>
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
									
									<!--<div class="submit-section">
										<button class="btn btn-primary submit-btn" id="submit_deparment_details" value="0">Submit</button>
									</div>-->
								</form>
							</div>
						</div>
					</div>
				</div>
				<!-- Branch - Department Modal -->				
				
				
				
				
				
				<!-- Delete Branch Modal -->
				<div class="modal custom-modal fade" id="BranchDeleteModal" role="dialog">
					<div class="modal-dialog modal-dialog-centered">
						<div class="modal-content">
							<div class="modal-body">
								<div class="form-header">
									<h3>Delete Branch</h3>
									<p>Are you sure want to delete?</p>
								</div>
								<div class="modal-btn delete-action">
									<div class="row">
									<div class="card text-center">
								<div class="card-header border-bottom-0 pb-0">
									<span class="ms-auto shadow-lg fs-17"></span>
								</div>
								<div class="card-body pt-1">
									<!--<span class="avatar avatar-xl avatar-rounded me-2 mb-2">
										<img src="assets/img/avatar/avatar-7.jpg" alt="img">
									</span>-->
									<div class="fw-semibold fs-16"><span id="delete_branch_complete_name"></span></div>
								</div>
							</div>
									</div>
									
									<div class="row">
										<button type="submit" class="col-6 btn btn-primary continue-btn" id="deleteBranchConfirmed">Delete</button>
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
				<div id="success-alert-modal" class="modal fade" tabindex="-1" role="dialog">
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
				
				
				<!-- Delete Department Modal -->
				<div class="modal custom-modal fade" id="DepartmentDeleteModal" role="dialog">
					<div class="modal-dialog modal-dialog-centered">
						<div class="modal-content">
							<div class="modal-body">
								<div class="form-header">
									<h3>Delete Department</h3>
									<p>Are you sure want to delete this Department from Branch <span id="branch_department_delete"></span>?</p>
								</div>
								<div class="modal-btn delete-action">
									<div class="row">
									<div class="card text-center">
								<div class="card-header border-bottom-0 pb-0">
									<span class="ms-auto shadow-lg fs-17"></span>
								</div>
								<div class="card-body">
									<!--<span class="avatar avatar-xl avatar-rounded me-2 mb-2">
										<img src="assets/img/avatar/avatar-7.jpg" alt="img">
									</span>-->
									<div class="fw-semibold fs-16"><span id="delete_department_complete_name"></span></div>
								</div>
							</div>
									</div>
									
									<div class="row">
										<button type="submit" class="col-6 btn btn-primary continue-btn" id="deleteDepartmentConfirmed">Delete</button>
										<input type="hidden" id="deleteDepartmentBranch_idx_Confirmed">
										<div class="col-6">
											<a href="javascript:void(0);" data-bs-dismiss="modal" class="btn btn-primary cancel-btn">Cancel</a>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
	
				
				</div>
				
				
				
			<!-- /Page Wrapper -->
			


@endsection