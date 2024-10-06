@extends('layouts.layout')  
@section('content')  

<main id="main" class="main">	

    <section class="section">
		<div class="card">
		
            <div class="card-body">
			
				<h5 class="card-title" align="center">Update Purchase Order Information</h5>
				<div class="row mb-2">
					<div class="col-sm-6">
					<h6>Control Number : <span class="badge" id='control_no' style="background-color: yellowgreen !important; color:#000 !important; font-weight:bold;">Info</span></h6>
					</div>
					<div class="col-sm-6">
						<div class="d-flex justify-content-end" id="">
							<div class="btn-group" role="group" aria-label="Basic outlined example" style="margin-top: -35px; position: absolute;">
								<a class="btn btn-secondary new_item bi bi-chevron-double-left form_button_icon" href="{{ route('purchaseorder_v2') }}" title="Back">  
								  <span title="Back to Sales Order List">Back</span>
								</a>
								<button type="button" class="btn btn-dark new_item bi-printer-fill form_button_icon" id="PrintPurchaseOrder">&nbsp;Print</button>
								<!--<button type="button" class="btn btn-success new_item bi bi-printer" onclick="#"></button>-->
								
							</div>					
						</div>
						
					</div>
				</div>
				
				<hr>
				<div class="row mb-2">
					<div class="col-sm-5">
						<form class="g-2 needs-validation" id="PurchaseOrderformUpdate">
								
							<div class="row mb-2">
								<div class="col-md-6">
									<label for="update_purchase_order_date" class="form-label">Date</label>
									<input type="date" class="form-control" id="update_purchase_order_date" name="update_purchase_order_date" value="<?=date('Y-m-d');?>">
								</div>

								<div class="col-md-6">
									<label for="update_company_header" class="form-label">Branch</label>
										<select class="form-select form-control" required="" name="update_company_header" id="update_company_header" onchange="UpdateBranch()">
											@foreach ($teves_branch as $teves_branch_cols)
												<option value="{{$teves_branch_cols->branch_id}}">{{$teves_branch_cols->branch_code}}</option>
											@endforeach
										</select>
								</div>
							</div>
							<hr>
							<div class="row mb-2">
								
								<div class="col-md-12">
								<label for="update_supplier_name" class="form-label">Supplier's Name</label>
									<input class="form-control" list="update_supplier_name_list" name="update_supplier_name" id="update_supplier_idx" required autocomplete="off" onChange="SupplierInfo()">
										<datalist id="update_supplier_name_list">
											@foreach ($supplier_data as $supplier_data_cols)
											  <option label="{{$supplier_data_cols->supplier_name}}" data-id="{{$supplier_data_cols->supplier_id}}" value="{{$supplier_data_cols->supplier_name}}">
											@endforeach
										</datalist>
										<span class="valid-feedback" id="update_supplier_idxError"></span>
								</div>
							</div>
							
							<hr>	
							
							<div class="row mb-2">
										
										<div class="col-md-6">
											  <label for="update_purchase_order_net_percentage" class="form-label">Net Value</label>
											  <input type="number" class="form-control" id="update_purchase_order_net_percentage" name="update_purchase_order_net_percentage" step=".01">
										</div>
									
										<div class="col-md-6">
										  <label for="update_purchase_order_less_percentage" class="form-label">Less Value</label>
										  <input type="number" class="form-control" id="update_purchase_order_less_percentage" name="update_purchase_order_less_percentage" step=".01">
										</div>
										
							</div>
							<hr>
							<div class="row mb-2">
									<div class="col-md-6">
										<label for="update_purchase_order_sales_order_number" class="form-label">Sales Order #</label>
										<input type="text" class="form-control" id="update_purchase_order_sales_order_number" name="update_purchase_order_sales_order_number">
										<span class="valid-feedback" id="update_purchase_order_sales_order_numberError"></span>
									</div>
									
									<div class="col-md-6"> 
									<label for="update_purchase_order_collection_receipt_no" class="form-label">Collection Receipt #</label>
									  <input type="text" class="form-control" id="update_purchase_order_collection_receipt_no" name="update_purchase_order_collection_receipt_no">
									  <span class="valid-feedback" id="update_purchase_order_collection_receipt_noError"></span>
									</div>
							</div>
							
							<div class="row mb-2">	
								
									<div class="col-md-6">
										<label for="update_purchase_order_official_receipt_no" class="form-label">Official Receipt #</label>
										<input type="text" class="form-control" id="update_purchase_order_official_receipt_no" name="update_purchase_order_official_receipt_no">
										<span class="valid-feedback" id="update_purchase_order_official_receipt_nooError"></span>
									</div>
									
									
									<div class="col-md-6">
										<label for="update_purchase_order_delivery_receipt_no" class="form-label">Delivery Receipt #</label>									
										<input type="text" class="form-control" id="update_purchase_order_delivery_receipt_no" name="update_purchase_order_delivery_receipt_no">
										<span class="valid-feedback" id="update_purchase_order_delivery_receipt_noError"></span>
									</div>
									
							</div>
							
							<hr>
								
							<div class="row mb-2">
								
									<div class="col-md-12">
									<label for="update_purchase_order_delivery_method" class="form-label">Delivery Method</label>
									  <input type="text" class="form-control" id="update_purchase_order_delivery_method" name="update_purchase_order_delivery_method">
									</div>
							</div>
								
							<div class="row mb-2">	
								
									<div class="col-md-12">
									<label for="update_purchase_loading_terminal" class="form-label">Loading Terminal</label>
									  <input type="text" class="form-control" id="update_purchase_loading_terminal" name="update_purchase_loading_terminal" list="purchase_loading_terminal_list">
										<datalist id="purchase_loading_terminal_list">
											@foreach ($purchase_data_suggestion as $purchase_loading_terminal_cols)
												<option value="{{$purchase_loading_terminal_cols->purchase_loading_terminal}}">
											@endforeach
										</datalist>
									</div>
							</div>
								
							<hr>	
							
							<div class="row mb-2">
								
									<div class="col-md-12">
									  <label for="update_hauler_operator" class="form-label">Hauler's Name</label>
									  <input type="text" class="form-control" id="update_hauler_operator" name="update_hauler_operator">
									</div>
								
									
							</div>
							
							<div class="row mb-2">
								
								
									<div class="col-md-12">
									  <label for="update_lorry_driver" class="form-label">Driver's Name</label>
									  <input type="text" class="form-control" id="update_lorry_driver" name="update_lorry_driver" list="lorry_driver_list">
											<datalist id="lorry_driver_list">
												@foreach ($purchase_data_suggestion as $lorry_driver_cols)
													<option value="{{$lorry_driver_cols->lorry_driver}}">
												@endforeach
											  </datalist>
									</div>
									
							</div>
							
							
							<div class="row mb-2">
							
									<div class="col-md-12">
									  <label for="update_plate_number" class="form-label">Plate Number</label>
									  <input type="text" class="form-control" id="update_plate_number" name="update_plate_number" list="plate_number_list">
											<datalist id="plate_number_list">
												@foreach ($purchase_data_suggestion as $plate_number_cols)
													<option value="{{$plate_number_cols->plate_number}}">
												@endforeach
											  </datalist>
									</div>
									<!--
									<div class="col-md-6">
									  <label for="update_contact_number" class="form-label">Contact Number</label>
									  <input type="text" class="form-control" id="update_contact_number" name="update_contact_number" list="contact_number_list">
											<datalist id="contact_number_list">
												@foreach ($purchase_data_suggestion as $contact_number_cols)
													<option value="{{$contact_number_cols->contact_number}}">
												@endforeach
											  </datalist>
									</div>-->
								
							</div>
							
							<div class="row mb-2">
								
									<div class="col-md-12">
									  <label for="update_purchase_destination" class="form-label">Destination</label>
									  <input type="text" class="form-control" id="update_purchase_destination" name="update_purchase_destination" list="purchase_destination_list">
											<datalist id="purchase_destination_list">
												@foreach ($purchase_data_suggestion as $purchase_destination_cols)
													<option value="{{$purchase_destination_cols->purchase_destination}}">
												@endforeach
											  </datalist>
									</div>
									<!--
									<div class="col-md-6">
									  <label for="update_purchase_destination_address" class="form-label">Address</label>
									  <input type="text" class="form-control" id="update_purchase_destination_address" name="update_purchase_destination_address" value=""list="purchase_destination_address_list">
											<datalist id="purchase_destination_address_list">
												@foreach ($purchase_data_suggestion as $purchase_destination_address_cols)
													<option value="{{$purchase_destination_address_cols->purchase_destination_address}}">
												@endforeach
											  </datalist>
									</div>-->
								</div>	
								<!--
								<div class="row mb-2">
									<div class="col-md-6">
									  <label for="update_purchase_date_of_departure" class="form-label">Date of Departure</label>
									  <input type="date" class="form-control" id="update_purchase_date_of_departure" name="update_purchase_date_of_departure">
									</div>
									
									<div class="col-md-6">
									  <label for="update_purchase_date_of_arrival" class="form-label">Date of Arrival</label>
									  <input type="date" class="form-control" id="update_purchase_date_of_arrival" name="update_purchase_date_of_arrival">
									</div>
								
								</div>
								-->
								<div class="row mb-2">
								
									<div class="col-md-6">
									  <label for="update_purchase_order_instructions" class="form-label">Instructions</label>
									  <textarea class="form-control" id="update_purchase_order_instructions" name="update_purchase_order_instructions" style="height: 38px;"></textarea>
									</div>
									
									<div class="col-md-6">
									  <label for="update_purchase_order_note" class="form-label">Notes</label>
									  <textarea class="form-control" id="update_purchase_order_note" name="update_purchase_order_note" style="height: 38px;"></textarea>
									</div>
					
								</div>
								
								<div class="card-footer">
												<div class="row mb-2">
												<div class="col-sm-6" align=''>
												<div id="loading_data_update_so" style="display:none;">
													<div class="spinner-border text-success" role="status">
														<span class="visually-hidden">Loading...</span>
													</div>
												</div>
												</div>
												<div class="col-sm-6" align='right'>
												<a class="btn btn-secondary btn-sm new_item bi bi-chevron-double-left form_button_icon" href="{{ route('purchaseorder_v2') }}" title="Back">  
												  <span title="Back to Sales Order List">Back</span>
												</a>
												<button type="submit" class="btn btn-success btn-sm bi bi-save-fill form_button_icon" id="update-purchase-order" title='Update Sales Order information'> Update</button>
												</div>
												</div>	
								</div>
						</form>
					</div>
					<div class="col-sm-7">
					 <!-- Default Tabs -->
              <ul class="nav nav-tabs" id="myTab" role="tablist">
                
				<li class="nav-item" role="presentation">
                  <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home" type="button" role="tab" aria-controls="home" aria-selected="true" onclick="LoadProduct()">Product</button>
                </li>
				
				<li class="nav-item" role="presentation">
                  <button class="nav-link" id="delivery-tab" data-bs-toggle="tab" data-bs-target="#delivery" type="button" role="tab" aria-controls="delivery" aria-selected="true" onclick="" title='Product Delivery List, Create, Update and Delete Delivery'>Delivery</button>
                </li>
				
                <li class="nav-item" role="presentation">
                  <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button" role="tab" aria-controls="profile" aria-selected="false" tabindex="-1">Payment</button>
                </li>
               
              </ul>
			  
              <div class="tab-content pt-2" id="myTabContent">
                <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
				<div class="d-flex justify-content-end" id="">
					<div class="btn-group" role="group" aria-label="Basic outlined example" style="margin-top: -50px; position: absolute;">
						<button type="button" class="btn btn-success new_item bi bi-plus-circle form_button_icon" data-bs-toggle="modal" data-bs-target="#AddProductModal" id="AddPurchaseOrderProductBTN"></button>
					</div>					
					</div>

						<table class="table table-striped" id="">
						<thead>
						<tr class='report'>
							<th style="text-align:center !important;">#</th>
							<th style="text-align:center !important;" class="action_column_class">Action</th>
							<th style="text-align:center !important;">Description</th>
							<th style="text-align:center !important;">Price</th>
							<th style="text-align:center !important;">Quantity</th>
							<th style="text-align:center !important;">Amount</th>	
						</tr>
						</thead>
							<tbody id="table_purchase_order_product_body_data">
									<tr style="display: none;">
										<td>HIDDEN</td>
									</tr>
							</tbody>
						</table>
                </div>
				
               <div class="tab-pane fade" id="delivery" role="tabpanel" aria-labelledby="delivery-tab">
				<div class="d-flex justify-content-end" id="">
					<div class="btn-group" role="group" aria-label="Basic outlined example" style="margin-top: -50px; position: absolute;">
						<button type="button" class="btn btn-success new_item bi bi-plus-circle form_button_icon" data-bs-toggle="modal" data-bs-target="#AddProductDeliveryModal" id="" onclick="ResetDeliveryForm()" title="Add Sales Order Delivery Items"></button>
						<button type="button" class="btn btn-dark new_item bi-printer-fill form_button_icon" id="PrintPurchaseOrderDeliveyStatus" title="Print Purchase Order Delivered Item / Status"></button>
					</div>											
					</div>
					
						<table class="table table-striped" id="">
						<thead>
						<tr class='report'>
							<th style="text-align:center !important;" class="">Action</th>
							<th style="text-align:center !important;">Item #</th>
							<th style="text-align:center !important;">Delivery Date</th>
							<th style="text-align:center !important;">Product</th>
							<th style="text-align:center !important;">Quantity</th>
						</tr>
						</thead>
							<tbody id="product_list_delivery_data">
									<tr style="display: none;">
										<td>HIDDEN</td>
									</tr>
							</tbody>
						</table>
                </div>				
				
                <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
				<div class="d-flex justify-content-end" id="">
					<div class="btn-group" role="group" aria-label="Basic outlined example" style="margin-top: -50px; position: absolute;">
						<button type="button" class="btn btn-warning new_item bi bi-plus-circle" data-bs-toggle="modal" data-bs-target="#AddPaymentModal" id="AddPaymentOrderProductBTN" onclick="ResetPaymentForm()"></button>
						<button type="button" class="btn new_item bi bi-images" data-bs-toggle="modal" data-bs-target="#ViewPaymentGalery" style="background-color: magenta;"></button>
					</div>					
					</div>

						<table class="table table-striped" id="">
						<thead>
						<tr class='report'>
							<th style="text-align:center !important;">#</th>
							<th style="text-align:center !important;">Action</th>
							<th style="text-align:center !important;">Bank</th>
							<th style="text-align:center !important;">Date of Payment</th>
							<th style="text-align:center !important;">Reference No.</th>
							<th style="text-align:center !important;">Amount</th>	
						</tr>
						</thead>
							<tbody id="update_table_payment_body_data">
									<tr style="display: none;">
										<td>HIDDEN</td>
									</tr>
							</tbody>
						</table>
                </div>
                
              </div><!-- End Default Tabs -->
					</div>
				</div>
			</div>
		</div>
        
	<!--Modal to Product-->
	<div class="modal fade" id="AddProductModal" tabindex="-1">
              <div class="modal-dialog modal-lg">
                  <div class="modal-content">
                    <div class="modal-header modal-header_form">
                      <h5 class="modal-title">Product</h5>
					  <div class="btn-group" role="group" aria-label="Basic outlined example">		
						
						<button type="button" class="btn btn-danger bi bi-x-circle form_button_icon" data-bs-dismiss="modal"></button>
					  </div>
                    </div>
                    <div class="modal-body">
					
					  <form class="g-3 needs-validation" id="AddProduct">

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
						  <button type="submit" class="btn btn-success btn-sm bi bi-save-fill form_button_icon" id="save-product" value="0"> Save</button>
						  <button type="reset" class="btn btn-primary btn-sm bi bi-backspace-fill form_button_icon" id="clear-so-save-product"> Reset</button>					  
					</div>
					</form><!-- End Multi Columns Form -->
                  </div>
                </div>
             </div>		

	<!-- Bill Delete Modal-->
    <div class="modal fade" id="PurchaseOrderProductDeleteModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header header_modal_bg">
                    <h5 class="modal-title" id="exampleModalLabel"></h5>
 					<div class="btn-sm btn-warning btn-circle bi bi-exclamation-circle btn_icon_modal"></div>
                </div>
				
                <div class="modal-body warning_modal_bg" id="modal-body">
				Are you sure you want to Delete This Product?<br>
				</div>
				<div align="left"style="margin: 10px;">
				
				Product: <span id="bill_delete_product_name"></span><br>
				Quantity: <span id="bill_delete_order_quantity"></span><br>
				
				Total Amount: <span id="bill_delete_order_total_amount"></span><br>
				
				</div>
                <div class="modal-footer footer_modal_bg">
					<button type="button" class="btn btn-danger" data-bs-dismiss="modal" id="deletePurchaseOrderComponentConfirmed" value=""><i class="bi bi-trash3 form_button_icon"></i> Delete</button>
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
	
	<!--Modal to Product-->
	<div class="modal fade" id="AddPaymentModal" tabindex="-1">
              <div class="modal-dialog">
                  <div class="modal-content">
                    <div class="modal-header modal-header_form">
                      <h5 class="modal-title">Payment</h5>
					  <div class="btn-group" role="group" aria-label="Basic outlined example">		
						
						<button type="button" class="btn btn-danger bi bi-x-circle form_button_icon" data-bs-dismiss="modal"></button>
					  </div>
                    </div>
                    <div class="modal-body">
					
					  <form class="g-3 needs-validation" id="AddPayment" enctype="multipart/form-data" action="{{route('save_purchase_order_payment')}}"  method="post" >
						@csrf
						<div class="col-sm-12">
						
						<div class="form-floating mb-3">
						
						<input type='text' class='form-control' id='purchase_order_bank' name='purchase_order_bank' list='purchase_order_bank_list' autocomplete='off' placeholder="Bank">
							<datalist id='purchase_order_bank_list'>
								<?php foreach ($purchase_payment_suggestion as $purchase_order_bank_cols) {?>
									<option value='<?=$purchase_order_bank_cols->purchase_order_bank;?>'>
								<?php } ?>
							</datalist>
							<label for="purchase_order_bank">Bank</label>
							<span class="valid-feedback" id="purchase_order_bankError"></span>
						</div>
						
						</div>
						
						<div class="col-sm-12">
						
							<div class="form-floating mb-3">
								<input type='date' class='form-control' id='purchase_order_date_of_payment' name='purchase_order_date_of_payment' value='<?=date('Y-m-d');?>'>
								<label for="purchase_order_date_of_payment">Date of Payment</label>
								<span class="valid-feedback" id="purchase_order_date_of_paymentError"></span>
							</div>
						 
						</div>
						
						<div class="col-sm-12">
							<div class="form-floating mb-3">
								<input type="text" class="form-control" aria-describedby="basic-addon1" name="purchase_order_reference_no" id="purchase_order_reference_no" required placeholder="Reference No.">
								<label for="order_quantity">Reference No.</label>
								<span class="valid-feedback" id="purchase_order_reference_noError"></span>
							</div>
							 
						</div>
						
						<div class="col-sm-12">
							<div class="form-floating mb-3">
								<input type="number" class="form-control" aria-describedby="basic-addon1" name="purchase_order_payment_amount" id="purchase_order_payment_amount" required step=".01" placeholder="Amount">
								<label for="order_quantity">Amount</label>
								<span class="valid-feedback" id="order_quantityError"></span>
							</div>
							 
						</div>
						
						<div class="row mb-3">
							<div class="col-sm-12">
							<label for="payment_image_reference" class="form-label">Upload</label>
							<input class="form-control" type="file" id="payment_image_reference" name="payment_image_reference">
							</div>
						
						<!--<img id="preview-image-before-upload" src="" alt="preview image" style="max-height: 250px;">-->
						 <input type="hidden" id="purchase_order_id_payment" name="purchase_order_id_payment" value="{{ $PurchaseOrderID }}">
						 <input type="hidden" id="purchase_order_payment_details_id" name="purchase_order_payment_details_id" value="">		
						</div>
						
						<div class="row mb-3">
							<div class="col-sm-12">
								<div class="img-holder" align="center" id="image_payment_div"></div>
							</div>
						</div>
						
						</div>
						
                    <div class="modal-footer modal-footer_form">
							<div id="loading_data_update_product" style="display:none;">
							<div class="spinner-border text-success" role="status">
								<span class="visually-hidden">Loading...</span>
							</div>
							</div>
						  <button type="submit" class="btn btn-success btn-sm bi bi-save-fill form_button_icon" id="save-payment" value="0"> Save</button>
						  <button type="reset" class="btn btn-primary btn-sm bi bi-backspace-fill form_button_icon" id="clear-payment"> Reset</button>					  
					</div>
					</form><!-- End Multi Columns Form -->
                  </div>
                </div>
             </div>			
	
	<!-- Bill Delete Modal-->
    <div class="modal fade" id="PurchaseOrderPaymentDeleteModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header header_modal_bg">
                    <h5 class="modal-title" id="exampleModalLabel"></h5>
 					<div class="btn-sm btn-warning btn-circle bi bi-exclamation-circle btn_icon_modal"></div>
                </div>
				
                <div class="modal-body warning_modal_bg" id="modal-body">
				Are you sure you want to Delete This Payment?<br>
				</div>
				<div class="row mb-2">
				<div class="col-sm-4">
				<div align="left"style="margin: 10px;">
				
				Bank: <span id="delete_purchase_order_bank"></span><br>
				Date Of Payment: <span id="delete_purchase_order_date_of_payment"></span><br>	
				Reference No.: <span id="delete_purchase_order_reference_no"></span><br>
				Amount: <span id="delete_purchase_order_payment_amount"></span><br>
				
				</div>
				</div>
				<div class="col-sm-8">
					<div class="delete_img-holder" align="center"></div>
				</div>
				</div>
				
                <div class="modal-footer footer_modal_bg">
				
					<button type="button" class="btn btn-danger" data-bs-dismiss="modal" id="deletePurchaseOrderPaymentConfirmed" value=""><i class="bi bi-trash3 form_button_icon"></i> Delete</button>
					<button type="button" class="btn btn-primary" data-bs-dismiss="modal"><i class="bi bi-x-circle form_button_icon"></i> Cancel</button>
                  
                </div>
            </div>
        </div>
    </div>	
	
	<!-- Bill Delete Modal-->
    <div class="modal fade" id="PurchaseOrderViewPaymentReferenceModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header header_modal_bg">
                    <h5 class="modal-title" id="exampleModalLabel">Payment Information</h5>
 					<div class="btn-sm btn-warning btn-circle bi bi-exclamation-circle btn_icon_modal"></div>
                </div>

				<div class="row mb-2">
				<div class="col-sm-4">
				<div align="left"style="margin: 10px;">
				
				Bank: <span id="view_purchase_order_bank"></span><br>
				Date Of Payment: <span id="view_purchase_order_date_of_payment"></span><br>	
				Reference No.: <span id="view_purchase_order_reference_no"></span><br>
				Amount: <span id="view_purchase_order_payment_amount"></span><br>
				
				</div>
				</div>
				<div class="col-sm-8">
					<div class="view_img-holder" align="center"></div>
				</div>
				</div>
				
                <div class="modal-footer footer_modal_bg">
				
					<button type="button" class="btn btn-primary" data-bs-dismiss="modal"><i class="bi bi-x-circle form_button_icon"></i> Close</button>
                  
                </div>
            </div>
        </div>
    </div>	

 	<!-- View Payment Gallery Modal-->
    <div class="modal fade" id="ViewPaymentGalery" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content" style="height: 900px;">
                <div class="modal-header header_modal_bg">
                    <h5 class="modal-title" id="exampleModalLabel">Payment Information</h5>
 					<button type="button" class="btn btn-danger bi bi-x-circle form_button_icon" data-bs-dismiss="modal"></button>
                </div>
				
				<br>
				
					<div id="carouselExampleCaptions" class="carousel slide" data-bs-ride="carousel" align="center">
					
					
					<div class="carousel-indicators">
				
					</div>
					<div class="carousel-inner" style="height: 700px;">
					 
					</div>
					
					
					
					<button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="prev">
					  <span class="carousel-control-prev-icon" aria-hidden="true" style="background-color: magenta; border-radius: 15px;"></span>
					  <span class="visually-hidden" >Previous</span>
					</button>
					<button class="carousel-control-next" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="next">
					  <span class="carousel-control-next-icon" aria-hidden="true" style="background-color: magenta; border-radius: 15px;"></span>
					  <span class="visually-hidden">Next</span>
					</button>

					</div>
					
				<br>
				
            </div>
        </div>
    </div>	
	
    </section>
