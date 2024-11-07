

<div class="row">
				<div align="center">
									<div class="table-responsive">
										<table class="table dataTable display nowrap cell-border" id="gatewaylist" width="100%" cellspacing="0">
											<thead>
												<tr>
													<th class="all">#</th>
													<th nowrap class="all" title="Gateway Device Serial Number">Serial Number</th>
													<th nowrap class="" title="Gateway Device IP Address">IP Address</th>
													<th class="none" title="Gateway Device MAC Address">MAC Address</th>
													<th class="" title="Status is based on Last Reading Logs from Meters">Status</th>
													<th class="" title="Device Configuration Updates for Meter List CSV File and Building Code">Configuration</th>
													<th class="all" title="View Meter List Per Gateway, Update, and Delete Gateway Devices">Action</th>
													<th title="Location Code" class="none" title="Gateway Device Location Code">Location Code: </th>
													<th title="Location Description" class="none" title="Gateway Device Location Description">Location Description: </th>		
													<th class="none" title="IDF Name assigned for the Gateway">IDF</th>
													<th class="none" title="Switch assigned for the Gateway">Switch</th>
													<th class="none" title="Port assigned for the Gateway">Port</th>
												</tr>
											</thead>				
											
											<tbody>
												
											</tbody>
											
										</table>
																
									</div></div></div>


	<!--Modal to Create Gateway-->
	<div class="modal fade" id="CreateGatewayModal" tabindex="-1">
                <div class="modal-dialog modal-lg">
                  <div class="modal-content">
                    <div class="modal-header modal-header_form">
                      <h5 class="modal-title">Create Gateway</h5>
					  <div class="btn-group" role="group" aria-label="Basic outlined example">		
						<!--<button type="button" class="btn btn-info bi bi-question navbar_icon" data-bs-toggle="modal" data-bs-target="#GatewayManual" id="manualbtn"></button>-->
						<button type="button" class="btn btn-danger bi bi-x-circle navbar_icon" data-bs-dismiss="modal"></button>
					  </div>
                    </div>
                    <div class="modal-body">

					  <form class="g-2 needs-validation" id="gatewayform">
					  
						<div class="row mb-2">
						  <label for="gateway_sn" class="col-sm-3 col-form-label">Serial Number</label>
						  <div class="col-sm-9">
							<input type="text" class="form-control" name="gateway_sn" id="gateway_sn" value="" required>
							<span class="valid-feedback" id="gateway_snError" title="Required"></span>
						  </div>
						</div>
						
						<div class="row mb-2">
						  <label for="gateway_mac" class="col-sm-3 col-form-label">MAC Address</label>
						  <div class="col-sm-9">
							<input type="text" class="form-control " name="gateway_mac" id="gateway_mac" value="" required>
							<span class="valid-feedback" id="gateway_macError"></span>
						  </div>
						</div>	
						
						<div class="row mb-2">
						  <label for="connection_type" class="col-sm-3 col-form-label">Connection Type</label>
						  <div class="col-sm-9">
							<select class="form-control form-select " name="connection_type" id="connection_type">
							<option value="LAN">Ethernet</option>
							<option value="3g Modem">3G Modem</option>
							<option value="4g Modem">4G Modem</option>
							</select>
						  </div>
						</div>
						
						<div class="row mb-2">
						  <label for="gateway_ip" class="col-sm-3 col-form-label">IP Address/SIM #</label>
						  <div class="col-sm-9">
							<input type="text" class="form-control " name="gateway_ip" id="gateway_ip" value="" required>
							<span class="valid-feedback" id="gateway_ipError"></span>
						  </div>
						</div>	

						<div class="row mb-2">
						<label for="gateway_ip" class="col-sm-3 col-form-label">Network Details</label>
						<div class="col-md-3">
						  <label for="idf_name">IDF Name</label>
						  <input type="text" class="form-control " name="idf_name" id="idf_name" value="" >
						</div>
						
						<div class="col-md-3">
						  <label for="idf_switch">Switch</label>
						  <input type="text" class="form-control " name="idf_switch" id="idf_switch" value="" >
						</div>
						
						<div class="col-md-3">
						  <label for="idf_name">Port</label>
						  <input type="text" class="form-control " name="idf_port" id="idf_port" value="" >
						</div>
						
						</div>
						
						<div class="row mb-2">
						  <label for="building_id" class="col-sm-3 col-form-label">Area/EE Room</label>
						  <div class="col-sm-9">
						    <input class="form-control" list="gateway_location_list" name="gateway_location" id="gateway_location" required autocomplete="off" value="">
									<datalist id="gateway_location_list">
											<option> </option>
									</datalist>
							<span class="valid-feedback" id="gateway_location_meterError"></span>	
						  </div>
						</div>
						
						<div class="row mb-2">
						  <label for="gateway_description" class="col-sm-3 col-form-label">Description</label>
						  <div class="col-sm-9">
							<input type="text" class="form-control " name="gateway_description" id="gateway_description" value="">
							<span class="valid-feedback" id="gateway_descriptionError"></span>
						  </div>
						</div>
									
						</div>
						
                    <div class="modal-footer modal-footer_form">
						
						  <button type="submit" class="btn btn-success btn-sm bi bi-save-fill navbar_icon" id="save-gateway"> Submit</button>
						  <button type="submit" class="btn btn-primary btn-sm bi bi-backspace-fill navbar_icon" id="clear-gateway" onclick="ResetFormAddGateway()"> Reset</button>
						  
					</div>
					</form><!-- End Multi Columns Form -->
                  </div>
                </div>
              </div>	
			  			  
	<!-- Update Gateway -->
	<div class="modal fade" id="UpdateGatewayModal" tabindex="-1">
                <div class="modal-dialog modal-lg">
                  <div class="modal-content">
                    <div class="modal-header modal-header_form">
                      <h5 class="modal-title">Update Gateway</h5>
					  <div class="btn-group" role="group" aria-label="Basic outlined example">		
						<!--<button type="button" class="btn btn-info bi bi-question navbar_icon" data-bs-toggle="modal" data-bs-target="#GatewayManual" id="manualbtn"></button>-->
						<button type="button" class="btn btn-danger bi bi-x-circle navbar_icon" data-bs-dismiss="modal"></button>
					  </div>
                    </div>
                    <div class="modal-body">

					  <form class="g-2 needs-validation" id="updategatewayform">
					  
						<div class="row mb-2">
						  <label for="update_gateway_sn" class="col-sm-3 col-form-label">Serial Number</label>
						  <div class="col-sm-9">
							<input type="text" class="form-control" name="update_gateway_sn" id="update_gateway_sn" value="" required>
							<span class="valid-feedback" id="update_gateway_snError" title="Required"></span>
						  </div>
						</div>
						
						<div class="row mb-2">
						  <label for="update_gateway_mac" class="col-sm-3 col-form-label">MAC Address</label>
						  <div class="col-sm-9">
							<input type="text" class="form-control " name="update_gateway_mac" id="update_gateway_mac" value="" required>
							<span class="valid-feedback" id="update_gateway_macError"></span>
						  </div>
						</div>	
						
						<div class="row mb-2">
						  <label for="update_connection_type" class="col-sm-3 col-form-label">Connection Type</label>
						  <div class="col-sm-9">
							<select class="form-control form-select " name="update_connection_type" id="update_connection_type">
							<option value="LAN">Ethernet</option>
							<option value="3g Modem">3G Modem</option>
							<option value="4g Modem">4G Modem</option>
							</select>
						  </div>
						</div>
						
						<div class="row mb-2">
						  <label for="update_gateway_ip" class="col-sm-3 col-form-label">IP Address/SIM #</label>
						  <div class="col-sm-9">
							<input type="text" class="form-control " name="update_gateway_ip" id="update_gateway_ip" value="" required>
							<span class="valid-feedback" id="update_gateway_ipError"></span>
						  </div>
						</div>	

						<div class="row mb-2">
						<label for="gateway_ip" class="col-sm-3 col-form-label">Network Details</label>
						<div class="col-md-3">
						  <label for="update_idf_name">IDF Name</label>
						  <input type="text" class="form-control " name="update_idf_name" id="update_idf_name" value="" >
						</div>
						
						<div class="col-md-3">
						  <label for="update_idf_switch">Switch</label>
						  <input type="text" class="form-control " name="update_idf_switch" id="update_idf_switch" value="" >
						</div>
						
						<div class="col-md-3">
						  <label for="update_idf_port">Port</label>
						  <input type="text" class="form-control " name="update_idf_port" id="update_idf_port" value="" >
						</div>
						
						</div>
						
						<div class="row mb-2">
						  <label for="update_gateway_location" class="col-sm-3 col-form-label">Area/EE Room</label>
						  <div class="col-sm-9">
						    <input class="form-control" list="update_gateway_location_list" name="update_gateway_location" id="update_gateway_location" required autocomplete="off" value="">
									<datalist id="update_gateway_location_list">
										<option> </option>
									</datalist>
							<span class="valid-feedback" id="update_gateway_location_meterError"></span>	
						  </div>
						</div>
						
						<div class="row mb-2">
						  <label for="update_gateway_description" class="col-sm-3 col-form-label">Description</label>
						  <div class="col-sm-9">
							<input type="text" class="form-control " name="update_gateway_description" id="update_gateway_description" value="">
							<span class="valid-feedback" id="update_gateway_descriptionError"></span>
						  </div>
						</div>
						
						
				
						</div>
						
                    <div class="modal-footer modal-footer_form">
							<div id="loading_data_updategateway" style="display:none;">
									<button class="btn btn-light btn-sm" type="button" disabled="">
									<span class="spinner-grow spinner-grow-sm text-danger" role="status" aria-hidden="true"></span>
									Checking for changes...
									</button>
							</div>
						  <button type="submit" class="btn btn-success btn-sm bi bi-save-fill navbar_icon" id="update-gateway" disabled> Submit</button>
						  
					</div>
					</form><!-- End Multi Columns Form -->
                  </div>
                </div>
              </div>	

	<!-- Update Gateway -->
	<div class="modal fade" id="ViewGatewayModal" tabindex="-1">
                <div class="modal-dialog modal-xl">
                  <div class="modal-content">
                    <div class="modal-header modal-header_form">
                      <h5 class="modal-title">Gateway Information</h5>
					  <div class="btn-group" role="group" aria-label="Basic outlined example">		
						<!--<button type="button" class="btn btn-info bi bi-question navbar_icon" data-bs-toggle="modal" data-bs-target="#GatewayManual" id="manualbtn"></button>-->
						<button type="button" class="btn btn-danger bi bi-x-circle navbar_icon" data-bs-dismiss="modal"></button>
					  </div>
                    </div>
                    <div class="modal-body">
					<div class="row">

					<div class="col-lg-4">
							
							<ol class="list-group list-group-numbered">
									<li class="list-group-item d-flex justify-content-between align-items-start">
									  <div class="ms-2 me-auto">
										<div class="fw-bold">Serial Number</div>
										<div id="view_serial_number"></div>
									  </div>
									  
									</li>
									<li class="list-group-item d-flex justify-content-between align-items-start">
									  <div class="ms-2 me-auto">
										<div class="fw-bold">IP Address</div>
										<div id="view_ip_address"></div>
									  </div>
									  
									</li>
									
									<li class="list-group-item d-flex justify-content-between align-items-start">
									  <div class="ms-2 me-auto">
										<div class="fw-bold">MAC Address</div>
										<div id="view_mac_address"></div>
									  </div>
									  
									<li class="list-group-item d-flex justify-content-between align-items-start">
									  <div class="ms-2 me-auto">
										<div class="fw-bold">Connection Type</div>
										<div id="view_connection_type"></div>
									  </div>
									  
									</li>
									<li class="list-group-item d-flex justify-content-between align-items-start">
									  <div class="ms-2 me-auto">
										<div class="fw-bold">Location</div>
										<div id="view_physical_location"></div>
									  </div>
									  
									</li>
									<li class="list-group-item d-flex justify-content-between align-items-start">
									  <div class="ms-2 me-auto">
										<div class="fw-bold">IDF Name</div>
										<div id="view_idf_name"></div>
									  </div>
									  
									</li>
									
									<li class="list-group-item d-flex justify-content-between align-items-start">
									  <div class="ms-2 me-auto">
										<div class="fw-bold">Switch</div>
										<div id="view_idf_switch"></div>
									  </div>
									  
									</li>
									
									<li class="list-group-item d-flex justify-content-between align-items-start">
									  <div class="ms-2 me-auto">
										<div class="fw-bold">Port</div>
										<div id="view_idf_port"></div>
									  </div>
									  
									</li>
									
									<li class="list-group-item d-flex justify-content-between align-items-start">
									  <div class="ms-2 me-auto">
										<div class="fw-bold">Description</div>
										<div id="view_gateway_description"></div>
									  </div>
									  
									</li>
									
								  </ol>
					</div>
					<div class="col-lg-8">
					
							<div class="card-0">
							<div class="card-body">  

							<div class="table-responsive">
										<table class="table table-bordered dataTable" id="meterlistLoadPerGateway" width="100%" cellspacing="0">
											<thead>
												<tr>
													<th class="all">#</th>
													<!--<th title="SAP Measuring Point" nowrap class="none">Measuring Point</th>-->
													<th title="Meter Serial Number" nowrap class="all">Meter Description</th>
													<th title="Meter Tagging/Tenant/Client Name" nowrap class="none">Name/Tagging:</th>													
													<th title="Last Reading Date" nowrap class="none">Last Reading Date:</th>
													<th title="Active/Inactive" class="none">Status:</th>
													<th title="The Meter Physical Location" class="none">Location Code: </th>
													<th title="The Meter Physical Location" class="none">Location Description: </th>
													<th title="Role : Tenant Meter/Tenant, Spare Meter, CUSA or Check Meter" nowrap class="none">Meter Role: </th>
													<th title="" class="all">Configuration File</th>
													<th title="" class="all">Address</th>
													<th title="" class="none">Remarks: </th>
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

					<!-- End Multi Columns Form -->
                  </div>
                </div>
              </div>	

	<!-- Gateway Delete Modal-->
    <div class="modal fade" id="GatewayDeleteModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header header_modal_bg">
                    <h5 class="modal-title" id="exampleModalLabel"></h5>
 					<div class="btn-sm btn-warning btn-circle bi bi-exclamation-circle btn_icon_modal"></div>
                </div>
                <div class="modal-body warning_modal_bg" id="modal-body">
				Are you sure you want to delete?
				</div>
				<div style="margin:10px;">
				
				<table width="100%">
				<tr>
				<th width="30%">Serial Number:</th>
				<td width="70%"><span id="delete_gateway_sn_info"></span></td>
				</tr>
				<tr>
				<th width="30%">IP Address:</th>
				<td width="70%"><span id="delete_gateway_ip_info"></span></td>
				</tr>
				<tr>
				<th width="30%">MAC Address:</th>
				<td width="70%"><span id="delete_gateway_mac_info"></span></td>
				</tr>
				<tr>
				<th width="30%">Area/EE Room:</th>
				<td width="70%"><span id="delete_gateway_location_info"></span></td>
				</tr>
				</table>
				
				</div>
                <div class="modal-footer footer_modal_bg">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal"><i class="bi bi-x-circle navbar_icon"></i> Cancel</button>
					<button type="button" class="btn btn-danger" data-bs-dismiss="modal" id="deleteGatewayConfirmed" value=""><i class="bi bi-trash3 navbar_icon"></i> Delete</button>
                  
                </div>
            </div>
        </div>
    </div>	

	<!-- Gateway Confirmed Deleted Modal-->
    <div class="modal fade" id="GatewayDeleteModalConfirmed" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header header_modal_bg">
                    <h5 class="modal-title" id="exampleModalLabel"></h5>
 					<div class="btn-sm btn-warning btn-circle bi bi-exclamation-circle btn_icon_modal"></div>
                </div>
                <div class="modal-body warning_modal_bg" id="modal-body">
				Successfully deleted!
				</div>
				<div style="margin:10px;">
				
				<table width="100%">
				<tr>
				<th width="30%">Serial Number:</th>
				<td width="70%"><span id="delete_gateway_sn_info_confirmed"></span></td>
				</tr>
				<tr>
				<th width="30%">IP Address:</th>
				<td width="70%"><span id="delete_gateway_ip_info_confirmed"></span></td>
				</tr>
				<tr>
				<th width="30%">MAC Address:</th>
				<td width="70%"><span id="delete_gateway_mac_info_confirmed"></span></td>
				</tr>
				<tr>
				<th width="30%">Area/EE Room:</th>
				<td width="70%"><span id="delete_gateway_location_info_confirmed"></span></td>
				</tr>
				</table>
				
				</div>
                <div class="modal-footer footer_modal_bg">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal"><i class="bi bi-x-circle navbar_icon"></i> Close</button>
					     
                </div>
            </div>
        </div>
    </div>
	
	<!-- Enabel/Disable CSV Update-->
    <div class="modal fade" id="CSVStatus" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header header_modal_bg">
                    <h5 class="modal-title" id="exampleModalLabel"></h5>
 					<div class="btn-sm btn-warning btn-circle bi bi-exclamation-circle btn_icon_modal"></div>
                </div>
                <div class="modal-body warning_modal_bg" id="modal-body">
				CSV Update Enabled!
				</div>
                <div class="modal-footer footer_modal_bg">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal"><i class="bi bi-x-circle navbar_icon"></i> Close</button>
					     
                </div>
            </div>
        </div>
    </div>
	
	<!-- Upload Gateway Meter List -->
	<div class="modal fade" id="UploadGatewayMeterModal" tabindex="-1">
                <div class="modal-dialog modal-xl">
                  <div class="modal-content">
                    <div class="modal-header modal-header_form">
                      <h5 class="modal-title">Upload Meter List</h5>
					  <div class="btn-group" role="group" aria-label="Basic outlined example">		
						<!--<button type="button" class="btn btn-info bi bi-question navbar_icon" data-bs-toggle="modal" data-bs-target="#GatewayManual" id="manualbtn"></button>-->
						<button type="button" class="btn btn-danger bi bi-x-circle navbar_icon" data-bs-dismiss="modal"></button>
					  </div>
                    </div>
                    <div class="modal-body">
					<div class="row">

					<div class="col-lg-4">
							
							<ol class="list-group list-group-numbered">
									<li class="list-group-item d-flex justify-content-between align-items-start">
									  <div class="ms-2 me-auto">
										<div class="fw-bold">Serial Number</div>
										<div id="view_serial_number"></div>
									  </div>
									  
									</li>
									<li class="list-group-item d-flex justify-content-between align-items-start">
									  <div class="ms-2 me-auto">
										<div class="fw-bold">IP Address</div>
										<div id="view_ip_address"></div>
									  </div>
									  
									</li>
									
									<li class="list-group-item d-flex justify-content-between align-items-start">
									  <div class="ms-2 me-auto">
										<div class="fw-bold">MAC Address</div>
										<div id="view_mac_address"></div>
									  </div>
									  
									<li class="list-group-item d-flex justify-content-between align-items-start">
									  <div class="ms-2 me-auto">
										<div class="fw-bold">Connection Type</div>
										<div id="view_connection_type"></div>
									  </div>
									  
									</li>
									<li class="list-group-item d-flex justify-content-between align-items-start">
									  <div class="ms-2 me-auto">
										<div class="fw-bold">Location</div>
										<div id="view_physical_location"></div>
									  </div>
									  
									</li>
									<li class="list-group-item d-flex justify-content-between align-items-start">
									  <div class="ms-2 me-auto">
										<div class="fw-bold">IDF Name</div>
										<div id="view_idf_name"></div>
									  </div>
									  
									</li>
									
									<li class="list-group-item d-flex justify-content-between align-items-start">
									  <div class="ms-2 me-auto">
										<div class="fw-bold">Switch</div>
										<div id="view_idf_switch"></div>
									  </div>
									  
									</li>
									
									<li class="list-group-item d-flex justify-content-between align-items-start">
									  <div class="ms-2 me-auto">
										<div class="fw-bold">Port</div>
										<div id="view_idf_port"></div>
									  </div>
									  
									</li>
									
									<li class="list-group-item d-flex justify-content-between align-items-start">
									  <div class="ms-2 me-auto">
										<div class="fw-bold">Description</div>
										<div id="view_gateway_description"></div>
									  </div>
									  
									</li>
									
								  </ol>
					</div>
					<div class="col-lg-8">
					
							<div class="card-0">
							<div class="card-body">  
									
									<span id="message"></span>
									   <form id="sample_form" method="POST" enctype="multipart/form-data" class="form-horizontal">
									 	@csrf				   
					
										<div class="row mb-2">
										
										  <div class="input-group mb-3">
											  <input class="form-control" type="file" name="csv_file" id="csv_file" />
											  <input type="hidden" name="import_gateway_idx" id="import_gateway_idx" value="" />
											  <input type="hidden" name="import_gateway_site_idx" id="import_gateway_site_idx" value="{{ $SiteData[0]->site_id }}" />
											   <input type="hidden" name="import_gateway_site_code" id="import_gateway_site_code" value="{{ $SiteData[0]->building_code }}" />
											  <input type="submit" name="import" id="import" class="btn btn-success btn-sm bi bi-save-fill navbar_icon" value="Import" />
											  <span class="valid-feedback" id="csv_fileError"></span>					 
										   </div>
										<!-- <span class="valid-feedback" id="csv_fileError"></span>-->
										</div>
										
									   </form>
									   
									<div id="loading_data" style="display:none;">
									<div class="spinner-border text-success" role="status">
										<span class="visually-hidden">Please wait...</span>
									</div>
									</div>
							
							
							</div>
							</div>

					</div>
					</div>
					
					</div>

					<!-- End Multi Columns Form -->
                  </div>
                </div>
              </div>	
