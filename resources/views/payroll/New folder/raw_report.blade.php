@extends('layouts.layout')  
@section('content')  

<main id="main" class="main">	
    <section class="section">	 
 	<!--Modal to Create Client-->
	<div class="modal fade" id="GenerateRAWReportModal" tabindex="-1">
              <div class="modal-dialog">
                  <div class="modal-content">
                    <div class="modal-header modal-header_form">
                      <h5 class="modal-title">Generate Raw Report</h5>
					  <div class="btn-group" role="group" aria-label="Basic outlined example">	
						<button type="button" class="btn btn-danger bi bi-x-circle navbar_icon" data-bs-dismiss="modal"></button>
					  </div>
                    </div>
					<form class="g-2 needs-validation" id="generate_report_form">
                    <div class="modal-body">
						<div class="row">
							<div class="col-lg-6tt">
								
								<div class="row mb-2">
								
									<label for="site_id" class="col-sm-3 col-form-label">Building</label>
									  <div class="col-sm-9">
										<input class="form-control" list="site_name" name="site_name" id="site_id" required autocomplete="off" onchange="LoadMeterList()">
											<datalist id="site_name">
												@foreach ($site_data as $site_data_cols)
													<option label="{{$site_data_cols->building_code}} - {{$site_data_cols->building_description}}" data-id="{{$site_data_cols->site_id}}" data-code="{{$site_data_cols->building_code}}"  data-description="{{$site_data_cols->building_description}}" value="{{$site_data_cols->building_code}} - {{$site_data_cols->building_description}}">
												@endforeach
											</datalist>
										<span class="valid-feedback" id="site_idError"></span>
									  </div>
									</div>
									
									<div class="row mb-2">
									  <label for="meter_id" class="col-sm-3 col-form-label">Meter</label>
									  <div class="col-sm-9">
									  <input class="form-control" list="meter_list" name="meter_list" id="meter_id" required autocomplete="off">
											<datalist id="meter_list">
												<option label>
											</datalist>
											<span class="valid-feedback" id="meter_idError"></span>
									  </div>
									</div>	
									
									<div class="row mb-2">
									  <label for="start_date" class="col-sm-3 col-form-label">Start Date</label>
									  <div class="col-sm-9">
										<div class="input-group">	
										<input type="date" class="form-control " name="start_date" id="start_date" value="<?=date('Y-m-d');?>" max="9999-12-31" required onchange="setMaxonEndDate()">
										<input type="time" class="form-control " name="start_time" id="start_time" value="<?=date('00:00');?>" required>
										<span class="valid-feedback" id="start_dateError"></span>	
										</div>
									  </div>
									</div>						
											
									<div class="row mb-2">
									  <label for="end_date" class="col-sm-3 col-form-label">End Date</label>
									  <div class="col-sm-9">
										<div class="input-group">	
										<input type="date" class="form-control " name="end_date" id="end_date" value="<?=date('Y-m-d');?>" required onchange="CheckEndDateValidity()">
										<input type="time" class="form-control " name="end_time" id="end_time" value="<?=date('23:59');?>" required>
										<span class="valid-feedback" id="end_dateError"></span>	
										</div>
									  </div>
									</div>
									
									<div class="row mb-2">
									  <label for="end_date" class="col-sm-3 col-form-label">Column</label>
									  <div class="col-sm-9">
										<div class="form-check">
										  <input class="form-check-input" type="checkbox" id="cols_set1">
										  <label class="form-check-label" for="cols_set1">
											vrms_a,vrms_b,vrms_c
										  </label>
										</div>
										
										<div class="form-check">
										  <input class="form-check-input" type="checkbox" id="cols_set2">
										  <label class="form-check-label" for="cols_set2">
											irms_a,irms_b,irms_c
										  </label>
										</div>
										
										<div class="form-check">
										  <input class="form-check-input" type="checkbox" id="cols_set3">
										  <label class="form-check-label" for="cols_set3">
											freq,pf,kw,kva,kvar
										  </label>
										</div>
										
										<div class="form-check">
										  <input class="form-check-input" type="checkbox" id="cols_set4" checked="" disabled>
										  <label class="form-check-label" for="cols_set4">
											kwh_del,kwh_rec,kwh_net,kwh_total
										  </label>
										</div>
										
										<div class="form-check">
										  <input class="form-check-input" type="checkbox" id="cols_set5">
										  <label class="form-check-label" for="cols_set5">
											kvarh_neg,kvarh_pos,kvarh_total,kvah_total
										  </label>
										</div>
										
										<div class="form-check">
										  <input class="form-check-input" type="checkbox" id="cols_set6">
										  <label class="form-check-label" for="cols_set6">
											max_rec_kw_dmd,max_rec_kw_dmd_time
										  </label>
										</div>
										
										<div class="form-check">
										  <input class="form-check-input" type="checkbox" id="cols_set7">
										  <label class="form-check-label" for="cols_set7">
											MAC Address,Firmware Revision
										  </label>
										</div>
									</div>
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
							
						  <button type="submit" class="btn btn-success btn-sm bi bi-save-fill navbar_icon" id="generate_raw_report" onclick="get_raw_data()"> Generate</button>
					</div>
					</form>
                  </div>
                </div>
             </div>
			 </div>
          <div class="card">
		  
			  <div class="card">
			  
				<div class="card-header ">
				  <h5 class="card-title">&nbsp;{{ $title }}</h5>
					<div class="d-flex justify-content-end" id="">

					<div class="btn-group" role="group" aria-label="Basic outlined example" style="margin-top: -50px; position: absolute;"><button type="button" class="btn btn-success new_item bi bi-option" data-bs-toggle="modal" data-bs-target="#GenerateRAWReportModal"> Report Parameters</button>
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
							<li class="list-group-item"><b>Meter Description:</b><span id="meter_description" style="font-weight: normal;"></span></li>
							<li class="list-group-item"><b>Tenant Name:</b><span id="tenant_name" style="font-weight: normal;"></span></li>
							<li class="list-group-item"><b>Gateway Serial Number:</b><span id="gateway_description" style="font-weight: normal;"></span></li>
							<li class="list-group-item"><b>Building Code:</b><span id="building_code" style="font-weight: normal;"></span></li>
							<li class="list-group-item"><b>Building Description:</b><span id="building_name" style="font-weight: normal;"></span></li>
							<li class="list-group-item"><b>Date Start:</b><span id="date_start_txt" style="font-weight: normal;"></span></li>
							<li class="list-group-item"><b>Date End:</b><span id="date_end_txt" style="font-weight: normal;"></span></li>
							<li class="list-group-item"></li>
						  </ul>
						  
						</div>
						
						<div class="col-sm-8">
							
						</div>
						<!--<style>
						table.dataTable td {
						  font-size: 0.9em;
						}
										table.dataTable th {
						  font-size: 0.9em;
						}
						</style>-->
						</div>								
						<div class="row">
						<div class="p-1" align="">
									<div class="table-responsive">
										<table class="table dataTable display nowrap cell-border" id="raw_report_html_table" width="100%" cellspacing="0">
											<thead>
												<tr class="">
												<th class="all" >#</th>
												<th class="all" >Datetime</th>
												<th class="" >kwh_del</th>
												<th class="" >kwh_rec</th>
												<th class="" >kwh_net</th>
												<th style="" class="all" >kwh_total</th>
												
												<th class="" >vrms_a</th>
												<th class="" >vrms_b</th>
												<th class="" >vrms_c</th>
												
												<th class="" >irms_a</th>
												<th class="" >irms_b</th>
												<th class="" >irms_c</th>
												
												<th class="" >freq</th>
												<th class="" >pf</th>
												<th class="" >kw</th>
												<th class="" >kva</th>
												<th class="" >kvar</th>
												
												<th class="" >kvarh_neg</th>
												<th class="" >kvarh_pos</th>
												<th class="" >kvarh_net</th>
												<th class="" >kvarh_total</th>
												<th class="" >kvah_total</th>
												
												<th class="" >max_rec_kw_dmd</th>
												<th class="" >max_rec_kw_dmd_time</th>
												
												<th class="none" >MAC Address</th>
												<th class="none" >Firmware Revision</th>
												</tr>
											</thead>				
											
											<tbody>
												
											</tbody>
											
											<!--
											<tfoot>
												<tr class="">
												<th style="text-align:center;" class="all" nowrap>#</th>
												<th style="text-align:center;" class="all" nowrap>Datetime</th>
												<th style="text-align:right;" class="all" nowrap>kwh_del</th>
												<th style="text-align:right;" class="all" nowrap>kwh_rec</th>
												<th style="text-align:right;" class="all" nowrap>kwh_net</th>
												<th style="text-align:right;" class="all" nowrap>kwh_total</th>
												
												<th style="text-align:right;" class="none" nowrap>vrms_a</th>
												<th style="text-align:right;" class="none" nowrap>vrms_b</th>
												<th style="text-align:right;" class="none" nowrap>vrms_c</th>
												
												<th style="text-align:right;" class="none" nowrap>irms_a</th>
												<th style="text-align:right;" class="none" nowrap>irms_b</th>
												<th style="text-align:right;" class="none" nowrap>irms_c</th>
												
												<th style="text-align:right;" class="none" nowrap>freq</th>
												<th style="text-align:right;" class="none" nowrap>pf</th>
												<th style="text-align:right;" class="none" nowrap>kw</th>
												<th style="text-align:right;" class="none" nowrap>kva</th>
												<th style="text-align:right;" class="none" nowrap>kvar</th>
												
												<th style="text-align:right;" class="none" nowrap>kvarh_neg</th>
												<th style="text-align:right;" class="none" nowrap>kvarh_pos</th>
												<th style="text-align:right;" class="none" nowrap>kvarh_net</th>
												<th style="text-align:right;" class="none" nowrap>kvarh_total</th>
												<th style="text-align:right;" class="none" nowrap>kvah_total</th>
												
												<th style="text-align:right;" class="none" nowrap>max_rec_kw_dmd</th>
												<th style="text-align:center;" class="none" nowrap>max_rec_kw_dmd_time</th>
												
												<th style="text-align:center;" class="none" nowrap>MAC Address</th>
												<th style="text-align:center;" class="none" nowrap>Firware Revision</th>
												</tr>
											</tfoot>
											-->
											
										</table>
									</div>	</div>	</div>		
				</div>									
            </div>
          </div>
		  <br>
		  <br>
		  <br>
		  
    </section>
</main>


@endsection

