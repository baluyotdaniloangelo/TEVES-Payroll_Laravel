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
						   <!--OPTIONS HERE-->
						   <div class="d-flex justify-content-end" id="site_option_per_tab">
						   <?php
						   if($meterlocation_tab==' active show'){
								?>
								<!--<div class="btn-group" role="group" aria-label="Basic outlined " id="meterlocation_option" style="margin-top: -50px;position: absolute;">
								<button type="button" class="btn btn-success new_item bi bi-plus-circle" data-bs-toggle="modal" data-bs-target="#CreateMeterLocationModal"></button>
								</div>-->
								<?php
							}
							else if($meter_tab==' active show'){
								?>
								<div class="btn-group" role="group" aria-label="Basic outlined " id="meter_option" style="margin-top: -50px;position: absolute;">
								<button type="button" class="btn btn-success new_item bi bi-plus-circle" data-bs-toggle="modal" data-bs-target="#CreateMeterModal" title="Add Meter" onclick="ResetFormAddMeter()"> Meter</button>
								<!--<button type="button" class="btn btn-danger offline_item bi bi-cloud-slash" data-bs-toggle="modal" data-bs-target="#OfflineMModal"></button>-->
								</div>
								<?php
							}
							else if($gateway_tab==' active show'){
								?>
								<div class="btn-group" role="group" aria-label="Basic outlined " id="gateway_option" style="margin-top: -50px;position: absolute;">
								<button type="button" class="btn btn-success new_item bi bi-plus-circle" data-bs-toggle="modal" data-bs-target="#CreateGatewayModal" title="Add Gaterway" onclick="ResetFormAddGateway()"> Gateway</button>
								<!--<button type="button" class="btn btn-danger offline_item bi bi-cloud-slash" data-bs-toggle="modal" data-bs-target="#OfflineGatewayModal"></button>-->
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
           	
              <!-- Bordered Tabs -->
			  
              <ul class="nav nav-tabs nav-tabs-bordered card-zero-btm" id="borderedTab" role="tablist">
               <!----> <li class="nav-item" role="presentation">
                  <button class="nav-link bi bi-info-circle{{ $status_tab }}" id="status-tab" data-bs-toggle="tab" data-bs-target="#bordered-status" type="button" role="tab" aria-controls="status" aria-selected="{{ $status_aria_selected }}" onclick="remember_sitetab('status')"> Status & Information</button>
                </li>
				<!--<li class="nav-item" role="presentation">
                  <button class="nav-link bi bi-building{{ $building_tab }}" id="sitebuildinglist-tab" data-bs-toggle="tab" data-bs-target="#bordered-sitebuildinglist" type="button" role="tab" aria-controls="sitebuildinglist" aria-selected="{{ $building_aria_selected }}" tabindex="-1" onclick="remember_sitetab('building')"> Building</button>
                </li>--> 
				
				<li class="nav-item" role="presentation">
                  <button class="nav-link bi bi-door-closed{{ $meterlocation_tab }}" id="sitemeterlocationlist-tab" data-bs-toggle="tab" data-bs-target="#bordered-sitemeterlocationlist" type="button" role="tab" aria-controls="sitemeterlocationlist" aria-selected="{{ $meterlocation_aria_selected }}" tabindex="-1" onclick="remember_sitetab('meterlocation')"> Area/EE Room</button>
                </li>
				<!--
                <li class="nav-item" role="presentation">
                  <button class="nav-link bi bi-cpu{{ $gateway_tab }}" id="sitegatewaylist-tab" data-bs-toggle="tab" data-bs-target="#bordered-sitegatewaylist" type="button" role="tab" aria-controls="sitegatewaylist" aria-selected="{{ $gateway_aria_selected }}" tabindex="-1" onclick="remember_sitetab('gateway')" title="List of All Gateways for this Site"> Gateway</button>
                </li>
                <li class="nav-item" role="presentation">
                  <button class="nav-link bi bi-speedometer{{ $meter_tab }}" id="sitemeterlist-tab" data-bs-toggle="tab" data-bs-target="#bordered-sitemeterlist" type="button" role="tab" aria-controls="sitemeterlist" aria-selected="{{ $meter_aria_selected }}" tabindex="-1" onclick="remember_sitetab('meter')" title="List of All Meters for this Site"> Meter</button>
                </li>--> 
				
              </ul>
			  
				<div class="tab-content pt-2 card-zero-btm" id="borderedTabContent">
				
                <!---->
				<div class="tab-pane fade {{ $status_tab }}" id="bordered-status" role="tabpanel" aria-labelledby="status-tab">
					@include('amr.site_information')
				</div>
				<div class="tab-pane fade {{ $meterlocation_tab }}" id="bordered-sitemeterlocationlist" role="tabpanel" aria-labelledby="sitemeterlocationlist-tab">
					@include('amr.site_building_ee_rooms')
				 </div>
				 <!--
				 <div class="tab-pane fade {{ $meterlocation_tab }}" id="bordered-sitemeterlocationlist" role="tabpanel" aria-labelledby="sitemeterlocationlist-tab">
					@in3cd3ludde('admr.site_meter_location')
				 </div>
				 
                <div class="tab-pane fade {{ $gateway_tab }}" id="bordered-sitegatewaylist" role="tabpanel" aria-labelledby="sitegatewaylist-tab">
					//@inclhhhude('hhhhamr.site_gateway')
				</div>
                
				 <div class="tab-pane fade {{ $meter_tab }}" id="bordered-sitemeterlist" role="tabpanel" aria-labelledby="sitemeterlist-tab">
					@inclsdsssude('amr.site_meter')
				 </div>-->
				 
				 </div>
          </div>
		  
		  </div>
    </section>
  </main>
 
@endsection

