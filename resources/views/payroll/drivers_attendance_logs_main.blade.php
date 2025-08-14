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
								<h3 class="page-title">Driver's Logs</h3>
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
										
										<div class="tab-content">

											<!--<div class="tab-pane active show" id="solid-tab5" role="tabpanel">-->
													
													<div class="row align-items-center mb-3">
														<div class="col-auto float-end ms-auto">
															<a href="#" class="btn add-btn" data-bs-toggle="modal" data-bs-target="#drivers_logs_details_modal" onclick="ResetDriversLogsForm()"><i class="fa-solid fa-plus"></i> Add Logs</a>
														</div>
													</div>
																										
													<div class="table-responsive">
													<table class="table dataTable display nowrap cell-border" id="DriversLogsListDatatable" width="100%" cellspacing="0">
																<thead>
																	<tr>
																		<th class="all">#</th>
																		<th class="all">Date</th>
																		<th class="all" title="Employee Number">Employee Number</th>
																		<th class="all" title="Employee Name">Employee Name</th>
								
																		<th class="all" title="Volume">Volume</th>
																		<th class="all" title="Rate">Rate(P/L)</th>
																		<th class="all" title="Gross Amount">Gross Amount</th>
																		<th class="all" title="Trip Pay">Trip Pay</th>
																		
																		<th class="all">Action</th>
																		
																		<th class="none">Plate Number</th>
																		<th class="none">Loading Terminal</th>
																		<th class="none">Destination</th>
																		
																	</tr>
																</thead>				
																
																<tbody>
																	
																</tbody>
													</table>
													</div>
												
											<!--</div>-->
											
										</div>
									</div>
									
									

						</div>
					</div>
                </div>
				
				<!-- /Page Content -->
				
				<!-- Forms -->
				<!-- Attendance Logs -->
				@include('payroll.drivers_attendance_logs')
				
				<!-- Forms -->
				
				
				</div>
				
				
				
			<!-- /Page Wrapper -->
			


@endsection
