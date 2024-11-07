
<!--<br>
<div class="row mb-1">
		<div class="col-sm-8">
		
		</div>
		<div class="col-sm-4">
			<div class="form-floating mb-1">
				<select class="form-select form-control" id="EERoomFilter_meter" name="EERoomFilter_meter">
					<option value="">Show All</option>
				</select>
				<label for="EERoomFilter_meter" title="EE Room Filter">Area/EE room</label>
			</div>
		</div>
		
</div>
-->		

									<div class="table-responsive">
										<table class="table dataTable display nowrap cell-border" id="meterlist" width="100%" cellspacing="0">
											<thead>
												<tr>
													<th class="all">#</th>
													<th title="Meter Serial Number" nowrap class="all">Meter Description</th>
													<th title="Meter Tagging/Tenant/Client Name" nowrap class="all">Name/Tagging</th>													
													<th title="Last Reading Date" nowrap class="">Last Reading Date</th>
													<th title="Active/Inactive" class="all">Status</th>
													<th title="View/Edit/Delete" class="all">Action</th>
													<th title="The Gateway Serial Number of Meter" class="none">Gateway Serial Number: </th>
													<th title="Location Code" class="none">Location Code: </th>
													<th title="Location Description" class="none">Location Description: </th>
													<th title="Role : Tenant Meter/Tenant, Spare Meter, CUSA or Check Meter" nowrap class="none">Meter Role: </th>
													<th title="" class="none" title="Meter Configuration file Assigned for Meter">Configuration File: </th>
													<th title="" class="none" title="Meter Alternate Address, This will serve as the Identifier of the Gateway to Read the Meters">Alternate Address: </th>
													<th title="" class="none">Remarks: </th>
												</tr>
											</thead>				
											
											<tbody>
												
											</tbody>
											
											
											
										</table>
										
									</div>			
						
	<!--Modal to Create Meter-->
	<div class="modal fade" id="CreateMeterModal" tabindex="-1">
	
                <div class="modal-dialog modal-lg">
                  <div class="modal-content">
                    <div class="modal-header modal-header_form">
                      <h5 class="modal-title">Create Meter</h5>
					  <div class="btn-group" role="group" aria-label="Basic outlined example">		
						<!--<button type="button" class="btn btn-info bi bi-question navbar_icon" data-bs-toggle="modal" data-bs-target="#MeterManual" id="manualbtn"></button>-->
						<button type="button" class="btn btn-danger bi bi-x-circle navbar_icon" data-bs-dismiss="modal"></button>
					  </div>
                    </div>
					<div class="modal-body">
					
					<form class="g-2 needs-validation" id="createmeterform">
					
					<div class="row mb-1">
						
						<div class="col-sm-8">
						
							<div class="form-floating mb-1">
							  <input type="text" class="form-control" name="meter_name" id="meter_name" value="" required placeholder="Meter Description">
							  <label for="meter_name"title="Meter Serial Number/Meter Description on SAP">Meter Description</label>
							  <span class="valid-feedback" id="meter_descriptionError"></span>	
							 </div>
								
						</div>
						
						<div class="col-sm-4">
							<div class="form-floating mb-1">
							  <input class="form-control" list="rtu_sn_number" name="rtu_sn_number" id="rtu_sn_number_id" required autocomplete="off" value="" placeholder="Gateway Serial #">
								<datalist id="rtu_sn_number">
								<option> </option>
									
								</datalist>
							  <label for="rtu_sn_number_id" class="col-sm-3 col-form-label">Gateway Serial #</label>
							  <span class="valid-feedback" id="rtu_sn_number_idError"></span>	
							 </div>	
						</div>
						
					</div>

					<div class="row mb-1">
					
						<div class="col-sm-8">
							<div class="form-floating mb-1">
							  <input type="text" class="form-control" placeholder="Name/Tagging" name="customer_name" id="customer_name">
							  <label for="customer_name" title="Meter Serial Number/Meter Description on SAP">Name/Tagging</label>
							  <span class="valid-feedback" id="customer_nameError"></span>	
							 </div>
						</div>
						
						<div class="col-sm-4">
							<div class="form-floating mb-1">
							  <select class="form-select form-control" id="meter_role" name="meter_role">
											<option value="Tenant Meter" title="Tenant Meter">Tenant Meter</option>
											<option value="Spare Meter">Spare Meter</option>
											<option value="CUSA">CUSA</option>
											<option value="Check Meter">Check Meter</option>
							  </select>
							  <label for="meter_role"title="Meter Role">Role</label>
							 </div>	
						</div>
						
					</div>
					
					<div class="row mb-1">
					
						<div class="col-sm-8">
							<div class="form-floating mb-1">
							  <input class="form-control" list="meter_model" name="meter_model" id="meter_model_id" required autocomplete="off" value="" placeholder="Configuration File">
								<datalist id="meter_model">
									@foreach ($configuration_file_data as $configuration_file_cols)
										<option label="{{$configuration_file_cols->config_file}}" data-id="{{$configuration_file_cols->config_id}}" value="{{$configuration_file_cols->config_file}}">
									@endforeach
								</datalist>
							  <label for="meter_model" title="Configuration File">Configuration File</label>
							  <span class="valid-feedback" id="meter_model_idError"></span>	
							 </div>
						</div>
						
						<div class="col-sm-4">
							<div class="input-group mb-1">
							<span class="input-group-text"><input class="form-check-input" type="checkbox" name="meter_name_addressable" id="meter_name_addressable"></span>
							  <div class="form-floating">
								  <input class="form-control" name="meter_default_name" id="meter_default_name" autocomplete="off" placeholder="Alternate Address" required disabled>
								  <label for="meter_default_name">Alternate Address</label>
								  
							  </div>
								<span class="valid-feedback" id="meter_default_nameError"></span>	
							</div>
						</div>
						
					</div>		
						
					<div class="row mb-1">
						
						<div class="col-sm-4">
							<div class="form-floating mb-1">
							  <input type="number" class="form-control " name="meter_multiplier" id="meter_multiplier" value="1" min="1" required placeholder="Meter Multiplier">
							  <label for="meter_multiplier" class="col-sm-3 col-form-label">Multiplier</label>
							  <span class="valid-feedback" id="meter_multiplierError"></span>
							 </div>
						</div>
						
						<div class="col-sm-4">
							<div class="form-floating mb-1">
							  <input type="text" class="form-control " name="meter_brand" id="meter_brand" placeholder="Meter Brand">
							  <label for="meter_brand" title="Meter Brand">Meter Brand</label>
							  <span class="valid-feedback" id="meter_brandError"></span>
							 </div>
						</div>
						
						<div class="col-sm-4">
							<div class="form-floating mb-1">
							  <input type="text" class="form-control " name="meter_type" id="meter_type" placeholder="Meter Type">
							  <label for="meter_type" title="Meter Type">Meter Type</label>
							  <span class="valid-feedback" id="meter_typeError"></span>
							 </div>	
						</div>
						
					</div>
					
					<div class="row mb-1">
						
						<div class="col-sm-12">
							<div class="form-floating mb-1">
							  <input class="form-control" list="location" name="location" id="location_id" required autocomplete="off" value="" placeholder="Area/EE Room">
									<datalist id="location">
										<option> </option>
									</datalist>
							  <label for="building_id" class="col-sm-3 col-form-label">Area/EE Room</label>
							  <span class="valid-feedback" id="location_meterError"></span>
							 </div>	
						</div>
						
					</div>					
					
					
					<div class="row mb-1">
						
						<div class="col-sm-6">
							<div class="form-floating mb-1">
							 <select class="form-control form-select " name="meter_status" id="meter_status">
										<option value="ACTIVE">Active</option>
										<option value="INACTIVE">Inactive</option>
									</select>
							  <label for="meter_status" class="col-sm-3 col-form-label">Status</label>
							 </div>
						</div>
						
						<div class="col-sm-6">
							<div class="form-floating mb-1">
							  <input type="text" class="form-control " name="meter_remarks" id="meter_remarks" value="" placeholder="Remarks">
							  <label for="meter_remarks" class="col-sm-3 col-form-label">Remarks</label>
							 </div>
						</div>
						
					</div>									
					
					
					
					</div>
					
					<div class="modal-footer modal-footer_form">
						
						  <button type="submit" class="btn btn-success btn-sm bi bi-save-fill navbar_icon" id="save-meter"> Submit</button>
						  <button type="submit" class="btn btn-primary btn-sm bi bi-backspace-fill navbar_icon" id="clear-meter" onclick="ResetFormAddMeter()"> Reset</button>
						  
					</div>
					</form>
                </div>
              </div>	
			</div>
				   
	<!--Modal to Create Meter-->
	<div class="modal fade" id="UpdateMeterModal" tabindex="-1">
                <div class="modal-dialog modal-lg">
                  <div class="modal-content">
                    <div class="modal-header modal-header_form">
                      <h5 class="modal-title">Update Meter</h5>
					  <div class="btn-group" role="group" aria-label="Basic outlined example">		
						<!--<button type="button" class="btn btn-info bi bi-question navbar_icon" data-bs-toggle="modal" data-bs-target="#MeterManual" id="manualbtn"></button>-->
						<button type="button" class="btn btn-danger bi bi-x-circle navbar_icon" data-bs-dismiss="modal"></button>
					  </div>
                    </div>
                    <div class="modal-body">

					  <form class="g-2 needs-validation" id="updatemeterform">
					  
					<div class="row mb-1">
						
						<div class="col-sm-8">
						
							<div class="form-floating mb-1">
							  <input type="text" class="form-control" name="update_meter_name" id="update_meter_name" value="" required placeholder="Meter Description">
							  <label for="meter_name"title="Meter Serial Number/Meter Description on SAP">Meter Description</label>
							  <span class="valid-feedback" id="update_meter_descriptionError"></span>	
							 </div>
								
						</div>
						
						<div class="col-sm-4">
							<div class="form-floating mb-1">
							  <input class="form-control" list="update_rtu_sn_number" name="update_rtu_sn_number" id="update_rtu_sn_number_id" required autocomplete="off" value="" placeholder="Gateway Serial #">
								<datalist id="update_rtu_sn_number">
									<option> </option>
								</datalist>
							  <label for="update_rtu_sn_number_id" class="col-sm-3 col-form-label">Gateway Serial #</label>
							  <span class="valid-feedback" id="update_rtu_sn_number_idError"></span>	
							 </div>	
						</div>
						
					</div>

					<div class="row mb-1">
					
						<div class="col-sm-8">
							<div class="form-floating mb-1">
							  <input type="text" class="form-control" placeholder="Name/Tagging" name="update_customer_name" id="update_customer_name">
							  <label for="update_customer_name" title="Meter Serial Number/Meter Description on SAP">Name/Tagging</label>
							  <span class="valid-feedback" id="update_customer_nameError"></span>	
							 </div>
						</div>
						
						<div class="col-sm-4">
							<div class="form-floating mb-1">
							  <select class="form-select form-control" id="update_meter_role" name="update_meter_role">
											<option value="Tenant Meter" title="Tenant Meter">Tenant Meter</option>
											<option value="Spare Meter">Spare Meter</option>
											<option value="CUSA">CUSA</option>
											<option value="Check Meter">Check Meter</option>
							  </select>
							  <label for="update_meter_role" title="Meter Role">Role</label>
							 </div>	
						</div>
						
					</div>
					
					<div class="row mb-1">
					
						<div class="col-sm-8">
							<div class="form-floating mb-1">
							  <input class="form-control" list="update_meter_model" name="update_meter_model" id="update_meter_model_id" required autocomplete="off" value="" placeholder="Configuration File">
								<datalist id="update_meter_model">
									@foreach ($configuration_file_data as $configuration_file_cols)
										<option label="{{$configuration_file_cols->config_file}}" data-id="{{$configuration_file_cols->config_id}}" value="{{$configuration_file_cols->config_file}}">
									@endforeach
								</datalist>
							  <label for="update_meter_model" title="Configuration File">Configuration File</label>
							  <span class="valid-feedback" id="update_meter_model_idError"></span>	
							 </div>
						</div>
						
						<div class="col-sm-4">
							<div class="input-group mb-1">
							<span class="input-group-text" style="padding-bottom: 10px;height: 58px;">
								<input class="form-check-input" type="checkbox" name="update_meter_name_addressable" id="update_meter_name_addressable">
							</span>
							  <div class="form-floating">
								  <input class="form-control" name="update_meter_default_name" id="update_meter_default_name" autocomplete="off" placeholder="Alternate Address" required>
								  <label for="update_meter_default_name">Alternate Address</label>
								  
							  </div>
							  <span class="row valid-feedback" id="update_meter_default_nameError"></span>	
							</div>
						</div>
						
					</div>		
						
					<div class="row mb-1">
						
						<div class="col-sm-4">
							<div class="form-floating mb-1">
							  <input type="number" class="form-control " name="update_meter_multiplier" id="update_meter_multiplier" value="1" min="1" required placeholder="Meter Multiplier">
							  <label for="update_meter_multiplier" class="col-sm-3 col-form-label">Multiplier</label>
							  <span class="valid-feedback" id="update_meter_multiplierError"></span>
							 </div>
						</div>
						
						<div class="col-sm-4">
							<div class="form-floating mb-1">
							  <input type="text" class="form-control " name="update_meter_brand" id="update_meter_brand" placeholder="Meter Brand">
							  <label for="update_meter_brand" title="Meter Brand">Meter Brand</label>
							  <span class="valid-feedback" id="update_meter_brandError"></span>
							 </div>
						</div>
						
						<div class="col-sm-4">
							<div class="form-floating mb-1">
							  <input type="text" class="form-control " name="update_meter_type" id="update_meter_type" placeholder="Meter Type">
							  <label for="update_meter_type" title="Meter Type">Meter Type</label>
							  <span class="valid-feedback" id="update_meter_typeError"></span>
							 </div>	
						</div>
						
					</div>
					
					<div class="row mb-1">
						
						<div class="col-sm-12">
							<div class="form-floating mb-1">
							  <input class="form-control" list="update_location" name="update_location" id="update_location_id" required autocomplete="off" value="" placeholder="Area/EE Room">
									<datalist id="update_location">
										<option> </option>
									</datalist>
							  <label for="update_building_id" class="col-sm-3 col-form-label">Area/EE Room</label>
							  <span class="valid-feedback" id="update_location_meterError"></span>
							 </div>	
						</div>
						
					</div>					
					
					
					<div class="row mb-1">
						
						<div class="col-sm-6">
							<div class="form-floating mb-1">
							 <select class="form-control form-select " name="update_meter_status" id="update_meter_status">
										<option value="ACTIVE">Active</option>
										<option value="INACTIVE">Inactive</option>
									</select>
							  <label for="update_meter_status" class="col-sm-3 col-form-label">Status</label>
							 </div>
						</div>
						
						<div class="col-sm-6">
							<div class="form-floating mb-1">
							  <input type="text" class="form-control " name="update_meter_remarks" id="update_meter_remarks" value="" placeholder="Remarks">
							  <label for="update_meter_remarks" class="col-sm-3 col-form-label">Remarks</label>
							 </div>
						</div>
						
					</div>
					
					
					</form>
					
					</div>
					
                    <div class="modal-footer modal-footer_form">
							<div id="loading_data_updatemeter" style="display:none;">
									<button class="btn btn-light btn-sm" type="button" disabled="">
									<span class="spinner-grow spinner-grow-sm text-danger" role="status" aria-hidden="true"></span>
									Checking for changes...
									</button>
							</div>
							<button type="submit" class="btn btn-success btn-sm bi bi-save-fill navbar_icon" id="update-meter"> Submit</button>
					</div><!-- End Multi Columns Form -->
					
                 </div>
                </div>
              </div>	
			</div>
			
	<!-- Meter Delete Modal-->
    <div class="modal fade" id="MeterDeleteModal" tabindex="-1" role="dialog" aria-hidden="true">
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
				<th width="30%">Meter Description:</th>
				<td width="70%"><span id="meter_name_delete"></span></td>
				</tr>
				<tr>
				<th width="30%">Name/Tagging:</th>
				<td width="70%"><span id="customer_name_delete"></span></td>
				</tr>
				<tr>
				<th width="30%">Status:</th>
				<td width="70%"><span id="meter_status_delete"></span></td>
				</tr>
				<tr>
				<th width="30%">Area/EE Room:</th>
				<td width="70%"><span id="meter_location_delete"></span></td>
				</tr>
				</table>
				</div>
				
                <div class="modal-footer footer_modal_bg">
                   
					<button type="button" class="btn btn-danger" data-bs-dismiss="modal" id="deleteMeterConfirmed" value=""><i class="bi bi-trash3 navbar_icon"></i> Delete</button>
					 <button type="button" class="btn btn-primary" data-bs-dismiss="modal"><i class="bi bi-x-circle navbar_icon"></i> Cancel</button>
                  
                </div>
            </div>
        </div>
    </div>	

	<!-- Meter Confirmed Deleted Modal-->
    <div class="modal fade" id="MeterDeleteModalConfirmed" tabindex="-1" role="dialog" aria-hidden="true">
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
				<th width="30%">Meter Description:</th>
				<td width="70%"><span id="meter_name_delete_confirmed"></span></td>
				</tr>
				<tr>
				<th width="30%">Name/Tagging:</th>
				<td width="70%"><span id="customer_name_delete_confirmed"></span></td>
				</tr>
				<tr>
				<th width="30%">Status:</th>
				<td width="70%"><span id="meter_status_delete_confirmed"></span></td>
				</tr>
				<tr>
				<th width="30%">Area/EE Room:</th>
				<td width="70%"><span id="meter_location_delete_confirmed"></span></td>
				</tr>
				</table>
				
				</div>
                <div class="modal-footer footer_modal_bg">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal"><i class="bi bi-x-circle navbar_icon"></i> Close</button>
					     
                </div>
            </div>
        </div>
    </div>			
		