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
								<h3 class="page-title">Employees</h3>
								<ul class="breadcrumb">
									<li class="breadcrumb-item">Administration</li>
									<li class="breadcrumb-item active">Employees</li>
								</ul>
							</div>
							<div class="col-auto float-end ms-auto">
								<button type="button" class="btn add-btn" data-bs-toggle="modal" data-bs-target="#employee_details_modal" onclick="ResetEmployeeForm()"><i class="fa-solid fa-plus"></i> Add Employee</button>
								
							</div>
						</div>
					</div>
					
					<div class="card employee-filter-card">
						<div class="card-body">
							<div class="row g-2 align-items-end">
								<div class="col-xl-4 col-lg-6">
									<label for="employee_search" class="form-label">Search employees</label>
									<input id="employee_search" type="search" class="form-control" placeholder="Name, employee ID, position, branch..." autocomplete="off">
								</div>
								<div class="col-xl-2 col-md-3 col-sm-6">
									<label for="employee_branch_filter" class="form-label">Branch</label>
									<select id="employee_branch_filter" class="form-select"><option value="">All branches</option>@foreach ($branch_data as $branch)<option value="{{ $branch->branch_id }}">{{ $branch->branch_code }} — {{ $branch->branch_name }}</option>@endforeach</select>
								</div>
								<div class="col-xl-2 col-md-3 col-sm-6">
									<label for="employee_department_filter" class="form-label">Department</label>
									<select id="employee_department_filter" class="form-select"><option value="">All departments</option>@foreach ($department_data as $department)<option value="{{ $department->department_id }}">{{ $department->department_name }}</option>@endforeach</select>
								</div>
								<div class="col-xl-1 col-md-3 col-sm-6">
									<label for="employee_status_filter" class="form-label">Status</label>
									<select id="employee_status_filter" class="form-select"><option value="">All</option><option value="Active">Active</option><option value="Inactive">Inactive</option></select>
								</div>
								<div class="col-xl-2 col-md-3 col-sm-6">
									<label for="employee_type_filter" class="form-label">Employment type</label>
									<select id="employee_type_filter" class="form-select"><option value="">All types</option><option value="Regular">Regular</option><option value="Probationary">Probationary</option><option value="Contractual">Contractual</option><option value="Others">Others / Drivers</option></select>
								</div>
								<div class="col-xl-1 col-md-3 col-sm-6"><button type="button" class="btn btn-light w-100" id="clear_employee_filters">Clear</button></div>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-12">
							<div class="table-responsive">
								<table class="table dataTable display nowrap cell-border" id="EmployeeListDatatable" width="100%" cellspacing="0">
											<thead>
												<tr>
													<th class="all">#</th>
										 			<th class="all" title="Employee Number">Employee No.</th>
													<th class="all" title="Employee Name">Employee Name</th>
													<th>Branch Code</th>
													<th class="none">Department</th>			
													<th class="none">Position</th>			
													<th class="none">Hourly Rate</th>				
													<th class="all">Employment Status</th>
													<th class="all">Employment Type</th>
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
									<div id="employee_form_error_summary" class="alert alert-danger d-none" role="alert" aria-live="polite"></div>
									<div class="employee-form-layout">
									<div class="employee-form-main">
									<div class="employee-form-tabs" role="tablist" aria-label="Employee information sections">
										<button type="button" class="employee-form-tab is-active" id="employee-tab-identity" role="tab" aria-selected="true" aria-controls="employee-panel-identity" data-employee-tab="identity"><i class="fa-solid fa-user" aria-hidden="true"></i><span>Identity &amp; assignment</span></button>
										<button type="button" class="employee-form-tab" id="employee-tab-employment" role="tab" aria-selected="false" aria-controls="employee-panel-employment" data-employee-tab="employment"><i class="fa-solid fa-briefcase" aria-hidden="true"></i><span>Employment &amp; pay</span></button>
										<button type="button" class="employee-form-tab" id="employee-tab-tax" role="tab" aria-selected="false" aria-controls="employee-panel-tax" data-employee-tab="tax"><i class="fa-solid fa-file-invoice-dollar" aria-hidden="true"></i><span>Tax information</span></button>
										<button type="button" class="employee-form-tab" id="employee-tab-schedule" role="tab" aria-selected="false" aria-controls="employee-panel-schedule" data-employee-tab="schedule"><i class="fa-solid fa-calendar-week" aria-hidden="true"></i><span>Schedule &amp; rest days</span></button>
									</div>
									<section class="employee-form-pane" id="employee-panel-identity" role="tabpanel" aria-labelledby="employee-tab-identity" data-employee-tab-panel="identity">
									<div class="row">
										
										<div class="col-sm-6">
											<div class="input-block mb-3">
												<label class="col-form-label">Branch <span class="text-danger">*</span></label>
												<input class="form-control" type="text" list="branch_list" id="branch_idx" name="branch_idx" onchange="LoadDepartment()" required>
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
												<label class="col-form-label">Department <span class="text-danger">*</span></label>
												<!--<select class="select" id="department_idx" name="department_idx">-->
												<input class="form-control" type="text" list="department_list" id="department_idx" name="department_idx" required>
												
													<datalist id="department_list">
														<option value=""></option>
													</datalist>
											</div>
										</div>
										
										<div class="col-sm-12">
											<div class="input-block mb-3">
												<label class="col-form-label">Last Name <span class="text-danger">*</span></label>
												<input class="form-control" type="text" id="employee_last_name" name="employee_last_name" required>
												<span class="valid-feedback" id="employee_last_nameError" title="Required"></span>
											</div>
										</div>
										<div class="col-sm-12">
											<div class="input-block mb-3">
												<label class="col-form-label">First Name <span class="text-danger">*</span></label>
												<input class="form-control" type="text" id="employee_first_name" name="employee_first_name" required>
												<span class="valid-feedback" id="employee_first_nameError" title="Required"></span>
											</div>
										</div>
										<div class="col-sm-12">
											<div class="input-block mb-3">
												<label class="col-form-label">Middle Name</label>
												<input class="form-control" type="text" id="employee_middle_name" name="employee_middle_name">
												<span class="valid-feedback" id="employee_middle_nameError" title="Required"></span>
											</div>
										</div>
										<div class="col-sm-12">
											<div class="input-block mb-3">
												<label class="col-form-label">Name Extension</label>
												<input class="form-control" type="text" id="employee_extension_name" name="employee_extension_name">
												<span class="invalid-feedback" id="employee_extension_nameError"></span>
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
												<label class="col-form-label">Birth Date <span class="text-danger">*</span></label>
												<input class="form-control" type="date" id="employee_birthday" name="employee_birthday" required>
												<span class="valid-feedback" id="employee_birthdayError" title="Required"></span>
											</div>
										</div>
										<div class="col-sm-6">
											<div class="input-block mb-3">
												<label class="col-form-label">Email <span class="text-danger"></span></label>
												<input class="form-control" type="email" name="employee_email" id="employee_email">
											</div>
										</div>
										<div class="col-sm-6">
											<div class="input-block mb-3">
												<label class="col-form-label">Phone </label>
												<input class="form-control" type="text" id="employee_phone" name="employee_phone">
											</div>
										</div>
									</div>
									</section>
									<section class="employee-form-pane" id="employee-panel-employment" role="tabpanel" aria-labelledby="employee-tab-employment" data-employee-tab-panel="employment" hidden>
									<div class="row">
										<div class="col-sm-4">
											<div class="input-block mb-3">
												<label class="col-form-label">Employment Status</label>
												<select class="form-select form-control" name="employee_status" id="employee_status">
													<option value="Active">Active</option>
													<option value="Inactive">Inactive</option>
												</select>
											</div>
										</div>
										<div class="col-sm-4">
											<div class="input-block mb-3">
												<label class="col-form-label">Employment Type</label>
												<select class="form-select form-control" name="employment_type" id="employment_type">
													<option value="Regular" selected>Regular</option>
													<option value="Probationary">Probationary</option>
													<option value="Contractual">Contractual</option>
													<option value="Others">Others/Outsource/Drivers</option>
												</select>
											</div>
										</div>
										
										<div class="col-sm-4">  
											<div class="input-block mb-3">
												<label class="col-form-label">Night differential pay?</label>
												<select class="form-select form-control" name="employee_night_diff_pay" id="employee_night_diff_pay">
													<option value="No" selected>No</option>
													<option value="Yes">Yes</option>
												</select>
											</div>
										</div>
										<div class="col-sm-6">
											<div class="input-block mb-3">
												<label class="col-form-label">Position</label>
												<input type="text" class="form-control" id="employee_position" name="employee_position">
											</div>
										</div>
										<div class="col-sm-6">
											<div class="input-block mb-3">
												<label class="col-form-label">Hourly Rate / Driver Percentage <span class="text-danger">*</span></label>
												<input type="number" class="form-control" id="employee_rate" name="employee_rate" min="0" step="0.01" required>
												<small class="text-muted">For drivers, enter the percentage (e.g. 13).</small>
												<span class="valid-feedback" id="employee_rateError" title="Required"></span>
											</div>
										</div>
										
									</div>
									</section>
									<section class="employee-form-pane" id="employee-panel-tax" role="tabpanel" aria-labelledby="employee-tab-tax" data-employee-tab-panel="tax" hidden>
									<div class="row">
										<div class="col-sm-6">
											<div class="input-block mb-3">
												<label class="col-form-label" for="employee_tin">TIN</label>
												<input class="form-control" type="text" id="employee_tin" name="employee_tin" maxlength="30" inputmode="numeric" autocomplete="off">
												<span class="invalid-feedback" id="employee_tinError"></span>
											</div>
										</div>
										<div class="col-sm-6">
											<div class="input-block mb-3">
												<label class="col-form-label" for="employee_tax_status">Tax status</label>
												<select class="form-select form-control" id="employee_tax_status" name="employee_tax_status">
													<option value="">Not specified</option>
													<option value="taxable">Taxable</option>
													<option value="non_taxable">Non-taxable</option>
												</select>
												<span class="invalid-feedback" id="employee_tax_statusError"></span>
											</div>
										</div>
									</div>
									</section>
									<section class="employee-form-pane employee-schedule-panel" id="employee-panel-schedule" role="tabpanel" aria-labelledby="employee-tab-schedule" data-employee-tab-panel="schedule" hidden>
									<div class="employee-schedule-grid">
									<div class="employee-schedule-times">
									<div class="row">
										<div class="col-12">
											<div class="input-block mb-3">
												<label class="col-form-label">Time In <span class="text-danger">*</span></label>
												<input class="form-control " type="time" id="time_in" name="time_in" required value="08:00:00">
												<span class="valid-feedback" id="time_inError" title="Required"></span>
											</div>
										</div>
										<div class="col-12">
											<div class="input-block mb-3">
												<label class="col-form-label">Breaktime Start <span class="text-danger">*</span></label>
												<input class="form-control " type="time" id="break_time_in" name="break_time_in" required value="12:00:00">
												<span class="valid-feedback" id="break_time_inError" title="Required"></span>
											</div>
										</div>
										<div class="col-12">
											<div class="input-block mb-3">
												<label class="col-form-label">Breaktime End <span class="text-danger">*</span></label>
												<input class="form-control " type="time" id="break_time_out" name="break_time_out" required value="13:00:00">
												<span class="valid-feedback" id="break_time_outError" title="Required"></span>
											</div>
										</div>
										<div class="col-12">
											<div class="input-block mb-3">
												<label class="col-form-label">Time Out <span class="text-danger">*</span></label>
												<input class="form-control " type="time" id="time_out" name="time_out" required value="17:00:00">
												<span class="valid-feedback" id="time_outError" title="Required"></span>
											</div>
										</div>
									</div>
									</div>
									<div class="employee-restday-panel" role="group" aria-label="Weekly rest days">
										<label class="employee-restday-option" for="restday_sunday"><span>Sunday</span><input type="checkbox" id="restday_sunday" name="restday_sunday" class="restday_sunday"></label>
										<label class="employee-restday-option" for="restday_monday"><span>Monday</span><input type="checkbox" id="restday_monday" name="restday_monday" class="restday_monday"></label>
										<label class="employee-restday-option" for="restday_tuesday"><span>Tuesday</span><input type="checkbox" id="restday_tuesday" name="restday_tuesday" class="restday_tuesday"></label>
										<label class="employee-restday-option" for="restday_wednesday"><span>Wednesday</span><input type="checkbox" id="restday_wednesday" name="restday_wednesday" class="restday_wednesday"></label>
										<label class="employee-restday-option" for="restday_thursday"><span>Thursday</span><input type="checkbox" id="restday_thursday" name="restday_thursday" class="restday_thursday"></label>
										<label class="employee-restday-option" for="restday_friday"><span>Friday</span><input type="checkbox" id="restday_friday" name="restday_friday" class="restday_friday"></label>
										<label class="employee-restday-option" for="restday_saturday"><span>Saturday</span><input type="checkbox" id="restday_saturday" name="restday_saturday" class="restday_saturday"></label>
									</div>
									</div>
									</section>
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
