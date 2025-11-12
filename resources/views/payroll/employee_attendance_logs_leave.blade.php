
				<div class="modal custom-modal fade" id="EmployeeLeaveLogDeleteModal" role="dialog">
					<div class="modal-dialog modal-dialog-centered">
						<div class="modal-content">
							<div class="modal-body">
								<div class="form-header">
									<h3>Employee Leave</h3>
									<p>Are you sure want to delete?</p>
								</div>
								<div class="modal-btn delete-action">
									<div class="row">
									<div class="card">
								<div class="card-header border-bottom-0 pb-0">
									<span class="ms-auto shadow-lg fs-17"></span>
								</div>
								<div class="card-body pt-1">
									
									<p class="mb-2 fs-16">Name:<span id="delete_employee_leave_logs_complete_name"></span></p>
									<p class="mb-2 fs-16">Date:<span id="delete_employee_leave_logs_date"></span></p>
									<p class="mb-2 fs-16">Type:<span id="delete_employee_leave_logs_reason"></span></p>
								</div>
							</div>
									</div>
									
									<div class="row">
										<button type="submit" class="col-6 btn btn-primary continue-btn" id="deleteEmployeeLeaveLogConfirmed">Delete</button>
										<div class="col-6">
											<a href="javascript:void(0);" data-bs-dismiss="modal" class="btn btn-primary cancel-btn">Cancel</a>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div id="employee_leave_logs_details_modal" class="modal custom-modal fade" data-bs-backdrop="static" role="dialog">
					<div class="modal-dialog modal-dialog-centered modal-lg" role="document">
						<div class="modal-content">
							<div class="modal-header">
								<h5 class="modal-title" id="modal_title_action_employee_leave_logs">Add Leave Logs</h5>
								<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
									<span aria-hidden="true" onclick="ResetEmployeeLeavesLogsForm()">&times;</span>
								</button>
							</div>
							<div class="modal-body">
								 <form class="g-2 needs-validation" id="EmployeeLeavesLogsForm">
									
									<div class="row">
									
										<div class="col-sm-6">
											<div class="input-block mb-3">
												<label class="col-form-label">Branch <span class="text-danger">*</span></label>
												<input class="form-control " type="text" list="leave_branch_list_logs" id="leave_branch_idx" name="leave_branch_idx" required autocomplete="off" onchange="leave_LoadEmployee()">
												<span class="valid-feedback" id="leave_branch_idxError" title="Required"></span>
												<datalist id="leave_branch_list_logs"><!--List Here--></datalist>	
											</div>
										</div>
										
										<div class="col-sm-6">
											<div class="input-block mb-3">
												<label class="col-form-label">Employee <span class="text-danger">*</span></label>
												<input class="form-control " type="text" list="leave_employee_list_logs" id="leave_employee_idx" name="leave_employee_idx" required autocomplete="off">
												<span class="valid-feedback" id="leave_employee_idxError" title="Required"></span>
												<datalist id="leave_employee_list_logs"><!--List Here--></datalist>	
											</div>
										</div>
										
										<div class="col-sm-12">
											<div class="input-block mb-3">
												<label class="col-form-label">Date <span class="text-danger">*</span></label>
												<input class="form-control" type="date" id="date_of_leave" name="date_of_leave" required value="<?=date('Y-m-d');?>">
												<span class="valid-feedback" id="date_of_leaveError" title="Required"></span>
											</div>
										</div>
										
										<div class="col-sm-12">
											<div class="input-block mb-3">
												<label class="col-form-label">Reason <span class="text-danger">*</span></label>
												<input class="form-control" type="text" id="reason_of_leave" name="reason_of_leave" required>
												<span class="valid-feedback" id="reason_of_leaveError" title="Required"></span>
											</div>
										</div>
									</div>
									
									<div class="submit-section">
										<button class="btn btn-primary submit-btn" id="submit_leave_details" value="0">Submit</button>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
