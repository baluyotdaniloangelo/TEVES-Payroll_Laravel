
				<div class="modal custom-modal fade" id="DriversLogDeleteModal" role="dialog">
					<div class="modal-dialog modal-dialog-centered">
						<div class="modal-content">
							<div class="modal-body">
								<div class="form-header">
									<h3>Drivers Log</h3>
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
									
									<div class="fw-semibold fs-16"><span id="delete_log_complete_name"></span></div>
									-->
									<p class="mb-2 fs-16">Name:<span id="delete_drivers_logs_complete_name"></span></p>
									<p class="mb-2 fs-16">Date:<span id="delete_drivers_logs_date"></span></p>
									<!--<p class="mb-2 fs-16">Type:<span id="delete_drivers_logs_reason"></span></p>-->
								</div>
							</div>
									</div>
									
									<div class="row">
										<button type="submit" class="col-6 btn btn-primary continue-btn" id="deleteDriversLogConfirmed">Delete</button>
										<div class="col-6">
											<a href="javascript:void(0);" data-bs-dismiss="modal" class="btn btn-primary cancel-btn">Cancel</a>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div id="drivers_logs_details_modal" class="modal custom-modal fade" data-bs-backdrop="static" role="dialog">
					<div class="modal-dialog modal-dialog-centered modal-lg" role="document">
						<div class="modal-content">
							<div class="modal-header">
								<h5 class="modal-title" id="modal_title_action_drivers_logs">Add Drivers Log</h5>
								<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
									<span aria-hidden="true">&times;</span>
								</button>
							</div>
							<div class="modal-body">
								 <form class="g-2 needs-validation" id="DriversLogsForm">
									
									<div class="row">
									
										<div class="col-sm-6">
											<div class="input-block mb-3">
												<label class="col-form-label">Branch <span class="text-danger">*</span></label>
												<input class="form-control " type="text" list="drivers_branch_list_logs" id="drivers_branch_idx" name="drivers_branch_idx" required autocomplete="off" onchange="Drivers_LoadList()">
												<span class="valid-feedback" id="drivers_branch_idxError" title="Required"></span>
												<datalist id="drivers_branch_list_logs"><!--List Here--></datalist>	
											</div>
										</div>
										
										<div class="col-sm-6">
											<div class="input-block mb-3">
												<label class="col-form-label"> <span class="text-danger">*</span></label>
												<input class="form-control " type="text" list="drivers_list_logs" id="drivers_idx" name="drivers_idx" required autocomplete="off">
												<span class="valid-feedback" id="drivers_idxError" title="Required"></span>
												<datalist id="drivers_list_logs"><!--List Here--></datalist>	
											</div>
										</div>
										
										<div class="col-sm-12">
											<div class="input-block mb-3">
												<label class="col-form-label">Date <span class="text-danger">*</span></label>
												<input class="form-control" type="date" id="travel_date" name="travel_date" required value="<?=date('Y-m-d');?>">
												<span class="valid-feedback" id="travel_dateError" title="Required"></span>
											</div>
										</div>
										
										<div class="col-sm-12">
											<div class="input-block mb-3">
												<label class="col-form-label">Plate Number <span class="text-danger">*</span></label>
												<input class="form-control" type="text" id="plate_number" name="plate_number" required>
												<span class="valid-feedback" id="plate_numberError" title="Required"></span>
											</div>
										</div>
										
										<div class="col-sm-12">
											<div class="input-block mb-3">
												<label class="col-form-label">Loading Terminal <span class="text-danger">*</span></label>
												<input class="form-control" type="text" id="loading_terminal" name="loading_terminal" required>
												<span class="valid-feedback" id="loading_terminalError" title="Required"></span>
											</div>
										</div>
										
										<div class="col-sm-12">
											<div class="input-block mb-3">
												<label class="col-form-label">Destination <span class="text-danger">*</span></label>
												<input class="form-control" type="text" id="destination" name="destination" required>
												<span class="valid-feedback" id="destinationError" title="Required"></span>
											</div>
										</div>
										
										<div class="col-sm-6">
											<div class="input-block mb-3">
												<label class="col-form-label">Volume <span class="text-danger">*</span></label>
												<input class="form-control" type="number" id="destination" name="destination" required>
												<span class="valid-feedback" id="destinationError" title="Required"></span>
											</div>
										</div>
										
										<div class="col-sm-6">
											<div class="input-block mb-3">
												<label class="col-form-label">Rate per Liter <span class="text-danger">*</span></label>
												<input class="form-control" type="number" id="rate_per_liter" name="rate_per_liter" required>
												<span class="valid-feedback" id="rate_per_literError" title="Required"></span>
											</div>
										</div>
										
									</div>
									
									<div class="submit-section">
										<button class="btn btn-primary submit-btn" id="submit_drivers_logs_details" value="0">Submit</button>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
				