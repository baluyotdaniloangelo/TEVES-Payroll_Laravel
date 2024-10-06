@extends('layouts.layout')  
@section('content')  

<main id="main" class="main">	
    <section class="section">	  
          <div class="card">
		  
			  <div class="card">
			  
				<div class="card-header ">
				  <h5 class="card-title">&nbsp;{{ $title }}</h5>
					<div class="d-flex justify-content-end" id="billing_option"></div>				  
				  </div>
				</div>			  
		 
            <div class="card-body">			
				<div class="p-d3">
									<div class="table-responsive">
									<table class="table dataTable display nowrap cell-border" id="getSOBillingTransactionList" width="100%" cellspacing="0">
											<thead>
												<tr>
													<th class="all">#</th>
													<th class="all">Date</th>
													<th class="all">Time</th>
													<th class="all">S.O No.</th>
													<th class="all">Account Name</th>
													<th class="none">Driver's Name</th>
													<th class="none">Description</th>
													<th>Action</th>
												</tr>
											</thead>				
											
											<tbody>
												
											</tbody>
	
											
										</table>
									</div>		
				</div>									
                   
            </div>
          </div>

	<!-- Bill Delete Modal-->
    <div class="modal fade" id="BillDeleteModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header header_modal_bg">
                    <h5 class="modal-title" id="exampleModalLabel"></h5>
 					<div class="btn-sm btn-warning btn-circle bi bi-exclamation-circle btn_icon_modal"></div>
                </div>
				
				
                <div class="modal-body warning_modal_bg" id="modal-body">
				Are you sure you want to Delete This Bill?<br>
				</div>
				<div align="left"style="margin: 10px;">
				Date: <span id="bill_delete_order_date"></span><br>
				Time: <span id="bill_delete_order_time"></span><br>
				
				PO #: <span id="bill_delete_order_po_number"></span><br>
				Client: <span id="bill_delete_client_name"></span><br>
				
				Description: <span id="bill_delete_plate_no"></span><br>
				Driver: <span id="bill_delete_drivers_name"></span><br>
				
				Product: <span id="bill_delete_product_name"></span><br>
				Quantity: <span id="bill_delete_order_quantity"></span><br>
				
				Total Amount: <span id="bill_delete_order_total_amount"></span><br>
				
				
				</div>
                <div class="modal-footer footer_modal_bg">
                    
					<button type="button" class="btn btn-danger" data-bs-dismiss="modal" id="deleteBillConfirmed" value=""><i class="bi bi-trash3 form_button_icon"></i> Delete</button>
					<button type="button" class="btn btn-primary" data-bs-dismiss="modal"><i class="bi bi-x-circle form_button_icon"></i> Cancel</button>
                  
                </div>
            </div>
        </div>
    </div>	

	<!-- Bill Info Modal-->
    <div class="modal fade" id="ViewBillingModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header header_modal_bg">
                    <h5 class="modal-title" id="exampleModalLabel"></h5>
 					<div class="btn-sm btn-warning btn-circle bi bi-exclamation-circle btn_icon_modal"></div>
                </div>
		
                <div class="modal-body warning_modal_bg" id="modal-body">
				Bill Information<br>
				</div>
				<div align="left"style="margin: 10px;">
				Date: <span id="view_order_date"></span><br>
				Time: <span id="view_order_time"></span><br>
				
				PO #: <span id="view_order_po_number"></span><br>
				Client: <span id="view_client_name"></span><br>
				
				Description: <span id="view_plate_no"></span><br>
				Driver: <span id="view_drivers_name"></span><br>
				
				Product: <span id="view_product_name"></span><br>
				Price: <span id="view_product_price"></span><br>
				Quantity: <span id="view_order_quantity"></span><br>
				
				Total Amount: <span id="ViewTotalAmount"></span><br>
				
				</div>
                <div class="modal-footer footer_modal_bg">
                    
					<button type="button" class="btn btn-primary" data-bs-dismiss="modal"><i class="bi bi-x-circle form_button_icon"></i> Close</button>
                  
                </div>
            </div>
        </div>
    </div>	

    </section>
