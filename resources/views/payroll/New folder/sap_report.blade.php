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
						  <label for="site_id" class="col-sm-4 col-form-label">Building</label>
						  <div class="col-sm-8">
							<input class="form-control" list="site_name" name="site_name" id="site_id" required autocomplete="off">
								<datalist id="site_name">
									@foreach ($site_data as $site_data_cols)
										<option label="{{$site_data_cols->building_code}} - {{$site_data_cols->building_description}}" data-id="{{$site_data_cols->site_id}}" data-code="{{$site_data_cols->building_code}}"  data-description="{{$site_data_cols->building_description}}" value="{{$site_data_cols->building_code}} - {{$site_data_cols->building_description}}">
									@endforeach
								</datalist>
							<span class="valid-feedback" id="site_idError"></span>
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
						  <label for="start_date" class="col-sm-4 col-form-label">Cut Off</label>
						  <div class="col-sm-8">
							<input type="date" class="form-control " name="start_date" id="start_date" value="<?=date('Y-m-d');?>" min="2023-01-01" max="9999-12-31" required>
							<span class="valid-feedback" id="start_dateError"></span>
						  </div>
						</div>						
						
						<!--
						<div class="row mb-2">
						  <label for="end_date" class="col-sm-4 col-form-label">End Date</label>
						  <div class="col-sm-8">
							<input type="date" class="form-control " name="end_date" id="end_date" value="<?=date('Y-m-d');?>" max="9999-12-31" required>
							<span class="valid-feedback" id="end_dateError"></span>
						  </div>
						</div>
						-->
						
						</div>
						
                    <div class="modal-footer modal-footer_form">
						<div id="loading_data" style="display:none;">
							<div class="spinner-border text-success" role="status">
								<span class="visually-hidden">Loading...</span>
							</div>
						</div>
						<button type="submit" class="btn btn-success btn-sm bi bi-save-fill navbar_icon" id="generate_sap_report"> Generate</button>
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
					
					
					<div class="btn-group" role="group" aria-label="Basic outlined example" style="margin-top: -50px; position: absolute;"><button type="button" class="btn btn-success new_item bi bi-option" data-bs-toggle="modal" data-bs-target="#GenerateSAPReportModal"> Report Parameters</button>
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
						
						<div class="col-sm-4">
						
						  <ul class="list-group list-group-flush">
							<li class="list-group-item"><b>Building Code:</b><span id="building_code" style="font-weight: normal;"></span></li>
							<li class="list-group-item"><b>Building Description:</b><span id="building_name" style="font-weight: normal;"></span></li>
							<li class="list-group-item"><b>Cut Off:</b><span id="cut_off" style="font-weight: normal;"></span></li>
							<li class="list-group-item"></li>
						  </ul>
						  
						</div>
						
						<div class="col-sm-8">
						
							
						</div>
						
						</div>		
						
						
									<div class="table-responsive table-data-span">
										<table class="table dataTable display nowrap cell-border" id="sap_report_html_table" width="100%" cellspacing="0">
											<thead>
												<tr>
												<th>#</th>
												<th>Meter</th>
												<th>Reading</th>
												<th>Date</th>
												<th>Time</th>
												<th>Initial</th>
												<th>Building</th>
												<th>Tenant Name</th>
												<th>Meter Type</th>
												<th>Current Reading</th>
												<th>Previous Reading</th>
												<th>Multiplier</th>
												<th>Current Cons.</th>
												<th>Previous Cons.</th>
												<th>% Diff</th>	
												</tr>
											</thead>				
											
											<tbody>
												
											</tbody>
											
										</table>
									</div>		
									<br>
									<br>
									<br>
									<br>
									<br>
				</div>									
            </div>
          </div>
    </section>
</main>


@endsection

