@extends('layouts.layout')  
@section('content')  

	<!-- Site Delete Modal-->
    <div class="modal fade" id="SiteDeleteModal" tabindex="-1" role="dialog" aria-hidden="true">
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
				<th width="40%">Building Code:</th>
				<td width="60%"><span id="building_code_delete_info"></span></td>
				</tr>
				
				<tr>
				<th width="40%">Building Description:</th>
				<td width="60%"><span id="building_description_delete_info"></span></td>
				</tr>
				
				</table>
				
				</div>
				
                <div class="modal-footer footer_modal_bg">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal"><i class="bi bi-x-circle navbar_icon"></i> Cancel</button>
					<button type="button" class="btn btn-danger" data-bs-dismiss="modal" id="deleteSiteConfirmed" value=""><i class="bi bi-trash3 navbar_icon"></i> Delete</button>
                  
                </div>
            </div>
        </div>
    </div>	

	<!-- Site Comfirm Delete Modal-->
    <div class="modal fade" id="SiteDeleteModalConfirmed" tabindex="-1" role="dialog" aria-hidden="true">
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
				<th width="40%">Building Code:</th>
				<td width="60%"><span id="building_code_delete_confirmed_info"></span></td>
				</tr>
				
				<tr>
				<th width="40%">Building Description:</th>
				<td width="60%"><span id="building_description_delete_confirmed_info"></span></td>
				</tr>
				
				</table>
				
				</div>
                <div class="modal-footer footer_modal_bg">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal"><i class="bi bi-x-circle navbar_icon"></i> Close</button>
					     
                </div>
            </div>
        </div>
    </div>			
			<!--Create Building-->

			  
