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
							<!--<div class="col-auto float-end ms-auto">
								<a href="#" class="btn add-btn" data-bs-toggle="modal" data-bs-target="#employee_regular_logs_details_modal"><i class="fa-solid fa-plus"></i> Add Attendance Logs</a>
								<a href="#" class="btn add-btn" data-bs-toggle="modal" data-bs-target="#employee_regular_logs_details_modal"><i class="fa-solid fa-plus"></i> Add Attendance Logs</a>

							</div>-->
						</div>
					</div>
					
					<!-- /Search Filter >-->
					<div class="row">
						<div class="col-md-12">
						
						<div class="card-body">
										<ul class="nav nav-tabs nav-tabs-solid" role="tablist">
											<li class="nav-item" role="presentation"><a class="nav-link active" href="#solid-tab1" data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1">Regular</a></li>
											<li class="nav-item" role="presentation"><a class="nav-link" href="#solid-tab3" data-bs-toggle="tab" aria-selected="true" role="tab">Restday</a></li>
											<li class="nav-item" role="presentation"><a class="nav-link" href="#solid-tab2" data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1">Overtime</a></li>
											<li class="nav-item" role="presentation"><a class="nav-link" href="#solid-tab4" data-bs-toggle="tab" aria-selected="true" role="tab">Leaves</a></li>
										    <li class="nav-item" role="presentation"><a class="nav-link" href="#solid-tab5" data-bs-toggle="tab" aria-selected="true" role="tab">Import</a></li>
										</ul>
										<div class="tab-content">
											<div class="tab-pane active show" id="solid-tab1" role="tabpanel">
													
													<div class="row align-items-center mb-3">
														<div class="col-auto float-end ms-auto">
															<a href="#" class="btn add-btn" data-bs-toggle="modal" data-bs-target="#employee_regular_logs_details_modal" style="margin-left: 5px;"><i class="fa-solid fa-plus"></i> Add Attendance Logs</a>
														</div>
													</div>
													
													<div class="table-responsive">
													<table class="table dataTable display nowrap cell-border" id="EmployeeRegularLogsListDatatable" width="100%" cellspacing="0">
																<thead>
																	<tr>
																		<th class="all">#</th>
																		<th class="all">Date</th>
																		<th class="all" title="Employee Number">Employee Number</th>
																		<th class="all" title="Employee Name">Employee Name</th>
																		<th class="all">Work Hours</th>
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
											
											<div class="tab-pane" id="solid-tab3" role="tabpanel">
													
													<div class="row align-items-center mb-3">
														<div class="col-auto float-end ms-auto">
															<a href="#" class="btn add-btn" data-bs-toggle="modal" data-bs-target="#employee_regular_logs_details_modal"><i class="fa-solid fa-plus"></i> Add Attendance Logs</a>
														</div>
													</div>
																										
													<div class="table-responsive">
													<table class="table dataTable display nowrap cell-border" id="EmployeeRestDayOTLogsListDatatable" width="100%" cellspacing="0">
																<thead>
																	<tr>
																		<th class="all">#</th>
																		<th class="all">Date</th>
																		<th class="all" title="Employee Number">Employee Number</th>
																		<th class="all" title="Employee Name">Employee Name</th>
																		<th class="all">Work Hours</th>
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

                                            <div class="tab-pane" id="solid-tab2" role="tabpanel">
													
													<div class="row align-items-center mb-3">
														<div class="col-auto float-end ms-auto">
															<a href="#" class="btn add-btn" data-bs-toggle="modal" data-bs-target="#employee_regular_logs_details_modal"><i class="fa-solid fa-plus"></i> Add Attendance Logs</a>
														</div>
													</div>
																										
													<div class="table-responsive">
													<table class="table dataTable display nowrap cell-border" id="EmployeeRegularOTLogsListDatatable" width="100%" cellspacing="0">
																<thead>
																	<tr>
																		<th class="all">#</th>
																		<th class="all">Date</th>
																		<th class="all">Type</th>
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

											<div class="tab-pane" id="solid-tab4" role="tabpanel">
													
													<div class="row align-items-center mb-3">
														<div class="col-auto float-end ms-auto">
															<a href="#" class="btn add-btn" data-bs-toggle="modal" data-bs-target="#employee_leave_logs_details_modal"><i class="fa-solid fa-plus"></i> Add Leaves</a>
														</div>
													</div>
																										
													<div class="table-responsive">
													<table class="table dataTable display nowrap cell-border" id="LeaveLogsListDatatable" width="100%" cellspacing="0">
																<thead>
																	<tr>
																		<th class="all">#</th>
																		<th class="all">Date</th>
																		<th class="all" title="Employee Number">Employee Number</th>
																		<th class="all" title="Employee Name">Employee Name</th>
																		<th class="all" title="Reason">Reason</th>
																		<th class="all">Action</th>
																	</tr>
																</thead>				
																
																<tbody>
																	
																</tbody>
													</table>
													</div>
												
											</div>
                                            <style>
                                            .progress {
                                              border-radius: 10px;
                                              overflow: hidden;
                                            }

                                            .progress-bar {
                                              font-weight: 500;
                                              transition: width 0.3s ease;
                                            }
                                            </style>
                                            <div class="tab-pane" id="solid-tab5" role="tabpanel">
													
													<div class="row align-items-center mb-3">
														<div class="col-auto float-end ms-auto">
															<!--<a href="#" class="btn add-btn" data-bs-toggle="modal" data-bs-target="#employee_leave_logs_details_modal"><i class="fa-solid fa-plus"></i> Add Leaves</a>-->
														</div>
													</div>

													<form id="csv-form" enctype="multipart/form-data">
                                                    @csrf								

                                                    <div class="form-group">
                                                      <label for="csv_file" class="form-label">Select CSV file</label>
                                                      <input type="file" name="csv_file" id="csv_file" accept=".csv" class="form-control" required>
                                                      <div class="mt-2" id="feedback"></div>
                                                    </div>

                                                    <!-- Spinner -->
                                                    <div class="text-center mt-3">
                                                      <div id="loading-spinner" class="spinner-border text-primary" role="status" style="display:none;">
                                                        <span class="visually-hidden">Loading...</span>
                                                      </div>
                                                    </div>

                                                    <div class="row">
									                
                                                    <div class="table-responsive">
                                                    <div id="total_current_consumption_top"></div>
													<table class="table dataTable display nowrap cell-border" id="csv-table" width="100%" cellspacing="0">
																<thead>
																	<tr>
                                                                        <th class="all">#</th>
                                                                        <!--Employee Information-->
                                                                        <th class="all">Employee ID</th>
                                                                        <th class="all">Name</th>
                                                                        <!--Logs Information-->
                                                                        <th class="all">Date</th>
                                                                        <th class="none">Log Type</th>
                                                                        <th class="none">Time In</th>
                                                                        <th class="none">Breaktime Start</th>
                                                                        <th class="none">Breaktime End</th>
                                                                        <th class="none">Time Out</th>
                                                                        <th class="none">OT In</th>
                                                                        <th class="none">OT Out</th>
                                                                        <!--Hours Information-->
                                                                        <th class="all">Regular Hours</th>
                                                                        <th class="all">Regular OTHours</th>
                                                                        <th class="all">Restday Hours</th>
                                                                        <th class="all">Restday OT Hours</th>
                                                                        <th class="all">Night Differential Hours</th>
                                                                        <!--Pay Information-->
                                                                        <th class="all">Regular Pay</th>
                                                                        <th class="all">Regular OT Pay</th>
                                                                        <th class="all">Restday Pay</th>
                                                                        <th class="all">Restday OT Pay</th>
                                                                        <th class="all">Night Differential Pay</th>
                                                                        <!--Holidays Information-->
                                                                        <th class="all">Regular Holiday Pay</th>
                                                                        <th class="all">Special Non Working Holiday Pay</th>
																	</tr>
																</thead>				
																
																<tbody>
																	
																</tbody>
													</table>
													</div>




									            </div>
                                                <div class="col-sm-12">
											        <p>
                                                        <strong></strong>
                                                        <i>
                                                        After choosing the file, use the View button to review the logs before importing. Once finalized, click the Import button.
                                                        </i>
                                                    </p>
										        </div>
												<div class="submit-section">
										            <button class="btn btn-primary submit-btn" id="submit_logs_view" value="0">View</button>
                                                    <button class="btn btn-primary submit-btn" id="submit_logs_import" value="0">Import</button>
									            </div>
											</div>
											  </form>


										</div>
									</div>
									
									

						</div>
					</div>
                </div>


				<!-- /Page Content -->
				
				<!-- Forms -->
				<!-- Attendance Logs -->
				@include('payroll.employee_attendance_logs_form')
				<!-- Leave Logs -->
				@include('payroll.employee_attendance_logs_leave')
				
				<!-- Forms -->
				
				
				</div>
				
				
				
			<!-- /Page Wrapper -->
			


@endsection
