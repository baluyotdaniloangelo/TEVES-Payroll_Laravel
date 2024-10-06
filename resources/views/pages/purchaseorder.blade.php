@extends('layouts.layout')  
@section('content')  

<main id="main" class="main">	
    <section class="section">	  
          <div class="card">
		  
			  <div class="card">
			  
				<div class="card-header ">
				  <h5 class="card-title">&nbsp;{{ $title }}</h5>
					<div class="d-flex justify-content-end" id="purchase_order_option"></div>				  
				  </div>
				</div>			  
		 
            <div class="card-body">			
				<div class="p-d3">
									<div class="table-responsive">
										<table class="table table-bordered dataTable display" id="getPurchaseOrderList" width="100%" cellspacing="0">
											<thead>
												<tr>
													<th class="all">#</th>
													<th class="all">Date</th>
													<th class="all">Control Number</th>
													<th class="all">Supplier</th>
													<th class="none">Total Payable</th>
													<th class="all">Status</th>
													<th class="all">Action</th>
												</tr>
											</thead>				
											
											<tbody>
												
											</tbody>
											
											<tfoot>
												<tr>
													<th class="all">#</th>
													<th class="all">Date</th>
													<th class="all">Control Number</th>
													<th class="all">Supplier</th>
													<th class="none">Total Payable</th>
													<th class="all">Status</th>
													<th class="all">Action</th>
												</tr>
											</tfoot>	
										</table>
									</div>		
				</div>									
                   
            </div>
          </div>


	<!--Modal to Create Purchase Order-->
	<div class="modal fade" id="CreatePurchaseOrderModal" tabindex="-1">
              <div class="modal-dialog modal-xl">
                  <div class="modal-content">
                    <div class="modal-header modal-header_form">
                      <h5 class="modal-title">Create Purchase Order</h5>
					  <div class="btn-group" role="group" aria-label="Basic outlined example">	
						<button type="button" class="btn btn-danger bi bi-x-circle form_button_icon" data-bs-dismiss="modal"></button>
					  </div>
                    </div>
                    <div class="modal-body">
					<form class="g-2 needs-validation" id="PurchaseOrderformNew">		
									<div class="row">
										<div class="col-md-6">
											<label for="company_header" class="form-label">Branch</label>
											<select class="form-select form-control" required="" name="company_header" id="company_header">
											@foreach ($teves_branch as $teves_branch_cols)
												<option value="{{$teves_branch_cols->branch_id}}">{{$teves_branch_cols->branch_code}}</option>
											@endforeach
											</select>
										</div>
									</div>
									
									<hr>
									
									<div class="row">

										<div class="col-md-6">
										  <label for="purchase_order_date" class="form-label">Date</label>
										  <input type="date" class="form-control" id="purchase_order_date" name="purchase_order_date" value="<?=date('Y-m-d');?>">
										</div>
										
										<div class="col-md-6">
										<label for="supplier_idx" class="form-label">Supplier's Name</label>
	
										 <input class="form-control" list="supplier_name" name="supplier_name" id="supplier_idx" required autocomplete="off" onChange="SupplierInfo()">
											<datalist id="supplier_name">
											  @foreach ($supplier_data as $supplier_data_cols)
											  <option label="{{$supplier_data_cols->supplier_name}}" data-id="{{$supplier_data_cols->supplier_id}}" value="{{$supplier_data_cols->supplier_name}}">
											  @endforeach
											</datalist>
																				
										<span class="valid-feedback" id="supplier_idxError"></span>
										</div>
										
									</div>
									
								<hr>
								
								<div class="row">
								
									<div class="col-md-3">
										<label for="purchase_order_sales_order_number" class="form-label">Sales Order #</label>
										<input type="text" class="form-control" id="purchase_order_sales_order_number" name="purchase_order_sales_order_number">
										<span class="valid-feedback" id="purchase_order_sales_order_numberError"></span>
									</div>
									
									<div class="col-md-3">
									  <label for="purchase_order_collection_receipt_no" class="form-label">Collection Receipt #</label>
									  <input type="text" class="form-control" id="purchase_order_collection_receipt_no" name="purchase_order_collection_receipt_no">
									  <span class="valid-feedback" id="purchase_order_collection_receipt_noError"></span>
									</div>
									
									<div class="col-md-3">
									  <label for="purchase_order_official_receipt_no" class="form-label">Official Receipt #</label>
									  <input type="text" class="form-control" id="purchase_order_official_receipt_no" name="purchase_order_official_receipt_no">
									  <span class="valid-feedback" id="purchase_order_official_receipt_nooError"></span>
									</div>
										
									<div class="col-md-3">
									  <label for="purchase_order_delivery_receipt_no" class="form-label">Delivery Receipt #</label>
									  <input type="text" class="form-control" id="purchase_order_delivery_receipt_no" name="purchase_order_delivery_receipt_no">
									  <span class="valid-feedback" id="purchase_order_delivery_receipt_noError"></span>
									</div>
									
								</div>
								
								<hr>
								
								
								<h6 class="modal-title">Payment Details</h6>
								<div align="right">
								<button type="button" class="btn btn-success new_item bi bi-plus-square" onclick="AddPaymentRow();" title="Add a Paymnet Option(1-3 items)"></button>
								</div>
								<br>
								
								<table class="table" id="table_payment">
								
									<thead>
										<tr class='report'>
										<th style="text-align:center !important;">Bank</th>
										<th style="text-align:center !important;">Date of Payment</th>
										<th style="text-align:center !important;">Reference No.</th>
										<th style="text-align:center !important;">Amount</th>
										<th style="text-align:center !important;">Action</th>
										
										</tr>
									</thead>
										
									<tbody id="table_payment_body_data">
										 <tr style="display: none;"><td>HIDDEN</td></tr>
									</tbody>
									
								</table>
								<div style="color:red;" id="table_paymentxError"></div>

								<hr>
							
								<div class="row">
								
								<h6 class="modal-title">Delivery Details</h6>
								
									<div class="col-md-6">
									  <label for="purchase_order_delivery_method" class="form-label">Delivery Method</label>
									  <input type="text" class="form-control" id="purchase_order_delivery_method" name="purchase_order_delivery_method">
									</div>
									
									<div class="col-md-6">
									  <label for="purchase_loading_terminal" class="form-label">Loading Terminal</label>
									  <input type="text" class="form-control" id="purchase_loading_terminal" name="purchase_loading_terminal" list="purchase_loading_terminal_list">
											<datalist id="purchase_loading_terminal_list">
												@foreach ($purchase_data_suggestion as $purchase_loading_terminal_cols)
													<option value="{{$purchase_loading_terminal_cols->purchase_loading_terminal}}">
												@endforeach
											  </datalist>
									</div>
									
								</div>
								
								<hr>
								
								<h6 class="modal-title">Product</h6>
								<div align="right">
								<button type="button" class="btn btn-success new_item bi bi-cart-plus-fill" onclick="AddProductRow();" title="Add a Product(1-5 items)"></button>
								</div>
								<br>
								
								<table class="table" id="table_product_data">
								
									<thead>
										<tr class='report'>
										<th style="text-align:center !important;" width='40%'>Product</th>
										<th style="text-align:center !important;">Quantity</th>
										<th style="text-align:center !important;">Price</th>
										<th style="text-align:center !important;">Action</th>
										
										</tr>
									</thead>
										
									<tbody id="table_product_body_data">
										 <tr style="display: none;"><td>HIDDEN</td></tr>
									</tbody>
									
								</table>
								<div style="color:red;" id="product_idxError"></div>
								
								<hr>
								
								<div class="row">
									<div class="col-md-6">
									  <label for="purchase_order_net_percentage" class="form-label">Net Value</label>
									  <input type="number" class="form-control" id="purchase_order_net_percentage" name="purchase_order_net_percentage" step=".01" value="1.12">
									</div>
									
									<div class="col-md-6">
									  <label for="purchase_order_less_percentage" class="form-label">Less Value</label>
									  <input type="number" class="form-control" id="purchase_order_less_percentage" name="purchase_order_less_percentage" step=".01" value="1">
									</div>
								</div>
								<hr>								
								<div class="row">
								
								<h6 class="modal-title">Hauler Details</h6>
								
									<div class="col-md-3">
									  <label for="hauler_operator" class="form-label">Hauler / Operator</label>
									  <input type="text" class="form-control" id="hauler_operator" name="hauler_operator" list="hauler_operator_list">
											<datalist id="hauler_operator_list">
												@foreach ($purchase_data_suggestion as $hauler_operator_cols)
													<option value="{{$hauler_operator_cols->hauler_operator}}">
												@endforeach
											  </datalist>
									</div>
									
									<div class="col-md-3">
									  <label for="lorry_driver" class="form-label">Lorry Driver</label>
									  <input type="text" class="form-control" id="lorry_driver" name="lorry_driver" list="lorry_driver_list">
											<datalist id="lorry_driver_list">
												@foreach ($purchase_data_suggestion as $lorry_driver_cols)
													<option value="{{$lorry_driver_cols->lorry_driver}}">
												@endforeach
											  </datalist>
									</div>
									
									<div class="col-md-3">
									  <label for="plate_number" class="form-label">Plate Number</label>
									  <input type="text" class="form-control" id="plate_number" name="plate_number" list="plate_number_list">
											<datalist id="plate_number_list">
												@foreach ($purchase_data_suggestion as $plate_number_cols)
													<option value="{{$plate_number_cols->plate_number}}">
												@endforeach
											  </datalist>
									</div>
									
									<div class="col-md-3">
									  <label for="contact_number" class="form-label">Contact Number</label>
									  <input type="text" class="form-control" id="contact_number" name="contact_number" list="contact_number_list">
											<datalist id="contact_number_list">
												@foreach ($purchase_data_suggestion as $contact_number_cols)
													<option value="{{$contact_number_cols->contact_number}}">
												@endforeach
											  </datalist>
									</div>
								
								</div>
								
								<div class="row">
								
									<div class="col-md-3">
									  <label for="purchase_destination" class="form-label">Destination</label>
									  <input type="text" class="form-control" id="purchase_destination" name="purchase_destination" list="purchase_destination_list">
											<datalist id="purchase_destination_list">
												@foreach ($purchase_data_suggestion as $purchase_destination_cols)
													<option value="{{$purchase_destination_cols->purchase_destination}}">
												@endforeach
											  </datalist>
									</div>
									<div class="col-md-3">
									  <label for="purchase_destination_address" class="form-label">Address</label>
									  <input type="text" class="form-control" id="purchase_destination_address" name="purchase_destination_address" list="purchase_destination_address_list">
											<datalist id="purchase_destination_address_list">
												@foreach ($purchase_data_suggestion as $purchase_destination_address_cols)
													<option value="{{$purchase_destination_address_cols->purchase_destination_address}}">
												@endforeach
											  </datalist>
									</div>
									
									<div class="col-md-3">
									  <label for="purchase_date_of_departure" class="form-label">Date of Departure</label>
									  <input type="date" class="form-control" id="purchase_date_of_departure" name="purchase_date_of_departure">
									</div>
									
									<div class="col-md-3">
									  <label for="purchase_date_of_arrival" class="form-label">Date of Arrival</label>
									  <input type="date" class="form-control" id="purchase_date_of_arrival" name="purchase_date_of_arrival">
									</div>
								
								</div>
								
								<div class="row">
								
									<div class="col-md-6">
									  <label for="purchase_order_instructions" class="form-label">Instructions</label>
									  <textarea class="form-control" id="purchase_order_instructions" name="purchase_order_instructions" style="height: 38px;"></textarea>
									</div>
									
									<div class="col-md-6">
									  <label for="purchase_order_note" class="form-label">Notes</label>
									  <textarea class="form-control" id="purchase_order_note" name="purchase_order_note" style="height: 38px;"></textarea>
									</div>
					
								</div>
								<hr>
								
					</div>
					<div class="modal-footer modal-footer_form">
							<div id="loading_data" style="display:none;">
							<div class="spinner-border text-success" role="status">
								<span class="visually-hidden">Loading...</span>
							</div>
							</div>					
						<button type="submit" class="btn btn-success btn-sm bi bi-save-fill form_button_icon" id="save-purchase-order"> Submit</button>
						<button type="reset" class="btn btn-primary btn-sm bi bi-backspace-fill form_button_icon"> Reset</button>
					</div>
					</form>		
                  </div>
              </div>
    </div>

	<!--Modal to Update Purchase Order-->
	<div class="modal fade" id="UpdatePurchaseOrderModal" tabindex="-1">
              <div class="modal-dialog modal-xl">
                  <div class="modal-content">
                    <div class="modal-header modal-header_form">
                      <h5 class="modal-title">Update Purchase Order</h5>
					  <div class="btn-group" role="group" aria-label="Basic outlined example">	
						<button type="button" class="btn btn-danger bi bi-x-circle form_button_icon" data-bs-dismiss="modal"></button>
					  </div>
                    </div>
                    <div class="modal-body">
					<form class="g-2 needs-validation" id="PurchaseOrderformUpdate">		
									<div class="row">
									
										<div class="col-md-6">
											
											<label for="update_company_header" class="form-label">Branch</label>
											<select class="form-select form-control" required="" name="update_company_header" id="update_company_header">
											@foreach ($teves_branch as $teves_branch_cols)
												<option value="{{$teves_branch_cols->branch_id}}">{{$teves_branch_cols->branch_code}}</option>
											@endforeach
											</select>
											
										</div>
										
									</div>
									
									<hr>
									<div class="row">

										<div class="col-md-6">
										  <label for="purchase_order_date" class="form-label">Date</label>
										  <input type="date" class="form-control" id="update_purchase_order_date" name="update_purchase_order_date" value="<?=date('Y-m-d');?>">
										</div>
									
										<div class="col-md-6">
										<label for="supplier_idx" class="form-label">Supplier's Name</label>
	
										 <input class="form-control" list="update_supplier_name" name="update_supplier_name" id="update_supplier_idx" required autocomplete="off">
											<datalist id="update_supplier_name">
											  @foreach ($supplier_data as $supplier_data_cols)
											  <option label="{{$supplier_data_cols->supplier_name}}" data-id="{{$supplier_data_cols->supplier_id}}" value="{{$supplier_data_cols->supplier_name}}">
											  @endforeach
											</datalist>
																				
										<span class="valid-feedback" id="update_supplier_idxError"></span>
										</div>
										
									</div>
									
								<hr>
								
								<div class="row">
								
									<div class="col-md-3">
										<label for="purchase_order_sales_order_number" class="form-label">Sales Order #</label>
										<input type="text" class="form-control" id="update_purchase_order_sales_order_number" name="update_purchase_order_sales_order_number">
										<span class="valid-feedback" id="update_purchase_order_sales_order_numberError"></span>
									</div>
									
									<div class="col-md-3">
									  <label for="purchase_order_collection_receipt_no" class="form-label">Collection Receipt #</label>
									  <input type="text" class="form-control" id="update_purchase_order_collection_receipt_no" name="update_purchase_order_collection_receipt_no">
									  <span class="valid-feedback" id="update_purchase_order_collection_receipt_noError"></span>
									</div>
									
									<div class="col-md-3">
									  <label for="purchase_order_official_receipt_no" class="form-label">Official Receipt #</label>
									  <input type="text" class="form-control" id="update_purchase_order_official_receipt_no" name="update_purchase_order_official_receipt_no">
									  <span class="valid-feedback" id="update_purchase_order_official_receipt_nooError"></span>
									</div>
										
									<div class="col-md-3">
									  <label for="purchase_order_delivery_receipt_no" class="form-label">Delivery Receipt #</label>
									  <input type="text" class="form-control" id="update_purchase_order_delivery_receipt_no" name="update_purchase_order_delivery_receipt_no">
									  <span class="valid-feedback" id="update_purchase_order_delivery_receipt_noError"></span>
									</div>
									
								</div>
								
								<hr>
								
								
								<h6 class="modal-title">Payment Details</h6>
								<div align="right">
								<button type="button" class="btn btn-success new_item bi bi-plus-square" onclick="UpdatePaymentRow();" title="Add a Product(1-5 items)"></button>
								</div>
								<br>
								
								<table class="table" id="update_table_payment">
								
									<thead>
										<tr class='report'>
										<th style="text-align:center !important;">Bank</th>
										<th style="text-align:center !important;">Date of Payment</th>
										<th style="text-align:center !important;">Reference No.</th>
										<th style="text-align:center !important;">Amount</th>
										<th style="text-align:center !important;">Action</th>
										
										</tr>
									</thead>
										
									<tbody id="update_table_payment_body_data">
										 <tr style="display: none;"><td>HIDDEN</td></tr>
									</tbody>
									
								</table>
								<div style="color:red;" id="table_paymentxError"></div>

								<hr>
								
								<div class="row">
								<h6 class="modal-title">Delivery Details</h6>
									<div class="col-md-6">
									  <label for="purchase_order_delivery_method" class="form-label">Delivery Method</label>
									  <input type="text" class="form-control" id="update_purchase_order_delivery_method" name="update_purchase_order_delivery_method">
									</div>
									
									<div class="col-md-6">
									  <label for="purchase_loading_terminal" class="form-label">Loading Terminal</label>
									  <input type="text" class="form-control" id="update_purchase_loading_terminal" name="update_purchase_loading_terminal" list="purchase_loading_terminal_list">
											<datalist id="purchase_loading_terminal_list">
												@foreach ($purchase_data_suggestion as $purchase_loading_terminal_cols)
													<option value="{{$purchase_loading_terminal_cols->purchase_loading_terminal}}">
												@endforeach
											</datalist>
									</div>
									
								</div>
								
								<hr>
								
								<h6 class="modal-title">Product</h6>
								<div align="right">
								<button type="button" class="btn btn-success new_item bi bi-cart-plus-fill" onclick="UpdateProductRow();" title="Add a Product(1-5 items)"></button>
								</div>
								<br>
								
								<table class="table" id="table_product_data">
								
									<thead>
										<tr class='report'>
										<th style="text-align:center !important;" width='40%'>Product</th>
										<th style="text-align:center !important;">Quantity</th>
										<th style="text-align:center !important;">Price</th>
										<th style="text-align:center !important;">Action</th>
										</tr>
									</thead>
										
									<tbody id="update_table_product_body_data">
										 <tr style="display: none;"><td>HIDDEN</td></tr>
									</tbody>
									
								</table>
								<div style="color:red;" id="product_idxError"></div>
								<hr>
								<div class="row">
									<div class="col-md-6">
									  <label for="purchase_order_net_percentage" class="form-label">Net Value</label>
									  <input type="number" class="form-control" id="update_purchase_order_net_percentage" name="update_purchase_order_net_percentage" step=".01">
									</div>
									
									<div class="col-md-6">
									  <label for="purchase_order_less_percentage" class="form-label">Less Value</label>
									  <input type="number" class="form-control" id="update_purchase_order_less_percentage" name="update_purchase_order_less_percentage" step=".01">
									</div>
								</div>
								<hr>								
								<div class="row">
								
								<h6 class="modal-title">Hauler Details</h6>
								
									<div class="col-md-3">
									  <label for="hauler_operator" class="form-label">Driver</label>
									  <input type="text" class="form-control" id="update_hauler_operator" name="update_hauler_operator">
									</div>
									<div class="col-md-3">
									  <label for="lorry_driver" class="form-label">Lory Plate #</label>
									  <input type="text" class="form-control" id="update_lorry_driver" name="update_lorry_driver" list="lorry_driver_list">
											<datalist id="lorry_driver_list">
												@foreach ($purchase_data_suggestion as $lorry_driver_cols)
													<option value="{{$lorry_driver_cols->lorry_driver}}">
												@endforeach
											  </datalist>
									</div>
									
									<div class="col-md-3">
									  <label for="plate_number" class="form-label">Plate Number</label>
									  <input type="text" class="form-control" id="update_plate_number" name="update_plate_number" list="plate_number_list">
											<datalist id="plate_number_list">
												@foreach ($purchase_data_suggestion as $plate_number_cols)
													<option value="{{$plate_number_cols->plate_number}}">
												@endforeach
											  </datalist>
									</div>
									
									<div class="col-md-3">
									  <label for="contact_number" class="form-label">Contact Number</label>
									  <input type="text" class="form-control" id="update_contact_number" name="update_contact_number" list="contact_number_list">
											<datalist id="contact_number_list">
												@foreach ($purchase_data_suggestion as $contact_number_cols)
													<option value="{{$contact_number_cols->contact_number}}">
												@endforeach
											  </datalist>
									</div>
								
								</div>
								
								<div class="row">
								
									<div class="col-md-3">
									  <label for="purchase_destination" class="form-label">Destination</label>
									  <input type="text" class="form-control" id="update_purchase_destination" name="update_purchase_destination" list="purchase_destination_list">
											<datalist id="purchase_destination_list">
												@foreach ($purchase_data_suggestion as $purchase_destination_cols)
													<option value="{{$purchase_destination_cols->purchase_destination}}">
												@endforeach
											  </datalist>
									</div>
									<div class="col-md-3">
									  <label for="purchase_destination_address" class="form-label">Address</label>
									  <input type="text" class="form-control" id="update_purchase_destination_address" name="update_purchase_destination_address" value=""list="purchase_destination_address_list">
											<datalist id="purchase_destination_address_list">
												@foreach ($purchase_data_suggestion as $purchase_destination_address_cols)
													<option value="{{$purchase_destination_address_cols->purchase_destination_address}}">
												@endforeach
											  </datalist>
									</div>
									
									<div class="col-md-3">
									  <label for="purchase_date_of_departure" class="form-label">Date of Departure</label>
									  <input type="date" class="form-control" id="update_purchase_date_of_departure" name="update_purchase_date_of_departure">
									</div>
									
									<div class="col-md-3">
									  <label for="purchase_date_of_arrival" class="form-label">Date of Arrival</label>
									  <input type="date" class="form-control" id="update_purchase_date_of_arrival" name="update_purchase_date_of_arrival">
									</div>
								
								</div>
								
								<div class="row">
								
									<div class="col-md-6">
									  <label for="purchase_order_instructions" class="form-label">Instructions</label>
									  <textarea class="form-control" id="update_purchase_order_instructions" name="update_purchase_order_instructions" style="height: 38px;"></textarea>
									</div>
									
									<div class="col-md-6">
									  <label for="purchase_order_note" class="form-label">Notes</label>
									  <textarea class="form-control" id="update_purchase_order_note" name="update_purchase_order_note" style="height: 38px;"></textarea>
									</div>
					
								</div>
								<hr>
								
					</div>
					<div class="modal-footer modal-footer_form">		
							<div id="update_loading_data" style="display:none;">
							<div class="spinner-border text-success" role="status">
								<span class="visually-hidden">Loading...</span>
							</div>
							</div>
						<button type="submit" class="btn btn-success btn-sm bi bi-save-fill form_button_icon" id="update-purchase-order" value=""> Submit</button>
						<button type="reset" class="btn btn-primary btn-sm bi bi-backspace-fill form_button_icon"> Reset</button>
					</div>
					</form>		
                  </div>
              </div>
    </div>

	<!-- Sales Order Delete Modal-->
    <div class="modal fade" id="SalesOrderDeleteModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header header_modal_bg">
                    <h5 class="modal-title" id="exampleModalLabel"></h5>
 					<div class="btn-sm btn-warning btn-circle bi bi-exclamation-circle btn_icon_modal"></div>
                </div>

                <div class="modal-body warning_modal_bg" id="modal-body">
				Are you sure you want to Delete This Purchase Order?<br>
				</div>
				
				<div align="left"style="margin: 10px;">
				Date: <span id="confirm_delete_purchase_order_date"></span><br>
				Control Number: <span id="confirm_delete_purchase_control_number"></span><br>
				Supplier's Name: <span id="confirm_delete_suppliers_name"></span><br>
				Amount: <span id="confirm_delete_amount"></span><br>
				</div>
                <div class="modal-footer footer_modal_bg">
                    
					<button type="button" class="btn btn-danger" data-bs-dismiss="modal" id="deletePurchaseOrderConfirmed" value=""><i class="bi bi-trash3 form_button_icon"></i> Delete</button>
					<button type="button" class="btn btn-primary" data-bs-dismiss="modal"><i class="bi bi-x-circle form_button_icon"></i> Cancel</button>
                  
                </div>
            </div>
        </div>
    </div>	

  </section>
</main>
@endsection