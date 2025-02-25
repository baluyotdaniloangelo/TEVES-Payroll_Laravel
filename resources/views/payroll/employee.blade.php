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
								<table class="table dataTable display nowrap cell-border" id="EmployeeListDatatable" width="100%" cellspacing="0">
											<thead>
												<tr>
													<th class="all">#</th>
										 			<th class="all" title="Employee Number">Employee No.</th>
													<th class="all" title="Employee Name">Employee Name</th>
													<th>Branch</th>
													<th>Department</th>			
													<th>Position</th>			
													<th>Rate</th>				
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
					<div class="modal-dialog modal-dialog-centered modal-xl" role="document">
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
										<div class="col-sm-3">
											<div class="input-block mb-3">
												<label class="col-form-label">Last Name <span class="text-danger">*</span></label>
												<input class="form-control" type="text" id="employee_last_name" name="employee_last_name" required>
												<span class="valid-feedback" id="employee_last_nameError" title="Required"></span>
											</div>
										</div>
										<div class="col-sm-3">
											<div class="input-block mb-3">
												<label class="col-form-label">First Name</label>
												<input class="form-control" type="text" id="employee_first_name" name="employee_first_name" required>
												<span class="valid-feedback" id="employee_first_nameError" title="Required"></span>
											</div>
										</div>
										<div class="col-sm-3">
											<div class="input-block mb-3">
												<label class="col-form-label">Middle Name</label>
												<input class="form-control" type="text" id="employee_middle_name" name="employee_middle_name">
												<span class="valid-feedback" id="employee_middle_nameError" title="Required"></span>
											</div>
										</div>
										<div class="col-sm-3">
											<div class="input-block mb-3">
												<label class="col-form-label">Name Extention</label>
												<input class="form-control" type="text" id="employee_extension_name" name="employee_extension_name">
												<span class="valid-feedback" id="employee_middle_nameError" title="Required"></span>
											</div>
										</div>
										<div class="col-sm-4">  
											<div class="input-block mb-3">
												<label class="col-form-label">Birth Date <span class="text-danger">*</span></label>
												<input class="form-control" type="date" id="employee_birthday" name="employee_birthday" required>
												<span class="valid-feedback" id="employee_birthdayError" title="Required"></span>
											</div>
										</div>
										<div class="col-sm-4">
											<div class="input-block mb-3">
												<label class="col-form-label">Email <span class="text-danger"></span></label>
												<input class="form-control" type="email" name="employee_email" id="employee_email" name="employee_email">
											</div>
										</div>
										<div class="col-sm-4">
											<div class="input-block mb-3">
												<label class="col-form-label">Phone </label>
												<input class="form-control" type="text" id="employee_phone" name="employee_phone">
											</div>
										</div>
										
										<div class="col-sm-3">  
											<div class="input-block mb-3">
												<label class="col-form-label">Employee ID <span class="text-danger">*</span></label>
												<input type="text" class="form-control" id="employee_number" name="employee_number" required>
												<span class="valid-feedback" id="employee_numberError" title="Required"></span>
											</div>
										</div>
										
										<div class="col-sm-3">  
											<div class="input-block mb-3">
												<label class="col-form-label">Position <span class="text-danger"></span></label>
												<input type="text" class="form-control" id="employee_position" name="employee_position">
											</div>
										</div>
										
										<div class="col-sm-3">  
											<div class="input-block mb-3">
												<label class="col-form-label">Rate <span class="text-danger">*</span></label>
												<input type="text" class="form-control" id="employee_rate" name="employee_rate" required>
												<span class="valid-feedback" id="employee_rateError" title="Required"></span>
											</div>
										</div>
										
										<div class="col-sm-3">
											<div class="input-block mb-3">
												<label class="col-form-label">Status</label>
												<select class="form-select form-control" name="employee_status" id="employee_status">
													<option value="Active">Active</option>
													<option value="Inactive">Inactive</option>
												</select>
											</div>
										</div>
										
										<div class="col-sm-6">
											<div class="input-block mb-3">
												<label class="col-form-label">Branch</label>
												<input class="form-control " type="text" list="branch_list" id="branch_idx" name="branch_idx" onchange="LoadDepartment()">
												<datalist id="branch_list">
												<!--List Here -->
													@foreach ($branch_data as $branch_data_cols)
													<option label="{{$branch_data_cols->branch_code}} - {{$branch_data_cols->branch_name}}" data-id="{{$branch_data_cols->branch_id}}" value="{{$branch_data_cols->branch_code}} - {{$branch_data_cols->branch_name}}"> </option>
													@endforeach
												</datalist>
												<!--</select>-->
											</div>
										</div>
										<div class="col-md-6">
											<div class="input-block mb-3">
												<label class="col-form-label">Department</label>
												<!--<select class="select" id="department_idx" name="department_idx">-->
												<input class="form-control " type="text" list="department_list" id="department_idx" name="department_idx">
												
													<datalist id="department_list">
														<option value=""></option>
													</datalist>
												</select>
											</div>
										</div>
										
										<div class="col-sm-3">  
											<div class="input-block mb-3">
												<label class="col-form-label">Time In <span class="text-danger">*</span></label>
												<input class="form-control " type="time" id="time_in" name="time_in" required value="08:00:00">
												<span class="valid-feedback" id="time_inError" title="Required"></span>
											</div>
										</div>
										<div class="col-sm-3">  
											<div class="input-block mb-3">
												<label class="col-form-label">Breaktime Start <span class="text-danger">*</span></label>
												<input class="form-control " type="time" id="break_time_in" name="break_time_in" required value="12:00:00">
												<span class="valid-feedback" id="break_time_inError" title="Required"></span>
											</div>
										</div>
										<div class="col-sm-3">  
											<div class="input-block mb-3">
												<label class="col-form-label">Breaktime End <span class="text-danger">*</span></label>
												<input class="form-control " type="time" id="break_time_out" name="break_time_out" required value="13:00:00">
												<span class="valid-feedback" id="break_time_outError" title="Required"></span>
											</div>
										</div>
										<div class="col-sm-3">  
											<div class="input-block mb-3">
												<label class="col-form-label">Time Out <span class="text-danger">*</span></label>
												<input class="form-control " type="time" id="time_out" name="time_out" required value="17:00:00">
												<span class="valid-feedback" id="time_outError" title="Required"></span>
											</div>
										</div>
									</div>
									
									<div class="table-responsive m-t-15">
										<table class="table table-striped custom-table">
											<thead>
												<tr>
													<th>&nbsp;</th>
													<th class="text-center">Sunday</th>
													<th class="text-center">Monday</th>
													<th class="text-center">Tuesday</th>
													<th class="text-center">Wednesday</th>
													<th class="text-center">Thursday</th>
													<th class="text-center">Friday</th>
													<th class="text-center">Saturday</th>
												</tr>
											</thead>
											<tbody>
												<tr>
													<td>Restday</td>
													<td class="text-center">
														<label class="custom_check">
															<input type="checkbox" name="restday_sunday" class="restday_sunday">													
															<span class="checkmark"></span>
													</label>																			
													</td>
													<td class="text-center">
														<label class="custom_check">
															<input type="checkbox" id="restday_monday" name="restday_monday" class="restday_monday">
															<span class="checkmark"></span>
														</label>
													</td>
													<td class="text-center">
														<label class="custom_check">
															<input type="checkbox" name="restday_tuesday" class="restday_tuesday">
															<span class="checkmark"></span>
														</label>
													</td>
													<td class="text-center">
														<label class="custom_check">
															<input type="checkbox" name="restday_wednesday" class="restday_wednesday">
															<span class="checkmark"></span>
														</label>
													</td>
													<td class="text-center">
														<label class="custom_check">
															<input type="checkbox" name="restday_thursday" class="restday_thursday">
															<span class="checkmark"></span>
														</label>
													</td>
													<td class="text-center">
														<label class="custom_check">
															<input type="checkbox" name="restday_friday" class="restday_friday">
															<span class="checkmark"></span>
														</label>
													</td>
													<td class="text-center">
														<label class="custom_check">
															<input type="checkbox" name="restday_saturday" class="restday_saturday">													
															<span class="checkmark"></span>
													</label>																			
													</td>
												</tr>
											</tbody>
										</table>
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