</main>

	<!--Modal to Create SO Product-->
	<div class="modal fade" id="AddSOModal" tabindex="-1">
              <div class="modal-dialog modal-lg">
                  <div class="modal-content">
                    <div class="modal-header modal-header_form">
                      <h5 class="modal-title">Create SO Information</h5>
					  <div class="btn-group" role="group" aria-label="Basic outlined example">		
						
						<button type="button" class="btn btn-danger bi bi-x-circle form_button_icon" data-bs-dismiss="modal"></button>
					  </div>
                    </div>
                    <div class="modal-body">
					
							 <div class="card-body">
						<br>
						<form class="g-3 needs-validation" id="SOBillingformNew">
							
														<div class="row mb-2">
														  <label for="branch_id" class="col-sm-3 col-form-label">Branch</label>
														  <div class="col-sm-9">
															<select class="form-select form-control" required="" name="branch_id" id="branch_id">
															@foreach ($teves_branch as $teves_branch_cols)
																<option value="{{$teves_branch_cols->branch_id}}">{{$teves_branch_cols->branch_code}}</option>
															@endforeach
															</select>
														  </div>
														</div>	
												
														<div class="row mb-2">
														  <label for="order_date" class="col-sm-3 col-form-label">Date</label>
														  <div class="col-sm-9">
															<input type="date" class="form-control" name="so_order_date" id="so_order_date" value="<?=date('Y-m-d');?>" required>
															<span class="valid-feedback" id="so_order_dateError" title="Required"></span>
														  </div>
														</div>
														
														<div class="row mb-2">
														  <label for="order_time" class="col-sm-3 col-form-label">Time</label>
														  <div class="col-sm-9">
															<input type="time" class="form-control " name="so_order_time" id="so_order_time" value="<?=date('H:i');?>" required>
															<span class="valid-feedback" id="so_order_timeError"></span>
														  </div>
														</div>	
														
														<div class="row mb-2">
														  <label for="so_number" class="col-sm-3 col-form-label">SO Number</label>
														  <div class="col-sm-9">
															<input type="text" class="form-control " name="so_number" id="so_number" value="" required>
															<span class="valid-feedback" id="so_numberError"></span>
														  </div>
														</div>
														
														<div class="row mb-2">
														  <label for="client_idx" class="col-sm-3 col-form-label">Client</label>
														  <div class="col-sm-9">
															<input class="form-control" list="so_client_name" name="so_client_name" id="so_client_idx" required autocomplete="off" value="">
																<datalist id="so_client_name">
																	@foreach ($client_data as $client_data_cols)
																		<option label="{{$client_data_cols->client_name}}" data-id="{{$client_data_cols->client_id}}" value="{{$client_data_cols->client_name}}">
																	@endforeach
																</datalist>
															<span class="valid-feedback" id="so_client_idxError"></span>
														  </div>
														</div>
														
														<div class="row mb-2">
														  <label for="plate_no" class="col-sm-3 col-form-label">Description</label>
														  <div class="col-sm-9">
															<input type="text" class="form-control " name="so_plate_no" id="so_plate_no" value="" required list="so_plate_no_list">
															<datalist id="so_plate_no_list">
																@foreach ($plate_no as $plate_no_cols)
																	<option value="{{$plate_no_cols->plate_no}}">
																@endforeach
															  </datalist>
															<span class="valid-feedback" id="plate_noError"></span>
														  </div>
														</div>	
														
														<div class="row mb-2">
														  <label for="drivers_name" class="col-sm-3 col-form-label">Drivers Name</label>
														  <div class="col-sm-9">
															<input type="text" class="form-control " name="so_drivers_name" id="so_drivers_name" value="" required list="so_drivers_list">
															<datalist id="so_drivers_list">
																@foreach ($drivers_name as $drivers_name_cols)
																	<option value="{{$drivers_name_cols->drivers_name}}">
																@endforeach
															  </datalist>
															<span class="valid-feedback" id="drivers_nameError"></span>
														  </div>
														</div>
														
															
						</form>
					 
					</div>
					<div class="card-footer">
														<div class="row mb-3">
														<div class="col-sm-9" align='right'>
														<div id="loading_data" style="display:none;">
															<div class="spinner-border text-success" role="status">
																<span class="visually-hidden">Loading...</span>
															</div>
														</div>
														</div>
														<div class="col-sm-3" align='right'>
														
														<button type="submit" class="btn btn-success btn-sm bi bi-save-fill form_button_icon" id="save-so-billing-transaction" title='Save SO information'> Save</button>
														</div>
														</div>	
					</div>
				  </div>
                  </div>
				  
                </div>
             </div>		
			 
			 
