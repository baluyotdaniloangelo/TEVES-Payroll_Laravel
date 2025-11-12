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
								<h3 class="page-title">Deduction Logs</h3>
								<!--<ul class="breadcrumb">
									<li class="breadcrumb-item"><a href="admin-dashboard.html">Dashboard</a></li>
									<li class="breadcrumb-item active">Employee</li>
								</ul>-->
							</div>
							<div class="col-auto float-end ms-auto">
								<a href="#" class="btn add-btn" data-bs-toggle="modal" data-bs-target="#employee_deduction_logs_details_modal" onclick="ResetEmployeeDeductionLogsForm()"><i class="fa-solid fa-plus"></i> Add Deduction</a>
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
													<table class="table dataTable display nowrap cell-border" id="EmployeeDeductionLogsListDatatable" width="100%" cellspacing="0">
																<thead>
																	<tr>
																		<th class="all">#</th>
																		<th class="all">Date</th>
																		<th class="all" title="Employee Number">Employee Number</th>
																		<th class="all" title="Employee Name">Employee Name</th>
																		<th class="all">Description</th>
																		<th class="all">Amount</th>
																		<th class="all">Action</th>
																		<th class="none">Branch</th>
																		<th class="none">Department</th>
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
				<div id="employee_deduction_logs_details_modal" class="modal custom-modal fade" data-bs-backdrop="static" role="dialog">
					<div class="modal-dialog modal-dialog-centered modal-lg" role="document">
						<div class="modal-content">
							<div class="modal-header">
								<h5 class="modal-title" id="modal_title_employee_deduction_logs">Add Employee's Deduction</h5>
								<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" onclick="ResetEmployeeDeductionLogsForm()">
									<span aria-hidden="true">&times;</span>
								</button>
							</div>
							<div class="modal-body">
								 <form class="g-2 needs-validation" id="EmployeeDeductionLogsForm">
									
									<div class="row">
									
										<div class="col-sm-12">
											<div class="input-block mb-3">
												<label class="col-form-label">Branch <span class="text-danger">*</span></label>
												<input class="form-control " type="text" list="branch_list_logs" id="branch_idx" name="branch_idx" required autocomplete="off" onchange="LoadEmployee()">
												<span class="valid-feedback" id="branch_idxError" title="Required"></span>
												<datalist id="branch_list_logs"><!--List Here--></datalist>	
											</div>
										</div>
										
										<div class="col-sm-12">
											<div class="input-block mb-3">
												<label class="col-form-label">Employee <span class="text-danger">*</span></label>
												<input class="form-control " type="text" list="employee_list_logs" id="employee_idx" name="employee_idx" required autocomplete="off">
												<span class="valid-feedback" id="employee_idxError" title="Required"></span>
												<datalist id="employee_list_logs"><!--List Here--></datalist>	
											</div>
										</div>
										
										<div class="col-sm-12">
											<div class="input-block mb-3">
												<label class="col-form-label">Deduction Type <span class="text-danger">*</span></label>
												<input class="form-control " type="text" list="deduction_list_logs" id="deduction_idx" name="deduction_idx" required autocomplete="off">
												<span class="valid-feedback" id="deduction_idxError" title="Required"></span>
												<datalist id="deduction_list_logs"><!--List Here--></datalist>	
											</div>
										</div>
										
										<div class="col-sm-12">
											<div class="input-block mb-3">
												<label class="col-form-label">Date <span class="text-danger">*</span></label>
												<input class="form-control" type="date" id="deduction_date" name="deduction_date" required value="<?=date('Y-m-d');?>">
												<span class="valid-feedback" id="deduction_dateError" title="Required"></span>
											</div>
										</div>
										
										<div class="col-sm-12">  
											<div class="input-block mb-3">
												<label class="col-form-label">Amount <span class="text-danger">*</span></label>
												<input class="form-control" type="number" id="deduction_amount" name="deduction_amount" required>
												<span class="valid-feedback" id="deduction_amountError" title="Required"></span>
											</div>
										</div>
										
									</div>
									
									<div class="submit-section">
										<button class="btn btn-primary submit-btn" id="submit_deduction_logs_details" value="0">Submit</button>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
				<!-- /Add Employee Modal -->
				
				<!-- Delete Employee Modal -->
				<!-- <div class="modal custom-modal fade" id="EmployeeDeleteModal" role="dialog" tabindex="-1" role="dialog">-->
				<div class="modal custom-modal fade" id="EmployeeDeductionLogDeleteModal" role="dialog">
					<div class="modal-dialog modal-dialog-centered">
						<div class="modal-content">
							<div class="modal-body">
								<div class="form-header">
									<h3>Employee Deduction Log</h3>
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
									<p class="mb-2 fs-16">Name:<span id="delete_employee_deduction_logs_complete_name"></span></p>
									<p class="mb-2 fs-16">Date:<span id="delete_deduction_date"></span></p>
									<p class="mb-2 fs-16">Deduction Description:<span id="delete_deduction_description"></span></p>
									<p class="mb-2 fs-16">Amount:<span id="delete_deduction_amount"></span></p>
								</div>
							</div>
									</div>
									
									<div class="row">
										<button type="submit" class="col-6 btn btn-primary continue-btn" id="deleteEmployeeDeductionLogConfirmed">Delete</button>
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
