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
								<h3 class="page-title">Generate Payroll</h3>
								<!--<ul class="breadcrumb">
									<li class="breadcrumb-item"><a href="admin-dashboard.html">Dashboard</a></li>
									<li class="breadcrumb-item active">Employee</li>
								</ul>-->
							</div>
							<div class="col-auto float-end ms-auto">
								<a href="#" class="btn add-btn" data-bs-toggle="modal" data-bs-target="#generate_payroll_modal"><i class="fa fa-tasks"></i> Options</a>
								<!-- onclick="ResetEmployeeAttendanceLogsForm()"-->
							</div>
						</div>
					</div>
					
					<!-- /Search Filter >-->
					<div class="row">
						<div class="col-md-12">
						
						<div class="card-body">
										
										<div class="tab-content">
											<div class="tab-pane active show" id="solid-tab1" role="tabpanel">
													
													<div class="table-responsive">
													<table class="table dataTable display nowrap cell-border" id="EmployeesPayrollResult" width="100%" cellspacing="0">
																<thead>
																	<tr>
																		<th class="all">#</th>
																		<th class="" title="Employee Number">Employee Number</th>
																		<th class="all" title="Employee Name">Employee Name</th>
																		<th class="" title="Employee Status">Employment Status</th>
																		<th class="all">Daily Rate</th>
																		<th class="">Number of Days Work</th>
																		<th class="">Number of Days Leave</th>
																		<th class="all">Basic Pay</th>
																		<th class="all">OT Pay</th>
																		<th class="all">Day Off Pay</th>
																		<th class="all">Allowance</th>
																		<th class="all">Deduction</th>
																		<th class="all">Gross Salary</th>
																		<th class="all">Net Salary</th>
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
				
		
				
				<div id="generate_payroll_modal" class="modal custom-modal fade" data-bs-backdrop="static" role="dialog">
					<div class="modal-dialog modal-dialog-centered modal-lg" role="document">
						<div class="modal-content">
							<div class="modal-header">
								<h5 class="modal-title" id="">Generate Payroll</h5>
								<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
									<span aria-hidden="true">&times;</span>
								</button>
							</div>
							<div class="modal-body">
								 <form class="g-2 needs-validation" id="generate_payroll_form">
									
									<div class="row">
									
										<div class="col-sm-12">
											<div class="input-block mb-3">
												<label class="col-form-label">Branch <span class="text-danger">*</span></label>
												<input class="form-control " type="text" list="branch_list" id="branch_idx" name="branch_idx" required autocomplete="off">
												<span class="valid-feedback" id="branch_idxError" title="Required"></span>
												<datalist id="branch_list"><!--List Here--></datalist>	
											</div>
										</div>
										
										
										<div class="col-sm-6">
											<div class="input-block mb-3">
												<label class="col-form-label">Start Date <span class="text-danger">*</span></label>
												<input class="form-control" type="date" id="start_date" name="start_date" required value="<?=date('Y-m-d');?>" max="9999-12-31" required onchange="setMaxonEndDate()">
												<span class="valid-feedback" id="start_dateError" title="Required"></span>
											</div>
										</div>
										
										<div class="col-sm-6">
											<div class="input-block mb-3">
												<label class="col-form-label">End Date <span class="text-danger">*</span></label>
												<input class="form-control" type="date" id="end_date" name="end_date" required value="<?=date('Y-m-d');?>" required onchange="CheckEndDateValidity()">
												<span class="valid-feedback" id="end_dateError" title="Required"></span>
											</div>
										</div>

										
									</div>
									
									<div class="submit-section">
										<button class="btn btn-primary submit-btn" id="generate_payroll" value="0">Submit</button>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>				
				
				</div>
				
				
				
			<!-- /Page Wrapper -->
			


@endsection