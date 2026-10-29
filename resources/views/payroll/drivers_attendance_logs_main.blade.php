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
						</div>
					</div>
					<div id="drivers-logs-feedback" class="alert d-none" role="alert" aria-live="polite"></div>
					
					<!-- /Search Filter >-->
					<div class="row">
						<div class="col-md-12">
						
						<div class="card-body">
							<ul class="nav nav-tabs mb-3" id="driver-log-tabs" role="tablist">
								<li class="nav-item" role="presentation"><button class="nav-link active" id="driver-logs-tab" data-bs-toggle="tab" data-bs-target="#driver-logs-pane" type="button" role="tab">Trip Logs</button></li>
								<li class="nav-item" role="presentation"><button class="nav-link" id="driver-comparison-tab" data-bs-toggle="tab" data-bs-target="#driver-comparison-pane" type="button" role="tab">Computation Comparison</button></li>
							</ul>
							<div class="tab-content">
								<div class="tab-pane fade show active" id="driver-logs-pane" role="tabpanel" aria-labelledby="driver-logs-tab">
									<div class="row align-items-center mb-3"><div class="col-auto ms-auto"><a href="#" class="btn add-btn js-add-driver-log"><i class="fa-solid fa-plus"></i> Add Logs</a></div></div>
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
								</div>
								<div class="tab-pane fade" id="driver-comparison-pane" role="tabpanel" aria-labelledby="driver-comparison-tab">
									<p class="text-muted">Compare the rate and trip pay saved on each log with the driver's current rate. Use <strong>Use current rate</strong> to recalculate an unpaid trip.</p>
									<div class="table-responsive"><table class="table dataTable display nowrap cell-border" id="DriversLogsComputationTable" width="100%" cellspacing="0">
										<thead><tr><th>#</th><th>Date</th><th>Employee Number</th><th>Employee Name</th><th>Gross Amount</th><th>Saved Rate</th><th>Saved Trip Pay</th><th>Current Rate</th><th>Recalculated Pay</th><th>Difference</th><th>Action</th></tr></thead><tbody></tbody>
									</table></div>
								</div>
										</div>
									</div>
									
									

						</div>
					</div>
                </div>
				
				<!-- /Page Content -->
				
				<!-- Forms -->
				<!-- Attendance Logs -->
				@include('payroll.partials.drivers_logs_modals')
				
				<!-- Forms -->
				
				
				</div>
				
				
				
			<!-- /Page Wrapper -->
			


@endsection
