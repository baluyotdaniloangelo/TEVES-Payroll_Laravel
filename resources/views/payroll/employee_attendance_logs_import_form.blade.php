
				<div id="employee_import_logs_modal" class="modal custom-modal fade" data-bs-backdrop="static" role="dialog">
					<div class="modal-dialog modal-dialog-centered modal-lg" role="document">
						<div class="modal-content">
							<div class="modal-header">
								<h5 class="modal-title" id="modal_title_action_employee_logs">Add Attendance Logs</h5>
								<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
									<span aria-hidden="true">&times;</span>
								</button>
							</div>
							<div class="modal-body">
								 <form id="csv-form" enctype="multipart/form-data">
                                  @csrf
									<div class="row">
									
										<div class="col-sm-6">
											<div class="input-block mb-3">
												<!--<label class="col-form-label">Choose a CSV file: <span class="text-danger">*</span></label>-->
												<input type="file" name="csv_file" id="csv_file" accept=".csv" required>
												<span class="valid-feedback" id="feedback" title="Required"></span>
												
											</div>
										</div>
										
									</div>

                                    <div class="row">
									    <table id="csv-table" border="1" style="width:100%; margin-top: 20px; display:none;">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Name</th>
                                                    <th>Email</th>
                                                    <th>Position</th>
                                                    <th>Department</th>
                                                    <th>Import Status</th>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
									</div>

									<div class="submit-section">
										<button class="btn btn-primary submit-btn" id="submit_logs_import" value="0">View</button>
                                        <button class="btn btn-primary submit-btn" id="submit_logs_import" value="0">Import</button>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
