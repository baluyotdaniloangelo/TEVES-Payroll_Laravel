
            <div class="card-body">
			
				<div class="row mb-2">
				
					<div class="col-sm-3">
						<div class="d-flex justify-content-end mb-2" id="">
							<div class="btn-group" role="group" aria-label="Basic outlined " id="building_option" style="">
									<!--<button type="button" class="btn btn-success btn-sm new_item bi bi-plus-circle" data-bs-toggle="modal" data-bs-target="#CreateBuildingModal" title="Add Building"> Building</button>-->
									<button type="button" class="btn btn-success btn-sm new_item bi bi-plus-circle" data-bs-toggle="modal" data-bs-target="#CreateMeterLocationModal" title="Add Area/EE Room" onclick="ResetFormEEroom()"> EE/MC Room</button>
							</div>
						</div>
						
					
						
						<ol class="list-group list-group-numbered" id="eeroomlist">
								<!-- Item Here -->
						</ol>
							
						
			  
					</div>
					
					<div class="col-sm-9">
						
						  <ul class="nav nav-tabs" id="myTab" role="tablist">
                
							<li class="nav-item" role="presentation">
							  <button class="nav-link active bi bi-cpu" id="home-tab" data-bs-toggle="tab" data-bs-target="#home" type="button" role="tab" aria-controls="home" aria-selected="true" onclick="autoAdjustColumns(LoadGatewayPerBuildingList);">&nbsp;Gateway</button>
							</li>
							<li class="nav-item" role="presentation">
							  <button class="nav-link bi bi-speedometer" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button" role="tab" aria-controls="profile" aria-selected="false" tabindex="-1">&nbsp;Meter</button>
							</li>
               
							</ul>
							
						    <div class="tab-content pt-2" id="myTabContent">
							<div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
							<div class="d-flex justify-content-end" id="">
								<div class="btn-group" role="group" aria-label="Basic outlined example" style="margin-top: -50px; position: absolute;">
									<button type="button" class="btn btn-success  btn-sm new_item bi bi-plus-circle form_button_icon" data-bs-toggle="modal" data-bs-target="#CreateGatewayModal" title="Add Gateway" onclick="ResetFormAddGateway()"> Gateway</button>
									<button type="button" class="btn btn-secondary  btn-sm new_item bi bi-arrow-clockwise form_button_icon" onclick="GatewayPerBuilding(0);" title="Reset Gateway List"> Reset List</button>
								</div>					
								</div>
									@include('amr.site_gateway')
							</div>
							
							<div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
							<div class="d-flex justify-content-end" id="">
								<div class="btn-group" role="group" aria-label="Basic outlined example" style="margin-top: -50px; position: absolute;">
									<button type="button" class="btn btn-success btn-sm new_item bi bi-plus-circle form_button_icon" data-bs-toggle="modal" data-bs-target="#CreateMeterModal" title="Add Meter" onclick="ResetFormAddMeter()"> Meter</button>
									<button type="button" class="btn btn-secondary btn-sm new_item bi bi-arrow-clockwise form_button_icon" onclick="MeterPerBuilding(0);" title="Reset Meter List"> Reset List</button>
								</div>					
								</div>
									@include('amr.site_meter')
							</div>
							</div>
							
					
					</div>
				
				</div>		
		
	<!--Modal to Create meterlocation-->
	<div class="modal fade" id="CreateMeterLocationModal" tabindex="-1">
                <div class="modal-dialog">
                  <div class="modal-content">
                    <div class="modal-header modal-header_form">
                      <h5 class="modal-title">Create EE/MC Room</h5>
					  <div class="btn-group" role="group" aria-label="Basic outlined example">		
						<!--<button type="button" class="btn btn-info bi bi-question navbar_icon" data-bs-toggle="modal" data-bs-target="#SiteManual" id="manualbtn"></button>-->
						<button type="button" class="btn btn-danger bi bi-x-circle navbar_icon" data-bs-dismiss="modal"></button>
					  </div>
                    </div>
					
                    <div class="modal-body">

					<form class="g-3 needs-validation" id="meterlocationform">
					  
					    <div class="row mb-2">
						  <label for="location_code" class="col-sm-3 col-form-label">Code</label>
						  <div class="col-sm-9">
							<input type="text" class="form-control " name="location_code" id="location_code" value="" required>
							<span class="valid-feedback" id="location_codeError"></span>
						  </div>
						</div>
						
						<div class="row mb-2">
						  <label for="location_description" class="col-sm-3 col-form-label">Description</label>
						  <div class="col-sm-9">
							<input type="text" class="form-control " name="location_description" id="location_description" value="" required>
							<span class="valid-feedback" id="location_descriptionError"></span>
						  </div>
						</div>
						</div>
                    <div class="modal-footer modal-footer_form">
						  <button type="submit" class="btn btn-success bi bi-save-fill navbar_icon btn-sm" id="save-meterlocation"> Submit</button>
						  <button type="submit" class="btn btn-primary bi bi-backspace-fill navbar_icon btn-sm" id="clear-meterlocation" onclick="ResetFormEEroom()"> Reset</button>  
					 </div>
					
					</form><!-- End Multi Columns Form -->
					
                  
                </div>
              </div>
		</div>
		
	<!--Modal to Create meterlocation-->
	<div class="modal fade" id="UpdateMeterLocationModal" tabindex="-1">
                <div class="modal-dialog">
                  <div class="modal-content">
                    <div class="modal-header modal-header_form">
                      <h5 class="modal-title">Update EE/MC Room</h5>
					  <div class="btn-group" role="group" aria-label="Basic outlined example">		
						<!--<button type="button" class="btn btn-info bi bi-question navbar_icon" data-bs-toggle="modal" data-bs-target="#SiteManual" id="manualbtn"></button>-->
						<button type="button" class="btn btn-danger bi bi-x-circle navbar_icon" data-bs-dismiss="modal"></button>
					  </div>
                    </div>
					
                    <div class="modal-body">

					<form class="g-3 needs-validation" id="updatemeterlocationform">
					  
					  <div class="row mb-2">
						  <label for="update_location_code" class="col-sm-3 col-form-label">Code</label>
						  <div class="col-sm-9">
							<input type="text" class="form-control " name="update_location_code" id="update_location_code" value="" required>
							<span class="valid-feedback" id="update_location_codeError"></span>
						  </div>
						</div>
						
						<div class="row mb-2">
						  <label for="update_location_description" class="col-sm-3 col-form-label">Description</label>
						  <div class="col-sm-9">
							<input type="text" class="form-control " name="update_location_description" id="update_location_description" value="" required>
							<span class="valid-feedback" id="update_location_descriptionError"></span>
						  </div>
						</div>
						</div>
                    <div class="modal-footer modal-footer_form">
							<div id="loading_data_updateLocation" style="display:none;">
									<button class="btn btn-light btn-sm" type="button" disabled="" style="background: aquamarine;">
									<span class="spinner-grow spinner-grow-sm text-danger" role="status" aria-hidden="true"></span>
									Checking for changes...
									</button>
							</div>
						  <button type="submit" class="btn btn-success bi bi-save-fill navbar_icon btn-sm" id="update-meterlocation"> Submit</button>
					 </div>
					
					</form><!-- End Multi Columns Form -->
					
                  </div>
                </div>
              </div>
		</div>
		
	<!-- meterlocation Delete Modal-->
    <div class="modal fade" id="meterlocationDeleteModal" tabindex="-1" role="dialog" aria-hidden="true">
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
				Location Code: <span id="meter_location_code_delete"></span><br>
				Location Description: <span id="meter_location_description_delete"></span><br>
				</div>
                <div class="modal-footer footer_modal_bg">
                    
					<button type="button" class="btn btn-danger" data-bs-dismiss="modal" id="deletemeterlocationConfirmed" value=""><i class="bi bi-trash3 navbar_icon"></i> Delete</button>
					<button type="button" class="btn btn-primary" data-bs-dismiss="modal"><i class="bi bi-x-circle navbar_icon"></i> Cancel</button>
                  
                </div>
            </div>
        </div>
    </div>	

	<!-- meterlocation Confirmed Deleted Modal-->
    <div class="modal fade" id="meterlocationDeleteModalConfirmed" tabindex="-1" role="dialog" aria-hidden="true">
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
				Location Code: <span id="meter_location_code_delete_confirmed"></span><br>
				Location Description: <span id="meter_location_description_delete_confirmed"></span><br>
				</div>
                <div class="modal-footer footer_modal_bg">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal"><i class="bi bi-x-circle navbar_icon"></i> Close</button>
					     
                </div>
            </div>
        </div>
    </div>			
		