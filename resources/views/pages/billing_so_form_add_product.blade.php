@extends('layouts.layout')  
@section('content')  

<main id="main" class="main">	
    <section class="section">

        <div class="row mb-2">
			
			<div class="col-sm-4">
			
			
			<div class="card">
            <div class="card-header"><h5 class="card-title">Update SO Information</h5></div>
            <div class="card-body">
				<br>
				<form class="g-3 needs-validation" id="SOBillingformUpdate">
					
												<div class="row mb-2">
												  <label for="branch_id" class="col-sm-3 col-form-label">Branch</label>
												  <div class="col-sm-9">
												 	<select class="form-select form-control" required="" name="branch_id" id="branch_id" onchange="UpdateBranch()">
													<?php $branch_idx = $so_data[0]['branch_idx']; ?>
													@foreach ($teves_branch as $teves_branch_cols)
													<?php 
													$branch_id = $teves_branch_cols->branch_id;
													?>
													<option value="{{$teves_branch_cols->branch_id}}" <?php if($branch_id==$branch_idx){ echo "selected";} else{} ?>>
														{{$teves_branch_cols->branch_code}}
													</option>
													@endforeach
													</select>
												  </div>
												</div>		
					
												<div class="row mb-2">
												  <label for="so_order_date" class="col-sm-3 col-form-label">Date</label>
												  <div class="col-sm-9">
													<input type="date" class="form-control" name="so_order_date" id="so_order_date" value="{{ $so_data[0]['order_date'] }}" required>
													<span class="valid-feedback" id="so_order_dateError" title="Required"></span>
												  </div>
												</div>
												
												<div class="row mb-2">
												  <label for="so_order_time" class="col-sm-3 col-form-label">Time</label>
												  <div class="col-sm-9">
													<input type="time" class="form-control " name="so_order_time" id="so_order_time" value="{{ $so_data[0]['order_time'] }}" required>
													<span class="valid-feedback" id="so_order_timeError"></span>
												  </div>
												</div>	
												
												<div class="row mb-2">
												  <label for="so_number" class="col-sm-3 col-form-label">SO Number</label>
												  <div class="col-sm-9">
													<input type="text" class="form-control " name="so_number" id="so_number" value="{{ $so_data[0]['so_number'] }}" required>
													<span class="valid-feedback" id="so_numberError"></span>
												  </div>
												</div>
												
												<div class="row mb-2">
												  <label for="so_client_idx" class="col-sm-3 col-form-label">Client</label>
												  <div class="col-sm-9">
													<input class="form-control" list="so_client_name" name="so_client_name" id="so_client_idx" required autocomplete="off" value="{{ $so_data[0]['client_name'] }}">
														<datalist id="so_client_name">
															@foreach ($client_data as $client_data_cols)
																<option label="{{$client_data_cols->client_name}}" data-id="{{$client_data_cols->client_id}}" value="{{$client_data_cols->client_name}}">
															@endforeach
														</datalist>
													<span class="valid-feedback" id="so_client_idxError"></span>
												  </div>
												</div>
												
												<div class="row mb-2">
												  <label for="so_plate_no" class="col-sm-3 col-form-label">Description</label>
												  <div class="col-sm-9">
													<input type="text" class="form-control " name="so_plate_no" id="so_plate_no" value="{{ $so_data[0]['plate_no'] }}" required list="so_plate_no_list">
													<datalist id="so_plate_no_list">
														@foreach ($plate_no as $plate_no_cols)
															<option value="{{$plate_no_cols->plate_no}}">
														@endforeach
													  </datalist>
													<span class="valid-feedback" id="plate_noError"></span>
												  </div>
												</div>	
												
												<div class="row mb-2">
												  <label for="so_drivers_name" class="col-sm-3 col-form-label">Drivers Name</label>
												  <div class="col-sm-9">
													<input type="text" class="form-control " name="so_drivers_name" id="so_drivers_name" value="{{ $so_data[0]['drivers_name'] }}" required list="so_drivers_list">
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
												<div class="col-sm-6" align='right'>
												<div id="loading_data_update_so" style="display:none;">
													<div class="spinner-border text-success" role="status">
														<span class="visually-hidden">Loading...</span>
													</div>
												</div>
												</div>
												<div class="col-sm-6" align='right'>
												<a class="btn btn-secondary btn-sm new_item bi bi-chevron-double-left form_button_icon" href="{{ route('so') }}" title="Back">  
												  <span title="Back to Sales Order List">Back</span>
												</a>
												<button type="submit" class="btn btn-success btn-sm bi bi-save-fill form_button_icon" id="update-so-billing-transaction" title='Update SO information'> Update</button>
												</div>
												</div>	
            </div>
          </div>
			</div>
			<div class="col-sm-8">
			
			<div class="card">
            <div class="card-body">
              <h5 class="card-title">Product</h5>
				<div class="d-flex justify-content-end" id="">
					<div class="btn-group" role="group" aria-label="Basic outlined example" style="margin-top: -50px; position: absolute;">
						
						<a class="btn btn-secondary new_item bi bi-chevron-double-left form_button_icon" href="{{ route('so') }}" title="Back">  
						  <span title="Back to Sales Order List">Back</span>
						</a>
						
						<button type="button" class="btn btn-success new_item bi bi-plus-circle form_button_icon" data-bs-toggle="modal" data-bs-target="#AddProductModal" id='AddProductBTN'></button>
						
					</div>					
				  </div>
            
			<table class="table table-striped" id="">
			<thead>
					<tr class='report'>
						<th style="text-align:center !important;">Item #</th>
						<th style="text-align:center !important;">Product</th>
						<th style="text-align:center !important;">Price</th>
						<th style="text-align:center !important;">Quantity</th>
						<th style="text-align:center !important;">Amount</th>
						<th style="text-align:center !important;" colspan="2">Action</th>
					</tr>
			</thead>
			<tbody id="table_so_product_body_data">
					<tr style="display: none;">
						<td>HIDDEN</td>
					</tr>
			</tbody>
		</table>
            </div>
          </div>
             
			</div>
	
		</div> 
		
	<!--Modal to Create SO Product-->
	<div class="modal fade" id="AddProductModal" tabindex="-1">
              <div class="modal-dialog modal-lg">
                  <div class="modal-content">
                    <div class="modal-header modal-header_form">
                      <h5 class="modal-title">Add Product</h5>
					  <div class="btn-group" role="group" aria-label="Basic outlined example">		
						
						<button type="button" class="btn btn-danger bi bi-x-circle form_button_icon" data-bs-dismiss="modal"></button>
					  </div>
                    </div>
                    <div class="modal-body">
					
					  <form class="g-3 needs-validation" id="AddSOProduct">

						<div class="row mb-2">
						
						<div class="col-sm-12">
						
						<div class="form-floating mb-3">
						  <input class="form-control" list="product_list" name="product_name" id="product_idx" required autocomplete="off" onchange="TotalAmount()"placeholder="Date">
						  <label for="product_idx">Product</label>
						 </div>
						<span class="valid-feedback" id="product_idxError"></span>
									
						</div>
						
						</div>
						
						<div class="row mb-2">
						
						<div class="col-sm-12">
						
							<div class="form-floating mb-3">
								<input type="number" class="form-control" aria-label="" name="product_manual_price" id="product_manual_price" value="" step=".01" onchange="TotalAmount()" placeholder="Custom Price">
								<label for="product_manual_price">Custom Price</label>
							</div>
						 
						</div>
						</div>
						
						<div class="col-sm-12">
						
							<div class="form-floating mb-3">
								<input type="number" class="form-control" aria-describedby="basic-addon1" name="order_quantity" id="order_quantity" required step=".01" onchange="TotalAmount()" placeholder="Quantity">
								<label for="order_quantity">Quantity</label>
							</div>
							 <span class="valid-feedback" id="order_quantityError"></span>
						</div>

						<div class="row mb-2">
						  <label class="col-sm-3 col-form-label">Amount</label>
						  <div class="col-sm-9">
								<span style="font-family: DejaVu Sans; sans-serif;">&#8369;</span> <span id="TotalAmount">0.00</span>
						  </div>
						</div>
						</div>
						
                    <div class="modal-footer modal-footer_form">
							<div id="loading_data_add_product" style="display:none;">
							<div class="spinner-border text-success" role="status">
								<span class="visually-hidden">Loading...</span>
							</div>
							</div>
						  <button type="submit" class="btn btn-success btn-sm bi bi-save-fill form_button_icon" id="so-save-product"> Save</button>
						  <button type="reset" class="btn btn-primary btn-sm bi bi-backspace-fill form_button_icon" id="clear-so-save-product"> Reset</button>					  
					</div>
					</form><!-- End Multi Columns Form -->
                  </div>
				  
                </div>
             </div>		

	<!--Modal to Create SO Product-->
	<div class="modal fade" id="EditProductModal" tabindex="-1">
              <div class="modal-dialog modal-lg">
                  <div class="modal-content">
                    <div class="modal-header modal-header_form">
                      <h5 class="modal-title">Update Product</h5>
					  <div class="btn-group" role="group" aria-label="Basic outlined example">				
						<button type="button" class="btn btn-danger bi bi-x-circle form_button_icon" data-bs-dismiss="modal"></button>
					  </div>
                    </div>
                    <div class="modal-body">
					
					  <form class="g-3 needs-validation" id="EditSOProduct">

						<div class="row mb-2">
						
						<div class="col-sm-12">
						
						<div class="form-floating mb-3">
						  <input class="form-control" list="product_list" name="edit_product_name" id="edit_product_idx" required autocomplete="off" onchange="UpdateTotalAmount()"placeholder="Date">
						  <label for="edit_product_idx">Product</label>
						 </div>
						<span class="valid-feedback" id="edit_product_idxError"></span>
									
						</div>
						
						</div>
						
						<div class="row mb-2">
						
						<div class="col-sm-12">
						
							<div class="form-floating mb-3">
								<input type="number" class="form-control" name="edit_product_manual_price" id="edit_product_manual_price" value="" step=".01" onchange="UpdateTotalAmount()" placeholder="Custom Price">
								<label for="edit_product_manual_price">Custom Price</label>
							</div>
						 
						</div>
						</div>
						
						<div class="col-sm-12">
						
							<div class="form-floating mb-3">
								<input type="number" class="form-control" aria-describedby="basic-addon1" name="edit_order_quantity" id="edit_order_quantity" required step=".01" onchange="UpdateTotalAmount()" placeholder="Quantity">
								<label for="edit_order_quantity">Quantity</label>
							</div>
							 <span class="valid-feedback" id="edit_order_quantityError"></span>
							 
						</div>

						<div class="row mb-2">
						  <div class="col-sm-3 col-form-label">Amount</div>
						  <div class="col-sm-9">
								<span style="font-family: DejaVu Sans; sans-serif;">&#8369;</span> <span id="UpdateTotalAmount">0.00</span>
						  </div>
						</div>
						</div>
						
                    <div class="modal-footer modal-footer_form">
							<div id="loading_data_update_product" style="display:none;">
							<div class="spinner-border text-success" role="status">
								<span class="visually-hidden">Loading...</span>
							</div>
							</div>
						  <button type="submit" class="btn btn-success btn-sm bi bi-save-fill form_button_icon" id="so-update-product"> Update</button>					  
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
			
	<!--Data List for Product-->
	<datalist id="product_list">
		@foreach ($product_data as $product_data_cols)
			<span style="font-family: DejaVu Sans; sans-serif;">
				<option label="&#8369; {{$product_data_cols->product_price}} | {{$product_data_cols->product_name}}" data-id="{{$product_data_cols->product_id}}" data-price="{{$product_data_cols->product_price}}" value="{{$product_data_cols->product_name}}">
			</span>
		@endforeach
	</datalist>	

    </section>
</main>


@endsection
