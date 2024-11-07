

									<div class="table-responsive">
										<table class="table table-bordered dataTable" id="meterlist" width="100%" cellspacing="0">
											<thead>
												<tr>
													<th class="all">#</th>
													<!--<th title="SAP Measuring Point" nowrap class="">Measuring Point</th>-->
													<th title="Meter Serial Number" nowrap class="all">Meter Description</th>
													<th title="Meter Tagging/Tenant/Client Name" nowrap class="all">Name/Tagging</th>													
													<th title="Last Reading Date" nowrap class="">Last Reading Date</th>
													<th title="Active/Inactive" class="">Status</th>
													<th title="The Gateway Serial Number of Meter" class="none">Gateway Serial Number: </th>
													<th title="Building Code" class="none">Building Code: </th>
													<th title="Building Name" class="none">Building Name: </th>
													<th title="Location Code" class="none">Location Code: </th>
													<th title="Location Description" class="none">Location Description: </th>
													<th title="Role : Tenant Meter/Tenant, Spare Meter, CUSA or Check Meter" nowrap class="none">Meter Role: </th>
													<th title="" class="none">Configuration File: </th>
													<th title="" class="none">Alternate Address: </th>
													<th title="" class="none">Remarks: </th>
													<th title="View/Edit/Delete" class="">Action</th>
												</tr>
											</thead>				
											
											<tbody>
												
											</tbody>
											
											<tfoot>
												<tr>
													<th class="all">#</th>
													<!--<th title="SAP Measuring Point" nowrap class="">Measuring Point</th>-->
													<th title="Meter Serial Number" nowrap class="all">Meter Description</th>
													<th title="Meter Tagging/Tenant/Client Name" nowrap class="all">Name/Tagging</th>													
													<th title="Last Reading Date" nowrap class="">Last Reading Date</th>
													<th title="Active/Inactive" class="">Status</th>
													<th title="The Gateway Serial Number of Meter" class="none">Gateway Serial Number: </th>
													<th title="Building Code" class="none">Building Code: </th>
													<th title="Building Name" class="none">Building Name: </th>
													<th title="Location Code" class="none">Location Code: </th>
													<th title="Location Description" class="none">Location Description: </th>
													<th title="Role : Tenant Meter/Tenant, Spare Meter, CUSA or Check Meter" nowrap class="none">Meter Role: </th>
													<th title="" class="none">Configuration File: </th>
													<th title="" class="none">Alternate Address: </th>
													<th title="" class="none">Remarks: </th>
													<th title="View/Edit/Delete" class="">Action</th>
												</tr>
											</tfoot>
											
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
					
					
					<div class="row mb-2">
						
						<div class="col-sm-8">
						
							<div class="form-floating mb-3">
							  <input type="text" class="form-control" name="meter_name" id="meter_name" value="" required placeholder="Meter Description">
							  <label for="meter_name"title="Meter Serial Number/Meter Description on SAP">Meter Description</label>
							  <span class="valid-feedback" id="meter_descriptionError"></span>	
							 </div>
								
						</div>
						
						<div class="col-sm-4">
							<div class="form-floating mb-3">
							  <input class="form-control" list="rtu_sn_number" name="rtu_sn_number" id="rtu_sn_number_id" required autocomplete="off" value="" placeholder="Gateway Serial #">
								<datalist id="rtu_sn_number">
									@foreach ($gateway_data as $gateway_data_cols)
										<option label="IP Address : {{$gateway_data_cols->phone_no_or_ip_address}}" data-id="{{$gateway_data_cols->id}}" value="{{$gateway_data_cols->rtu_sn_number}}">
									@endforeach
								</datalist>
							  <label for="rtu_sn_number_id" class="col-sm-3 col-form-label">Gateway Serial #</label>
							  <span class="valid-feedback" id="rtu_sn_number_idError"></span>	
							 </div>	
						</div>
						
					</div>
					
					<!--
					<div class="row mb-2">
						  <label for="rtu_sn_number_id" class="col-sm-3 col-form-label">Gateway Serial #</label>
						  <div class="col-sm-9">
						   
						   <input class="form-control" list="rtu_sn_number" name="rtu_sn_number" id="rtu_sn_number_id" required autocomplete="off" value="">
								<datalist id="rtu_sn_number">
									@foreach ($gateway_data as $gateway_data_cols)
										<option label="IP Address : {{$gateway_data_cols->phone_no_or_ip_address}}" data-id="{{$gateway_data_cols->id}}" value="{{$gateway_data_cols->rtu_sn_number}}">
									@endforeach
								</datalist>
							
							<span class="valid-feedback" id="rtu_sn_number_idError"></span>	
						  </div>
						</div>
					
						<div class="row mb-2">
							<label for="meter_name" class="col-sm-3 col-form-label" title="Meter Serial Number/Meter Description on SAP">Meter Description</label>
							<div class="col-sm-9">
									<div class="input-group">	
										<input type="text" class="form-control" name="meter_name" id="meter_name" value="" required>
										<span class="input-group-text" id="inputGroupPrepend2">Role</span>
										<select class="form-select form-control" id="meter_role" name="meter_role">
											<option value="Tenant Meter" title="Tenant Meter">Tenant Meter</option>
											<option value="Spare Meter">Spare Meter</option>
											<option value="CUSA">CUSA</option>
											<option value="Check Meter">Check Meter</option>
										</select>	
										<span class="valid-feedback" id="meter_descriptionError"></span>
									</div>	  
							</div>
						</div>	



						
					-->
					
					<div class="row mb-2">
					
						<div class="col-sm-8">
							<div class="form-floating mb-3">
							  <input type="text" class="form-control" placeholder="Name/Tagging" name="customer_name" id="customer_name">
							  <label for="customer_name" title="Meter Serial Number/Meter Description on SAP">Name/Tagging</label>
							  <span class="valid-feedback" id="customer_nameError"></span>	
							 </div>
						</div>
						
						<div class="col-sm-4">
							<div class="form-floating mb-3">
							  <select class="form-select form-control" id="meter_role" name="meter_role">
											<!--<option selected="" disabled="" value="">Choose Meter Role</option>-->
											<option value="Tenant Meter" title="Tenant Meter">Tenant Meter</option>
											<option value="Spare Meter">Spare Meter</option>
											<option value="CUSA">CUSA</option>
											<option value="Check Meter">Check Meter</option>
							  </select>
							  <label for="meter_role"title="Meter Role">Role</label>
							 </div>	
						</div>
						
					</div>
					
					
							<!--
							<div class="row mb-2">
							  <label for="customer_name" class="col-sm-3 col-form-label">Name/Tagging</label>
							  
								<div class="col-sm-9">
								  <div class="input-group">
										<textarea class="form-control" placeholder="" name="customer_name" id="customer_name" style="height: 38px;"></textarea>
										<span class="valid-feedback" id="customer_nameError"></span>			
								  </div>
								</div>
							</div>
							-->
							
					<div class="row mb-2">
					
						<div class="col-sm-8">
							<div class="form-floating mb-3">
							  <input class="form-control" list="meter_model" name="meter_model" id="meter_model_id" required autocomplete="off" value="" placeholder="Configuration File">
								<datalist id="meter_model">
									@foreach ($configuration_file_data as $configuration_file_cols)
										<option label="{{$configuration_file_cols->config_file}}" data-id="{{$configuration_file_cols->id}}" value="{{$configuration_file_cols->config_file}}">
									@endforeach
								</datalist>
							  <label for="meter_model" title="Configuration File">Configuration File</label>
							  <span class="valid-feedback" id="meter_model_idError"></span>	
							 </div>
						</div>
						
						<div class="col-sm-4">
							<div class="input-group mb-3">
							<span class="input-group-text"><input class="form-check-input" type="checkbox" name="meter_name_addressable" id="meter_name_addressable"></span>
							  <div class="form-floating">
								  <input class="form-control" name="meter_default_name" id="meter_default_name" autocomplete="off" placeholder="Alternate Address">
								  <label for="meter_default_name">Alternate Address</label>
							  </div>
							</div>
						</div>
						
					</div>		
							
							<!--
						  <div class="row mb-2">
						  <label for="meter_model_id" class="col-sm-3 col-form-label">Configuration File</label>
						  <div class="col-sm-9">
						  <input class="form-control" list="meter_model" name="meter_model" id="meter_model_id" required autocomplete="off" value="">
								<datalist id="meter_model">
									@foreach ($configuration_file_data as $configuration_file_cols)
										<option label="{{$configuration_file_cols->config_file}}" data-id="{{$configuration_file_cols->id}}" value="{{$configuration_file_cols->config_file}}">
									@endforeach
								</datalist>
							<span class="valid-feedback" id="meter_model_idError"></span>	
						  </div>
						</div>	
						

					<div class="row mb-2">
						<div class="col-sm-12">
							<div class="input-group mb-3">
							<span class="input-group-text"><input class="form-check-input" type="checkbox" name="meter_name_addressable" id="meter_name_addressable"></span>
							  <div class="form-floating">
								  <input class="form-control" name="meter_default_name" id="meter_default_name" autocomplete="off" placeholder="Alternate Address">
								  <label for="meter_default_name">Alternate Address</label>
							  </div>
							</div>
						</div>
					</div>	
						-->
						<!--
						
						<div class="row mb-2">
						  <label for="meter_name_addressable" class="col-sm-3 col-form-label">Alternate Address</label>
						  <div class="col-sm-9">
							<div class="input-group">
								<span class="input-group-text" id="">
								<input class="form-check-input" type="checkbox" name="meter_name_addressable" id="meter_name_addressable">
								</span>
								<input type="text" class="form-control" name="meter_default_name" id="meter_default_name" aria-describedby="">
							</div>
						  </div>
						</div>-->
						
						
					<div class="row mb-2">
						
						<div class="col-sm-4">
							<div class="form-floating mb-3">
							  <input type="text" class="form-control " name="meter_multiplier" id="meter_multiplier" value="1" required placeholder="Meter Multiplier">
							  <label for="meter_multiplier" class="col-sm-3 col-form-label">Multiplier</label>
							  <span class="valid-feedback" id="meter_multiplierError"></span>
							 </div>
						</div>
						
						<div class="col-sm-4">
							<div class="form-floating mb-3">
							  <input type="text" class="form-control " name="meter_brand" id="meter_brand" placeholder="Meter Brand">
							  <label for="meter_brand" title="Meter Brand">Meter Brand</label>
							  <span class="valid-feedback" id="meter_brandError"></span>
							 </div>
						</div>
						
						<div class="col-sm-4">
							<div class="form-floating mb-3">
							  <input type="text" class="form-control " name="meter_type" id="meter_type" placeholder="Meter Type">
							  <label for="meter_type" title="Meter Type">Meter Type</label>
							  <span class="valid-feedback" id="meter_typeError"></span>
							 </div>	
						</div>
						
					</div>
						
						<!--
						<div class="row mb-2">
						  <label for="meter_type" class="col-sm-3 col-form-label">Meter Type</label>
						  <div class="col-sm-9">
							<input type="text" class="form-control " name="meter_type" id="meter_type" value="">
							<span class="valid-feedback" id="meter_typeError"></span>
						  </div>
						</div>
