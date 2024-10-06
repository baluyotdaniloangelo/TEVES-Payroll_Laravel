@extends('layouts.layout')  
@section('content')  

<main id="main" class="main">	
    <section class="section">	  
          <div class="card">
		  
			  <div class="card">
			  
				<div class="card-header ">
				  <h5 class="card-title">&nbsp;{{ $title }}</h5>
					<div class="d-flex justify-content-end" id="">
					
					
					<div class="btn-group" role="group" aria-label="Basic outlined example" style="margin-top: -50px; position: absolute;"><button type="button" class="btn btn-success new_item bi bi-plus-circle" data-bs-toggle="modal" data-bs-target="#CreateReportModal"></button>
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
						
						<div class="col-sm-6">
							<div class="ms-2">
								<div class="fw-bold">ACCOUNT NAME: <span id="client_name_report" style="font-weight: normal;"></span></div>
							</div>
							<div class="ms-2">
								<div class="fw-bold">TIN: <span id="client_tin_receivables" style="font-weight: normal;"></span></div>			
							</div>
							<div class="ms-2">
								<div class="fw-bold">ADDRESS: <span id="client_address_report" style="font-weight: normal;"></span></div>
							</div>
							
						</div>
						
						<div class="col-sm-6">
							
							<div class="ms-2">
								<div class="fw-bold">SALES ORDER PERIOD: <span id="po_info" style="font-weight: normal;"></span></div>			
							</div>
							<div class="ms-2">
								<div class="fw-bold">PAYMENT TERMS: Not Available. Please save as receivables</div>
							</div>
							<div class="ms-2">
								<div class="fw-bold">BILLING DATE: <span id="billing_date_info" style="font-weight: normal;"></span></div>
							</div>
	
						</div>
						</div>
						
									<div class="table-responsive">
										<!--<table class="table table-bordered dataTable" id="billingstatementreport" width="100%" cellspacing="0" style="font-size:12px !important">-->
										<table class="table dataTable display nowrap cell-border"  id="billingstatementreport" width="100%" cellspacing="0">
											<thead>
												<tr>
													<th>#</th>
													<th>Date</th>
													<th>Time</th>
													<th>Driver's Name</th>
													<th>S.O. No.</th>
													<th>Description</th>
													<th>Product</th>
													<th>Quantity</th>
													<th>Unit</th>
													<th>Price</th>
													<th>Amount</th>
													<th>Action</th>
												</tr>
											</thead>				
											
											<tbody>
												
											</tbody>
											<tfoot>
											
											<tr class="" >
												<td align="left" colspan="5"></td>
												<td align="right" colspan="2" nowrap style="text-align: right;">Total Volume (L) (Fuel):</td>
												<td align="left" nowrap><span id="total_volume" style="font-weight: normal;"></span></td>
												<td align="left" nowrap><span id="" style="font-weight: normal;">L</span></td>
												<td align="left" nowrap><b>Total Sales:</b></td>
												<td align="left" nowrap nowrap colspan="1"><span style="font-family: DejaVu Sans; sans-serif;">&#8369;</span>  <span id="total_due" style="font-weight: normal;"></span></td>
												<td align="left" ></td>
												
											</tr>
											
											<tr class="" >
												<td align="left" colspan="5"></td>
												<td align="right" colspan="2" nowrap style="text-align: right;">Discount Per liter (Fuel):</td>
												<td align="left" nowrap><span id="report_less_per_liter" style="font-weight: normal;"></span></td>
												<td align="left" nowrap><span id="" style="font-weight: normal;">L</span></td>
												<td align="left" colspan="1" nowrap>VATable Sales</td>
												<td align="left" nowrap nowrap colspan="1"><span style="font-family: DejaVu Sans; sans-serif;">&#8369;</span> <span id="vatable_sales" style="font-weight: normal;"></span></td>
												<td align="left" ></td>
											</tr>
											
											<tr class="" >
												<td align="left" colspan="7"></td>
												<td align="left" colspan="1"><b></b></td>
												<td align="left" ></td>
												<td align="left" nowrap colspan="1">VAT Amount</td>
												<td align="left" nowrap nowrap colspan="1"><span style="font-family: DejaVu Sans; sans-serif;">&#8369;</span> <span id="vat_amount" style="font-weight: normal;"></span></td>
												<td align="left" ></td>
											</tr>
											
											<tr class="" >
												<td align="left" colspan="7"></td>
												<td align="left" colspan="1"><b></b></td>
												<td align="left" ></td>
												<td align="left" nowrap colspan="1">Less: Discount </td>
												<td align="left" nowrap nowrap colspan="1"><span style="font-family: DejaVu Sans; sans-serif;">&#8369;</span> <span id="total_liters_discount" style="font-weight: normal;"></span></td>
												<td align="left" ></td>
											</tr>
											
											<tr class="">
												<td align="left" colspan="7"></td>
												<td align="left" colspan="1"><b></b></td>
												<td align="left" ></td>
												<td align="left" nowrap colspan="1">Less: With Holding Tax </td>
												<td align="left" nowrap colspan="1"><span style="font-family: DejaVu Sans; sans-serif;">&#8369;</span> <span id="with_holding_tax" style="font-weight: normal;"></span></td>
												<td align="left" ></td>
											</tr>
											
											<tr class="" >
												<td align="left" colspan="7"></td>
												<td align="left" colspan="1"></td>
												<td align="left" ></td>
												
												<td align="left" nowrap colspan="1"><b>TOTAL AMOUNT DUE:</b></td>
												<td align="left" nowrap colspan="1"><span style="font-family: DejaVu Sans; sans-serif;">&#8369;</span> <span id="total_payable" style="font-weight: normal;"></span></td>
												<td align="left" ></td>
											</tr>
											
											</tfoot>
										</table>
									</div>		
				</div>									
            </div>
          </div>

	<!--Modal to Create Client-->
	<div class="modal fade" id="CreateReceivablesModal" tabindex="-1">
              <div class="modal-dialog modal-xl">
                  <div class="modal-content">
                    <div class="modal-header modal-header_form">
                      <h5 class="modal-title">Create Receivable</h5>
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
							<div class="fw-bold">Account Name</div>
							<div id="client_name_receivables"></div>
						  </div>
						 
						</li>
						
						<li class="list-group-item d-flex justify-content-between align-items-start">
						  <div class="ms-2 me-auto">
							<div class="fw-bold">Address</div>
							<div id="client_address_receivables"></div>
						  </div>
						 
						</li>
						
						<li class="list-group-item d-flex justify-content-between align-items-start">
						  <div class="ms-2 me-auto">
							<div class="fw-bold">TIN</div>
							<div id="client_tin_receivables"></div>
						  </div>
						 
						</li>
						  
						</li>
						<li class="list-group-item d-flex justify-content-between align-items-start">
						  <div class="ms-2 me-auto">
							<div class="fw-bold">Billing Date</div>
							<?php echo date('m/d/y'); ?>
						  </div>
						 
						</li>
						
						</li>
						<li class="list-group-item d-flex justify-content-between align-items-start">
						  <div class="ms-2 me-auto">
							<div class="fw-bold">Control No.</div>
							***Auto Generated after Save
						  </div>
						 
						</li>
						
						</li>
						<li class="list-group-item d-flex justify-content-between align-items-start">
						  <div class="ms-2 me-auto">
							<div class="fw-bold">Amount</div>
							<div id="amount_receivables"></div>
						  </div>
						 
						</li>
						
					  </ol>					
					
					</div>
					<div class="col-lg-8">
									
					  <form class="g-2 needs-validation pt-4" id="ReceivableformNew">
				
						<div class="row mb-2">
						  <label for="payment_term" class="col-sm-3 col-form-label">Payment Term : </label>
						  <div class="col-sm-9">
							<input type="text" class="form-control " name="payment_term" id="payment_term" value="" required>
							<span class="valid-feedback" id="payment_termError"></span>
						  </div>
						</div>							
						
						<div class="row mb-2">
						  <label for="receivable_description" class="col-sm-3 col-form-label">Description : </label>
						  <div class="col-sm-9">
							<textarea class="form-control" id="receivable_description" style="height: 100px;" required></textarea>
							<span class="valid-feedback" id="receivable_descriptionError"></span>
						  </div>
						</div>		
						
						</div>
						
					</div>
					</div>
                    <div class="modal-footer modal-footer_form">
							<div id="loading_data_save_receivables" style="display:none;">
							<div class="spinner-border text-success" role="status">
								<span class="visually-hidden">Loading...</span>
							</div>
							</div>
						  <button type="submit" class="btn btn-success btn-sm bi bi-save-fill form_button_icon" id="save-receivables"> Submit</button>
						  <button type="reset" class="btn btn-primary btn-sm bi bi-backspace-fill form_button_icon"> Reset</button>
						  
					</div>
					</form><!-- End Multi Columns Form -->
                  </div>
                </div>
             </div>

	<!--Modal to Create Client-->
	<div class="modal fade" id="CreateReportModal" tabindex="-1">
              <div class="modal-dialog modal-xl">
                  <div class="modal-content">
                    <div class="modal-header modal-header_form">
                      <h5 class="modal-title">Create Receivable - Generate List</h5>
					  <div class="btn-group" role="group" aria-label="Basic outlined example">	
						<button type="button" class="btn btn-danger bi bi-x-circle form_button_icon" data-bs-dismiss="modal"></button>
					  </div>
                    </div>
                    <div class="modal-body">
					
					  <form class="g-2 needs-validation" id="generate_report_form">

					  
						<div class="row mb-2">
						  <label for="company_header" class="col-sm-4 col-form-label">Branch</label>
						  <div class="col-sm-8">
							<select class="form-select form-control" required="" name="company_header" id="company_header">
							@foreach ($teves_branch as $teves_branch_cols)
								<option value="{{$teves_branch_cols->branch_id}}">{{$teves_branch_cols->branch_code}}</option>
							@endforeach
							</select>
						  </div>
						</div>
					  
						<div class="row mb-2">
						  <label for="client_id" class="col-sm-4 col-form-label">Client</label>
						  <div class="col-sm-8">
							<input class="form-control" list="client_name" name="client_name" id="client_id" required autocomplete="off" onchange="get_client_details()">
								<datalist id="client_name">
									@foreach ($client_data as $client_data_cols)
										<option label="{{$client_data_cols->client_name}}" data-id="{{$client_data_cols->client_id}}" value="{{$client_data_cols->client_name}}">
									@endforeach
								</datalist>
							<span class="valid-feedback" id="client_idxError"></span>
						  </div>
						</div>
						
						<div class="row mb-2">
						  <label for="start_date" class="col-sm-4 col-form-label">Start Date</label>
						  <div class="col-sm-8">
							<input type="date" class="form-control " name="start_date" id="start_date" value="<?=date('Y-m-d');?>" required>
							<span class="valid-feedback" id="start_dateError"></span>
						  </div>
						</div>						
								
						<div class="row mb-2">
						  <label for="end_date" class="col-sm-4 col-form-label">End Date</label>
						  <div class="col-sm-8">
							<input type="date" class="form-control " name="end_date" id="end_date" value="<?=date('Y-m-d');?>" required>
							<span class="valid-feedback" id="end_dateError"></span>
						  </div>
						</div>
						
						<div class="row mb-2">
						  <label for="less_per_liter" class="col-sm-4 col-form-label" title="Applicable to All Product with Liter as unit of measurement">Discount Per Liter</label>
						  <div class="col-sm-8">
							<input type="text" class="form-control " name="less_per_liter" id="less_per_liter" value="" disabled>
							<span class="valid-feedback" id="less_per_literError"></span>
						  </div>
						</div>
						
						<div class="row mb-2">
						  <label for="net_value_percentage" class="col-sm-4 col-form-label" title="Net Value">Net Value</label>
						  <div class="col-sm-8">
							<input type="text" class="form-control " name="net_value_percentage" id="net_value_percentage" value="1.12" disabled>
							<span class="valid-feedback" id="net_value_percentage_taxError"></span>
						  </div>
						</div>
						
						<div class="row mb-2">
						  <label for="vat_value_percentage" class="col-sm-4 col-form-label" title="Net Value">VAT Value</label>
						  <div class="col-sm-8">
							<input type="text" class="form-control " name="vat_value_percentage" id="vat_value_percentage" value="12" disabled>
							<span class="valid-feedback" id="vat_value_percentageError"></span>
						  </div>
						</div>
						
						<div class="row mb-2">
						  <label for="withholding_tax_percentage" class="col-sm-4 col-form-label" title="Withholding Tax">Withholding Tax</label>
						  <div class="col-sm-8">
							<input type="text" class="form-control " name="withholding_tax_percentage" id="withholding_tax_percentage" value="1" disabled>
							<span class="valid-feedback" id="withholding_taxError"></span>
						  </div>
						</div>
						
						</div>
						
                    <div class="modal-footer modal-footer_form">
							<div id="loading_data" style="display:none;">
							<div class="spinner-border text-success" role="status">
								<span class="visually-hidden">Loading...</span>
							</div>
							</div>
						  <button type="submit" class="btn btn-success btn-sm bi bi-save-fill form_button_icon" id="generate_report"> Submit</button>
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

