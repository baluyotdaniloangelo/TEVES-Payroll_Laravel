@extends('layouts.layout')  
@section('content')  
<!-- Begin Page Content -->
<main id="main" class="main">

    <section class="section">
	  <div class="card">
	  
			<div class="card card-zero-btm">
            <div class="card-header" style="text-align:center;">
				  
				  <div class="row">
					
						  <div class="col-sm-12">
						  
						   <h5 class="card-title bi bi-building">&nbsp;{{ $SiteData->site_name }}</h5>
						   <!--OPTIONS HERE-->
						   <div class="d-flex justify-content-end" id="site_option_per_tab">
						   <?php
							if($meter_tab==' active show'){
								?>
								<div class="btn-group" role="group" aria-label="Basic outlined " id="meter_option" style="margin-top: -50px;position: absolute;">
								<button type="button" class="btn btn-success new_item bi bi-plus-circle" data-bs-toggle="modal" data-bs-target="#CreateMeterModal"></button>
								<button type="button" class="btn btn-danger offline_item bi bi-cloud-slash" data-bs-toggle="modal" data-bs-target="#OfflineMModal"></button>
								</div>
								<?php
							}
							else if($gateway_tab==' active show'){
								?>
								<div class="btn-group" role="group" aria-label="Basic outlined " id="gateway_option" style="margin-top: -50px;position: absolute;">
								<button type="button" class="btn btn-success new_item bi bi-plus-circle" data-bs-toggle="modal" data-bs-target="#CreateGatewayModal"></button>
								<button type="button" class="btn btn-danger offline_item bi bi-cloud-slash" data-bs-toggle="modal" data-bs-target="#OfflineGatewayModal"></button>
								</div>
								<?php
							}
							else{
								
							}
						   ?>
						   </div>
						  </div>
						</div>

			</div>

            </div>  
	  
            <div class="card-body">
           	  <!--   <style>
			  [contenteditable] {
  border: solid 1px lightgreen;
  padding: 5px;
  border-radius: 3px;
}
			  </style>
		
		
		TESTING FOR TABS
			  <div class="container">
					<div class="row">
						<div class="col-md-12">
							<p>
								<button id="btn-add-tab" type="button" class="btn btn-primary pull-right">Add Tab</button>
							</p>-->
							<!-- Nav tabs 
							<ul id="tab-list" class="nav nav-tabs" role="tablist">
								<li class="active"><a href="#tab1" role="tab" data-toggle="tab"><span>Tab 1 </span><span class="glyphicon glyphicon-pencil text-muted edit"></span></a></li>
							</ul>
