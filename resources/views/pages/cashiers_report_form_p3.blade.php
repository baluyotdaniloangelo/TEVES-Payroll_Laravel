	<!--Part 3-->	
	<div align="right">
		<button type="button" class="btn btn-success new_item bi bi-plus-circle" data-bs-toggle="modal" data-bs-target="#CRPH3_Modal"></button>
	</div>

              <!-- Pills Tabs -->
              <ul class="nav nav-tabs mb-3" id="pills-tab" role="tablist">
                <li class="nav-item" role="presentation">
                  <button class="nav-link active" id="pills-sales_order-tab" data-bs-toggle="pill" data-bs-target="#pills-sales_order" type="button" role="tab" aria-controls="pills-sales_order" aria-selected="true">SALES ORDER - CREDIT SALES</button>
                </li>
                <li class="nav-item" role="presentation">
                  <button class="nav-link" id="pills-discount-tab" data-bs-toggle="pill" data-bs-target="#pills-discount" type="button" role="tab" aria-controls="pills-discount" aria-selected="false" tabindex="-1">DISCOUNTS ( WHOLE SALE - FUEL)</button>
                </li>
                <li class="nav-item" role="presentation">
                  <button class="nav-link" id="pills-others-tab" data-bs-toggle="pill" data-bs-target="#pills-others" type="button" role="tab" aria-controls="pills-others" aria-selected="false" tabindex="-1">OTHERS (Lubricants discounts / Money Cash Out / Misload)</button>
                </li>
              </ul>
              <div class="tab-content pt-2" id="myTabContent">
                <div class="tab-pane fade active show" id="pills-sales_order" role="tabpanel" aria-labelledby="sales_order-tab">
				
					<table class="table" id="">
						<thead>
							<tr class='report'>
								<th style="text-align:center !important;">#</th>
								<th style="text-align:center !important;">Product</th>
								<th style="text-align:center !important;">Liters</th>
								<th style="text-align:center !important;">Pump Price</th>
								<th style="text-align:center !important;">Amount</th>
								<th style="text-align:center !important;">Action</th>
							</tr>
						</thead>		
						<tbody id="table_product_data_msc_SALES_CREDIT">
							<tr style="display: none;">
								<td>HIDDEN</td></tr>
							</tbody>	
					</table>
					
                </div>
				
                <div class="tab-pane fade" id="pills-discount" role="tabpanel" aria-labelledby="discount-tab">
					
					<table class="table" id="">
						<thead>
							<tr class='report'>
								<th style="text-align:center !important;">#</th>
								<th style="text-align:center !important;">Reference No.</th>
								<th style="text-align:center !important;">Product</th>
								<th style="text-align:center !important;">Liters</th>
								<th style="text-align:center !important;">Pump Price</th>
								<th style="text-align:center !important;">Unit Price</th>
								<th style="text-align:center !important;">Discounted Price</th>
								<th style="text-align:center !important;">Amount</th>
								<th style="text-align:center !important;">Action</th>
							</tr>
						</thead>		
						<tbody id="table_product_data_msc_DISCOUNT">
							<tr style="display: none;">
								<td>HIDDEN</td></tr>
							</tbody>	
					</table>                 

				</div>
				
                <div class="tab-pane fade" id="pills-others" role="tabpanel" aria-labelledby="others-tab">
                      <table class="table" id="">
							<thead>
								<tr class='report'>
									<th style="text-align:center !important;">#</th>
									<th style="text-align:center !important;">Reference No.</th>
									<th style="text-align:center !important;">Description</th>
									<th style="text-align:center !important;">Liters / Pieces</th>
									<th style="text-align:center !important;">Amount</th>
									<th style="text-align:center !important;">Action</th>
								</tr>
							</thead>		
							<tbody id="table_product_data_msc_OTHERS">
								<tr style="display: none;">
									<td>HIDDEN</td></tr>
								</tbody>	
						</table>
				</div>
              </div><!-- End Pills Tabs -->

	<!--Modal to Create Other Sales-->
	<div class="modal fade" id="CRPH3_Modal" tabindex="-1">
              <div class="modal-dialog modal-lg">
                  <div class="modal-content">
                    <div class="modal-header modal-header_form">
                      <h5 class="modal-title">Create Miscellaneous Sales</h5>
					  <div class="btn-group" role="group" aria-label="Basic outlined example">		
						
						<button type="button" class="btn btn-danger bi bi-x-circle form_button_icon" data-bs-dismiss="modal"></button>
					  </div>
                    </div>
                    <div class="modal-body">
					
					  <form class="g-3 needs-validation" id="CRPH3_form">

                      <div class="row mb-2">
						  <label for="miscellaneous_items_type_PH3" class="col-sm-4 col-form-label">MISCELLANEOUS TYPE</label>
						  <div class="col-sm-8">
									<select class="form-select form-control" required="" name="miscellaneous_items_type_PH3" id="miscellaneous_items_type_PH3" onchange="input_settings_create_PH3()">
                                        <option value="">Please Select</option>
                                        <option value="SALES_CREDIT">SALES ORDER - CREDIT SALES</option>
								        <option value="DISCOUNTS">DISCOUNTS</option>
                                        <option value="OTHERS">OTHERS</option>
										 <option value="CASHOUT">CASH OUT</option>
							        </select>
									<span class="valid-feedback" id="miscellaneous_items_type_PH3Error"></span>
									</div>	
					  </div>

                      <div class="row mb-2">
						  <label for="reference_no_PH3" class="col-sm-4 col-form-label">REFERENCE NO.</label>
						  <div class="col-sm-8">
									<input class="form-control" list="reference_no_PH3" name="reference_no_PH3" id="reference_no_PH3" autocomplete="off">
									<span class="valid-feedback" id="reference_no_PH3Error"></span>
									</div>	
					  </div>

					  <div class="row mb-2">
						  <label for="product_idx_PH3" class="col-sm-4 col-form-label">PRODUCT / DESCRIPTION</label>
						  <div class="col-sm-8">
									<input class="form-control" list="product_list_PH3" name="product_name_PH3" id="product_idx_PH3" required autocomplete="off" onchange="TotalAmount_PH3()">
									<span class="valid-feedback" id="product_idx_PH3Error"></span>
									</div>	
						</div>	
									
						<div class="row mb-2">
						  <label for="order_quantity_PH3" class="col-sm-4 col-form-label" id="quantity_label">QUANTITY</label>
						  <div class="col-sm-8">
							  <input type="number" class="form-control" placeholder=""  name="order_quantity_PH3" id="order_quantity_PH3" required step=".01" onchange="TotalAmount_PH3()" >
							  <span class="valid-feedback" id="order_quantity_PH3Error"></span>						  
						  </div>
						</div>	
						
						<div class="row mb-2">
						  <label for="product_manual_price_PH3" class="col-sm-4 col-form-label" id="manual_price_label">UNIT PRICE</label>
						  <div class="col-sm-8">
							  <input type="number" class="form-control" placeholder="" name="product_manual_price_PH3" id="product_manual_price_PH3" step=".01" onchange="TotalAmount_PH3()" >
							  <span class="valid-feedback" id="product_manual_price_PH3Error"></span>						  
						  </div>
						</div>			
						
						<div class="row mb-2">
						  <label for="product_idx_PH3" class="col-sm-4 col-form-label">PUMP PRICE</label>
						  <div class="col-sm-8">
									<span style="font-family: DejaVu Sans; sans-serif;">&#8369;</span> <span id="pump_price_txt"></span>
						  </div>	
						</div>
						
						<div class="row mb-2">
						  <label for="product_idx_PH3" class="col-sm-4 col-form-label">DISCOUNTED PRICE</label>
						  <div class="col-sm-8">
									<span style="font-family: DejaVu Sans; sans-serif;">&#8369;</span> <span id="discounted_price_txt"></span>
						  </div>	
						</div>
			
						<div class="row mb-2">
						  <label class="col-sm-4 col-form-label">TOTAL AMOUNT</label>
						  <div class="col-sm-8">
								<span style="font-family: DejaVu Sans; sans-serif;">&#8369;</span> <span id="TotalAmount_PH3">0.00</span>
						  </div>
						</div>
						
						<div class="modal-footer modal-footer_form">
						  <button type="submit" class="btn btn-success btn-sm bi bi-save-fill form_button_icon" id="save-CRPH3"> Submit</button>
						  <button type="reset" class="btn btn-primary btn-sm bi bi-backspace-fill form_button_icon" id="clear-CRPH3-save"> Reset</button>					  
						</div>
					</form><!-- End Multi Columns Form -->
                  </div>
                </div>
             </div>				
	</div>
	
	<!--Modal to Create Other Sales-->
	<div class="modal fade" id="Update_CRPH3_Modal" tabindex="-1">
              <div class="modal-dialog modal-lg">
                  <div class="modal-content">
                    <div class="modal-header modal-header_form">
                      <h5 class="modal-title">Update Miscellaneous Sales</h5>
					  <div class="btn-group" role="group" aria-label="Basic outlined example">		
						
						<button type="button" class="btn btn-danger bi bi-x-circle form_button_icon" data-bs-dismiss="modal"></button>
					  </div>
                    </div>
                    <div class="modal-body">
					
					  <form class="g-3 needs-validation" id="CRPH3_form_edit">
					  
                      <div class="row mb-2">
						  <label for="update_miscellaneous_items_type_PH3" class="col-sm-4 col-form-label">MISCELLANEOUS TYPE</label>
						  <div class="col-sm-8">
									<select class="form-select form-control" required="" name="update_miscellaneous_items_type_PH3" id="update_miscellaneous_items_type_PH3" onchange="update_input_settings_create_PH3()">
                                        <option value="">Please Select</option>
                                        <option value="SALES_CREDIT">SALES ORDER - CREDIT SALES</option>
								        <option value="DISCOUNTS">DISCOUNTS</option>
                                        <option value="OTHERS">OTHERS</option>
										 <option value="CASHOUT">CASH OUT</option>
							        </select>
									<span class="valid-feedback" id="update_miscellaneous_items_type_PH3Error"></span>
									</div>	
					  </div>

                      <div class="row mb-2">
						  <label for="reference_no_PH3" class="col-sm-4 col-form-label">REFERENCE NO.</label>
						  <div class="col-sm-8">
									<input class="form-control" list="update_reference_no_PH3" name="update_reference_no_PH3" id="update_reference_no_PH3" autocomplete="off">
									<span class="valid-feedback" id="update_reference_no_PH3Error"></span>
									</div>	
					  </div>					  
					  
					  <div class="row mb-2">
						  <label for="update_product_idx_PH3" class="col-sm-4 col-form-label">PRODUCT / DESCRIPTION</label>
						  <div class="col-sm-8">
									<input class="form-control" list="product_list_PH3" name="update_product_name_PH3" id="update_product_idx_PH3" required autocomplete="off" onchange="UpdateTotalAmount_PH3()">
									<span class="valid-feedback" id="update_product_idx_PH3Error"></span>
									</div>	
						</div>	
						
						<div class="row mb-2">
						  <label for="update_order_quantity_PH3" class="col-sm-4 col-form-label" id="update_quantity_label">QUANTITY</label>
						  <div class="col-sm-8">
							  <input type="number" class="form-control" placeholder=""  name="update_order_quantity_PH3" id="update_order_quantity_PH3" required step=".01" onchange="UpdateTotalAmount_PH3()" >
							  <span class="valid-feedback" id="update_order_quantity_PH3Error"></span>						  
						  </div>
						</div>	
						
						<div class="row mb-2">
						  <label for="update_product_manual_price_PH3" class="col-sm-4 col-form-label" id="update_manual_price_label">UNIT PRICE</label>
						  <div class="col-sm-8">
							  <input type="number" class="form-control" placeholder="" name="update_product_manual_price_PH3" id="update_product_manual_price_PH3" step=".01" onchange="UpdateTotalAmount_PH3()" >
							  <span class="valid-feedback" id="update_product_manual_price_PH3Error"></span>						  
						  </div>
						</div>						
						
						<div class="row mb-2">
						  <label for="product_idx_PH3" class="col-sm-4 col-form-label">PUMP PRICE</label>
						  <div class="col-sm-8">
									<span style="font-family: DejaVu Sans; sans-serif;">&#8369;</span> <span id="pump_price_txt_update"></span>
						  </div>	
						</div>
						
						<div class="row mb-2">
						  <label for="product_idx_PH3" class="col-sm-4 col-form-label">DISCOUNTED PRICE</label>
						  <div class="col-sm-8">
									<span style="font-family: DejaVu Sans; sans-serif;">&#8369;</span> <span id="discounted_price_txt_update"></span>
						  </div>	
						</div>
						
						<div class="row mb-2">
						  <label class="col-sm-4 col-form-label">TOTAL AMOUNT</label>
						  <div class="col-sm-8">
								<span style="font-family: DejaVu Sans; sans-serif;">&#8369;</span> <span id="UpdateTotalAmount_PH3">0.00</span>
						  </div>
						</div>
						
						<div class="modal-footer modal-footer_form">
						  <button type="submit" class="btn btn-success btn-sm bi bi-save-fill form_button_icon" id="update-CRPH3"> Submit</button>
						  <button type="reset" class="btn btn-primary btn-sm bi bi-backspace-fill form_button_icon" id="clear-CRPH3-update"> Reset</button>					  
						</div>
					</form><!-- End Multi Columns Form -->
                  </div>
                </div>
             </div>		
	</div>
	
	<!--Product List-->
	<datalist id="product_list_PH3">
		@foreach ($product_data as $product_data_cols)
			<span style="font-family: DejaVu Sans; sans-serif;">
			<option label="&#8369; {{$product_data_cols->product_price}} | {{$product_data_cols->product_name}}" data-id="{{$product_data_cols->product_id}}" data-price="{{$product_data_cols->product_price}}" value="{{$product_data_cols->product_name}}">
			</span>
		@endforeach
	</datalist>
	
	<!-- CRP2 Delete Modal-->
    <div class="modal fade" id="CRPH3DeleteModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header header_modal_bg">
                    <h5 class="modal-title" id="exampleModalLabel"></h5>
 					<div class="btn-sm btn-warning btn-circle bi bi-exclamation-circle btn_icon_modal"></div>
                </div>

                <div class="modal-body warning_modal_bg" id="modal-body">
				Are you sure you want to Delete This Item?<br>
				</div>
				<div align="left"style="margin: 10px;">
				PRODUCT: <span id="delete_product_idx_PH3"></span><br>
				QUANTITY: <span id="delete_order_quantity_PH3"></span><br>
				PRICE: <span id="delete_product_manual_price_PH3"></span><br>
				PESO SALES: <span id="delete_TotalAmount_PH3"></span><br>
				</div>	
                <div class="modal-footer footer_modal_bg">
                    
					<button type="button" class="btn btn-danger" data-bs-dismiss="modal" id="deleteCRPH3Confirmed" value=""><i class="bi bi-trash3 form_button_icon"></i> Delete</button>
					<button type="button" class="btn btn-primary" data-bs-dismiss="modal"><i class="bi bi-x-circle form_button_icon"></i> Cancel</button>
                  
                </div>
            </div>
        </div>
    </div>
	<!-- CRP2 Delete Modal-->
    <div class="modal fade" id="CRPH3DeleteModal_DISCOUNT" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header header_modal_bg">
                    <h5 class="modal-title" id="exampleModalLabel"></h5>
 					<div class="btn-sm btn-warning btn-circle bi bi-exclamation-circle btn_icon_modal"></div>
                </div>

                <div class="modal-body warning_modal_bg" id="modal-body">
				Are you sure you want to Delete This Item?<br>
				</div>
				<div align="left"style="margin: 10px;">
				REFERENCE NO.: <span id="delete_reference_PH3_DISCOUNT"></span><br>
				PRODUCT: <span id="delete_product_idx_PH3_DISCOUNT"></span><br>
				QUANTITY: <span id="delete_order_quantity_PH3_DISCOUNT"></span><br>
				PRICE: <span id="delete_product_manual_price_PH3_DISCOUNT"></span><br>
				PESO SALES: <span id="delete_TotalAmount_PH3_DISCOUNT"></span><br>
				</div>	
                <div class="modal-footer footer_modal_bg">
                    
					<button type="button" class="btn btn-danger" data-bs-dismiss="modal" id="deleteCRPH3Confirmed_DISCOUNT" value=""><i class="bi bi-trash3 form_button_icon"></i> Delete</button>
					<button type="button" class="btn btn-primary" data-bs-dismiss="modal"><i class="bi bi-x-circle form_button_icon"></i> Cancel</button>
                  
                </div>
            </div>
        </div>
    </div>	
	<!-- CRP2 Delete Modal-->
    <div class="modal fade" id="CRPH3DeleteModal_OTHERS" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header header_modal_bg">
                    <h5 class="modal-title" id="exampleModalLabel"></h5>
 					<div class="btn-sm btn-warning btn-circle bi bi-exclamation-circle btn_icon_modal"></div>
                </div>

                <div class="modal-body warning_modal_bg" id="modal-body">
				Are you sure you want to Delete This Item?<br>
				</div>
				<div align="left"style="margin: 10px;">
				Reference No.: <span id="delete_reference_PH3_others"></span><br>
				Liters / Pieces: <span id="delete_liters_pcs_PH3_others"></span><br>
				Amount	: <span id="delete_amount_PH3_others"></span><br>
				</div>	
                <div class="modal-footer footer_modal_bg">
                    
					<button type="button" class="btn btn-danger" data-bs-dismiss="modal" id="deleteCRPH3Confirmed_OTHERS" value=""><i class="bi bi-trash3 form_button_icon"></i> Delete</button>
					<button type="button" class="btn btn-primary" data-bs-dismiss="modal"><i class="bi bi-x-circle form_button_icon"></i> Cancel</button>
                  
                </div>
            </div>
        </div>
    </div>
