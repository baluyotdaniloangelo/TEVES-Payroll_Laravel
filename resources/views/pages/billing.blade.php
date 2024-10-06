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
				
				<ul class="nav nav-tabs nav-tabs-bordered" id="borderedTab" role="tablist">
                <li class="nav-item" role="presentation">
                  <button class="nav-link active" id="billing-tab" data-bs-toggle="tab" data-bs-target="#bordered-billing" type="button" role="tab" aria-controls="home" aria-selected="true" title="From Billing">Unbilled</button>
                </li>
                <li class="nav-item" role="presentation"  data-bs-toggle="modal" data-bs-target="#BilledModal">
                  <button class="nav-link" id="billed-tab" data-bs-toggle="tab" data-bs-target="#bordered-billed" type="button" role="tab" aria-controls="profile" aria-selected="false" tabindex="-1" title="From Sales Oder">Billed</button>
                </li>
              
				</ul>					
				
				<div class="tab-content pt-2" id="borderedTabContent">
				
                <div class="tab-pane fade show active" id="bordered-billing" role="tabpanel" aria-labelledby="billing-tab">
											<div class="table-responsive">
											<table class="table dataTable display nowrap cell-border" id="getBillingTransactionList" width="100%" cellspacing="0">
											<thead>
												<tr>
													<th class="all">#</th>
													<th class="all">Date</th>
													<th class="all">Time</th>
													<th class="all">Control Number</th>
													<th class="all">Driver's Name</th>
													<th class="all">S.O No.</th>
													<th class="all">Description</th>																	
													<th class="all">Product</th>
													<th class="none">Price : </th>
													<th class="none">Quantity : </th>
													<th class="none">Amount : </th>
													<th>Action</th>
												</tr>
											</thead>				
											
											<tbody>
												
											</tbody>
									
										</table>
									</div>
				</div>
				
                <div class="tab-pane fade" id="bordered-billed" role="tabpanel" aria-labelledby="billed-tab">
					<div class="d-flex justify-content-end" id="">
					<div class="btn-group" role="group" aria-label="Basic outlined example" style="margin-top: -58px; position: absolute;">
						<button type="button" class="btn btn-primary new_item bi bi-input-cursor-text" data-bs-toggle="modal" data-bs-target="#BilledModal"> Options</button>
					</div>					
					</div>
									    <div class="table-responsive">
										
										<table class="table dataTable display nowrap cell-border" id="BillingListTable_billed" width="100%" cellspacing="0">
											<thead>
												<tr>
													<th class="all">#</th>
													<th class="all">Date</th>
													<th class="all">Time</th>
													<th class="all">Control Number</th>
													<th class="all">Driver's Name</th>
													<th class="all">S.O No.</th>
													<th class="all">Description</th>																	
													<th class="all">Product</th>
													<th class="none">Price : </th>
													<th class="none">Quantity : </th>
													<th class="none">Amount : </th>
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
                   
            </div>
          </div>

	<!--Modal to Create Client-->
	<div class="modal fade" id="BilledModal" tabindex="-1">
              <div class="modal-dialog modal-lg">
                  <div class="modal-content">
                    <div class="modal-header modal-header_form">
                      <h5 class="modal-title">Billed History</h5>
					  <div class="btn-group" role="group" aria-label="Basic outlined example">	
						<button type="button" class="btn btn-danger bi bi-x-circle form_button_icon" data-bs-dismiss="modal"></button>
					  </div>
                    </div>
                    <div class="modal-body">
					
					  <form class="g-2 needs-validation" id="generate_billed_form">
					  
						<div class="row mb-2">
						  <label for="client_idx_billed" class="col-sm-4 col-form-label" title="***Client is Optional, You Can Generate the Billed item using the Date Range.">Client(Optional)</label>
						  <div class="col-sm-8">
							<input class="form-control" list="client_name_billed" name="client_name_billed" id="client_id_billed" required autocomplete="off">
								<datalist id="client_name_billed">
									@foreach ($client_data as $client_data_cols)
										<option label="{{$client_data_cols->client_name}}" data-id="{{$client_data_cols->client_id}}" value="{{$client_data_cols->client_name}}">
									@endforeach
								</datalist>
							<span class="valid-feedback" id="client_idxError"></span>
						  </div>
						</div>
						
						<div class="row mb-2">
						  <label for="start_date_billed" class="col-sm-4 col-form-label">Start Date</label>
						  <div class="col-sm-8">
							<input type="date" class="form-control " name="start_date_billed" id="start_date_billed" value="<?=date('Y-m-d');?>" required>
							<span class="valid-feedback" id="start_date_billedError"></span>
						  </div>
						</div>						
								
						<div class="row mb-2">
						  <label for="end_date_billed" class="col-sm-4 col-form-label">End Date</label>
						  <div class="col-sm-8">
							<input type="date" class="form-control " name="end_date_billed" id="end_date_billed" value="<?=date('Y-m-d');?>" required>
							<span class="valid-feedback" id="end_date_billedError"></span>
						  </div>
						</div>
						
						</div>
						
                    <div class="modal-footer modal-footer_form">
					
							<div id="loading_data" style="display:none;">
							<div class="spinner-border text-success" role="status">
								<span class="visually-hidden">Loading...</span>
							</div>
							</div>
						  <button type="submit" class="btn btn-success btn-sm bi bi-save-fill form_button_icon" id="generate_billed"> Submit</button>
					</div>
					</form><!-- End Multi Columns Form -->
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
				
				Plate #: <span id="bill_delete_plate_no"></span><br>
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
				
				Plate #: <span id="view_plate_no"></span><br>
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

	<!--Modal to Create Bill-->
	<div class="modal fade" id="CreateBillingModal" tabindex="-1">
              <div class="modal-dialog modal-lg">
                  <div class="modal-content">
                    <div class="modal-header modal-header_form">
                      <h5 class="modal-title">Create Bill</h5>
					  <div class="btn-group" role="group" aria-label="Basic outlined example">		
						
						<button type="button" class="btn btn-danger bi bi-x-circle form_button_icon" data-bs-dismiss="modal"></button>
					  </div>
                    </div>
                    <div class="modal-body">
					
					  <form class="g-3 needs-validation" id="BillingformNew">
					  
						<div class="row mb-2">
						  <label for="order_date" class="col-sm-3 col-form-label">Date</label>
						  <div class="col-sm-9">
							<input type="date" class="form-control" name="order_date" id="order_date" value="<?=date('Y-m-d');?>" required>
							<span class="valid-feedback" id="order_dateError" title="Required"></span>
						  </div>
						</div>
						
						<div class="row mb-2">
						  <label for="order_time" class="col-sm-3 col-form-label">Time</label>
						  <div class="col-sm-9">
							<input type="time" class="form-control " name="order_time" id="order_time" value="<?=date('H:i');?>" required>
							<span class="valid-feedback" id="order_timeError"></span>
						  </div>
						</div>	
						
						<div class="row mb-2">
						  <label for="order_po_number" class="col-sm-3 col-form-label">SO Number</label>
						  <div class="col-sm-9">
							<input type="text" class="form-control " name="order_po_number" id="order_po_number" value="" required>
							<span class="valid-feedback" id="order_po_numberError"></span>
						  </div>
						</div>
						
						<div class="row mb-2">
						  <label for="client_idx" class="col-sm-3 col-form-label">Client</label>
						  <div class="col-sm-9">
							<input class="form-control" list="client_name" name="client_name" id="client_idx" required autocomplete="off" value="">
								<datalist id="client_name">
									@foreach ($client_data as $client_data_cols)
										<option label="{{$client_data_cols->client_name}}" data-id="{{$client_data_cols->client_id}}" value="{{$client_data_cols->client_name}}">
									@endforeach
								</datalist>
							<span class="valid-feedback" id="client_idxError"></span>
						  </div>
						</div>
						
						<div class="row mb-2">
						  <label for="plate_no" class="col-sm-3 col-form-label">Plate Number</label>
						  <div class="col-sm-9">
							<input type="text" class="form-control " name="plate_no" id="plate_no" value="" required list="plate_no_list">
							<datalist id="plate_no_list">
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
							<input type="text" class="form-control " name="drivers_name" id="drivers_name" value="" required list="drivers_list">
							<datalist id="drivers_list">
								@foreach ($drivers_name as $drivers_name_cols)
									<option value="{{$drivers_name_cols->drivers_name}}">
								@endforeach
							  </datalist>
							<span class="valid-feedback" id="drivers_nameError"></span>
						  </div>
						</div>
						
						<div class="row mb-2">
						  <label for="product_idx" class="col-sm-3 col-form-label">Product</label>						  
						  <div class="col-sm-9">  
						  
							  <div class="input-group">
							  
									<input class="form-control" list="product_name" name="product_name" id="product_idx" required autocomplete="off" onchange="TotalAmount()">
									<datalist id="product_name">
										
									</datalist>
									
									<span class="input-group-text">Manual Price</span>
									<input type="text" class="form-control" placeholder="0.00" aria-label="" name="product_manual_price" id="product_manual_price" value="" step=".01" onchange="TotalAmount()">
									<span class="valid-feedback" id="product_idxError"></span>
									
								</div>
							</div>
						</div>	
						
						<div class="row mb-2">
						  <label for="order_quantity" class="col-sm-3 col-form-label">Quantity</label>
						  <div class="col-sm-9">
							  <input type="number" class="form-control" placeholder="" aria-label="Username" aria-describedby="basic-addon1" name="order_quantity" id="order_quantity" required step=".01" onchange="TotalAmount()" >
							  <span class="valid-feedback" id="order_quantityError"></span>						  
						  </div>
						</div>	
						
						<div class="row mb-2">
						  <label class="col-sm-3 col-form-label">Amount</label>
						  <div class="col-sm-9">
								<span style="font-family: DejaVu Sans; sans-serif;">&#8369;</span> <span id="TotalAmount">0.00</span>
						  </div>
						</div>
						</div>
						
                    <div class="modal-footer modal-footer_form">
							<div id="loading_data" style="display:none;">
							<div class="spinner-border text-success" role="status">
								<span class="visually-hidden">Loading...</span>
							</div>
							</div>
						  <button type="submit" class="btn btn-success btn-sm bi bi-save-fill form_button_icon" id="save-billing-transaction"> Submit</button>
						  <button type="reset" class="btn btn-primary btn-sm bi bi-backspace-fill form_button_icon" id="clear-billing-save"> Reset</button>					  
					</div>
					</form><!-- End Multi Columns Form -->
                  </div>
				  
                </div>
             </div>
	
	
	<!--Modal to Upadate Site-->
	<div class="modal fade" id="UpdateBillingModal" tabindex="-1">
              <div class="modal-dialog modal-lg">
                  <div class="modal-content">
                    <div class="modal-header modal-header_form">
                      <h5 class="modal-title">Update Bill</h5>
					  <div class="btn-group" role="group" aria-label="Basic outlined example">		
						<button type="button" class="btn btn-danger bi bi-x-circle form_button_icon" data-bs-dismiss="modal"></button>
					  </div>
                    </div>
                    <div class="modal-body">
					
					  <form class="g-2 needs-validation" id="BillingformEdit">
					  
						<div class="row mb-2">
						  <label for="update_order_date" class="col-sm-3 col-form-label">Date</label>
						  <div class="col-sm-9">
							<input type="date" class="form-control" name="update_order_date" id="update_order_date" value="" required>
							<span class="valid-feedback" id="update_order_dateError" title="Required"></span>
						  </div>
						</div>
						
						<div class="row mb-2">
						  <label for="update_order_time" class="col-sm-3 col-form-label">Time</label>
						  <div class="col-sm-9">
							<input type="time" class="form-control " name="update_order_time" id="update_order_time" value="" required>
							<span class="valid-feedback" id="update_order_timeError"></span>
						  </div>
						</div>	
						
						<div class="row mb-2">
						  <label for="update_order_po_number" class="col-sm-3 col-form-label">SO Number</label>
						  <div class="col-sm-9">
							<input type="text" class="form-control " name="update_order_po_number" id="update_order_po_number" value="" required disabled>
							<span class="valid-feedback" id="update_order_po_numberError"></span>
						  </div>
						</div>
						
						<div class="row mb-2">
						  <label for="update_client_idx" class="col-sm-3 col-form-label">Client</label>
						  <div class="col-sm-9">
							<input class="form-control" list="update_client_name" name="update_client_name" id="update_client_idx" required autocomplete="off">
											<datalist id="update_client_name">
											  @foreach ($client_data as $client_data_cols)
											  <option label="{{$client_data_cols->client_name}}" data-id="{{$client_data_cols->client_id}}" value="{{$client_data_cols->client_name}}">
											  @endforeach
											</datalist>
							<span class="valid-feedback" id="update_client_idxError"></span>
						  </div>
						</div>
						
						<div class="row mb-2">
						  <label for="update_plate_no" class="col-sm-3 col-form-label">Plate Number</label>
						  <div class="col-sm-9">
							<input type="text" class="form-control " name="update_plate_no" id="update_plate_no" value="" required list="plate_no_list">
							<datalist id="plate_no_list">
								@foreach ($plate_no as $plate_no_cols)
									<option value="{{$plate_no_cols->plate_no}}">
								@endforeach
							  </datalist>
							<span class="valid-feedback" id="update_plate_noError"></span>
						  </div>
						</div>	
						
						<div class="row mb-2">
						  <label for="update_drivers_name" class="col-sm-3 col-form-label">Drivers Name</label>
						  <div class="col-sm-9">
							<input type="text" class="form-control " name="update_drivers_name" id="update_drivers_name" value="" required list="drivers_list">
							<datalist id="drivers_list">
								@foreach ($drivers_name as $drivers_name_cols)
									<option value="{{$drivers_name_cols->drivers_name}}">
								@endforeach
							  </datalist>
							<span class="valid-feedback" id="update_drivers_nameError"></span>
						  </div>
						</div>
						
						<div class="row mb-2">
						  <label for="update_product_idx" class="col-sm-3 col-form-label">Product</label>						  
						  <div class="col-sm-9">  
						  
							  <div class="input-group">
							  
									<input class="form-control" list="update_product_name" name="update_product_name" id="update_product_idx" required autocomplete="off" onchange="UpdateTotalAmount()">
									<datalist id="update_product_name">
										
									</datalist>
									
									<span class="input-group-text">Manual Price</span>
									<input type="text" class="form-control" placeholder="0.00" aria-label="" name="update_product_manual_price" id="update_product_manual_price" value="" step=".01" onchange="UpdateTotalAmount()">
									<span class="valid-feedback" id="update_product_idxError"></span>
									
								</div>
							</div>
						</div>	
						
						<div class="row mb-2">
						  <label for="update_order_quantity" class="col-sm-3 col-form-label">Quantity</label>
						  <div class="col-sm-9">
							  <input type="number" class="form-control" placeholder="" aria-label="Username" aria-describedby="basic-addon1" name="update_order_quantity" id="update_order_quantity" required step=".01" onchange="UpdateTotalAmount()">
							  <span class="valid-feedback" id="update_order_quantityError"></span>
						  </div>
						</div>	
						
						<div class="row mb-2">
						  <label class="col-sm-3 col-form-label">Amount</label>
						  <div class="col-sm-9">
								<span style="font-family: DejaVu Sans; sans-serif;">&#8369;</span> <span id="UpdateTotalAmount">0.00</span>
						  </div>
						</div>	
						
						</div>
						
                    <div class="modal-footer modal-footer_form">
							<div id="update_loading_data" style="display:none;">
							<div class="spinner-border text-success" role="status">
								<span class="visually-hidden">Loading...</span>
							</div>
							</div>
						  <button type="submit" class="btn btn-success btn-sm bi bi-save-fill form_button_icon" id="update-billing-transaction"> Submit</button>
						  <button type="reset" class="btn btn-primary btn-sm bi bi-backspace-fill form_button_icon" id="clear-billing-update"> Reset</button>
						  
					</div>
					</form><!-- End Multi Columns Form -->
                  </div>
                </div>
             </div>
	

    </section>
</main>


@endsection

