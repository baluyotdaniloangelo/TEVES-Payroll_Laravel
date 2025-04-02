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
								<div class="row">
									<a href="#" class="col-6 btn add-btn" data-bs-toggle="modal" data-bs-target="#generate_payroll_modal"><i class="fa fa-tasks"></i> Options</a>
									<div id="save_options" class="col-6 "></div>
								</div>
							</div>
						</div>
					</div>
					
					<!-- /Search Filter >-->
					<div class="row">
										<div class="col-sm-12">
											<div>
												<!--<h4 class="m-b-10"><strong>Earnings</strong></h4>-->
												<table class="table table-bordered">
													<tbody>
														<tr>
															<td><strong>Branch Code:</strong> <span class="float-left" id="branch_code_details"></span></td>
														</tr>
														<tr>
															<td><strong>Branch Name:</strong> <span class="float-left" id="branch_name_details"></span></td>
														</tr>
														<tr>
															<td><strong>Branch TIN:</strong> <span class="float-left" id="branch_tin_details"></span></td>
														</tr>
														<tr>
															<td><strong>Period Covered:</strong> <span class="float-left" id="covered_period_details"></span></td>
														</tr>
														
													</tbody>
												</table>
												
											</div>
										</div>
										<!--<div class="col-sm-6">
											<div>
												<h4 class="m-b-10"><strong>Deductions</strong></h4>
												<table class="table table-bordered">
													<tbody>
														<tr>
															<td><strong>Branch Name</strong> <span class="float-end">$0</span></td>
														</tr>
														<tr>
															<td><strong>Branch Code</strong> <span class="float-end">$0</span></td>
														</tr>
														<tr>
															<td><strong>Branch TIN</strong> <span class="float-end">$0</span></td>
														</tr>
														<tr>
															<td><strong>Period Covered</strong> <span class="float-end">$300</span></td>
														</tr>
														
													</tbody>
												</table>
											</div>
										</div>-->
										<div class="col-sm-12">
											<p><strong></strong><i>Before saving the cut-off, please review all the generated salary details below. Once the selected period has been saved, all data on payroll items will be disabled for editing or deletion.</i></p>
										</div>
									</div>
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
																		<th class="all">Night Differential Pay</th>
																		<th class="all">OT Pay</th>
																		<th class="all">Day Off Pay</th>
																		<th class="all">Special Holiday</th>
																		<th class="all">Regular Holiday</th>
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
					<div class="modal-dialog modal-dialog-centered" role="document">
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
				
				
				<!-- Delete Department Modal -->
				<div class="modal custom-modal fade" id="SaveCutOffModal" role="dialog">
					<div class="modal-dialog modal-dialog-centered">
						<div class="modal-content">
							<div class="modal-body">
								<div class="form-header">
									<h3>Save Cut Off</h3>
									<p>Are you sure want to save this cut off?</p>
								</div>
								<div class="modal-btn delete-action">
									<div class="row">
									<div class="card">
								<div class="card-header border-bottom-0 pb-0">
									<span class="ms-auto shadow-lg fs-17"></span>
								</div>
								<div class="card-body">
									<table class="table table-bordered">
													<tbody>
														<tr>
															<td><strong>Branch Code:</strong> <span class="float-left" id="branch_code_details_save"></span></td>
														</tr>
														<tr>
															<td><strong>Branch Name:</strong> <span class="float-left" id="branch_name_details_save"></span></td>
														</tr>
														<tr>
															<td><strong>Branch TIN:</strong> <span class="float-left" id="branch_tin_details_save"></span></td>
														</tr>
														<tr>
															<td><strong>Period Covered:</strong> <span class="float-left" id="covered_period_details_save"></span></td>
														</tr>
														
													</tbody>
												</table>
									</div>
								</div>
								</div>
									
									<div class="row">
										<button type="submit" class="col-6 btn btn-primary continue-btn" id="saveCutOffConfirmed">Save</button>
										<input type="hidden" id="deleteDepartmentBranch_idx_Confirmed">
										<div class="col-6">
											<a href="javascript:void(0);" data-bs-dismiss="modal" class="btn btn-primary cancel-btn">Cancel</a>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				
				</div>
				
				
				
			<!-- /Page Wrapper -->
			


@endsection