--><!--
						<div class="row mb-2">
						  <label for="meter_multiplier" class="col-sm-3 col-form-label">Multiplier</label>
						  <div class="col-sm-9">
							<input type="text" class="form-control " name="meter_multiplier" id="meter_multiplier" value="1" required>
							<span class="valid-feedback" id="meter_multiplierError"></span>
						  </div>
						</div>
					
						<div class="row mb-2">
						  <label for="rtu_sn_number_id" class="col-sm-3 col-form-label">Gateway Serial #</label>
						  <div class="col-sm-9">
						   
						   <input class="form-control" list="rtu_sn_number" name="rtu_sn_number" id="rtu_sn_number_id" required autocomplete="off" value="">
								<datalist id="rtu_sn_number">
									@foreach ($gateway_data as $gateway_data_cols)
										<option label="IP Address : {{$gateway_data_cols->phone_no_or_ip_address}}" data-id="{{$gateway_data_cols->id}}" value="{{$gateway_data_cols->rtu_sn_number}}">
									@endforeach
								</datalist>
							
							<span class="valid-feedback" id="rtu_sn_number_idError"></span>	
						  </div>
						</div>
					-->
					
					
					<div class="row mb-2">
					
						<div class="col-sm-6">
							<div class="form-floating mb-3">
							  <input class="form-control" list="building" name="building" id="building_id" required autocomplete="off" value="" placeholder="Building">
									<datalist id="building">
										@foreach ($building_data as $building_data_cols)
											<option label="Description : {{$building_data_cols->building_code}} | {{$building_data_cols->building_description}}" data-id="{{$building_data_cols->id}}" value="{{$building_data_cols->building_code}}">
										@endforeach
									</datalist>
							  <label for="building_id" class="col-sm-3 col-form-label">Building</label>
							  <span class="valid-feedback" id="meter_buildingError"></span>	
							 </div>
						</div>
						
						<div class="col-sm-6">
							<div class="form-floating mb-3">
							  <input class="form-control" list="location" name="location" id="location_id" required autocomplete="off" value="" placeholder="Area/EE Room">
									<datalist id="location">
										@foreach ($location_data as $location_data_cols)
											<option label="Description {{$location_data_cols->location_description}}" data-id="{{$location_data_cols->id}}" value="{{$location_data_cols->location_description}}">
										@endforeach
									</datalist>
							  <label for="building_id" class="col-sm-3 col-form-label">Area/EE Room</label>
							  <span class="valid-feedback" id="location_meterError"></span>
							 </div>	
						</div>
						
					</div>					
					
					
					<!--
						<div class="row mb-2">
						  <label for="building_id" class="col-sm-3 col-form-label">Building</label>
						  <div class="col-sm-9">
						   
						   <input class="form-control" list="building" name="building" id="building_id" required autocomplete="off" value="">
									<datalist id="building">
										@foreach ($building_data as $building_data_cols)
											<option label="Description : {{$building_data_cols->building_code}} | {{$building_data_cols->building_description}}" data-id="{{$building_data_cols->id}}" value="{{$building_data_cols->building_code}}">
										@endforeach
									</datalist>
							
							<span class="valid-feedback" id="meter_buildingError"></span>	
						  </div>
						</div>
						
						<div class="row mb-2">
						  <label for="building_id" class="col-sm-3 col-form-label">Area/EE Room</label>
						  <div class="col-sm-9">
						   
							<input class="form-control" list="location" name="location" id="location_id" required autocomplete="off" value="">
									<datalist id="location">
										@foreach ($location_data as $location_data_cols)
											<option label="Description {{$location_data_cols->location_description}}" data-id="{{$location_data_cols->id}}" value="{{$location_data_cols->location_description}}">
										@endforeach
									</datalist>
							
							<span class="valid-feedback" id="location_meterError"></span>	
						  </div>
						</div>
						-->
						<div class="row mb-2"> 
						
						  <label for="meter_status" class="col-sm-3 col-form-label">Status</label>
						  <div class="col-sm-9">
				
							  <div class="input-group">
							  
									<select class="form-control form-select " name="meter_status" id="meter_status">
										<option value="ACTIVE">Active</option>
										<option value="INACTIVE">Inactive</option>
									</select>
									
									<span class="input-group-text" id="inputGroupPrepend2">Remarks</span>
									
									<input type="text" class="form-control " name="meter_remarks" id="meter_remarks" value="">
							  
							  </div>
						
						  </div>									
						
						</div>	
						
						
                    <div class="modal-footer modal-footer_form">
						
						  <button type="submit" class="btn btn-success btn-sm bi bi-save-fill navbar_icon" id="save-meter"> Submit</button>
						  <button type="reset" class="btn btn-primary btn-sm bi bi-backspace-fill navbar_icon" id="clear-meter"> Reset</button>
						  
					</div>
						</form>
					</div>
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
					  
						<div class="row mb-2">
						  <label for="update_meter_name" class="col-sm-3 col-form-label" title="Meter Serial Number/Meter Description on SAP">Meter Description</label>
						  <div class="col-sm-9">
						  <div class="input-group">	
									<input type="text" class="form-control" name="update_meter_name" id="update_meter_name" value="" required>
									<span class="input-group-text" id="inputGroupPrepend2">Role</span>
									<select class="form-select form-control" id="update_meter_role" name="update_meter_role">
										<option value="Tenant Meter" title="Tenant Meter">Tenant Meter</option>
										<option value="Spare Meter">Spare Meter</option>
										<option value="CUSA">CUSA</option>
										<option value="Check Meter">Check Meter</option>
									</select>	
									<span class="valid-feedback" id="update_meter_descriptionError"></span>		
							  </div>	
						  </div>
						</div>
						
						<div class="row mb-2">
						  <label for="update_customer_name" class="col-sm-3 col-form-label">Name/Tagging</label>
						  <div class="col-sm-9">
							  <div class="input-group">
									<textarea class="form-control" placeholder="" name="update_customer_name" id="update_customer_name" style="height: 38px;"></textarea>
									<span class="valid-feedback" id="update_customer_nameError"></span>			
							  </div>
						  </div>
						  
						</div>	
										
						<div class="row mb-2">
						  <label for="update_meter_model_id" class="col-sm-3 col-form-label">Configuration File</label>
						  <div class="col-sm-9">
						  <input class="form-control" list="update_meter_model" name="update_meter_model" id="update_meter_model_id" required autocomplete="off" value="">
								<datalist id="update_meter_model">
									@foreach ($configuration_file_data as $configuration_file_cols)
										<option label="{{$configuration_file_cols->config_file}}" data-id="{{$configuration_file_cols->id}}" value="{{$configuration_file_cols->config_file}}">
									@endforeach
								</datalist>
							<span class="valid-feedback" id="update_meter_model_idError"></span>	
						  </div>
						</div>	
						
						<div class="row mb-2">
						  <label for="update_meter_name_addressable" class="col-sm-3 col-form-label">Alternate Address</label>
						  <div class="col-sm-9">
							<div class="input-group">
								<span class="input-group-text" id=""><input class="form-check-input" type="checkbox" name="update_meter_name_addressable" id="update_meter_name_addressable"></span>
								<input type="text" class="form-control" name="update_meter_default_name" id="update_meter_default_name" aria-describedby="">
							</div>
						  </div>
						</div>
						
						<div class="row mb-2">
						  <label for="update_meter_type" class="col-sm-3 col-form-label">Meter Type</label>
						  <div class="col-sm-9">
							<input type="text" class="form-control " name="update_meter_type" id="update_meter_type" value="">
							<span class="valid-feedback" id="update_meter_typeError"></span>
						  </div>
						</div>

						<div class="row mb-2">
						  <label for="update_meter_multiplier" class="col-sm-3 col-form-label">Multiplier</label>
						  <div class="col-sm-9">
							<input type="text" class="form-control " name="update_meter_multiplier" id="update_meter_multiplier" value="1" required>
							<span class="valid-feedback" id="update_meter_multiplierError"></span>
						  </div>
						</div>
					
						<div class="row mb-2">
						  <label for="update_rtu_sn_number_id" class="col-sm-3 col-form-label">Gateway Serial #</label>
						  <div class="col-sm-9">
						   
						   <input class="form-control" list="update_rtu_sn_number" name="update_rtu_sn_number" id="update_rtu_sn_number_id" required autocomplete="off" value="">
								<datalist id="update_rtu_sn_number">
									@foreach ($gateway_data as $gateway_data_cols)
										<option label="IP Address : {{$gateway_data_cols->phone_no_or_ip_address}}" data-id="{{$gateway_data_cols->id}}" value="{{$gateway_data_cols->rtu_sn_number}}">
									@endforeach
								</datalist>
							
							<span class="valid-feedback" id="update_rtu_sn_number_idError"></span>	
						  </div>
						</div>
						
						<div class="row mb-2">
						  <label for="update_building_id" class="col-sm-3 col-form-label">Building</label>
						  <div class="col-sm-9">
						   
						   <input class="form-control" list="update_building" name="update_building" id="update_building_id" required autocomplete="off" value="">
									<datalist id="update_building">
										@foreach ($building_data as $building_data_cols)
											<option label="Description : {{$building_data_cols->building_code}} | {{$building_data_cols->building_description}}" data-id="{{$building_data_cols->id}}" value="{{$building_data_cols->building_code}}">
										@endforeach
									</datalist>
							
							<span class="valid-feedback" id="update_meter_buildingError"></span>	
						  </div>
						</div>
						
						<div class="row mb-2">
						  <label for="update_building_id" class="col-sm-3 col-form-label">Area/EE Room</label>
						  <div class="col-sm-9">
						   
							<input class="form-control" list="update_location" name="update_location" id="update_location_id" required autocomplete="off" value="">
									<datalist id="update_location">
										@foreach ($location_data as $location_data_cols)
											<option label="Description {{$location_data_cols->location_description}}" data-id="{{$location_data_cols->id}}" value="{{$location_data_cols->location_description}}">
										@endforeach
									</datalist>
							
							<span class="valid-feedback" id="update_location_meterError"></span>	
						  </div>
						</div>
						
					
						<div class="row mb-2"> 
						
						  <label for="update_meter_status" class="col-sm-3 col-form-label">Status</label>
						  <div class="col-sm-9">
				
							  <div class="input-group">
							  
									<select class="form-control form-select " name="update_meter_status" id="update_meter_status">
										<option value="ACTIVE">Active</option>
										<option value="INACTIVE">Inactive</option>
									</select>
									
									<span class="input-group-text" id="inputGroupPrepend2">Remarks</span>
									
									<input type="text" class="form-control " name="update_meter_remarks" id="update_meter_remarks" value="">
							  
							  </div>
						
						  </div>									
						
						</div>	
						</div>
                    <div class="modal-footer modal-footer_form">
						
						  <button type="submit" class="btn btn-success btn-sm bi bi-save-fill navbar_icon" id="update-meter"> Submit</button>
						  
					</div>
					</form><!-- End Multi Columns Form -->
                
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
				Are you sure you want to Delete <span id="meter_name_info"></span>?
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
				Successfully Deleted <span id="meter_name_info_confirmed"></span>!
				</div>
                <div class="modal-footer footer_modal_bg">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal"><i class="bi bi-x-circle navbar_icon"></i> Close</button>
					     
                </div>
            </div>
        </div>
    </div>			
		