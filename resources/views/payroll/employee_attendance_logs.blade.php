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
								<h3 class="page-title">Employee Logs</h3>
								<!--<ul class="breadcrumb">
									<li class="breadcrumb-item"><a href="admin-dashboard.html">Dashboard</a></li>
									<li class="breadcrumb-item active">Employee</li>
								</ul>-->
							</div>
							<div class="col-auto float-end ms-auto">
								<a href="#" class="btn add-btn" data-bs-toggle="modal" data-bs-target="#employee_regular_logs_details_modal"><i class="fa-solid fa-plus"></i> Add Attendance Logs</a>
								<!-- onclick="ResetEmployeeAttendanceLogsForm()"-->
							</div>
						</div>
					</div>
					
					<!-- /Search Filter >-->
					<div class="row">
						<div class="col-md-12">
						
						<div class="card-body">
										<ul class="nav nav-tabs nav-tabs-solid" role="tablist">
											<li class="nav-item" role="presentation"><a class="nav-link active" href="#solid-tab1" data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1">Regular</a></li>
											<li class="nav-item" role="presentation"><a class="nav-link" href="#solid-tab2" data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1">Regular Overtime</a></li>
											<li class="nav-item" role="presentation"><a class="nav-link" href="#solid-tab3" data-bs-toggle="tab" aria-selected="true" role="tab">Restday Overtime</a></li>
										</ul>
										<div class="tab-content">
											<div class="tab-pane active show" id="solid-tab1" role="tabpanel">
													
													<div class="table-responsive">
													<table class="table dataTable display nowrap cell-border" id="EmployeeRegularLogsListDatatable" width="100%" cellspacing="0">
																<thead>
																	<tr>
																		<th class="all">#</th>
																		<th class="all">Date</th>
																		<th class="all" title="Employee Number">Employee Number</th>
																		<th class="all" title="Employee Name">Employee Name</th>
																		<th class="all">Regular Hours</th>
																		<th class="all">Night Differential Hours</th>
																		<th class="none">Branch:</th>
																		<th class="none">Department:</th>
																		<th class="none">Time In:</th>
																		<th class="none">Time Out:</th>
																		<th class="none">Break Start:</th>
																		<th class="none">Break End:</th>
																		<th class="none">Basic Pay:</th>
																		<th class="none">Night Differential Pay:</th>
																		<th class="none">Regular Holiday:</th>
																		<th class="none">Special (Non-Working) Days:</th>
																		<th class="all">Action</th>
																	</tr>
																</thead>				
																
																<tbody>
																	
																</tbody>
													</table>
													</div>
											
											</div>
											<div class="tab-pane" id="solid-tab2" role="tabpanel">
													
													<div class="table-responsive">
													<table class="table dataTable display nowrap cell-border" id="EmployeeRegularOTLogsListDatatable" width="100%" cellspacing="0">
																<thead>
																	<tr>
																		<th class="all">#</th>
																		<th class="all">Date</th>
																		<th class="all" title="Employee Number">Employee Number</th>
																		<th class="all" title="Employee Name">Employee Name</th>
																		<th class="all">Overtime Hours</th>
																		<th class="all">Night Differential Hours</th>
																		<th class="none">Branch:</th>
																		<th class="none">Department:</th>
																		<th class="none">Time In:</th>
																		<th class="none">Time Out:</th>
																		<th class="none">Break Start:</th>
																		<th class="none">Break End:</th>
																		<th class="none">Overtime Pay:</th>
																		<th class="none">Night Differential Pay:</th>
																		<th class="none">Regular Holiday:</th>
																		<th class="none">Special (Non-Working) Days:</th>
																		<th class="all">Action</th>
																	</tr>
																</thead>				
																
																<tbody>
																	
																</tbody>
													</table>
													</div>
													
											</div>
											<div class="tab-pane" id="solid-tab3" role="tabpanel">
													
													<div class="table-responsive">
													<table class="table dataTable display nowrap cell-border" id="EmployeeRestDayOTLogsListDatatable" width="100%" cellspacing="0">
																<thead>
																	<tr>
																		<th class="all">#</th>
																		<th class="all">Date</th>
																		<th class="all" title="Employee Number">Employee Number</th>
																		<th class="all" title="Employee Name">Employee Name</th>
																		<th class="all">Day-off Hours</th>
																		<th class="all">Night Differential Hours</th>
																		<th class="none">Branch:</th>
																		<th class="none">Department:</th>
																		<th class="none">Time In:</th>
																		<th class="none">Time Out:</th>
																		<th class="none">Break Start:</th>
																		<th class="none">Break End:</th>
																		<th class="none">Day-off Pay:</th>
																		<th class="none">Night Differential Pay:</th>
																		<th class="none">Regular Holiday:</th>
																		<th class="none">Special (Non-Working) Days:</th>
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
									
									

						</div>
					</div>
                </div>
				<!-- /Page Content -->
				
				<!-- Add Employee Modal -->
				<div id="employee_regular_logs_details_modal" class="modal custom-modal fade" data-bs-backdrop="static" role="dialog">
					<div class="modal-dialog modal-dialog-centered modal-lg" role="document">
						<div class="modal-content">
							<div class="modal-header">
								<h5 class="modal-title" id="modal_title_action_employee_logs">Add Attendance Logs</h5>
								<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
									<span aria-hidden="true">&times;</span>
								</button>
							</div>
							<div class="modal-body">
								 <form class="g-2 needs-validation" id="EmployeeAttendanceLogsForm">
									
									<div class="row">
									
										<div class="col-sm-6">
											<div class="input-block mb-3">
												<label class="col-form-label">Branch <span class="text-danger">*</span></label>
												<input class="form-control " type="text" list="branch_list_logs" id="branch_idx" name="branch_idx" required autocomplete="off" onchange="LoadEmployee()">
												<span class="valid-feedback" id="branch_idxError" title="Required"></span>
												<datalist id="branch_list_logs"><!--List Here--></datalist>	
											</div>
										</div>
										
										<div class="col-sm-6">
											<div class="input-block mb-3">
												<label class="col-form-label">Employee <span class="text-danger">*</span></label>
												<input class="form-control " type="text" list="employee_list_logs" id="employee_idx" name="employee_idx" required autocomplete="off" onchange="Load_Employee_Default_Logs();">
												<span class="valid-feedback" id="employee_idxError" title="Required"></span>
												<datalist id="employee_list_logs"><!--List Here--></datalist>	
											</div>
										</div>

										<div class="col-sm-6">  
											<div class="input-block mb-3" title="Select yes if logs falls under regular overtime">
												<label class="col-form-label">Overtime? <span class="text-danger"></span></label>
												<select class="form-select form-control" name="overtime_status" id="overtime_status">
													<option value="No" selected>No</option>
													<option value="Yes">Yes</option>
												</select>
												<span class="valid-feedback" id="" title="Required"></span>
											</div>
										</div>
										<div class="col-sm-6">  
											<div class="input-block mb-3" title="This will allow the System to Disregard the Assigned Shift for the Employee, This is Applicable to those employee wish sudden shifting schedule. However this will not compute the Tardiness and Undertime">
												<label class="col-form-label">Override Default Shift? <span class="text-danger"></span></label>
												<select class="form-select form-control" name="override_default_shift" id="override_default_shift">
													<option value="No" selected>No</option>
													<option value="Yes">Yes</option>
												</select>
												<span class="valid-feedback" id="" title="Required"></span>
											</div>
										</div>
										
										<div class="col-sm-12">
											<div class="input-block mb-3">
												<label class="col-form-label">Date <span class="text-danger">*</span></label>
												<input class="form-control" type="date" id="attendance_date" name="attendance_date" required value="<?=date('Y-m-d');?>" onchange="Load_Employee_Default_Logs()">
												<span class="valid-feedback" id="attendance_dateError" title="Required"></span>
											</div>
										</div>
										
										<div class="col-sm-6">  
											<div class="input-block mb-3">
												<label class="col-form-label">Time In <span class="text-danger">*</span></label>
												<input class="form-control" type="datetime-local" id="log_in" name="log_in" required>
												<span class="valid-feedback" id="log_inError" title="Required"></span>
											</div>
										</div>
										<div class="col-sm-6">  
											<div class="input-block mb-3">
												<label class="col-form-label">Time Out <span class="text-danger">*</span></label>
												<input class="form-control" type="datetime-local" id="log_out" name="log_out" required>
												<span class="valid-feedback" id="log_outError" title="Required"></span>
											</div>
										</div>
										<div class="col-sm-6">  
											<div class="input-block mb-3">
												<label class="col-form-label">Breaktime Start <span class="text-danger">*</span></label>
												<input class="form-control" type="datetime-local" id="breaktime_start" name="breaktime_start" required>
												<span class="valid-feedback" id="breaktime_startError" title="Required"></span>
											</div>
										</div>
										<div class="col-sm-6">  
											<div class="input-block mb-3">
												<label class="col-form-label">Breaktime End <span class="text-danger">*</span></label>
												<input class="form-control" type="datetime-local" id="breaktime_end" name="breaktime_end" required>
												<span class="valid-feedback" id="breaktime_endError" title="Required"></span>
											</div>
										</div>
										
									</div>
									
									
									<div class="submit-section">
										<button class="btn btn-primary submit-btn" id="submit_logs_details" value="0">Submit</button>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
				<!-- /Add Employee Modal -->
				
				<!-- Delete Employee Modal -->
				<!-- <div class="modal custom-modal fade" id="EmployeeDeleteModal" role="dialog" tabindex="-1" role="dialog">-->
				<div class="modal custom-modal fade" id="EmployeeLogDeleteModal" role="dialog">
					<div class="modal-dialog modal-dialog-centered">
						<div class="modal-content">
							<div class="modal-body">
								<div class="form-header">
									<h3>Employee Log</h3>
									<p>Are you sure want to delete?</p>
								</div>
								<div class="modal-btn delete-action">
									<div class="row">
									<div class="card">
								<div class="card-header border-bottom-0 pb-0">
									<span class="ms-auto shadow-lg fs-17"></span>
								</div>
								<div class="card-body pt-1">
									<!--
									<span class="avatar avatar-xl avatar-rounded me-2 mb-2">
										<img src="assets/img/avatar/avatar-7.jpg" alt="img">
									</span
									
									<div class="fw-semibold fs-16"><span id="delete_employee_log_complete_name"></span></div>
									-->
									<p class="mb-2 fs-16">Name:<span id="delete_employee_logs_complete_name"></span></p>
									<p class="mb-2 fs-16">Date:<span id="delete_employee_logs_date"></span></p>
									<p class="mb-2 fs-16">Type:<span id="delete_log_type"></span></p>
								</div>
							</div>
									</div>
									
									<div class="row">
										<button type="submit" class="col-6 btn btn-primary continue-btn" id="deleteEmployeeLogConfirmed">Delete</button>
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