-->
							<!-- Tab panes
							<div id="tab-content" class="tab-content">
								<div class="tab-pane fade in active" id="tab1">Tab 1 content</div>
							</div>
						</div>
					</div>
				</div>
			   -->
			  
              <!-- Bordered Tabs -->
			  
              <ul class="nav nav-tabs nav-tabs-bordered card-zero-btm" id="borderedTab" role="tablist">
                <li class="nav-item" role="presentation">
                  <button class="nav-link bi bi-info-circle{{ $status_tab }}" id="status-tab" data-bs-toggle="tab" data-bs-target="#bordered-status" type="button" role="tab" aria-controls="status" aria-selected="{{ $status_aria_selected }}" onclick="remember_sitetab('status')"> Status & Information</button>
                </li>
                <li class="nav-item" role="presentation">
                  <button class="nav-link bi bi-cpu{{ $gateway_tab }}" id="sitegatewaylist-tab" data-bs-toggle="tab" data-bs-target="#bordered-sitegatewaylist" type="button" role="tab" aria-controls="sitegatewaylist" aria-selected="{{ $gateway_aria_selected }}" tabindex="-1" onclick="remember_sitetab('gateway')"> Gateway</button>
                </li>
                <li class="nav-item" role="presentation">
                  <button class="nav-link bi bi-speedometer{{ $meter_tab }}" id="sitemeterlist-tab" data-bs-toggle="tab" data-bs-target="#bordered-sitemeterlist" type="button" role="tab" aria-controls="sitemeterlist" aria-selected="{{ $meter_aria_selected }}" tabindex="-1" onclick="remember_sitetab('meter')"> Meter</button>
                </li>
              </ul>
              <div class="tab-content pt-2 card-zero-btm" id="borderedTabContent">
                <div class="tab-pane fade {{ $status_tab }}" id="bordered-status" role="tabpanel" aria-labelledby="status-tab">
                  
				<div class="row">

				  <div class="col-lg-4">
				  <div class="card">
				  
						<div class="card-header mb-3">	
								<h5 class="card-title" align="center">Details</h5>								
						</div>
						
						<div class="card-body">
						
						<ul class="nav nav-tabs nav-tabs-bordered d-flex" id="borderedTabJustified" role="tablist">
							<li class="nav-item flex-fill" role="presentation">
							  <button class="nav-link w-100 active" id="basic-tab" data-bs-toggle="tab" data-bs-target="#bordered-justified-basic" type="button" role="tab" aria-controls="basic" aria-selected="true">CAMR</button>
							</li>
						   
							<li class="nav-item flex-fill" role="presentation">
							  <button class="nav-link w-100" id="sap-tab" data-bs-toggle="tab" data-bs-target="#bordered-justified-sap" type="button" role="tab" aria-controls="sap" aria-selected="false" tabindex="-1">SAP</button>
							</li>
						  </ul>
						  
						  <div class="tab-content pt-2" id="borderedTabJustifiedContent">
							<div class="tab-pane fade active show" id="bordered-justified-basic" role="tabpanel" aria-labelledby="status-tab">
							 
							<ol class="list-group list-group-numbered">
							<li class="list-group-item d-flex justify-content-between align-items-start">
							  <div class="ms-2 me-auto">
								<div class="fw-bold">Site Description</div>
								{{ $SiteData->site_name }}
							  </div>
							  
							</li>
							<li class="list-group-item d-flex justify-content-between align-items-start">
							  <div class="ms-2 me-auto">
								<div class="fw-bold">Site Code</div>
								{{ $SiteData->site_code }}
							  </div>
							  
							</li>
							
							<li class="list-group-item d-flex justify-content-between align-items-start">
							  <div class="ms-2 me-auto">
								<div class="fw-bold">Type</div>
								{{ $SiteData->building_type }}
							  </div>
							  
							</li>
							<li class="list-group-item d-flex justify-content-between align-items-start">
							  <div class="ms-2 me-auto">
								<div class="fw-bold">IP Address Range</div>
								{{ $SiteData->device_ip_range }}
							  </div>
							  
							</li>
							<li class="list-group-item d-flex justify-content-between align-items-start">
							  <div class="ms-2 me-auto">
								<div class="fw-bold">Netmask</div>
								{{ $SiteData->ip_netmask }}
							  </div>
							  
							</li>
							
							<li class="list-group-item d-flex justify-content-between align-items-start">
							  <div class="ms-2 me-auto">
								<div class="fw-bold">Network</div>
								{{ $SiteData->ip_network }}
							  </div>
							  
							</li>
							
							<li class="list-group-item d-flex justify-content-between align-items-start">
							  <div class="ms-2 me-auto">
								<div class="fw-bold">Gateway</div>
								{{ $SiteData->ip_gateway }}
							  </div>
							  
							</li>
							
						  </ol>

							 </div>
							

							<div class="tab-pane fade" id="bordered-justified-sap" role="tabpanel" aria-labelledby="sap-tab">
							  
							<ol class="list-group list-group-numbered">
							<li class="list-group-item d-flex justify-content-between align-items-start">
							  <div class="ms-2 me-auto">
								<div class="fw-bold">Business Entity</div>
								{{ $SiteData->business_entity }}
							  </div>
							  
							</li>
							
							<li class="list-group-item d-flex justify-content-between align-items-start">
							  <div class="ms-2 me-auto">
								<div class="fw-bold">Company Number</div>
								{{ $SiteData->company_no }}
							  </div>
							  
							</li>
							
							<li class="list-group-item d-flex justify-content-between align-items-start">
							  <div class="ms-2 me-auto">
								<div class="fw-bold">Service Charge Key</div>
								{{ $SiteData->service_charge_key }}
							  </div>
							  
							</li>
							
							<li class="list-group-item d-flex justify-content-between align-items-start">
							  <div class="ms-2 me-auto">
								<div class="fw-bold">Participation Group</div>
								{{ $SiteData->participation_group }}
							  </div>
							  
							</li>
							
							<li class="list-group-item d-flex justify-content-between align-items-start">
							  <div class="ms-2 me-auto">
								<div class="fw-bold">Settlement Unit:</div>
								{{ $SiteData->settlement_unit }}
							  </div>
							  
							</li>
							
							<li class="list-group-item d-flex justify-content-between align-items-start">
							  <div class="ms-2 me-auto">
								<div class="fw-bold">Settlement Variant Text</div>
								{{ $SiteData->settlement_variant_text }}
							  </div>
							  
							</li>
							
							<li class="list-group-item d-flex justify-content-between align-items-start">
							  <div class="ms-2 me-auto">
								<div class="fw-bold">Settlement Validity</div>
								{{ $SiteData->settlement_valid_from }} - {{ $SiteData->settlement_valid_to }}
							  </div>
							  
							</li>
							
							<li class="list-group-item d-flex justify-content-between align-items-start">
							  <div class="ms-2 me-auto">
								<div class="fw-bold">Date Created</div>
								{{ $SiteData->sap_created_on }} {{ $SiteData->sap_created_at }}
							  </div>
							  
							</li>
							
							<li class="list-group-item d-flex justify-content-between align-items-start">
							  <div class="ms-2 me-auto">
								<div class="fw-bold">Last Edited</div>
								{{ $SiteData->sap_last_edited_on }} {{ $SiteData->sap_last_edited_at }}
							  </div>
							  
							</li>
							
							<li class="list-group-item d-flex justify-content-between align-items-start">
							  <div class="ms-2 me-auto">
								<div class="fw-bold">SAP Server</div>
								{{ $SiteData->sap_server }}
							  </div>
							  
							</li>
							
						  </ol>
						  
							  </div>
						  </div>
					 
					 </div>
						<!--
						<div class="card-footer">
						  
						</div>
						-->
					  </div>
				  </div>
				  
				<div class="col-lg-4">
				<div class="card-0">
				<div class="card-body">
				  
				  <h5 class="card-title" align="center">Gateway Status(sample)</h5>

				  <!-- Pie Chart -->
				  <canvas id="gateway" style="max-height: 400px; display: block; box-sizing: border-box; height: 400px; width: 719px;" width="719" height="400"></canvas>	  
				  <!-- End Pie CHart -->

				</div>
				</div>
				
				</div>				
				
				<div class="col-lg-4">
				<div class="card-0">
				<div class="card-body">
				  
				  <h5 class="card-title" align="center">Meter Status(sample)</h5>

				  <!-- Pie Chart -->
				  <canvas id="meter" style="max-height: 400px; display: block; box-sizing: border-box; height: 400px; width: 719px;" width="719" height="400"></canvas>	  
				  <!-- End Pie CHart -->

				</div>
				</div>
				
				</div>
		
				</div>
				
				</div>
				
				
                <div class="tab-pane fade {{ $gateway_tab }}" id="bordered-sitegatewaylist" role="tabpanel" aria-labelledby="sitegatewaylist-tab">
					
						
									<div class="table-responsive">
										<table class="table dataTable display nowrap cell-border" id="gatewaylist" width="100%" cellspacing="0">
											<thead>
												<tr>
													<th class="all">#</th>
													<th nowrap class="all">Serial #</th>
													<th nowrap class="all">IP Address</th>
													<th nowrap class="all">MAC Address</th>
													<th nowrap class="">Location</th>	
													<th nowrap class="none">IDF</th>
													<th nowrap class="none">Switch</th>
													<th nowrap class="none">Port</th>
													<th class="">Status</th>
													<th nowrap class="">Configuration</th>
													<th nowrap>Action</th>
												</tr>
											</thead>				
											
											<tbody>
												
											</tbody>
											
											<tfoot>
												<tr>
													<th class="all">#</th>
													<th nowrap class="all">Serial #</th>
													<th nowrap class="all">IP Address</th>
													<th nowrap class="all">MAC Address</th>
													<th nowrap class="">Location</th>	
													<th nowrap class="none">IDF</th>
													<th nowrap class="none">Switch</th>
													<th nowrap class="none">Port</th>
													<th class="">Status</th>
													<th nowrap class="">Configuration</th>
													<th nowrap>Action</th>
												</tr>
											</tfoot>
											
										</table>
																
                   </div>


	<!--Modal to Create Gateway-->
	<div class="modal fade" id="CreateGatewayModal" tabindex="-1">
                <div class="modal-dialog modal-lg">
                  <div class="modal-content">
                    <div class="modal-header modal-header_form">
                      <h5 class="modal-title">Create Gateway</h5>
					  <div class="btn-group" role="group" aria-label="Basic outlined example">		
						<button type="button" class="btn btn-info bi bi-question navbar_icon" data-bs-toggle="modal" data-bs-target="#GatewayManual" id="manualbtn"></button>
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
						<label for="gateway_ip" class="col-sm-3 col-form-label">&nbsp;</label>
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
						  <label for="physical_location" class="col-sm-3 col-form-label">Physical Location</label>
						  <div class="col-sm-9">
							<input type="text" class="form-control " name="physical_location" id="physical_location" value="" required>
							<span class="valid-feedback" id="physical_locationError"></span>
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
						  <button type="reset" class="btn btn-primary btn-sm bi bi-backspace-fill navbar_icon" id="clear-gateway"> Reset</button>
						  
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
						<button type="button" class="btn btn-info bi bi-question navbar_icon" data-bs-toggle="modal" data-bs-target="#GatewayManual" id="manualbtn"></button>
						<button type="button" class="btn btn-danger bi bi-x-circle navbar_icon" data-bs-dismiss="modal"></button>
					  </div>
                    </div>
                    <div class="modal-body">

					  <form class="g-2 needs-validation" id="gatewayform">
					  
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
						<label for="gateway_ip" class="col-sm-3 col-form-label">&nbsp;</label>
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
						  <label for="update_physical_location" class="col-sm-3 col-form-label">Physical Location</label>
						  <div class="col-sm-9">
							<input type="text" class="form-control " name="update_physical_location" id="update_physical_location" value="" required>
							<span class="valid-feedback" id="update_physical_locationError"></span>
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
						
						  <button type="submit" class="btn btn-success btn-sm bi bi-save-fill navbar_icon" id="update-gateway"> Submit</button>
						  <!--<button type="reset" class="btn btn-primary btn-sm bi bi-backspace-fill navbar_icon" id="clear-gateway"> Reset</button>-->
						  
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
							
							<!--
							  <h5 class="card-title" align="center">Gateway Meters(sample)</h5>-->
							  <!-- Pie Chart -->
							<!--  <canvas id="gateway_meters" style="max-height: 120px; display: block; box-sizing: border-box; height: 120px; width: 120px;" width="120" height="120"></canvas>	-->  
							  <!-- End Pie CHart -->
							<div class="table-responsive">
										<table class="table dataTable display nowrap cell-border" id="meterlistLoadPerGateway" width="100%" cellspacing="0">
											<thead>
												<tr>
													<th class="all">#</th>
													<th title="SAP Measuring Point" nowrap class="none">Measuring Point</th>
													<th title="Meter Serial Number" nowrap class="all">Meter Description</th>
													<th title="Meter Tagging/Tenant/Client Name" nowrap class="none">Name/Tagging</th>													
													<th title="Last Reading Date" nowrap class="none">Last Reading Date</th>
													<th title="Active/Inactive" class="none">Status</th>
													<th title="The Gateway Serial Number of Meter" class="none">Gateway Serial Number: </th>
													<th title="The Meter Physical Location" class="none">Meter Physical Location: </th>
													<th title="Role : Tenant Meter/Tenant, Spare Meter, CUSA or Check Meter" nowrap class="none">Meter Role: </th>
													<th title="" class="all">Configuration File</th>
													<th title="" class="all">Address</th>
													<th title="" class="none">Remarks: </th>
													<!--<th title="View/Edit/Delete" class="">Action</th>-->
												</tr>
											</thead>				
											
											<tbody>
												
											</tbody>
											
											<tfoot>
												<tr>
													<th class="all">#</th>
													<th title="SAP Measuring Point" nowrap class="none">Measuring Point</th>
													<th title="Meter Serial Number" nowrap class="all">Meter Description</th>
													<th title="Meter Tagging/Tenant/Client Name" nowrap class="none">Name/Tagging</th>													
													<th title="Last Reading Date" nowrap class="none">Last Reading Date</th>
													<th title="Active/Inactive" class="none">Status</th>
													<th title="The Gateway Serial Number of Meter" class="none">Gateway Serial Number: </th>
													<th title="The Meter Physical Location" class="none">Meter Physical Location: </th>
													<th title="Role : Tenant Meter/Tenant, Spare Meter, CUSA or Check Meter" nowrap class="none">Meter Role: </th>
													<th title="" class="all">Configuration File</th>
													<th title="" class="all">Address</th>
													<th title="" class="none">Remarks: </th>
													<!--<th title="View/Edit/Delete" class="">Action</th>-->
												</tr>
											</tfoot>
											
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
				Are you sure you want to Delete <span id="rtu_sn_number_info"></span>?
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
				Successfully Deleted <span id="gateway_description_info_confirmed"></span>!
				</div>
                <div class="modal-footer footer_modal_bg">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal"><i class="bi bi-x-circle navbar_icon"></i> Close</button>
					     
                </div>
            </div>
        </div>
    </div>
	

				</div>
                
				  <div class="tab-pane fade {{ $meter_tab }}" id="bordered-sitemeterlist" role="tabpanel" aria-labelledby="sitemeterlist-tab">

									<div class="table-responsive">
										<table class="table table-bordered dataTable" id="meterlist" width="100%" cellspacing="0">
											<thead>
												<tr>
													<th class="all">#</th>
													<th title="SAP Measuring Point" nowrap class="">Measuring Point</th>
													<th title="Meter Serial Number" nowrap class="all">Meter Description</th>
													<th title="Meter Tagging/Tenant/Client Name" nowrap class="all">Name/Tagging</th>													
													<th title="Last Reading Date" nowrap class="">Last Reading Date</th>
													<th title="Active/Inactive" class="">Status</th>
													<th title="The Gateway Serial Number of Meter" class="none">Gateway Serial Number: </th>
													<th title="The Meter Physical Location" class="none">Meter Physical Location: </th>
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
													<th title="SAP Measuring Point" nowrap class="">Measuring Point</th>
													<th title="Meter Serial Number" nowrap class="all">Meter Description</th>
													<th title="Meter Tagging/Tenant/Client Name" nowrap class="all">Name/Tagging</th>													
													<th title="Last Reading Date" nowrap class="">Last Reading Date</th>
													<th title="Active/Inactive" class="">Status</th>
													<th title="The Gateway Serial Number of Meter" class="none">Gateway Serial Number: </th>
													<th title="The Meter Physical Location" class="none">Meter Physical Location: </th>
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
						  <label for="meter_name" class="col-sm-3 col-form-label" title="Meter Serial Number/Meter Description on SAP">Meter Description</label>
						  <div class="col-sm-9">
							
						  
						  <div class="input-group">	
						  
									<input type="text" class="form-control" name="meter_name" id="meter_name" value="" required>


									<span class="input-group-text" id="inputGroupPrepend2">Role</span>
									<select class="form-select form-control" id="meter_role" name="meter_role">
										<!--<option selected="" disabled="" value="">Choose Meter Role</option>-->
										<option value="Tenant Meter" title="Client/Tenant Meter">Tenant Meter</option>
										<option value="Spare Meter">Spare Meter</option>
										<option value="CUSA">CUSA</option>
										<option value="Check Meter">Check Meter</option>
									</select>	
									
									<span class="valid-feedback" id="meter_descriptionError"></span>
									
							  </div>	

							  
						  </div>
						</div>
						
						<div class="row mb-2">
						  <label for="customer_name" class="col-sm-3 col-form-label">Name/Tagging</label>
						  
						  <div class="col-sm-9">
							  <div class="input-group">
									<textarea class="form-control" placeholder="" name="customer_name" id="customer_name" style="height: 38px;"></textarea>
									<span class="valid-feedback" id="customer_nameError"></span>			
							  </div>
						  </div>
						  
						</div>	
										
						<div class="row mb-2">
						  <label for="meter_model_id" class="col-sm-3 col-form-label">Configuration File</label>
						  <div class="col-sm-9">
						  <input class="form-control" list="meter_model" name="meter_model" id="meter_model_id" required autocomplete="off" value="">
								<datalist id="meter_model">
									@foreach ($configuration_file_data as $configuration_file_cols)
										<option label="{{$configuration_file_cols->config_file}}" data-id="{{$configuration_file_cols->id}}" value="{{$configuration_file_cols->config_file}}">{{$configuration_file_cols->config_file}}">
									@endforeach
								</datalist>
							<span class="valid-feedback" id="meter_model_idError"></span>	
						  </div>
						</div>	
						
						<div class="row mb-2">
						  <label for="meter_name_addressable" class="col-sm-3 col-form-label">Alternate Address</label>
						  <div class="col-sm-9">
							<div class="input-group">
								<span class="input-group-text" id=""><input class="form-check-input" type="checkbox" name="meter_name_addressable" id="meter_name_addressable"></span>
								<input type="text" class="form-control" name="meter_default_name" id="meter_default_name" aria-describedby="">
							</div>
						  </div>
						</div>
						
						<div class="row mb-2">
						  <label for="meter_type" class="col-sm-3 col-form-label">Meter Type</label>
						  <div class="col-sm-9">
							<input type="text" class="form-control " name="meter_type" id="meter_type" value="">
							<span class="valid-feedback" id="meter_typeError"></span>
						  </div>
						</div>

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
										<option label="IP Address : {{$gateway_data_cols->phone_no_or_ip_address}}" data-id="{{$gateway_data_cols->id}}" value="{{$gateway_data_cols->rtu_sn_number}}">{{$gateway_data_cols->rtu_sn_number}}">
									@endforeach
								</datalist>
							
							<span class="valid-feedback" id="rtu_sn_number_idError"></span>	
						  </div>
						</div>
						
						<div class="row mb-2">
						  <label for="meter_physical_location" class="col-sm-3 col-form-label">Physical Location</label>
						  <div class="col-sm-9">
							<input type="text" class="form-control " name="meter_physical_location" id="meter_physical_location" value="" required>
							<span class="valid-feedback" id="meter_physical_locationError"></span>
						  </div>
						</div>	
					
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
						
						</div>
						
                    <div class="modal-footer modal-footer_form">
						
						  <button type="submit" class="btn btn-success btn-sm bi bi-save-fill navbar_icon" id="save-meter"> Submit</button>
						  <button type="reset" class="btn btn-primary btn-sm bi bi-backspace-fill navbar_icon" id="clear-meter"> Reset</button>
						  
					</div>
					</form><!-- End Multi Columns Form -->
                  </div>
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
										<option value="Tenant Meter" title="Client/Tenant Meter">Tenant Meter</option>
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
										<option label="{{$configuration_file_cols->config_file}}" data-id="{{$configuration_file_cols->id}}" value="{{$configuration_file_cols->config_file}}">{{$configuration_file_cols->config_file}}">
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
										<option label="IP Address : {{$gateway_data_cols->phone_no_or_ip_address}}" data-id="{{$gateway_data_cols->id}}" value="{{$gateway_data_cols->rtu_sn_number}}">{{$gateway_data_cols->rtu_sn_number}}">
									@endforeach
								</datalist>
							
							<span class="valid-feedback" id="update_rtu_sn_number_idError"></span>	
						  </div>
						</div>
						
						<div class="row mb-2">
						  <label for="update_meter_physical_location" class="col-sm-3 col-form-label">Physical Location</label>
						  <div class="col-sm-9">
							<input type="text" class="form-control " name="update_meter_physical_location" id="update_meter_physical_location" value="" required>
							<span class="valid-feedback" id="update_meter_physical_locationError"></span>
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
						  <!--<button type="reset" class="btn btn-primary btn-sm bi bi-backspace-fill navbar_icon" id="clear-meter"> Reset</button>-->
						  
					</div>
					</form><!-- End Multi Columns Form -->
                  </div>
                </div>
              </div>	
			</div>
				   
            </div>
			
	<!-- Gateway Delete Modal-->
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
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal"><i class="bi bi-x-circle navbar_icon"></i> Cancel</button>
					<button type="button" class="btn btn-danger" data-bs-dismiss="modal" id="deleteMeterConfirmed" value=""><i class="bi bi-trash3 navbar_icon"></i> Delete</button>
                  
                </div>
            </div>
        </div>
    </div>	

	<!-- Gateway Confirmed Deleted Modal-->
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
			
					
              </div><!-- End Bordered Tabs -->

            </div>
          </div>
		  
    </section>
	

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
  </main>
 
@endsection

