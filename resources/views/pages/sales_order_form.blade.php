@extends('layouts.layout')  
@section('content')  

<main id="main" class="main">	

    <section class="section">
		<div class="card">
		
            <div class="card-body">
			
				<h5 class="card-title" align="center">Sales Order Information</h5>
				<div class="row mb-2">
					<div class="col-sm-6">
					<h6>Control Number : <span class="badge" id='control_no' style="background-color: orange !important; color:#000 !important; font-weight:bold;">{{ $sales_order_data[0]['sales_order_control_number'] }}</span></h6>
					</div>
					<div class="col-sm-6">
						<div class="d-flex justify-content-end" id="">
							<div class="btn-group" role="group" aria-label="Basic outlined example" style="margin-top: -35px; position: absolute;">
								<a class="btn btn-secondary new_item bi bi-chevron-double-left form_button_icon" href="{{ route('salesorder') }}" title="Back">  
								  <span title="Back to Sales Order List">Back</span>
								</a>
								<button type="button" class="btn btn-dark new_item bi-printer-fill form_button_icon" id="PrintSalesOrder">&nbsp;Print</button>
								<!--<button type="button" class="btn btn-success new_item bi bi-printer" onclick="#"></button>-->
								
							</div>					
						</div>
						
					</div>
				</div>
				
				<hr>
				<div class="row mb-2">
					<div class="col-sm-5">
						
						<form class="g-3 needs-validation" id="UpdateSalesOrderformUpdate">
							<div class="row mb-2">
												  <div class="col-sm-12">
												  <label for="sales_order_date" class="form-label">Date</label>
													<input type="date" class="form-control" id="sales_order_date" name="sales_order_date" value="{{ $sales_order_data[0]['sales_order_date'] }}" required max="9999-12-31" >
													<span class="valid-feedback" id="sales_order_dateError"></span>
												  </div>
												</div>
												
												<div class="row mb-2">
												  <div class="col-sm-12">
												   <label for="company_header" class="form-label">Branch</label>
												 	<select class="form-select form-control" required="" name="company_header" id="company_header" onchange="UpdateBranch()">
													<?php $branch_idx = $sales_order_data[0]['company_header']; ?>
										
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
												  <div class="col-sm-12">
												   <label for="client_id" class="form-label">Sold To</label>
													<input class="form-control" list="client_name" name="client_name" id="client_id" required autocomplete="off" value="{{ $sales_order_data[0]['client_name'] }}" onchange="ClientInfo()">
														<datalist id="client_name">
														  @foreach ($client_data as $client_data_cols)
														  <option label="{{$client_data_cols->client_name}}" data-id="{{$client_data_cols->client_id}}" value="{{$client_data_cols->client_name}}">
														  @endforeach
														</datalist>							
													<span class="valid-feedback" id="client_idxError"></span>
												  </div>
												</div>

												<hr>
												
												<div class="row">
														
														<label for="sales_order_payment_type" class="col-sm-3 col-form-label">Payment Type</label>
														<div class="col-md-9">
														
														<?php $sales_order_payment_type = $sales_order_data[0]->sales_order_payment_type; ?>
														
															<select class="form-select form-control" required="" name="sales_order_payment_type" id="sales_order_payment_type"> 
															
																<option value="Receivable" <?php if($sales_order_payment_type=='Receivable'){ echo "selected";} else{} ?>>Receivable</option>
																<option value="PBD" <?php if($sales_order_payment_type=='PBD'){ echo "selected";} else{} ?>>Paid Before Delivery</option>
															
															</select>
															
														</div>
												</div>

												<hr>
												<div class="row mb-2">
									  
														<label for="sales_order_net_percentage" class="col-sm-3 col-form-label">Net Value</label>
														<div class="col-sm-9">			  
															<input type="number" class="form-control" id="sales_order_net_percentage" name="sales_order_net_percentage" value="{{ $sales_order_data[0]['sales_order_net_percentage'] }}" disabled>
														</div>
												
												</div>
												<div class="row mb-2">
									  
														<label for="sales_order_withholding_tax" class="col-sm-3 col-form-label">Withholding Tax</label>
														<div class="col-sm-9">			  
															<input type="number" class="form-control" id="sales_order_withholding_tax" name="sales_order_withholding_tax" value="{{ $sales_order_data[0]['sales_order_withholding_tax'] }}" disabled>
														</div>
												
												</div>
												
												<hr>
												<div class="row mb-2">
												  <label for="dr_number" class="col-sm-3 col-form-label">D.R Number</label>
												  <div class="col-sm-9">			  
													<input type="text" class="form-control" id="dr_number" name="dr_number" value="{{ $sales_order_data[0]['sales_order_dr_number'] }}" >
												  </div>
												</div>
												
												<div class="row mb-2">
													<label for="sales_order_po_number" class="col-sm-3 col-form-label">P.O Number</label>
													<div class="col-sm-9">
														<input type="text" class="form-control" id="sales_order_po_number" name="sales_order_po_number" value="{{ $sales_order_data[0]['sales_order_po_number'] }}" >
													</div>
												</div>
												
												<div class="row mb-2">
													<label for="sales_order_or_number" class="col-sm-3 col-form-label">Sales Invoice</label>
													<div class="col-sm-9">
														<input type="text" class="form-control" id="sales_order_or_number" name="sales_order_or_number" value="{{ $sales_order_data[0]['sales_order_or_number'] }}" title="OR Number">
													</div>
												</div>
												
												<div class="row mb-2">
													<label for="sales_order_charge_invoice" class="col-sm-3 col-form-label">Charge Invoice</label>
													<div class="col-sm-9">
														<input type="text" class="form-control" id="sales_order_charge_invoice" name="sales_order_charge_invoice" value="{{ $sales_order_data[0]['sales_order_charge_invoice'] }}" >
													</div>
												</div>
												
												<div class="row mb-2">
													<label for="sales_order_collection_receipt" class="col-sm-3 col-form-label">Collection Receipt</label>
													<div class="col-sm-9">
														<input type="text" class="form-control" id="sales_order_collection_receipt" name="sales_order_collection_receipt" value="{{ $sales_order_data[0]['sales_order_collection_receipt'] }}" >
													</div>
												</div>
												
												<div class="row mb-2">
													<label for="payment_term" class="col-sm-3 col-form-label">Payment Term</label>
													<div class="col-sm-9">
														<input type="text" class="form-control" id="payment_term" name="payment_term" value="{{ $sales_order_data[0]['sales_order_payment_term'] }}" >
													</div>
												</div>
												<hr>
												<div class="row mb-2">
													
													<div class="col-sm-12">
													<label for="delivered_to" class="form-label">Delivered To</label>
													<input type="text" class="form-control" id="delivered_to" name="delivered_to" list="sales_order_delivered_to_list" value="{{ $sales_order_data[0]['sales_order_delivered_to'] }}">
														<datalist id="sales_order_delivered_to_list">
															@foreach ($sales_order_delivered_to as $sales_order_delivered_to_cols)
																<option value="{{$sales_order_delivered_to_cols->sales_order_delivered_to}}">
															@endforeach
														  </datalist>
													<span class="valid-feedback" id="delivered_toError"></span>
													</div>
												</div>
												
												<div class="row mb-2">
												  
												  <div class="col-sm-12">
												  <label for="delivered_to_address" class="form-label">Delivered To Address</label>
												  <input type="text" class="form-control" id="delivered_to_address" name="delivered_to_address" list="delivered_to_address_list" value="{{ $sales_order_data[0]['sales_order_delivered_to_address'] }}">
														<datalist id="delivered_to_address_list">
															@foreach ($sales_order_delivered_to_address as $sales_order_delivered_to_address_cols)
																<option value="{{$sales_order_delivered_to_address_cols->sales_order_delivered_to_address}}">
															@endforeach
														  </datalist>
												  <span class="valid-feedback" id="delivered_to_addressError"></span>
												  </div>
												</div>
												
												<div class="row mb-2">
												  <div class="col-sm-12">
												  <label for="sales_order_date" class="form-label">Delivery Method</label>
													<input type="text" class="form-control" id="delivery_method" name="delivery_method" value="{{ $sales_order_data[0]['sales_order_delivery_method'] }}">
												  </div>
												</div>
												
												<div class="row mb-2">
												  <div class="col-sm-12">
												  <label for="hauler" class="form-label">Hauler</label>
												  <input type="text" class="form-control" id="hauler" name="hauler" value="{{ $sales_order_data[0]['sales_order_hauler'] }}">
												  </div>
												</div>
												
												<div class="row mb-2">
												  <div class="col-sm-12">
												  <label for="required_date" class="form-label">Required Date</label>
												  <input type="date" class="form-control" id="required_date" name="required_date" value="{{ $sales_order_data[0]['sales_order_required_date'] }}">
												  </div>
												</div>
												
												<hr>
												
												<div class="row mb-2">
												  <div class="col-sm-12">
													<label for="instructions" class="form-label">Instructions</label>
													<textarea class="form-control" id="instructions" name="instructions" style="height: 38px;">{{ $sales_order_data[0]['sales_order_instructions'] }}</textarea>
												  </div>
												  
												  
												</div>
												
												<div class="row mb-2">
												  <div class="col-sm-12">
													<label for="note" class="form-label">Notes</label>
													<textarea class="form-control" id="note" name="note" style="height: 38px;">{{ $sales_order_data[0]['sales_order_note'] }}</textarea>
												  </div>
												</div>
												
												
												<div class="card-footer">
												<div class="row mb-3">
												<div class="col-sm-6" align=''>
												<div id="loading_data_update_so" style="display:none;">
													<div class="spinner-border text-success" role="status">
														<span class="visually-hidden">Loading...</span>
													</div>
												</div>
												</div>
												<div class="col-sm-6" align='right'>
												<a class="btn btn-secondary btn-sm new_item bi bi-chevron-double-left form_button_icon" href="{{ route('salesorder') }}" title="Back">  
												  <span title="Back to Sales Order List">Back</span>
												</a>
												<button type="submit" class="btn btn-success btn-sm bi bi-save-fill form_button_icon" id="update-sales-order" title='Update Sales Order information'> Update</button>
												</div>
												</div>	
						</div>
						
						</form>
					</div>
					<div class="col-sm-7">
					 <!-- Default Tabs -->
              <ul class="nav nav-tabs" id="myTab" role="tablist">
                <!-- nav-link active -->
				<li class="nav-item" role="presentation">
                  <button class="nav-link <?php if($tab=='product') { echo 'active'; } ?>" id="product-tab" data-bs-toggle="tab" data-bs-target="#product" type="button" role="tab" aria-controls="product" aria-selected="true" onclick="LoadProduct()" title='Product List, Create, Update and Delete Product'>Product</button>
                </li>
				
				<li class="nav-item" role="presentation">
                  <button class="nav-link <?php if($tab=='delivery') { echo 'active'; } ?>" id="delivery-tab" data-bs-toggle="tab" data-bs-target="#delivery" type="button" role="tab" aria-controls="delivery" aria-selected="true" onclick="" title='Product Delivery List, Create, Update and Delete Delivery'>Delivery</button>
                </li>
				
                <li class="nav-item" role="presentation">
                  <button class="nav-link <?php if($tab=='payment') { echo 'active'; } ?>" id="payment-tab" data-bs-toggle="tab" data-bs-target="#payment" type="button" role="tab" aria-controls="payment" aria-selected="false" tabindex="-1" onclick="LoadPayment()" title='Payment List, Create, Update and Delete Payment'>Payment</button>
                </li>
				
				<li class="nav-item" role="presentation">
                  <button class="nav-link <?php if($tab=='receivable') { echo 'active'; } ?>" id="receivable-tab" data-bs-toggle="tab" data-bs-target="#receivable" type="button" role="tab" aria-controls="receivable" aria-selected="false" tabindex="-1" title='Update Receivable Information'>Recievable Details</button>
                </li>
				
              </ul>
			  
              <div class="tab-content pt-2" id="myTabContent">
               
			   <div class="tab-pane fade  <?php if($tab=='product') { echo ' show active'; } ?>"" id="product" role="tabpanel" aria-labelledby="product-tab">
				<div class="d-flex justify-content-end" id="">
					<div class="btn-group" role="group" aria-label="Basic outlined example" style="margin-top: -50px; position: absolute;">
						<button type="button" class="btn btn-success new_item bi bi-plus-circle form_button_icon" data-bs-toggle="modal" data-bs-target="#AddProductModal" id="AddSalesOrderProductBTN"></button>
						
					</div>											
					</div>
					
						<table class="table table-striped" id="">
						<thead>
						<tr class='report'>
							<th style="text-align:center !important;" class="action_column_class">Action</th>
							<th style="text-align:center !important;">Item #</th>
							<th style="text-align:center !important;">Description</th>
							<th style="text-align:center !important;">Price</th>
							<th style="text-align:center !important;">Quantity</th>
							<th style="text-align:center !important;">Amount</th>	
						</tr>
						</thead>
							<tbody id="table_sales_order_product_body_data">
									<tr style="display: none;">
										<td>HIDDEN</td>
									</tr>
							</tbody>
						</table>
                </div>
				
				
                <div class="tab-pane fade  <?php if($tab=='delivery') { echo ' show active'; } ?>" id="delivery" role="tabpanel" aria-labelledby="delivery-tab">
				<div class="d-flex justify-content-end" id="">
					<div class="btn-group" role="group" aria-label="Basic outlined example" style="margin-top: -50px; position: absolute;">
						<button type="button" class="btn btn-success new_item bi bi-plus-circle form_button_icon" data-bs-toggle="modal" data-bs-target="#AddProductDeliveryModal" id="" onclick="ResetDeliveryForm()" title="Add Sales Order Delivery Items"></button>
						<button type="button" class="btn btn-dark new_item bi-printer-fill form_button_icon" id="PrintSalesOrderDeliveyStatus" title="Print Sales Order Delivered Item / Status"></button>
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
				
                <div class="tab-pane fade <?php if($tab=='payment') { echo 'show active'; } ?>" id="payment" role="tabpanel" aria-labelledby="payment-tab">
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
							<th style="text-align:center !important;">Mode of Payment</th>
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
                
				<div class="tab-pane fade <?php if($tab=='receivable') { echo 'show active'; } ?>" id="receivable" role="tabpanel" aria-labelledby="receivable-tab">
		
				<div class="d-flex justify-content-end" id="">
		
							<div class="btn-group" role="group" aria-label="Basic outlined example" style="margin-top: -50px; position: absolute;">
							
								<button type="button" class="btn btn-dark new_item bi-printer-fill form_button_icon" id="PrintSOA">&nbsp;SOA</button>
								<button type="button" class="btn btn-dark new_item bi-printer-fill form_button_icon" id="PrintReceivable">&nbsp;Receivable</button>
								<!--<button type="button" class="btn btn-success new_item bi bi-printer" onclick="#"></button>-->
								
							</div>
							
						</div>
					<div class="row mb-2">
					
					<div class="col-sm-12">
					<h6>
						<div class="row mb-2">
							<label class="col-sm-4 col-form-label">Control Number :</label>
							<label class="col-sm-8 col-form-label" id='receivable_control_number_info'>{{ $receivables_details['control_number'] }}</label>
						</div>
					</h6>
					
					<form class="g-2 needs-validation pt-1" id="ReceivableformEditFromSalesOrder">
						
						<div class="row mb-2">
						  <label for="receivable_billing_date_SO" class="col-sm-4 col-form-label">Billing Date : </label>
						  <div class="col-sm-8">
							<input type="date" class="form-control " name="receivable_billing_date_SO" id="receivable_billing_date_SO" value="" required>
							<span class="valid-feedback" id="receivable_billing_date_SO_Error"></span>
						  </div>
						</div>
						
						<div class="row mb-2">
						  <label for="receivable_payment_term_SO" class="col-sm-4 col-form-label">Payment Term : </label>
						  <div class="col-sm-8">
							<input type="text" class="form-control " name="receivable_payment_term_SO" id="receivable_payment_term_SO" value="">
							<span class="valid-feedback" id="receivable_payment_term_SO_Error"></span>
						  </div>
						</div>							
						
						<div class="row mb-2">
						  <label for="receivable_description_SO" class="col-sm-4 col-form-label">Description : </label>
						  <div class="col-sm-8">
							<textarea class="form-control" id="receivable_description_SO" style="height: 50px;" required></textarea>
							<span class="valid-feedback" id="receivable_description_SO_Error"></span>
						  </div>
						</div>
						
						<div class="row mb-2">
						  <label for="" class="col-sm-4 col-form-label">Amount : </label>
						  <label class="col-sm-8 col-form-label" id="receivable_amount_info"></label>
						</div>
					<div class="card-footer">
												<div class="row mb-3">
												<div class="col-sm-6" align="">
												<div id="update_loading_data_receivable" style="display:none;">
													<div class="spinner-border text-success" role="status">
														<span class="visually-hidden">Loading...</span>
													</div>
												</div>
												</div>
												<div class="col-sm-6" align="right">
												
												<button type="submit" class="btn btn-success btn-sm bi bi-save-fill form_button_icon" id="SO-update-receivables" title="Update Receivable Information"> Update</button>
												</div>
												</div>	
					</div>	

					
					</form><!-- End Multi Columns Form -->
					</div>	
						
						
					</div>
                </div>
                
			  
			  
			  
			  </div>
			  
	                
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
						  <button type="submit" class="btn btn-success btn-sm bi bi-save-fill form_button_icon" id="save-product"> Save</button>
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
					
					  <form class="g-3 needs-validation" id="EditSalesOrderComponentProduct">

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
						  <button type="submit" class="btn btn-success btn-sm bi bi-save-fill form_button_icon" id="update-product"> Update</button>					  
					</div>
					</form><!-- End Multi Columns Form -->
                  </div>
				  
                </div>
             </div>		

	<!-- Bill Delete Modal-->
    <div class="modal fade" id="SalesOrderComponentDeleteModal" tabindex="-1" role="dialog" aria-hidden="true">
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
					<button type="button" class="btn btn-danger" data-bs-dismiss="modal" id="deleteSalesOrderComponentConfirmed" value=""><i class="bi bi-trash3 form_button_icon"></i> Delete</button>
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
					
					  <form class="g-3 needs-validation" id="AddPayment" enctype="multipart/form-data" action="{{route('sales_order_receivable_payment')}}"  method="post" >
						@csrf
						<div class="col-sm-12">
						
						<div class="form-floating mb-3">
						
						<input type='text' class='form-control' id='receivable_mode_of_payment' name='receivable_mode_of_payment' list='receivable_mode_of_payment' autocomplete='off' placeholder="Bank">
							<datalist id='sales_order_bank_list'>
								<?php foreach ($receivables_payment_suggestion as $receivables_payment_suggestion_cols) {?>
									<option value='<?=$receivables_payment_suggestion_cols->receivable_mode_of_payment;?>'>
								<?php } ?>
							</datalist>
							<label for="receivable_mode_of_payment">Mode of Payment</label>
							<span class="valid-feedback" id="receivable_mode_of_paymentError"></span>
						 </div>
						
						
						</div>
						
						<div class="col-sm-12">
						
							<div class="form-floating mb-3">
								<input type='date' class='form-control' id='receivable_date_of_payment' name='receivable_date_of_payment' value='<?=date('Y-m-d');?>' max="9999-12-31" >
								<label for="receivable_date_of_payment">Date of Payment</label>
								<span class="valid-feedback" id="receivable_date_of_paymentError"></span>
							</div>
						 
						</div>
						
						<div class="col-sm-12">
							<div class="form-floating mb-3">
								<input type="text" class="form-control" aria-describedby="basic-addon1" name="receivable_reference" id="receivable_reference" required placeholder="Reference No.">
								<label for="receivable_reference">Reference No.</label>
								<span class="valid-feedback" id="receivable_referenceError"></span>
							</div>
							 
						</div>
						
						<div class="col-sm-12">
							<div class="form-floating mb-3">
								<input type="number" class="form-control" aria-describedby="basic-addon1" name="receivable_payment_amount" id="receivable_payment_amount" required step=".01" placeholder="Amount">
								<label for="receivable_payment_amount">Amount</label>
								<span class="valid-feedback" id="receivable_payment_amountError"></span>
							</div>
							 
						</div>
						
						<div class="row mb-3">
							<div class="col-sm-12">
							<label for="payment_image_reference" class="form-label">Upload</label>
							<input class="form-control" type="file" id="payment_image_reference" name="payment_image_reference">
							</div>
						
						<!--<img id="preview-image-before-upload" src="" alt="preview image" style="max-height: 250px;">-->
						 <input type="hidden" id="receivable_idx_payment" name="receivable_idx_payment" value="{{ @$receivables_details['receivable_id'] }}">
						 <input type="hidden" id="receivable_payment_id" name="receivable_payment_id" value="">		
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
	
	<!-- Bill Delete Modal-->
    <div class="modal fade" id="SalesOrderPaymentDeleteModal" tabindex="-1" role="dialog" aria-hidden="true">
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
				
				Mode of Payment: <span id="delete_receivable_mode_of_payment"></span><br>
				Date Of Payment: <span id="delete_receivable_date_of_payment"></span><br>	
				Reference No.: <span id="delete_receivable_reference"></span><br>
				Amount: <span id="delete_receivable_payment_amount"></span><br>
				
				</div>
				</div>
				<div class="col-sm-8">
					<div class="delete_img-holder" align="center"></div>
				</div>
				</div>
				
                <div class="modal-footer footer_modal_bg">
				
					<button type="button" class="btn btn-danger" data-bs-dismiss="modal" id="deleteSalesOrderPaymentConfirmed" value=""><i class="bi bi-trash3 form_button_icon"></i> Delete</button>
					<button type="button" class="btn btn-primary" data-bs-dismiss="modal"><i class="bi bi-x-circle form_button_icon"></i> Cancel</button>
                  
                </div>
            </div>
        </div>
    </div>	
	
	<!-- Bill Delete Modal-->
    <div class="modal fade" id="ViewOrderViewPaymentReferenceModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header header_modal_bg">
                    <h5 class="modal-title" id="exampleModalLabel">Payment Information</h5>
 					<div class="btn-sm btn-warning btn-circle bi bi-exclamation-circle btn_icon_modal"></div>
                </div>

				<div class="row mb-2">
				<div class="col-sm-4">
				<div align="left"style="margin: 10px;">
				
				Mode of Payment: <span id="view_receivable_mode_of_payment"></span><br>
				Date Of Payment: <span id="view_receivable_date_of_payment"></span><br>	
				Reference No.: <span id="view_receivable_reference"></span><br>
				Amount: <span id="view_receivable_payment_amount"></span><br>
				
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
	
	
	<!--Delivery Form-->
	
	
    </section>

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
								<input type='date' class='form-control' id='sales_order_delivery_date' name='sales_order_delivery_date' value='<?=date('Y-m-d');?>' max="9999-12-31" required>
								<label for="sales_order_delivery_date">Delivery Date</label>
								<span class="valid-feedback" id="sales_order_delivery_dateError"></span>
							</div>
						 
						</div>

						
						<div class="col-sm-12">
						
						<div class="form-floating mb-3">
						  <input class="form-control" list="product_list_delivery" name="sales_order_component_product_idx" id="sales_order_component_product_idx" required autocomplete="off" placeholder="Product">
						<!--Data List for Product-->
							<datalist id="product_list_delivery">
							<span >	</span>
							
							</datalist>								
							<label for="product_delivery_idx">Product</label>
							<span class="valid-feedback" id="sales_order_component_product_idxError"></span>
						 </div>
						
						
						</div>
						
						<div class="col-sm-12">
						
							<div class="form-floating mb-3">
								<input type="number" class="form-control" aria-describedby="basic-addon1" name="sales_order_delivery_quantity" id="sales_order_delivery_quantity" required step=".01" placeholder="Quantity">
								<label for="sales_order_delivery_quantity">Quantity</label>
								<span class="valid-feedback" id="sales_order_delivery_quantityError"></span>
							</div>
							 
							 
						</div>

						<div class="col-sm-12">
						
							<div class="form-floating mb-3">
								<input type="text" class="form-control" aria-describedby="basic-addon1" name="sales_order_delivery_withdrawal_reference" id="sales_order_delivery_withdrawal_reference" placeholder="Delivery Reference">
								<label for="sales_order_delivery_withdrawal_reference">Delivery Reference</label>
							</div>
							 <span class="valid-feedback" id="sales_order_delivery_withdrawal_referenceError"></span>
							 
						</div>
						
						<div class="col-sm-12">
						
							<div class="form-floating mb-3">
								<input type="text" class="form-control" aria-describedby="basic-addon1" name="sales_order_delivery_hauler_details" id="sales_order_delivery_hauler_details" placeholder="Hauler Details">
								<label for="sales_order_delivery_hauler_details">Hauler Details</label>
							</div>
							 <span class="valid-feedback" id="sales_order_delivery_hauler_detailsError"></span>
							 
						</div>
						
						<div class="col-sm-12">
						
							<div class="form-floating mb-3">
								<input type="text" class="form-control" aria-describedby="basic-addon1" name="sales_order_delivery_remarks" id="sales_order_delivery_remarks" placeholder="Ship To Account">
								<label for="sales_order_delivery_remarks">Remarks</label>
							</div>
							 <span class="valid-feedback" id="sales_order_delivery_remarksError"></span>
							 
						</div>
						
						<!--<div class="col-sm-12">
						
							<div class="form-floating mb-3">
								<select class="form-select form-control" required="" name="sales_order_branch_delivery" id="sales_order_branch_delivery">
									<//?php $branch_idx = $sales_order_data[0]['company_header']; ?>
										
										@foreach ($teves_branch as $teves_branch_cols)
											<//?php 
												$branch_id = $teves_branch_cols->branch_id;
													
											//?>
												<option value="{{$teves_branch_cols->branch_id}}" <//?php if($branch_id==$branch_idx){ echo "selected";} else{} ?>>
													{{$teves_branch_cols->branch_code}}
												</option>
										@endforeach

								</select>
								<label for="sales_order_branch_delivery">Branch Delivery</label>
							</div>
							<span class="valid-feedback" id="sales_order_branch_deliveryError"></span>
							 
						</div>-->
						
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
    <div class="modal fade" id="SalesOrderProductDeliveryDeleteModal" tabindex="-1" role="dialog" aria-hidden="true">
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
				
				Delivery Date: <span id="delete_delivery_sales_order_delivery_date"></span><br>
				
				Product: <span id="delete_delivery_delete_product_name"></span><br>
				Quantity: <span id="delete_delivery_delete_sales_order_delivery_quantity"></span><br>
				
				Delivery Reference: <span id="delete_delivery_sales_order_delivery_withdrawal_reference"></span><br>
				Hauler Details: <span id="delete_delivery_sales_order_delivery_hauler_details"></span><br>
				
				Remarks: <span id="delete_delivery_sales_order_delivery_remarks"></span><br>
				
				</div>
                <div class="modal-footer footer_modal_bg">
					<button type="button" class="btn btn-danger" data-bs-dismiss="modal" id="deleteSalesOrderProdcutDeliveryConfirmed" value=""><i class="bi bi-trash3 form_button_icon"></i> Delete</button>
					<button type="button" class="btn btn-primary" data-bs-dismiss="modal"><i class="bi bi-x-circle form_button_icon"></i> Cancel</button>
                  
                </div>
            </div>
        </div>
    </div>
	
		<!-- Product Delivery Delete Modal-->
    <div class="modal fade" id="SalesOrderProductDeliveryViewModal" tabindex="-1" role="dialog" aria-hidden="true">
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
				
				Delivery Date: <span id="view_delivery_sales_order_delivery_date"></span><br>
				
				Product: <span id="view_delivery_delete_product_name"></span><br>
				Quantity: <span id="view_delivery_delete_sales_order_delivery_quantity"></span><br>
				
				Delivery Reference: <span id="view_delivery_sales_order_delivery_withdrawal_reference"></span><br>
				Hauler Details: <span id="view_delivery_sales_order_delivery_hauler_details"></span><br>
				
				Remarks: <span id="view_delivery_sales_order_delivery_remarks"></span><br>
				
				</div>
                <div class="modal-footer footer_modal_bg">
					<button type="button" class="btn btn-primary" data-bs-dismiss="modal"><i class="bi bi-x-circle form_button_icon"></i> Cancel</button>
                </div>
            </div>
        </div>
    </div>
	
</main>

@endsection

