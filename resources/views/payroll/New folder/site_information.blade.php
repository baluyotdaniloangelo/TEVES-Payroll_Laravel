				<div class="row">

				<div class="col-lg-6">
				
					<div class="row">

					  <div class="col-lg-6">
						<div class="card-0">
							<div class="card-body">
								<h5 class="card-title" align="center">Gateway</h5>
								<!-- Pie Chart -->
								<canvas id="gateway" style="max-height: 250px; display: block; box-sizing: border-box; height: 250px; width: 250px;"></canvas>	  
								<!-- End Pie CHart -->
							</div>
						</div>
					  </div>
					  
					  <div class="col-lg-6">
					   <div class="card-0">
							<div class="card-body">
								<h5 class="card-title" align="center">Meter</h5>
								<!-- Pie Chart -->
								<canvas id="meter" style="max-height: 250px; display: block; box-sizing: border-box; height: 250px; width: 250px;"></canvas>	  
								<!-- End Pie CHart -->
							</div>
						</div>
					  </div>
					  
					  </div>
				 
							
					</div>		
				  <div class="col-lg-6">
				  <div class="card">
				  
						<!--<div class="card-header mb-3">	
								<h5 class="card-title" align="center">Details</h5>								
						</div>-->
						<h5 class="card-title" align="center">Building Details</h5>
						<div class="card-body">
						<!--
						<ul class="nav nav-tabs nav-tabs-bordered d-flex" id="borderedTabJustified" role="tablist">
							<li class="nav-item flex-fill" role="presentation">
							  <button class="nav-link w-100 active" id="basic-tab" data-bs-toggle="tab" data-bs-target="#bordered-justified-basic" type="button" role="tab" aria-controls="basic" aria-selected="true">CAMR</button>
							</li>
						   
							<li class="nav-item flex-fill" role="presentation">
							  <button class="nav-link w-100" id="sap-tab" data-bs-toggle="tab" data-bs-target="#bordered-justified-sap" type="button" role="tab" aria-controls="sap" aria-selected="false" tabindex="-1">SAP</button>
							</li>
						  </ul>
						 -->
						 
						  <div class="tab-content pt-2" id="borderedTabJustifiedContent">
							<div class="tab-pane fade active show" id="bordered-justified-basic" role="tabpanel" aria-labelledby="status-tab">
							  
							<ol class="list-group list-group-flush list-group-numbered">
							<li class="list-group-item d-flex justify-content-between align-items-start">
							  <div class="ms-2 me-auto">
								<div class="fw-bold">Building Code: {{ $SiteData[0]->building_code }}</div>
								
							  </div>
							  
							</li>
							<li class="list-group-item d-flex justify-content-between align-items-start">
							  <div class="ms-2 me-auto">
								<div class="fw-bold">Building Description: {{ $SiteData[0]->building_description }}</div>
								
							  </div>
							  
							</li>
							<li class="list-group-item d-flex justify-content-between align-items-start">
							  <div class="ms-2 me-auto">
								<div class="fw-bold">Company: {{ $SiteData[0]->company_name }}</div>
								
							  </div>
							  
							</li>
							<li class="list-group-item d-flex justify-content-between align-items-start">
							  <div class="ms-2 me-auto">
								<div class="fw-bold">Division: {{ $SiteData[0]->division_code }} - {{ $SiteData[0]->division_name }}</div>
								
							  </div>
							  
							</li>
							<li class="list-group-item d-flex justify-content-between align-items-start">
							  <div class="ms-2 me-auto">
								<div class="fw-bold">IP Address Range: {{ $SiteData[0]->device_ip_range }}</div>
								
							  </div>
							  
							</li>
							<li class="list-group-item d-flex justify-content-between align-items-start">
							  <div class="ms-2 me-auto">
								<div class="fw-bold">Netmask: {{ $SiteData[0]->ip_netmask }}</div>
								
							  </div>
							  
							</li>
							
							<li class="list-group-item d-flex justify-content-between align-items-start">
							  <div class="ms-2 me-auto">
								<div class="fw-bold">Network: {{ $SiteData[0]->ip_network }}</div>
								
							  </div>
							  
							</li>
							
							<li class="list-group-item d-flex justify-content-between align-items-start">
							  <div class="ms-2 me-auto">
								<div class="fw-bold">Gateway: {{ $SiteData[0]->ip_gateway }}</div>
								
							  </div>
							  
							</li>
							
						 </ol>

							 </div>
							 <!--
							
							<div class="tab-pane fade" id="bordered-justified-sap" role="tabpanel" aria-labelledby="sap-tab">
							  
							<ol class="list-group list-group-numbered">
							<li class="list-group-item d-flex justify-content-between align-items-start">
							  <div class="ms-2 me-auto">
								<div class="fw-bold">Business Entity</div>
								{{ $SiteData[0]->business_entity }}
							  </div>
							  
							</li>
							
							<li class="list-group-item d-flex justify-content-between align-items-start">
							  <div class="ms-2 me-auto">
								<div class="fw-bold">Company Number</div>
								{{ $SiteData[0]->company_no }}
							  </div>
							  
							</li>
							
							<li class="list-group-item d-flex justify-content-between align-items-start">
							  <div class="ms-2 me-auto">
								<div class="fw-bold">Service Charge Key</div>
								{{ $SiteData[0]->service_charge_key }}
							  </div>
							  
							</li>
							
							<li class="list-group-item d-flex justify-content-between align-items-start">
							  <div class="ms-2 me-auto">
								<div class="fw-bold">Participation Group</div>
								{{ $SiteData[0]->participation_group }}
							  </div>
							  
							</li>
							
							<li class="list-group-item d-flex justify-content-between align-items-start">
							  <div class="ms-2 me-auto">
								<div class="fw-bold">Settlement Unit:</div>
								{{ $SiteData[0]->settlement_unit }}
							  </div>
							  
							</li>
							
							<li class="list-group-item d-flex justify-content-between align-items-start">
							  <div class="ms-2 me-auto">
								<div class="fw-bold">Settlement Variant Text</div>
								{{ $SiteData[0]->settlement_variant_text }}
							  </div>
							  
							</li>
							
							<li class="list-group-item d-flex justify-content-between align-items-start">
							  <div class="ms-2 me-auto">
								<div class="fw-bold">Settlement Validity</div>
								{{ $SiteData[0]->settlement_valid_from }} - {{ $SiteData[0]->settlement_valid_to }}
							  </div>
							  
							</li>
							
							<li class="list-group-item d-flex justify-content-between align-items-start">
							  <div class="ms-2 me-auto">
								<div class="fw-bold">Date Created</div>
								{{ $SiteData[0]->sap_created_on }} {{ $SiteData[0]->sap_created_at }}
							  </div>
							  
							</li>
							
							<li class="list-group-item d-flex justify-content-between align-items-start">
							  <div class="ms-2 me-auto">
								<div class="fw-bold">Last Edited</div>
								{{ $SiteData[0]->sap_last_edited_on }} {{ $SiteData[0]->sap_last_edited_at }}
							  </div>
							  
							</li>
							
							<li class="list-group-item d-flex justify-content-between align-items-start">
							  <div class="ms-2 me-auto">
								<div class="fw-bold">SAP Server</div>
								{{ $SiteData[0]->sap_server }}
							  </div>
							  
							</li>
							
						  </ol>
						  </div>
						  -->
						  </div>
					 
					 </div>
						<!--
						<div class="card-footer">
						  
						</div>
						-->
					  </div>
				  </div>				  
				  
				</div>
			<div class="row">

				  <div class="col-lg-12">
					  <div class="card mb-3">
							<ul class="nav nav-tabs" id="myTab2" role="tablist">
                
							<li class="nav-item" role="presentation">
							  <button class="nav-link active bi bi-cpu" id="home-tab" data-bs-toggle="tab" data-bs-target="#home2" type="button" role="tab" aria-controls="home2" aria-selected="true">&nbsp;Gateway</button>
							</li>
							<li class="nav-item" role="presentation">
							  <button class="nav-link bi bi-speedometer" id="profile-tab2" data-bs-toggle="tab" data-bs-target="#profile2" type="button" role="tab" aria-controls="profile2" aria-selected="false" tabindex="-1">&nbsp;Meter</button>
							</li>
               
							</ul>
							
						    <div class="tab-content pt-2" id="myTabContent">
							<div class="tab-pane fade show active" id="home2" role="tabpanel" aria-labelledby="home2-tab">
							
							<div class="d-flex justify-content-end" id="">
								<div class="btn-group" role="group" aria-label="Basic outlined example" style="margin-top: -50px; position: absolute;">
									<a href="#" class="btn btn-warning new_item bi bi bi-download form_button_icon" onclick="downloadofflinegateway();" title="Download Offline Gateway"> Offline Gateway</a> 
								</div>					
							</div>
							
									<div class="table-responsive">
										<table class="table dataTable display nowrap cell-border" id="offlinegatewaylist" width="100%" cellspacing="0">
											<thead>
												<tr>
													<th class="all">#</th>
													<th nowrap class="all" title="Gateway Device Serial Number">Serial Number</th>
													<th nowrap class="all" title="Gateway Device IP Address">IP Address</th>
													<th class="all" title="Gateway Device MAC Address">MAC Address</th>
														
													<th title="Location Code" class="all" title="Gateway Device Location Code">Location Code</th>
													<th class="" title="Last Online Date. Status is based on Last Reading Logs from Meters">Last Online</th>													<th title="Location Description" class="none" title="Gateway Device Location Description">Location Description: </th>		
													<th class="none" title="IDF Name assigned for the Gateway">IDF</th>
													<th class="none" title="Switch assigned for the Gateway">Switch</th>
													<th class="none" title="Port assigned for the Gateway">Port</th>
												
													<!--<th class="all">#</th>
													<th nowrap class="all" title="Gateway Serial Number">Serial Number</th>
													<th nowrap class="all" title="Gateway IP Address/Mobile Number for 3g/4g Modem">IP Address</th>
													<th nowrap class="all" title="Gateway MAC Address">MAC Address</th>
													<th title="Gateway Area/Location">Location</th>
													<th class="" title="Last Online Date. Status is based on Last Reading Logs from Meters">Last Online</th>	
													<th nowrap class="none">IDF</th>
													<th nowrap class="none">Switch</th>
													<th nowrap class="none">Port</th>-->
												</tr>
											</thead>				
											
											<tbody>
												
											</tbody>
											
										</table>
																
									</div>
							</div>
							
							<div class="tab-pane fade" id="profile2" role="tabpanel" aria-labelledby="profile-tab2">
							
							<div class="d-flex justify-content-end" id="">
								<div class="btn-group" role="group" aria-label="Basic outlined example" style="margin-top: -50px; position: absolute;">
									<a href="#" class="btn btn-warning new_item bi bi bi-download form_button_icon" onclick="downloadofflinemeter();" title="Download Offline Meter"> Offline Meter</a> 
								</div>					
							</div>

							<div class="table-responsive">
										<table class="table dataTable display nowrap cell-border" id="meterofflinelist" width="100%" cellspacing="0">
											<thead>
												<tr>
													<th class="all">#</th>
													<!--<th title="SAP Measuring Point" nowrap class="">Measuring Point</th>-->
													<th title="Meter Serial Number" nowrap class="all">Meter Description</th>
													<th title="Meter Tagging/Tenant/Client Name" nowrap class="all">Name/Tagging</th>													
													<th title="Last Reading Date" nowrap class="">Last Online</th>
													<th title="Active/Inactive" class="">Status</th>
													<th title="The Gateway Serial Number of Meter" class="none">Gateway Serial Number: </th>
													<th title="Location Code" class="none">Location Code: </th>
													<th title="Location Description" class="none">Location Description: </th>
													<th title="Role : Tenant Meter/Tenant, Spare Meter, CUSA or Check Meter" nowrap class="none">Meter Role: </th>
													<th title="" class="none">Configuration File: </th>
													<th title="" class="none">Alternate Address: </th>
													<th title="" class="none">Remarks: </th>
												</tr>
											</thead>				
											
											<tbody>
												
											</tbody>
											
										</table>
										
									</div>
									
							</div>

									
							</div>
							
					  <div>
				  </div>
				  
			</div></div>
				
			</div>				
				
			
				
				