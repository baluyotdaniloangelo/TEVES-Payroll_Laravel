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
						  
						   <h5 class="card-title bi bi-building">&nbsp;{{ $SiteData[0]->building_description }}</h5>
						   <div class="d-flex justify-content-end" id="">
								<div class="btn-group" role="group" aria-label="Basic outlined example" style="margin-top: -50px; position: absolute;">
									<a href="#" class="btn btn-warning new_item bi bi bi-download form_button_icon" onclick="downloadAsbuilt();" title="Download Meter and Gateway List">  Download</a> 
								</div>					
							</div>
						   
						  </div>
						</div>

			</div>

            </div>  
	  
            <div class="card-body">
           	
              <!-- Bordered Tabs -->
			  
              <ul class="nav nav-tabs nav-tabs-bordered card-zero-btm" id="borderedTab" role="tablist">
                <li class="nav-item" role="presentation">
                  <button class="nav-link bi bi-info-circle{{ $status_tab }}" id="status-tab" data-bs-toggle="tab" data-bs-target="#bordered-status" type="button" role="tab" aria-controls="status" aria-selected="{{ $status_aria_selected }}" onclick="remember_sitetab('status')"> Status & Information</button>
                </li>
				
				<li class="nav-item" role="presentation">
                  <button class="nav-link bi bi-door-closed{{ $meterlocation_tab }}" id="sitemeterlocationlist-tab" data-bs-toggle="tab" data-bs-target="#bordered-sitemeterlocationlist" type="button" role="tab" aria-controls="sitemeterlocationlist" aria-selected="{{ $meterlocation_aria_selected }}" tabindex="-1" onclick="remember_sitetab('meterlocation')"> Area/EE Room</button>
                </li>
                <li class="nav-item" role="presentation">
                  <button class="nav-link bi bi-cpu{{ $gateway_tab }}" id="sitegatewaylist-tab" data-bs-toggle="tab" data-bs-target="#bordered-sitegatewaylist" type="button" role="tab" aria-controls="sitegatewaylist" aria-selected="{{ $gateway_aria_selected }}" tabindex="-1" onclick="remember_sitetab('gateway')" title="List of All Gateways for this Site"> Gateway</button>
                </li>
                <li class="nav-item" role="presentation">
                  <button class="nav-link bi bi-speedometer{{ $meter_tab }}" id="sitemeterlist-tab" data-bs-toggle="tab" data-bs-target="#bordered-sitemeterlist" type="button" role="tab" aria-controls="sitemeterlist" aria-selected="{{ $meter_aria_selected }}" tabindex="-1" onclick="remember_sitetab('meter')" title="List of All Meters for this Site"> Meter</button>
                </li>
				
              </ul>
			  
				<div class="tab-content pt-2 card-zero-btm" id="borderedTabContent">