<div class="modal fade" id="DeleteSOModal" tabindex="-1">
	
              <div class="modal-dialog modal-xl">
                  <div class="modal-content">
                    <div class="modal-header modal-header_form">
                      <h5 class="modal-title">SO Information and Product</h5>
					  <div class="btn-group" role="group" aria-label="Basic outlined example">	
						<button type="button" class="btn btn-danger bi bi-x-circle form_button_icon" data-bs-dismiss="modal"></button>
					  </div>
                    </div>
                    <div class="modal-body">
						<div class="row">
					<div class="col-lg-4">
					  
					  <ol class="list-group list-group-numbered">
						
						<li class="list-group-item d-flex justify-content-between align-items-start">
						  <div class="ms-2 me-auto">
							<div class="fw-bold">Date</div>
							<div id="delete_so_order_date"></div>
						  </div>
						 
						</li>
						
						<li class="list-group-item d-flex justify-content-between align-items-start">
						  <div class="ms-2 me-auto">
							<div class="fw-bold">Time</div>
							<div id="delete_so_order_time"></div>
						  </div>
						 
						</li>
						
						<li class="list-group-item d-flex justify-content-between align-items-start">
						  <div class="ms-2 me-auto">
							<div class="fw-bold">SO Number</div>
							<div id="delete_so_so_number"></div>
						  </div>
						 
						</li>
						  
						<li class="list-group-item d-flex justify-content-between align-items-start">
						  <div class="ms-2 me-auto">
							<div class="fw-bold">Account Name</div>
							<div id="delete_so_client_name"></div>
						  </div>
						 
						</li>
						
						<li class="list-group-item d-flex justify-content-between align-items-start">
						  <div class="ms-2 me-auto">
							<div class="fw-bold">Driver</div>
							<div id="delete_so_drivers_name"></div>
						  </div>
						 
						</li>
						
						<li class="list-group-item d-flex justify-content-between align-items-start">
						  <div class="ms-2 me-auto">
							<div class="fw-bold">Description</div>
							<div id="delete_so_plate_no"></div>
						  </div>
						 
						</li>
						
						<li class="list-group-item d-flex justify-content-between align-items-start">
						  <div class="ms-2 me-auto">
							<div class="fw-bold">Total</div>
							<div id="delete_so_total"></div>
						  </div>
						 
						</li>
						
					  </ol>					
					
					</div>
					<div class="col-lg-8">
								<br>
								<table class="table" id="so_product_list">
									<thead>
										<tr class='report'>
											<th style="text-align:center !important;">Item #</th>
											<th style="text-align:center !important;">Product</th>
											<th style="text-align:center !important;">Price</th>
											<th style="text-align:center !important;">Quantity</th>
											<th style="text-align:center !important;">Amount</th>
										</tr>
									</thead>
										
									<tbody id="so_product_list_data_delete">
										 <tr style="display: none;"><td>HIDDEN</td></tr>
									</tbody>
									
								</table>
					  
					</div>
						
					</div>
					</div>
					
                    <div class="modal-footer modal-footer_form">
					<h5>Are you sure you want to Delete this item?</h5><br><br>
					
						<button type="button" class="btn btn-danger" data-bs-dismiss="modal" id="deleteSOConfirmed" value="">
						<i class="bi bi-trash3 form_button_icon"></i> Yes
						</button>
						
					</div>
					<!-- End Multi Columns Form -->
                  
				  </div>
                </div>
             </div>		

			 
@endsection