<div class="modal fade " id="CreateSiteModal" aria-modal="true" role="dialog" style="display: none;">
                <div class="modal-dialog modal-dialog-scrollable">
                  <div class="modal-content">
                    <div class="modal-header modal-header_form">
                      <h5 class="modal-title">Create Building</h5>
                      <button type="button" class="btn btn-danger bi bi-x-circle navbar_icon" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
					 <form class=" g-2 needs-validation" id="siteform">
							<div class="form-floating mb-1">
							  <input type="text" class="form-control " name="building_code" id="building_code" value="" required placeholder="Building Code">		
							  <label for="building_code" class="col-sm-3 col-form-label">Building Code</label>
							  <span class="valid-feedback" id="building_codeError"></span>
							</div>	
					
							<div class="form-floating mb-1">
							  <input type="text" class="form-control " name="building_description" id="building_description" value="" required placeholder="Building Description">		
							  <label for="building_description" class="col-sm-3 col-form-label">Building Description</label>
							  <span class="valid-feedback" id="building_descriptionError"></span>
							</div>
							
							<div class="form-floating mb-1">
							  <input class="form-control" list="division_list" name="division_id" id="division_id"  autocomplete="off" value="" placeholder="Division" required>
									<datalist id="division_list">
										@foreach ($division_data as $division_data_cols)
											<option label="Code : {{$division_data_cols->division_code}} | Name : {{$division_data_cols->division_name}}" data-id="{{$division_data_cols->division_id}}" value="{{$division_data_cols->division_code}} - {{$division_data_cols->division_name}}">
										@endforeach
									</datalist>
							  <label for="division_id" class="col-sm-3 col-form-label">Division</label>
							  <span class="valid-feedback" id="division_idError"></span>
							 </div>	
						
							<div class="form-floating mb-1">
							  <input class="form-control" list="company_list" name="company_id" id="company_id"  autocomplete="off" value="" placeholder="Company" required>
									<datalist id="company_list">
										@foreach ($company_data as $company_data_cols)
											<option label="Name : {{$company_data_cols->company_name}}" data-id="{{$company_data_cols->company_id}}" value="{{$company_data_cols->company_name}}">
										@endforeach
									</datalist>
							  <label for="company_id" class="col-sm-3 col-form-label">Company</label>
							  <span class="valid-feedback" id="company_idError"></span>
							</div>	
							
							<!--
							<div class="form-floating mb-3">
							  <input class="form-control" name="site_cut_off" id="site_cut_off" required autocomplete="off" value="" placeholder="Cut Off">		
							  <label for="site_cut_off" class="col-sm-3 col-form-label">Cut Off</label>
							  <span class="valid-feedback" id="site_cut_offError"></span>
							</div>	
							-->
							<div class="form-floating mb-1">
							  <input class="form-control" name="device_ip_range" id="device_ip_range"  autocomplete="off" value="" placeholder="IP Address Range">		
							  <label for="device_ip_range" class="col-sm-3 col-form-label">IP Address Range</label>
							  <span class="valid-feedback" id="device_ip_rangeError"></span>
							</div>
							
							<div class="form-floating mb-1">
							  <input class="form-control" name="ip_netmask" id="ip_netmask"  autocomplete="off" value="" placeholder="Netmask">		
							  <label for="ip_netmask" class="col-sm-3 col-form-label">Netmask</label>
							  <span class="valid-feedback" id="ip_netmaskError"></span>
							</div>
							
							<div class="form-floating mb-1">
							  <input class="form-control" name="ip_network" id="ip_network"  autocomplete="off" value="" placeholder="Network">		
							  <label for="ip_network" class="col-sm-3 col-form-label">Network</label>
							  <span class="valid-feedback" id="ip_networkError"></span>
							</div>
							
							<div class="form-floating mb-1">
							  <input class="form-control" name="ip_gateway" id="ip_gateway"  autocomplete="off" value="" placeholder="Gateway">		
							  <label for="ip_gateway" class="col-sm-3 col-form-label">Gateway</label>
							  <span class="valid-feedback" id="ip_gatewayError"></span>
							</div>
							</form>
                    </div>
                    <div class="modal-footer modal-footer_form">
						<button type="submit" class="btn btn-success bi bi-save-fill navbar_icon btn-sm" id="save-site"> Submit</button>
						<button type="submit" class="btn btn-primary bi bi-backspace-fill navbar_icon btn-sm" id="clear-site" onclick="ResetFormSite()"> Reset</button>
                    </div>
                  </div>
                </div>
              </div>			  
			  
			  
			  

			<!--Create Building-->
			<div class="modal fade" id="UpdateSiteModal" tabindex="-1" aria-modal="true" role="dialog" style="display: none;">
                <div class="modal-dialog modal-dialog-scrollable">
                  <div class="modal-content">
				  
                    <div class="modal-header modal-header_form">
                      <h5 class="modal-title">Update Building</h5>
                      <button type="button" class="btn btn-danger bi bi-x-circle navbar_icon" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                    <form class=" g-2 needs-validation" id="updatesiteform">
						<input type="hidden" name="update_building_id" id="update_building_id"></input>
					   		<div class="form-floating mb-1">
							  <input type="text" class="form-control " name="update_building_code" id="update_building_code" value="" required placeholder="Building Code">		
							  <label for="update_building_code" class="col-sm-3 col-form-label">Building Code</label>
							  <span class="valid-feedback" id="update_building_codeError"></span>
							</div>	
					
							<div class="form-floating mb-1">
							  <input type="text" class="form-control " name="update_building_description" id="update_building_description" value="" required placeholder="Building Description">		
							  <label for="update_building_description" class="col-sm-3 col-form-label">Building Description</label>
							  <span class="valid-feedback" id="update_building_descriptionError"></span>
							</div>
							
							<div class="form-floating mb-1">
							  <input class="form-control" list="update_division_list" name="update_division_id" id="update_division_id"  autocomplete="off" value="" placeholder="Division" required>
									<datalist id="update_division_list">
										@foreach ($division_data as $division_data_cols)
											<option label="Code : {{$division_data_cols->division_code}} | Name : {{$division_data_cols->division_name}}" data-id="{{$division_data_cols->division_id}}" value="{{$division_data_cols->division_code}} - {{$division_data_cols->division_name}}">
										@endforeach
									</datalist>
							  <label for="update_division_id" class="col-sm-3 col-form-label">Division</label>
							  <span class="valid-feedback" id="update_division_idError"></span>
							 </div>	
						
							<div class="form-floating mb-1">
							  <input class="form-control" list="update_company_list" name="update_company_id" id="update_company_id"  autocomplete="off" value="" placeholder="Company" required>
									<datalist id="update_company_list">
										@foreach ($company_data as $company_data_cols)
											<option label="Name : {{$company_data_cols->company_name}}" data-id="{{$company_data_cols->company_id}}" value="{{$company_data_cols->company_name}}">
										@endforeach
									</datalist>
							  <label for="update_company_id" class="col-sm-3 col-form-label">Company</label>
							  <span class="valid-feedback" id="update_company_idError"></span>
							</div>	
							<!--
							<div class="form-floating mb-3">
							  <input class="form-control" name="update_site_cut_off" id="update_site_cut_off" required autocomplete="off" value="" placeholder="Cut Off">		
							  <label for="update_site_cut_off" class="col-sm-3 col-form-label">Cut Off</label>
							  <span class="valid-feedback" id="update_site_cut_offError"></span>
							</div>	
							-->
							<div class="form-floating mb-1">
							  <input class="form-control" name="update_device_ip_range" id="update_device_ip_range"  autocomplete="off" value="" placeholder="IP Address Range">		
							  <label for="update_device_ip_range" class="col-sm-3 col-form-label">IP Address Range</label>
							  <span class="valid-feedback" id="update_device_ip_rangeError"></span>
							</div>
							
							<div class="form-floating mb-1">
							  <input class="form-control" name="update_ip_netmask" id="update_ip_netmask"  autocomplete="off" value="" placeholder="Netmask">		
							  <label for="update_ip_netmask" class="col-sm-3 col-form-label">Netmask</label>
							  <span class="valid-feedback" id="update_ip_netmaskError"></span>
							</div>
							
							<div class="form-floating mb-1">
							  <input class="form-control" name="update_ip_network" id="update_ip_network"  autocomplete="off" value="" placeholder="Network">		
							  <label for="update_ip_network" class="col-sm-3 col-form-label">Network</label>
							  <span class="valid-feedback" id="update_ip_networkError"></span>
							</div>
							
							<div class="form-floating mb-1">
							  <input class="form-control" name="update_ip_gateway" id="update_ip_gateway"  autocomplete="off" value="" placeholder="Gateway">		
							  <label for="ip_gateway" class="col-sm-3 col-form-label">Gateway</label>
							  <span class="valid-feedback" id="update_ip_gatewayError"></span>
							</div>
