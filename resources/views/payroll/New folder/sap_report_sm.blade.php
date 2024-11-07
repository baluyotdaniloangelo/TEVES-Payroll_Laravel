@extends('layouts.layout')  
@section('content')  

<main id="main" class="main">	
    <section class="section">	 
	<!--Modal to Create Client-->
	<div class="modal fade" id="GenerateSAPReportModal" tabindex="-1">
              <div class="modal-dialog">
                  <div class="modal-content">
                    <div class="modal-header modal-header_form">
                      <h5 class="modal-title">Generate SAP Report</h5>
					  <div class="btn-group" role="group" aria-label="Basic outlined example">	
						<button type="button" class="btn btn-danger bi bi-x-circle navbar_icon" data-bs-dismiss="modal"></button>
					  </div>
                    </div>
                    <div class="modal-body">
					
					  <form class="g-2 needs-validation" id="generate_report_form">			  
						<div class="row mb-2">
						  <label for="site_id" class="col-sm-4 col-form-label">Site</label>
						  <div class="col-sm-8">
							<input class="form-control" list="site_name" name="site_name" id="site_id" required autocomplete="off" onchange="LoadBuildingList()">
								<datalist id="site_name">
									@foreach ($site_data as $site_data_cols)
										<option label="{{$site_data_cols->site_name}}" data-id="{{$site_data_cols->id}}" data-code="{{$site_data_cols->site_code}}"  data-business-entity="{{$site_data_cols->business_entity}}" value="{{$site_data_cols->site_name}}">
									@endforeach
								</datalist>
							<span class="valid-feedback" id="site_idError"></span>
						  </div>
						</div>
						<div class="row mb-2">
						  <label for="building_id" class="col-sm-4 col-form-label">Building</label>
						  <div class="col-sm-8">
						  <input class="form-control" list="building_list" name="building_list" id="building_id" required autocomplete="off">
								<datalist id="building_list">
									<option label>
								</datalist>
								<span class="valid-feedback" id="building_idError"></span>
						  </div>
						</div>	
						<div class="row mb-2">
						  <label for="meter_role" class="col-sm-4 col-form-label">Meter Role</label>
						  <div class="col-sm-8">
										<select class="form-select form-control" id="meter_role" name="meter_role">
											<option value="" title="Client/Tenant Meter">All</option>
											<option value="Tenant Meter" title="Client/Tenant Meter">Tenant Meter</option>
											<option value="Spare Meter">Spare Meter</option>
											<option value="CUSA">CUSA</option>
											<option value="Check Meter">Check Meter</option>
										</select>
						  </div>
						</div>						
						
						<div class="row mb-2">
						  <label for="cut_off" class="col-sm-4 col-form-label">Cut Off</label>
						  <div class="col-sm-8">
							<input type="date" class="form-control " name="cut_off" id="cut_off" value="<?=date('Y-m-d');?>" required>
							<span class="valid-feedback" id="cut_offError"></span>
						  </div>
						</div>						
						
						</div>
						
                    <div class="modal-footer modal-footer_form">
						<div id="loading_data" style="display:none;">
							<div class="spinner-border text-success" role="status">
								<span class="visually-hidden">Loading...</span>
							</div>
						</div>
						<button type="submit" class="btn btn-success btn-sm bi bi-save-fill navbar_icon" id="generate_sap_report"> Submit</button>
					</div>
					</form><!-- End Multi Columns Form -->
                  </div>
                </div>
             </div>
          <div class="card">
		  
			  <div class="card">
			  
				<div class="card-header ">
				  <h5 class="card-title">&nbsp;{{ $title }}</h5>
					<div class="d-flex justify-content-end" id="">
					
					
					<div class="btn-group" role="group" aria-label="Basic outlined example" style="margin-top: -50px; position: absolute;"><button type="button" class="btn btn-success new_item bi bi-plus-circle" data-bs-toggle="modal" data-bs-target="#GenerateSAPReportModal"></button>
				</div>
					
					</div>				  
				  </div>
				</div>			  
		 
            <div class="card-body">			
				<div class="p-d3">
						<div class="row">
						
						<div class="col-sm-12 d-flex justify-content-end">
							<div id="download_options"></div>&nbsp;
							<div id="save_options"></div>
						</div>
						
						</div>
						
						<div class="row mb-2">
						
						<div class="col-sm-12">
							<div class="ms-2">
								<div class="fw-bold">BUSINESS ENTITY: <span id="business_entity_text" style="font-weight: normal;"></span></div>
							</div>
							<div class="ms-2">
								<div class="fw-bold">SITE CODE: <span id="site_code_text" style="font-weight: normal;"></span></div>			
							</div>
							<div class="ms-2">
								<div class="fw-bold">SITE DESCRIPTION: <span id="site_description_text" style="font-weight: normal;"></span></div>
							</div>
							<div class="ms-2">
								<div class="fw-bold">BUILDING CODE: <span id="building_code_text" style="font-weight: normal;"></span></div>
							</div>
							<div class="ms-2">
								<div class="fw-bold">BUILDING NAME: <span id="building_name_text" style="font-weight: normal;"></span></div>
							</div>
							<div class="ms-2">
								<div class="fw-bold">CUT OFF: <span id="cut_off_text" style="font-weight: normal;"></span></div>
							</div>
							
						</div>
						
						</div>
						
									<div class="table-responsive table-data-span">
										<table class="table table-bordered dataTable" id="sap_report_html_table" width="100%" cellspacing="0">
											<thead>
												<tr class="report">
												<th scope="col">#</th>
												<th scope="col">Meter</th>
												<th scope="col">Tenant Name</th>
												<th scope="col">Reading</th>
												<th scope="col">Date</th>
												<th scope="col">Time</th>
												<th scope="col">Measuring Point</th>
												</tr>
											</thead>				
											
											<tbody>
												
											</tbody>
											
										</table>
									</div>		
				</div>									
            </div>
          </div>
    </section>
</main>


@endsection