</main>


	<!--Modal to Product Delivery-->
	<div class="modal fade" id="AddProductDeliveryModal" tabindex="-1">
              <div class="modal-dialog modal-lg">
                  <div class="modal-content">
                    <div class="modal-header modal-header_form">
                      <h5 class="modal-title">Add Product Delivery Details</h5>
					  <div class="btn-group" role="group" aria-label="Basic outlined example">		
						
						<button type="button" class="btn btn-danger bi bi-x-circle form_button_icon" data-bs-dismiss="modal"></button>
					  </div>
                    </div>
                    <div class="modal-body">
					
					  <form class="g-3 needs-validation" id="AddProductDelivery">

						<div class="col-sm-12">
						
							<div class="form-floating mb-3">
								<input type='date' class='form-control' id='purchase_order_delivery_date' name='purchase_order_delivery_date' value='<?=date('Y-m-d');?>' max="9999-12-31" required>
								<label for="purchase_order_delivery_date">Delivery Date</label>
								<span class="valid-feedback" id="purchase_order_delivery_dateError"></span>
							</div>
						 
						</div>

						
						<div class="col-sm-12">
						
						<div class="form-floating mb-3">
						  <input class="form-control" list="product_list_delivery" name="purchase_order_component_product_idx" id="purchase_order_component_product_idx" required autocomplete="off" placeholder="Product">
						<!--Data List for Product-->
							<datalist id="product_list_delivery">
							<span >	</span>
							
							</datalist>								
							<label for="product_delivery_idx">Product</label>
							<span class="valid-feedback" id="purchase_order_component_product_idxError"></span>
						 </div>
						
						
						</div>
						
						<div class="col-sm-12">
						
							<div class="form-floating mb-3">
								<input type="number" class="form-control" aria-describedby="basic-addon1" name="purchase_order_delivery_quantity" id="purchase_order_delivery_quantity" required step=".01" placeholder="Quantity">
								<label for="purchase_order_delivery_quantity">Quantity</label>
								<span class="valid-feedback" id="purchase_order_delivery_quantityError"></span>
							</div>
							 
							 
						</div>

						<div class="col-sm-12">
						
							<div class="form-floating mb-3">
								<input type="text" class="form-control" aria-describedby="basic-addon1" name="purchase_order_delivery_withdrawal_reference" id="purchase_order_delivery_withdrawal_reference" placeholder="Withdrawal Reference">
								<label for="purchase_order_delivery_withdrawal_reference">Withdrawal Reference</label>
							</div>
							 <span class="valid-feedback" id="purchase_order_delivery_withdrawal_referenceError"></span>
							 
						</div>
						
						<div class="col-sm-12">
						
							<div class="form-floating mb-3">
								<input type="text" class="form-control" aria-describedby="basic-addon1" name="purchase_order_delivery_hauler_details" id="purchase_order_delivery_hauler_details" placeholder="Hauler Details">
								<label for="purchase_order_delivery_hauler_details">Hauler Details</label>
							</div>
							 <span class="valid-feedback" id="purchase_order_delivery_hauler_detailsError"></span>
							 
						</div>
						
						<div class="col-sm-12">
						
							<div class="form-floating mb-3">
								<input type="text" class="form-control" aria-describedby="basic-addon1" name="purchase_order_delivery_remarks" id="purchase_order_delivery_remarks" placeholder="Ship To Account">
								<label for="purchase_order_delivery_remarks">Remarks</label>
							</div>
							 <span class="valid-feedback" id="purchase_order_delivery_remarksError"></span>
							 
						</div>
						
						</div>
						
                    <div class="modal-footer modal-footer_form">
							<div id="loading_data_add_product_delivery" style="display:none;">
							<div class="spinner-border text-success" role="status">
								<span class="visually-hidden">Loading...</span>
							</div>
							</div>
						  <button type="submit" class="btn btn-success btn-sm bi bi-save-fill form_button_icon" id="save-product-delivery"> Save</button>
						  <button type="reset" class="btn btn-primary btn-sm bi bi-backspace-fill form_button_icon" id="clear-so-save-product"> Reset</button>					  
					</div>
					</form><!-- End Multi Columns Form -->
                  </div>
				  
                </div>
             </div>		
			 
	<!-- Product Delivery Delete Modal-->
    <div class="modal fade" id="PurchaseOrderProductDeliveryDeleteModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header header_modal_bg">
                    <h5 class="modal-title" id="exampleModalLabel"></h5>
 					<div class="btn-sm btn-warning btn-circle bi bi-exclamation-circle btn_icon_modal"></div>
                </div>
				
                <div class="modal-body warning_modal_bg" id="modal-body">
				Are you sure you want to Delete This Product Delivery?<br>
				</div>
				<div align="left"style="margin: 10px;">
				
				Delivery Date: <span id="delete_delivery_purchase_order_delivery_date"></span><br>
				
				Product: <span id="delete_delivery_delete_product_name"></span><br>
				Quantity: <span id="delete_delivery_delete_purchase_order_delivery_quantity"></span><br>
				
				Withdrawal Reference: <span id="delete_delivery_purchase_order_delivery_withdrawal_reference"></span><br>
				Hauler Details: <span id="delete_delivery_purchase_order_delivery_hauler_details"></span><br>
				
				Remarks: <span id="delete_delivery_purchase_order_delivery_remarks"></span><br>
				
				</div>
                <div class="modal-footer footer_modal_bg">
					<button type="button" class="btn btn-danger" data-bs-dismiss="modal" id="deletePurchaseOrderProdcutDeliveryConfirmed" value=""><i class="bi bi-trash3 form_button_icon"></i> Delete</button>
					<button type="button" class="btn btn-primary" data-bs-dismiss="modal"><i class="bi bi-x-circle form_button_icon"></i> Cancel</button>
                  
                </div>
            </div>
        </div>
    </div>
	
		<!-- Product Delivery Delete Modal-->
    <div class="modal fade" id="PurchaseOrderProductDeliveryViewModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header header_modal_bg">
                    <h5 class="modal-title" id="exampleModalLabel"></h5>
 					<div class="btn-sm btn-warning btn-circle bi bi-exclamation-circle btn_icon_modal"></div>
                </div>
				
				
                <div class="modal-body warning_modal_bg" id="modal-body">
				Delivery Information<br>
				</div>
				<div align="left"style="margin: 10px;">
				
				Delivery Date: <span id="view_delivery_purchase_order_delivery_date"></span><br>
				
				Product: <span id="view_delivery_delete_product_name"></span><br>
				Quantity: <span id="view_delivery_delete_purchase_order_delivery_quantity"></span><br>
				
				Withdrawal Reference: <span id="view_delivery_purchase_order_delivery_withdrawal_reference"></span><br>
				Hauler Details: <span id="view_delivery_purchase_order_delivery_hauler_details"></span><br>
				
				Remarks: <span id="view_delivery_purchase_order_delivery_remarks"></span><br>
				
				</div>
                <div class="modal-footer footer_modal_bg">
					<button type="button" class="btn btn-primary" data-bs-dismiss="modal"><i class="bi bi-x-circle form_button_icon"></i> Cancel</button>
                </div>
            </div>
        </div>
    </div>
@endsection

