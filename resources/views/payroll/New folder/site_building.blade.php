
            <div class="card-body">
			
				<div class="row mb-2">
				
					<div class="col-sm-3">
						<div class="d-flex justify-content-end mb-2" id="">
							<div class="btn-group" role="group" aria-label="Basic outlined " id="building_option" style="">
									<button type="button" class="btn btn-success btn-sm new_item bi bi-plus-circle" data-bs-toggle="modal" data-bs-target="#CreateBuildingModal" title="Add Building"> Building</button>
									<button type="button" class="btn btn-success btn-sm new_item bi bi-plus-circle" data-bs-toggle="modal" data-bs-target="#CreateMeterLocationModal" title="Add Area/EE Room"> Area/EE Room</button>
							</div>
						</div>
						
						<div class="accordion" id="accordionExample">
							

							<div class="accordion-item">
								<!-- Item Here -->
							</div>
						
						</div>
			  
					</div>
					
					<div class="col-sm-9">
						
						  <ul class="nav nav-tabs" id="myTab" role="tablist">
                
							<li class="nav-item" role="presentation">
							  <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home" type="button" role="tab" aria-controls="home" aria-selected="true">Gateway</button>
							</li>
							<li class="nav-item" role="presentation">
							  <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button" role="tab" aria-controls="profile" aria-selected="false" tabindex="-1">Meter</button>
							</li>
               
							</ul>
							
						    <div class="tab-content pt-2" id="myTabContent">
							<div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
							<div class="d-flex justify-content-end" id="">
								<div class="btn-group" role="group" aria-label="Basic outlined example" style="margin-top: -50px; position: absolute;">
									<button type="button" class="btn btn-success  btn-sm new_item bi bi-plus-circle form_button_icon" data-bs-toggle="modal" data-bs-target="#CreateGatewayModal" title="Add Gateway"> Gateway</button>
									<button type="button" class="btn btn-secondary  btn-sm new_item bi bi-arrow-clockwise form_button_icon" onclick="GatewayPerBuilding(0,0);" title="Reset Gateway List"> Reset List</button>
								</div>					
								</div>
									@include('amr.site_gateway')
							</div>
							
							<div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
							<div class="d-flex justify-content-end" id="">
								<div class="btn-group" role="group" aria-label="Basic outlined example" style="margin-top: -50px; position: absolute;">
									<button type="button" class="btn btn-success btn-sm new_item bi bi-plus-circle form_button_icon" data-bs-toggle="modal" data-bs-target="#CreateMeterModal" title="Add Meter"> Meter</button>
									<button type="button" class="btn btn-secondary btn-sm new_item bi bi-arrow-clockwise form_button_icon" onclick="MeterPerBuilding(0,0);" title="Reset Meter List"> Reset List</button>
								</div>					
								</div>

									@include('amr.site_meter')
							</div>
							</div>
							
					
					</div>
				
				</div>		


	<!--Modal to Create Building-->
	<div class="modal fade" id="CreateBuildingModal" tabindex="-1">
                <div class="modal-dialog">
                  <div class="modal-content">
                    <div class="modal-header modal-header_form">
                      <h5 class="modal-title">Create Building</h5>
					  <div class="btn-group" role="group" aria-label="Basic outlined example">		
						<!--<button type="button" class="btn btn-info bi bi-question navbar_icon" data-bs-toggle="modal" data-bs-target="#SiteManual" id="manualbtn"></button>-->
						<button type="button" class="btn btn-danger bi bi-x-circle navbar_icon" data-bs-dismiss="modal"></button>
					  </div>
                    </div>
					
                    <div class="modal-body">

					<form class="g-3 needs-validation" id="buildingform">
					  
						<div class="row mb-2">
						  <label for="building_code" class="col-sm-3 col-form-label">Code</label>
						  <div class="col-sm-9">
							<input type="text" class="form-control " name="building_code" id="building_code" value="" required>
							<span class="valid-feedback" id="building_codeError"></span>
						  </div>
						</div>
						
						<div class="row mb-2">
						  <label for="building_description" class="col-sm-3 col-form-label">Description</label>
						  <div class="col-sm-9">
							<input type="text" class="form-control " name="building_description" id="building_description" value="" required>
							<span class="valid-feedback" id="building_descriptionError"></span>
						  </div>
						</div>
						
						</div>
						
                    <div class="modal-footer modal-footer_form">
						  <button type="submit" class="btn btn-success bi bi-save-fill navbar_icon btn-sm" id="save-building"> Submit</button>
						  <button type="reset" class="btn btn-primary bi bi-backspace-fill navbar_icon btn-sm" id="clear-building"> Reset</button>  
					 </div>
					
					</form><!-- End Multi Columns Form -->
					
                  
                </div>
              </div>
		</div>
		
	<!--Modal to Create Building-->
	<div class="modal fade" id="UpdateBuildingModal" tabindex="-1">
                <div class="modal-dialog">
                  <div class="modal-content">
                    <div class="modal-header modal-header_form">
                      <h5 class="modal-title">Update Building</h5>
					  <div class="btn-group" role="group" aria-label="Basic outlined example">		
						<!--<button type="button" class="btn btn-info bi bi-question navbar_icon" data-bs-toggle="modal" data-bs-target="#SiteManual" id="manualbtn"></button>-->
						<button type="button" class="btn btn-danger bi bi-x-circle navbar_icon" data-bs-dismiss="modal"></button>
					  </div>
                    </div>
					
                    <div class="modal-body">

					<form class="g-3 needs-validation" id="updatebuildingform">
					  
					<div class="row mb-2">
						  <label for="update_building_code" class="col-sm-3 col-form-label">Code</label>
						  <div class="col-sm-9">
							<input type="text" class="form-control " name="update_building_code" id="update_building_code" value="" required>
							<span class="valid-feedback" id="update_building_codeError"></span>
						  </div>
						</div>
						
					<div class="row mb-2">
						  <label for="update_building_description" class="col-sm-3 col-form-label">Description</label>
						  <div class="col-sm-9">
							<input type="text" class="form-control " name="update_building_description" id="update_building_description" value="" required>
							<span class="valid-feedback" id="update_building_descriptionError"></span>
						  </div>
						</div>
					</div>	
                    <div class="modal-footer modal-footer_form">
						  <button type="submit" class="btn btn-success bi bi-save-fill navbar_icon btn-sm" id="update-building"> Submit</button> 
					 </div>
					
					</form><!-- End Multi Columns Form -->
					
								
                </div>
              </div>
		</div>
		
	<!-- Building Delete Modal-->
    <div class="modal fade" id="BuildingDeleteModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header header_modal_bg">
                    <h5 class="modal-title" id="exampleModalLabel"></h5>
 					<div class="btn-sm btn-warning btn-circle bi bi-exclamation-circle btn_icon_modal"></div>
                </div>
                <div class="modal-body warning_modal_bg" id="modal-body">
				Are you sure you want to Delete <span id="building_info"></span>?
				</div>
                <div class="modal-footer footer_modal_bg">
                    
					<button type="button" class="btn btn-danger" data-bs-dismiss="modal" id="deleteBuildingConfirmed" value=""><i class="bi bi-trash3 navbar_icon"></i> Delete</button>
					<button type="button" class="btn btn-primary" data-bs-dismiss="modal"><i class="bi bi-x-circle navbar_icon"></i> Cancel</button>
                  
                </div>
            </div>
        </div>
    </div>	

	<!-- Building Confirmed Deleted Modal-->
    <div class="modal fade" id="BuildingDeleteModalConfirmed" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header header_modal_bg">
                    <h5 class="modal-title" id="exampleModalLabel"></h5>
 					<div class="btn-sm btn-warning btn-circle bi bi-exclamation-circle btn_icon_modal"></div>
                </div>
                <div class="modal-body warning_modal_bg" id="modal-body">
				Successfully Deleted <span id="building_info_confirmed"></span>!
				</div>
                <div class="modal-footer footer_modal_bg">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal"><i class="bi bi-x-circle navbar_icon"></i> Close</button>     
                </div>
            </div>
        </div>
    </div>			
		
	<!--Modal to Create meterlocation-->
	<div class="modal fade" id="CreateMeterLocationModal" tabindex="-1">
                <div class="modal-dialog">
                  <div class="modal-content">
                    <div class="modal-header modal-header_form">
                      <h5 class="modal-title">Create Meter Location</h5>
					  <div class="btn-group" role="group" aria-label="Basic outlined example">		
						<!--<button type="button" class="btn btn-info bi bi-question navbar_icon" data-bs-toggle="modal" data-bs-target="#SiteManual" id="manualbtn"></button>-->
						<button type="button" class="btn btn-danger bi bi-x-circle navbar_icon" data-bs-dismiss="modal"></button>
					  </div>
                    </div>
					
                    <div class="modal-body">

					<form class="g-3 needs-validation" id="meterlocationform">
						
						<div class="row mb-2">
						  <label for="location_code" class="col-sm-3 col-form-label">Building</label>
						  <div class="col-sm-9">
							<input class="form-control" list="ee_room_per_blg_list" name="ee_room_per_blg" id="ee_room_per_blg" required autocomplete="off" value="">
									<datalist id="ee_room_per_blg_list">
										@foreach ($building_data as $building_data_cols)
											<option label="Description : {{$building_data_cols->building_code}} | {{$building_data_cols->building_description}}" data-id="{{$building_data_cols->id}}" value="{{$building_data_cols->building_code}}">
										@endforeach
									</datalist>
							<span class="valid-feedback" id="ee_room_per_blgError"></span>
						  </div>
						</div>
					  
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
						  <button type="reset" class="btn btn-primary bi bi-backspace-fill navbar_icon btn-sm" id="clear-meterlocation"> Reset</button>  
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
                      <h5 class="modal-title">Update Meter Location</h5>
					  <div class="btn-group" role="group" aria-label="Basic outlined example">		
						<!--<button type="button" class="btn btn-info bi bi-question navbar_icon" data-bs-toggle="modal" data-bs-target="#SiteManual" id="manualbtn"></button>-->
						<button type="button" class="btn btn-danger bi bi-x-circle navbar_icon" data-bs-dismiss="modal"></button>
					  </div>
                    </div>
					
                    <div class="modal-body">

					<form class="g-3 needs-validation" id="updatemeterlocationform">
					  
					  <div class="row mb-2">
						  <label for="location_code" class="col-sm-3 col-form-label">Building</label>
						  <div class="col-sm-9">
							<input class="form-control" list="update_ee_room_per_blg_list" name="update_ee_room_per_blg" id="update_ee_room_per_blg" required autocomplete="off" value="">
									<datalist id="update_ee_room_per_blg_list">
										@foreach ($building_data as $building_data_cols)
											<option label="Description : {{$building_data_cols->building_code}} | {{$building_data_cols->building_description}}" data-id="{{$building_data_cols->id}}" value="{{$building_data_cols->building_code}}">
										@endforeach
									</datalist>
							<span class="valid-feedback" id="ee_room_per_blgError"></span>
						  </div>
					  </div>
						
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
						
                    <div class="modal-footer modal-footer_form">
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
				Are you sure you want to Delete <span id="meterlocation_info"></span>?
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
				Successfully Deleted <span id="meterlocation_info_confirmed"></span>!
				</div>
                <div class="modal-footer footer_modal_bg">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal"><i class="bi bi-x-circle navbar_icon"></i> Close</button>
					     
                </div>
            </div>
        </div>
    </div>			
		