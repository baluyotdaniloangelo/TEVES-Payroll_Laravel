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
								<h3 class="page-title">Employee</h3>
								<ul class="breadcrumb">
									<li class="breadcrumb-item"><a href="admin-dashboard.html">Dashboard</a></li>
									<li class="breadcrumb-item active">Employee</li>
								</ul>
							</div>
							<div class="col-auto float-end ms-auto">
								<a href="#" class="btn add-btn" data-bs-toggle="modal" data-bs-target="#employee_details_modal" onclick="ResetEmployeeForm()"><i class="fa-solid fa-plus"></i> Add Employee</a>
								
							</div>
						</div>
					</div>
					
					<!-- /Search Filter >-->
					<div class="row">
						<div class="col-md-12">
							<div class="table-responsive">
								<table class="table dataTable display nowrap cell-border"" id="EmployeeListDatatable" width="100%" cellspacing="0">
											<thead>
												<tr>
													<th class="all">#</th>
										 			<th class="all" title="Employee Number">Employee Number</th>
													<th class="all" title="Employee Name">Last Name</th>
													<th class="all" title="Employee Name">First Name</th>
													<th class="all" title="Employee Name">Middle Name</th>
													<th class="all" title="Employee Name">Extension Name</th>
													<th>Branch</th>
													<th>Department</th>			
													<th>Position</th>				
													<th class="all">Status</th>
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
				
				<!-- Add Employee Modal -->
				<div id="employee_details_modal" class="modal custom-modal fade" data-bs-backdrop="static" role="dialog">
					<div class="modal-dialog modal-dialog-centered modal-lg" role="document">
						<div class="modal-content">
							<div class="modal-header">
								<h5 class="modal-title" id="modal_title_action_employee">Add Employee</h5>
								<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
									<span aria-hidden="true">&times;</span>
								</button>
							</div>
							<div class="modal-body">
								 <form class="g-2 needs-validation" id="Employeeform">
									
									<div class="row">
										<div class="col-sm-12">
											<div class="input-block mb-3">
												<label class="col-form-label">Employee <span class="text-danger">*</span></label>
												<input class="form-control " type="text" id="employee_last_name" name="employee_last_name" required>
												<span class="valid-feedback" id="employee_last_nameError" title="Required"></span>
											</div>
										</div>
										
										<div class="col-sm-12">
											<div class="input-block mb-3">
												<label class="col-form-label">Date <span class="text-danger">*</span></label>
												<input class="form-control" type="date" id="employee_last_name" name="employee_last_name" required>
												<span class="valid-feedback" id="employee_last_nameError" title="Required"></span>
											</div>
										</div>
										
										<div class="col-sm-12">  
											<div class="input-block mb-3">
												<label class="col-form-label">Time In <span class="text-danger">*</span></label>
												<input class="form-control" type="datetime-local" id="time_in" name="time_in" required>
												<span class="valid-feedback" id="time_inError" title="Required"></span>
											</div>
										</div>
										<div class="col-sm-12">  
											<div class="input-block mb-3">
												<label class="col-form-label">Breaktime In <span class="text-danger">*</span></label>
												<input class="form-control" type="datetime-local" id="break_time_in" name="break_time_in" required>
												<span class="valid-feedback" id="break_time_inError" title="Required"></span>
											</div>
										</div>
										<div class="col-sm-12">  
											<div class="input-block mb-3">
												<label class="col-form-label">Breaktime Out <span class="text-danger">*</span></label>
												<input class="form-control" type="datetime-local" id="break_time_out" name="break_time_out" required>
												<span class="valid-feedback" id="break_time_outError" title="Required"></span>
											</div>
										</div>
										<div class="col-sm-12">  
											<div class="input-block mb-3">
												<label class="col-form-label">Time Out <span class="text-danger">*</span></label>
												<input class="form-control" type="datetime-local" id="time_out" name="time_out" required>
												<span class="valid-feedback" id="time_outError" title="Required"></span>
											</div>
										</div>
									</div>
									
									
									<div class="submit-section">
										<button class="btn btn-primary submit-btn" id="submit_employee_details" value="0">Submit</button>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
				<!-- /Add Employee Modal -->
				
				<!-- Delete Employee Modal -->
				<!-- <div class="modal custom-modal fade" id="EmployeeDeleteModal" role="dialog" tabindex="-1" role="dialog">-->
				<div class="modal custom-modal fade" id="EmployeeDeleteModal" role="dialog">
					<div class="modal-dialog modal-dialog-centered">
						<div class="modal-content">
							<div class="modal-body">
								<div class="form-header">
									<h3>Delete Employee</h3>
									<p>Are you sure want to delete?</p>
								</div>
								<div class="modal-btn delete-action">
									<div class="row">
									<div class="card text-center">
								<div class="card-header border-bottom-0 pb-0">
									<span class="ms-auto shadow-lg fs-17"></span>
								</div>
								<div class="card-body pt-1">
									<span class="avatar avatar-xl avatar-rounded me-2 mb-2">
										<img src="assets/img/avatar/avatar-7.jpg" alt="img">
									</span>
									<div class="fw-semibold fs-16"><span id="delete_employee_complete_name"></span></div>
									<p class="mb-4 text-muted fs-11" id="delete_employee_position"></p>
								</div>
							</div>
									</div>
									
									<div class="row">
										<button type="submit" class="col-6 btn btn-primary continue-btn" id="deleteEmployeeConfirmed">Delete</button>
										<div class="col-6">
											<a href="javascript:void(0);" data-bs-dismiss="modal" class="btn btn-primary cancel-btn">Cancel</a>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				
				<!-- /Delete Employee Modal -->
				
				<!-- Successful Action -->
				<div id="success-alert-modal" class="modal fade" tabindex="-1" role="dialog">
					<div class="modal-dialog modal-lg">
						<div class="modal-content modal-filled bg-success">
							<div class="modal-body p-4">
								<div class="text-center">
									<i class="dripicons-checkmark h1 text-white"></i>
										<h4 class="mt-2 text-white" id="modal_success_details"></h4>
											<!--<p class="mt-3 text-white">Cras mattis consectetur purus sit amet fermentum. Cras justo odio, dapibus ac facilisis in, egestas eget quam.</p>-->
											<button type="button" class="btn btn-light my-2" data-bs-dismiss="modal">Continue</button>
									</div>
								</div>
								</div><!-- /.modal-content -->
					</div><!-- /.modal-dialog -->
				</div>
				
				<!-- Succeful Action End -->
				
				</div>
				
				
				
			<!-- /Page Wrapper -->
			


@endsection