</form>
					</div>
                    <div class="modal-footer modal-footer_form">
							<div id="loading_data" style="display:none;">
									<button class="btn btn-light btn-sm" type="button" disabled="">
									<span class="spinner-grow spinner-grow-sm text-danger" role="status" aria-hidden="true"></span>
									Checking for changes...
									</button>
							</div>
							<button type="submit" class="btn btn-success bi bi-save-fill navbar_icon btn-sm" id="update-site"> Submit</button>
							<!--<button type="reset" class="btn btn-primary bi bi-backspace-fill navbar_icon btn-sm" id="update-clear-site"> Reset</button>-->
                    </div>
                  </div>
                </div>
              </div>
			  
<main id="main" class="main">	
    <section class="section">	  
          <div class="card">
		  
			  <div class="card card-12-btm">
			  
				<div class="card-header " style="text-align:center;">
				  <h5 class="card-title bi bi-building">&nbsp;Building Management</h5>
						<div class="d-flex justify-content-end">
						<div class="btn-group" role="group" aria-label="Basic outlined example" style="margin-top: -50px;position: absolute;">
							<?php if($data->user_type=="Admin"){ ?>
							<button type="button" class="btn btn-success new_item bi bi-plus-circle" data-bs-toggle="modal" data-bs-target="#CreateSiteModal" onclick="ResetFormSite()"> Building</button>
							<?php } ?>
						</div>
						</div>				  
				  </div>
				</div>			  
		 
            <div class="card-body">
				<div class="row">
				<div class="p-1" align="">
									<div class="table-responsive">
										<table class="table dataTable display nowrap cell-border"" id="siteList" width="100%" cellspacing="0">
											<thead>
												<tr>
													<th class="all">#</th>
										 			<th class="all" title="Building Code/The Site Code">Building Code</th>
													<th class="all" title="Building Description/The Site Name">Building Description</th>
													<th>Company Name</th>
													<th>Division</th>																							
													<th class="all">Cut Off</th>
													<th class="all">Status</th>
													<th class="all">Action</th>
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
	
    </section>
</main>



@endsection

