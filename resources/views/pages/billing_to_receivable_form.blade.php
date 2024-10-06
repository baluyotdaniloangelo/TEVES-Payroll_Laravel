@extends('layouts.layout')  
@section('content')  

<main id="main" class="main">	

    <section class="section">
		<div class="card">
		
            <div class="card-body">
			
				<h5 class="card-title" align="center">Receivable and Billing Information</h5>
				<div class="row mb-2">
					<div class="col-sm-6">
					<h6>Receivable Control Number : <span class="badge" id='control_no' style="background-color: orange !important; color:#000 !important; font-weight:bold;">{{ $receivables_details['control_number'] }}</span></h6>
					</div>
					<div class="col-sm-6">
						<div class="d-flex justify-content-end" id="">
							<div class="btn-group" role="group" aria-label="Basic outlined example" style="margin-top: -35px; position: absolute;">
								<a class="btn btn-secondary new_item bi bi-chevron-double-left form_button_icon" href="{{ route('receivables') }}" title="Back">  
								  <span title="Back to Sales Order List">Back</span>
								</a>
								<button type="button" class="btn btn-dark new_item bi-printer-fill form_button_icon" id="PrintReceivableOrder" onclick="print_billing()">&nbsp;Billing</button>
								<button type="button" class="btn btn-dark new_item bi-printer-fill form_button_icon" id="PrintSOA" onclick="print_soa()">&nbsp;SOA</button>
								<button type="button" class="btn btn-dark new_item bi-printer-fill form_button_icon" id="PrintReceivable" onclick="print_receivable()">&nbsp;Receivable</button>
								<!--<button type="button" class="btn btn-success new_item bi bi-printer" onclick="#"></button>-->
								
							</div>					
						</div>
						
					</div>
				</div>
				
				<hr>
				<div class="row mb-2">
				
					<div class="col-sm-5">
							
					<div class="row mb-2">
					<div class="col-sm-12">
					
					<form class="g-2 needs-validation pt-1" id="ReceivableformEditFromSalesOrder">
						
						<h6>
						<div class="row mb-2">
							<label class="col-sm-3 col-form-label">Client:</label>
							<label class="col-sm-9 col-form-label" id='client_name_receivables'>{{ $receivables_details['control_number'] }}</label>
						</div>
						</h6>
						
						<div class="row mb-2">
						  <label for="billing_date" class="col-sm-3 col-form-label">Branch : </label>
						  <div class="col-sm-9">
							<select class="form-select form-control" required="" name="company_header" id="company_header">
							@foreach ($teves_branch as $teves_branch_cols)
								<option value="{{$teves_branch_cols->branch_id}}">{{$teves_branch_cols->branch_code}}</option>
							@endforeach		
							</select>
						  </div>
						</div>
						
						<div class="row mb-2">
						  <label for="billing_date" class="col-sm-3 col-form-label">Billing Date : </label>
						  <div class="col-sm-9">
							<input type="date" class="form-control " name="billing_date" id="billing_date" value="" required>
							<span class="valid-feedback" id="billing_dateError"></span>
						  </div>
						</div>

						<div class="row mb-2">
						  <label for="start_date" class="col-sm-3 col-form-label">Period:</label>
						  <div class="col-sm-9">
						  
						  <div class="input-group">
									<input type="date" class="form-control" name="start_date" id="start_date" required readonly>
									<input type="date" class="form-control" name="end_date" id="end_date" required readonly>
							<span class="valid-feedback" id="end_dateError"></span>
							<span class="valid-feedback" id="start_dateError"></span>		
							</div>

						  </div>
						</div>						
								
						<div class="row mb-2">
						  <label for="less_per_liter" class="col-sm-3 col-form-label" title="Applicable to All Product with Liter as a unit of measurement">Discount Per Liter</label>
						  <div class="col-sm-9">
							<input type="text" class="form-control " name="less_per_liter" id="less_per_liter" value="" disabled>
							<span class="valid-feedback" id="less_per_literError"></span>
						  </div>
						</div>

						<div class="row mb-2">
						  <label for="less_per_liter" class="col-sm-3 col-form-label" title="Applicable to All Product with Liter as a unit of measurement">Net Value</label>
						  <div class="col-sm-9">
							<input type="text" class="form-control " name="net_value_percentage" id="net_value_percentage" value="1.12" disabled>
							<span class="valid-feedback" id=""></span>
						  </div>
						</div>
						
						<div class="row mb-2">
						  <label for="less_per_liter" class="col-sm-3 col-form-label" title="Applicable to All Product with Liter as a unit of measurement">VAT Value</label>
						  <div class="col-sm-9">
							<input type="text" class="form-control " name="vat_value_percentage" id="vat_value_percentage" value="12" disabled>
							<span class="valid-feedback" id=""></span>
						  </div>
						</div>

						<div class="row mb-2">
						  <label for="less_per_liter" class="col-sm-3 col-form-label" title="Applicable to All Product with Liter as a unit of measurement">Withholding Tax</label>
						  <div class="col-sm-9">
							<input type="text" class="form-control " name="withholding_tax_percentage" id="withholding_tax_percentage" value="1" disabled>
							<span class="valid-feedback" id="less_per_literError"></span>
						  </div>
						</div>						
						
						<div class="row mb-2">
						  <label for="payment_term" class="col-sm-3 col-form-label">Payment Term : </label>
						  <div class="col-sm-9">
							<input type="text" class="form-control " name="payment_term" id="payment_term" value="">
							<span class="valid-feedback" id="payment_termError"></span>
						  </div>
						</div>							
						
						<div class="row mb-2">
						  <label for="receivable_description" class="col-sm-3 col-form-label">Description : </label>
						  <div class="col-sm-9">
							<textarea class="form-control" id="receivable_description" style="height: 50px;" required></textarea>
							<span class="valid-feedback" id="receivable_descriptionError"></span>
						  </div>
						</div>
						
						<div class="row mb-2">
						  <label for="" class="col-sm-3 col-form-label">Amount : </label>
						  <label class="col-sm-9 col-form-label" id="receivable_amount_info"></label>
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
				
					<div class="col-sm-7">
					 <!-- Default Tabs -->
              <ul class="nav nav-tabs" id="myTab" role="tablist">
                <!-- nav-link active -->
				<li class="nav-item" role="presentation">
                  <button class="nav-link <?php if($tab=='product') { echo 'active'; } ?>" id="product-tab" data-bs-toggle="tab" data-bs-target="#product" type="button" role="tab" aria-controls="product" aria-selected="true" onclick="LoadProduct()" title='Product List, Create, Update and Delete Product'>Product</button>
                </li>
				
                <li class="nav-item" role="presentation">
                  <button class="nav-link <?php if($tab=='payment') { echo 'active'; } ?>" id="payment-tab" data-bs-toggle="tab" data-bs-target="#payment" type="button" role="tab" aria-controls="payment" aria-selected="false" tabindex="-1" onclick="LoadPayment()" title='Payment List, Create, Update and Delete Payment'>Payment</button>
                </li>
				
              </ul>
              <div class="tab-content pt-2" id="myTabContent">
                <div class="tab-pane fade  <?php if($tab=='product') { echo ' show active'; } ?>"" id="product" role="tabpanel" aria-labelledby="product-tab">
					<!--<div class="d-flex justify-content-end" id="">
					<div class="btn-group" role="group" aria-label="Basic outlined example" style="margin-top: -50px; position: absolute;">
						<button type="button" class="btn btn-success new_item bi bi-plus-circle form_button_icon" data-bs-toggle="modal" data-bs-target="#AddProductModal" id="AddSalesOrderProductBTN"></button>

					</div>											
					</div>-->
					
						<table class="table table-striped" id="">
						<thead>
						<tr class='report'>
							<th style="text-align:center !important;" class="action_column_class">Action</th>
							<th style="text-align:center !important;">Item #</th>
							<th style="text-align:center !important;">Description</th>
							<th style="text-align:center !important;">Price</th>
							<th style="text-align:center !important;">Quantity</th>
							<th style="text-align:center !important;">Unit</th>
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
                
				
			  
			  </div>
			  
	                
					</div>
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
							<input class="form-control" list="update_client_name" name="update_client_name" id="update_client_idx" required autocomplete="off" disabled>
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
							  
									<input class="form-control" list="product_list" name="update_product_name" id="update_product_idx" required autocomplete="off" onchange="UpdateTotalAmount()">
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
						
						  <button type="submit" class="btn btn-success btn-sm bi bi-save-fill form_button_icon" id="update-billing-transaction"> Submit</button>
						  <button type="reset" class="btn btn-primary btn-sm bi bi-backspace-fill form_button_icon" id="clear-billing-update"> Reset</button>
						  
					</div>
					</form><!-- End Multi Columns Form -->
                  </div>
                </div>
             </div>
	<!-- Site Delete Modal-->
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

	<!-- Site Delete Modal-->
    <div class="modal fade" id="BillViewModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header header_modal_bg">
                    <h5 class="modal-title" id="exampleModalLabel"></h5>
 					<div class="btn-sm btn-warning btn-circle bi bi-exclamation-circle btn_icon_modal"></div>
                </div>
				
				
                <div class="modal-body warning_modal_bg" id="modal-body">
				Details<br>
				</div>
				<div align="left"style="margin: 10px;">
				Date: <span id="bill_view_order_date"></span><br>
				Time: <span id="bill_view_order_time"></span><br>
				
				PO #: <span id="bill_view_order_po_number"></span><br>
				Client: <span id="bill_view_client_name"></span><br>
				
				Plate #: <span id="bill_view_plate_no"></span><br>
				Driver: <span id="bill_view_drivers_name"></span><br>
				
				Product: <span id="bill_view_product_name"></span><br>
				Quantity: <span id="bill_view_order_quantity"></span><br>
				
				Total Amount: <span id="bill_view_order_total_amount"></span><br>
				
				
				</div>
                <div class="modal-footer footer_modal_bg">
                    
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
					
					  <form class="g-3 needs-validation" id="AddPayment" enctype="multipart/form-data" action="{{route('billing_receivable_payment_post')}}"  method="post" >
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
								<input type='date' class='form-control' id='receivable_date_of_payment' name='receivable_date_of_payment' value='<?=date('Y-m-d');?>'>
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
    </section>
</main>


@endsection

