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
								<a href="#" class="btn add-btn" data-bs-toggle="modal" data-bs-target="#add_employee"><i class="fa-solid fa-plus"></i> Add Employee</a>
								<!--<div class="view-icons">
									<a href="employees.html" class="grid-view btn btn-link"><i class="fa fa-th"></i></a>
									<a href="employees-list.html" class="list-view btn btn-link active"><i class="fa-solid fa-bars"></i></a>
								</div>-->
							</div>
						</div>
					</div>
					<!-- /Page Header -->
					
					<!-- Search Filter
					<div class="row filter-row">
						<div class="col-sm-6 col-md-3">  
							<div class="input-block mb-3 form-focus">
								<input type="text" class="form-control floating">
								<label class="focus-label">Employee ID</label>
							</div>
						</div>
						<div class="col-sm-6 col-md-3">  
							<div class="input-block mb-3 form-focus">
								<input type="text" class="form-control floating">
								<label class="focus-label">Employee Name</label>
							</div>
						</div>
						<div class="col-sm-6 col-md-3"> 
							<div class="input-block mb-3 form-focus select-focus">
								<select class="select floating"> 
									<option>Select Designation</option>
									<option>Web Developer</option>
									<option>Web Designer</option>
									<option>Android Developer</option>
									<option>Ios Developer</option>
								</select>
								<label class="focus-label">Designation</label>
							</div>
						</div>
						<div class="col-sm-6 col-md-3">  
							<a href="#" class="btn btn-success w-100"> Search </a>  
						</div>     
                    </div> -->
					<!-- /Search Filter -->
					<style>
					.dt-scroll-body{
						min-height:400px !important;
					}
					.invalid-tooltip {
						
						top: none !important;
					}
					</style>
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
				<div id="add_employee" class="modal custom-modal fade" role="dialog">
					<div class="modal-dialog modal-dialog-centered modal-xl" role="document">
						<div class="modal-content">
							<div class="modal-header">
								<h5 class="modal-title">Add Employee</h5>
								<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
									<span aria-hidden="true">&times;</span>
								</button>
							</div>
							<div class="modal-body">
								 <form class="g-2 needs-validation" id="Employeeform">
								 
								<!--
								'employee_number',
								'employee_last_name',
								'employee_first_name',
								'employee_middle_name',
								'employee_extension_name',
								'employee_birthday',
								'employee_position',
								'employee_picture',
								'employee_phone',
								'employee_email',
								'branch_idx',
								'department_idx',
								'time_in',
								'break_time',
								'time_out',
								'restday_monday',
								'restday_tuesday',
								'restday_wednesday',
								'restday_thursday',
								'restday_friday',
								'restday_saturday',
								'restday_sunday',
								-->
								
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
												<div class="cal-icon"><input class="form-control datetimepicker" type="text" id="employee_birthday" name="employee_birthday" required></div>
												<span class="valid-feedback" id="employee_birthdayError" title="Required"></span>
											</div>
										</div>
										<div class="col-sm-4">
											<div class="input-block mb-3">
												<label class="col-form-label">Email <span class="text-danger"></span></label>
												<input class="form-control" type="email" id="" name="" id="employee_email" name="employee_email">
											</div>
										</div>
										<div class="col-sm-4">
											<div class="input-block mb-3">
												<label class="col-form-label">Phone </label>
												<input class="form-control" type="text" id="employee_phone" name="employee_phone">
											</div>
										</div>
										<div class="col-sm-6">  
											<div class="input-block mb-3">
												<label class="col-form-label">Employee ID <span class="text-danger">*</span></label>
												<input type="text" class="form-control" id="employee_number" name="employee_number" required>
												<span class="valid-feedback" id="employee_numberError" title="Required"></span>
											</div>
										</div>
										
										<div class="col-sm-6">  
											<div class="input-block mb-3">
												<label class="col-form-label">Position <span class="text-danger"></span></label>
												<input type="text" class="form-control" id="employee_position" name="employee_position">
											</div>
										</div>
										<div class="col-sm-6">
											<div class="input-block mb-3">
												<label class="col-form-label">Company/Branch</label>
												<select class="select" id="branch_idx" name="branch_idx">
													<option value="">Global Technologies</option>
													<option value="1">Delta Infotech</option>
												</select>
											</div>
										</div>
										<div class="col-md-6">
											<div class="input-block mb-3">
												<label class="col-form-label">Department <span class="text-danger"></span></label>
												<select class="select" id="department_idx" name="department_idx">
													<option>Select Department</option>
													<option>Web Development</option>
													<option>IT Management</option>
													<option>Marketing</option>
												</select>
											</div>
										</div>
										
										<div class="col-sm-4">  
											<div class="input-block mb-3">
												<label class="col-form-label">Time In <span class="text-danger">*</span></label>
												<input class="form-control timepicker" type="text" id="time_in" name="time_in" required value="08:00">
												<span class="valid-feedback" id="time_inError" title="Required"></span>
											</div>
										</div>
										<div class="col-sm-4">  
											<div class="input-block mb-3">
												<label class="col-form-label">Breaktime <span class="text-danger">*</span></label>
												<input class="form-control timepicker" type="text" id="break_time" name="break_time" required value="12:00">
												<span class="valid-feedback" id="break_timeError" title="Required"></span>
											</div>
										</div>
										<div class="col-sm-4">  
											<div class="input-block mb-3">
												<label class="col-form-label">Time Out <span class="text-danger">*</span></label>
												<input class="form-control timepicker" type="text" id="time_out" name="time_out" required value="17:00">
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
															<input type="checkbox" checked name="restday_sunday" class="restday_sunday">													
															<span class="checkmark"></span>
													</label>																			
													</td>
													<td class="text-center">
														<label class="custom_check">
															<input type="checkbox" name="restday_monday" class="restday_monday">
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
															<input type="checkbox" checked name="restday_saturday" class="restday_saturday">													
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
									<p class="mb-4 text-muted fs-11" id="delete_employee_position">Web Developer</p>
								</div>
							</div>
									</div>
									<div class="row">
										<div class="col-6">
											<a href="javascript:void(0);" class="btn btn-primary continue-btn" id="deleteEmployeeConfirmed" data-id="">Delete</a>
										</div>
										<div class="col-6">
											<a href="javascript:void(0);" data-bs-dismiss="modal" class="btn btn-primary cancel-btn">Cancel</a>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				
	<!-- Meter Delete Modal-->
    <div class="modal fade" id="" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header header_modal_bg">
                    <h5 class="modal-title" id="exampleModalLabel"></h5>
 					<div class="btn-sm btn-warning btn-circle bi bi-exclamation-circle btn_icon_modal"></div>
                </div>
                <div class="modal-body warning_modal_bg" id="modal-body">
				Are you sure you want to delete?
				</div>
				
				<div style="margin:10px;">	
				<table width="100%">
				<tr>
				<th width="30%">Meter Description:</th>
				<td width="70%"><span id="meter_name_delete"></span></td>
				</tr>
				<tr>
				<th width="30%">Name/Tagging:</th>
				<td width="70%"><span id="customer_name_delete"></span></td>
				</tr>
				<tr>
				<th width="30%">Status:</th>
				<td width="70%"><span id="meter_status_delete"></span></td>
				</tr>
				<tr>
				<th width="30%">Location:</th>
				<td width="70%"><span id="meter_location_delete"></span></td>
				</tr>
				</table>
				</div>
				
                <div class="modal-footer footer_modal_bg">
                   
					<button type="button" class="btn btn-danger" data-bs-dismiss="modal" id="deleteMeterConfirmed" value=""><i class="bi bi-trash3 navbar_icon"></i> Delete</button>
					 <button type="button" class="btn btn-primary" data-bs-dismiss="modal"><i class="bi bi-x-circle navbar_icon"></i> Cancel</button>
                  
                </div>
            </div>
        </div>
    </div>					
				
				
				<!-- /Delete Employee Modal -->
				
            </div>
			<!-- /Page Wrapper -->
			


@endsection