<!--OPTIONS HERE-->
									<div class="row mb-1">
										<div class="col-sm-6">
										
												<div id="EERoomfilter_meter_div" 
													<?php 
													if($meter_tab==' active show'){ 
													?> 
														style="display: block;" 
													<?php 
													}
													else{ 
													?> 
														style="display: none;" 
													<?php 
													} 
													?> 
													>
												<div class="row mb-1">
													<label class="col-sm-3 col-form-label" for="EERoomFilter_meter" title="EE Room Filter">Area/EE room:</label>
													<div class="col-sm-9">
													<select class="form-select form-control" id="EERoomFilter_meter" name="EERoomFilter_meter">
														<option value="">Show All</option>
													</select>
													</div>	
													</div>
												</div> 
												
												<div id="EERoomfilter_gateway_div"
													<?php 
													if($gateway_tab==' active show'){ 
													?> 
														style="display: block;" 
													<?php 
													}
													else{ 
													?> 
														style="display: none;" 
													<?php 
													} 
													?> 
													>		
												<div class="row mb-1">												
													<label class="col-sm-3 col-form-label" for="EERoomFilter_gateway" title="EE Room Filter">Area/EE room:</label>
													<div class="col-sm-9">
													<select class="form-select form-control" id="EERoomFilter_gateway" name="EERoomFilter_gateway">
														<option value="">Show All</option>
													</select>
													</div>
													</div>	
												</div>
												
										</div>   
						   <?php
						   if($meterlocation_tab==' active show'){
								?>
								
								
										<div class="col-sm-6">
											<div class="d-flex justify-content-end" id="site_option_per_tab">
												<div class="btn-group" role="group" aria-label="Basic outlined " id="meter_option" style="">
													<button type="button" class="btn btn-success new_item bi bi-plus-circle" data-bs-toggle="modal" data-bs-target="#CreateMeterModal" title="Add Meter" onclick="ResetFormAddMeter()"> Meter</button>
											<!--<button type="button" class="btn btn-danger offline_item bi bi-cloud-slash" data-bs-toggle="modal" data-bs-target="#OfflineMModal"></button>-->
												</div>
											 </div>
										</div>
										
								
				
								<?php
							}
							else if($meter_tab==' active show'){
								?>

										<div class="col-sm-6">
											<div class="d-flex justify-content-end" id="site_option_per_tab">
												<div class="btn-group" role="group" aria-label="Basic outlined " id="meter_option" style="">
													<button type="button" class="btn btn-success new_item bi bi-plus-circle" data-bs-toggle="modal" data-bs-target="#CreateMeterModal" title="Add Meter" onclick="ResetFormAddMeter()"> Meter</button>
											<!--<button type="button" class="btn btn-danger offline_item bi bi-cloud-slash" data-bs-toggle="modal" data-bs-target="#OfflineMModal"></button>-->
												</div>
											 </div>
										</div>
										
								
								
								<?php
							}
							else if($gateway_tab==' active show'){
								?>
								
										<div class="col-sm-6">
											<div class="d-flex justify-content-end" id="site_option_per_tab">
											<div class="btn-group" role="group" aria-label="Basic outlined " id="gateway_option">
												<button type="button" class="btn btn-success new_item bi bi-plus-circle" data-bs-toggle="modal" data-bs-target="#CreateGatewayModal" title="Add Gateway" onclick="ResetFormAddGateway()"> Gateway</button>
										<!--<button type="button" class="btn btn-danger offline_item bi bi-cloud-slash" data-bs-toggle="modal" data-bs-target="#OfflineGatewayModal"></button>-->
											</div>
										</div>
										
										</div>
										
								<?php
							}
							else{
								?>
								
										<div class="col-sm-6">
											<div class="d-flex justify-content-end" id="site_option_per_tab">
											<div class="btn-group" role="group" aria-label="Basic outlined " id="gateway_option">
												<button type="button" class="btn btn-success new_item bi bi-plus-circle" data-bs-toggle="modal" data-bs-target="#CreateGatewayModal" title="Add Gateway" onclick="ResetFormAddGateway()"> Gateway</button>
										<!--<button type="button" class="btn btn-danger offline_item bi bi-cloud-slash" data-bs-toggle="modal" data-bs-target="#OfflineGatewayModal"></button>-->
											</div>
										</div>
										
										</div>
										
								<?php
							}
						   ?>
						  </div>	
						   
						   
						   
				<div class="tab-pane fade {{ $status_tab }}" id="bordered-status" role="tabpanel" aria-labelledby="status-tab">
					@include('amr.site_information')
				</div>
				
				 <div class="tab-pane fade {{ $meterlocation_tab }}" id="bordered-sitemeterlocationlist" role="tabpanel" aria-labelledby="sitemeterlocationlist-tab">
					@include('amr.site_meter_location')
				 </div>
				 
                <div class="tab-pane fade {{ $gateway_tab }}" id="bordered-sitegatewaylist" role="tabpanel" aria-labelledby="sitegatewaylist-tab">
					@include('amr.site_gateway')
				</div>
                
				 <div class="tab-pane fade {{ $meter_tab }}" id="bordered-sitemeterlist" role="tabpanel" aria-labelledby="sitemeterlist-tab">
					@include('amr.site_meter')
				 </div>
				 
				 </div>
          </div>
		  
		  </div>
    </section>
  </main>
 
@endsection

