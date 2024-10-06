@extends('layouts.layout')  
@section('content')  

<main id="main" class="main">	
    <section class="section">	 
	<!--Modal to Create Client-->
	<div class="modal fade" id="GenerateSAPReportModal" tabindex="-1">
              <div class="modal-dialog">
                  <div class="modal-content">
                    <div class="modal-header modal-header_form">
                      <h5 class="modal-title">Generate Building Report</h5>
					  <div class="btn-group" role="group" aria-label="Basic outlined example">	
						<button type="button" class="btn btn-danger bi bi-x-circle navbar_icon" data-bs-dismiss="modal"></button>
					  </div>
                    </div>
                    <div class="modal-body">
					
					  <form class="g-2 needs-validation" id="generate_report_form">			  
						<div class="row mb-2">
						  <label for="site_id" class="col-sm-3 col-form-label">Building</label>
						  <div class="col-sm-9">
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
						  <label for="meter_role" class="col-sm-3 col-form-label">Meter Role</label>
						  <div class="col-sm-9">
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
							<label for="start_date" class="col-sm-3 col-form-label">Start Date</label>
								<div class="col-sm-9">
									<div class="input-group">
										<?php
										
										$next_day = date('d')+1;
										
										$current_date = date('Y-m-d');
										$previous_date =  date('Y-m-d', strtotime($current_date. ' - 1 days'));
										
										?>	
										<input type="date" class="form-control " name="start_date" id="start_date" value="" max="9999-12-31" required onchange="setMaxonEndDate()">
										<input type="time" class="form-control " name="start_time" id="start_time" value="<?=date('00:00');?>" required>
										<span class="valid-feedback" id="start_dateError"></span>	
									</div>
								</div>
						</div>						
											
						<div class="row mb-2">
							<label for="end_date" class="col-sm-3 col-form-label">End Date</label>
								<div class="col-sm-9">
									<div class="input-group">	
										<input type="date" class="form-control " name="end_date" id="end_date" value="<?=$current_date;?>" max="9999-12-31" required onchange="CheckEndDateValidity()">
										<input type="time" class="form-control " name="end_time" id="end_time" value="<?=date('23:59');?>" required>
										<span class="valid-feedback" id="end_dateError"></span>	
									</div>
								</div>
						</div>
						
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
				
				<style>
				
				table.dataTable td {
				  font-size: 0.9em;
				}
								table.dataTable th {
				  font-size: 0.9em;
				}
				</style>
		 
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
							<li class="list-group-item"><b>Date Start:</b><span id="date_start_txt" style="font-weight: normal;"></span></li>
							<li class="list-group-item"><b>Date Start:</b><span id="date_end_txt" style="font-weight: normal;"></span></li>
							<li class="list-group-item"><b>Total Consumption:</b><span id="total_current_consumption_top" style="font-weight: normal;"></span></li>
							
							<li class="list-group-item"></li>
						  </ul>
						  
						</div>
						
						<div class="col-sm-8">
						
							
						</div>
						
						</div>		
						
									<div class="table-responsive table-data-span">
										<table class="table dataTable display nowrap cell-border"  id="site_report_html_table" width="100%" cellspacing="0">
											<thead>
												<tr class="report">
													<th>#</th>
													<th>Tenant Name</th>
													<th>Meter Description</th>
													<th class="none">Gateway</th>
													<th class="none">Physical Location</th>
													<th>Date Start</th>
													<th>Start Reading</th>
													<th>Date End</th>
													<th>End Reading</th>
													<th class="all">Multiplier</th>
													<th class="all">KWh</th>
												</tr>
											</thead>				
											
											<tbody>
												
											</tbody>
											<!----><tfoot>
												<tr>
													<th scope="col" colspan="10" style="text-align: right;">Total KWh:</th>
													<th scope="col" id="total_current_consumption" style="text-align: right;">0.00</th>
												</tr>
											</tfoot>	
										</table>
									</div>		
									<br>
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

