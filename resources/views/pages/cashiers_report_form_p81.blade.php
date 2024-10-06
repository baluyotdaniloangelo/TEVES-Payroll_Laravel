	<!--Part 1-->
	
	<div align="right">
			<button type="button" class="btn btn-success new_item bi bi-plus-circle" data-bs-toggle="modal" data-bs-target="#CRPH8_Modal" id="CRPH8_Modal_add"></button>
	</div>
	<br>
		<table class="table" id="table_dipstick_inventory">
			<thead>
					<tr class='report'>
						<th style="text-align:center !important;">PRODUCT</th>
						<th style="text-align:center !important;">TANK CAPACITY</th>
						<th style="text-align:center !important;">BEGINNING</th>
						<th style="text-align:center !important;">SALES in LITERS</th>
						<th style="text-align:center !important;">UGT PUMPING</th>
						<th style="text-align:center !important;">DELIVERY</th>
						<th style="text-align:center !important;">ENDING</th>
						<th style="text-align:center !important;">BOOK STOCK</th>
						<th style="text-align:center !important;">VARIANCE</th>
						<th style="text-align:center !important;" colspan="2">ACTION</th>
					</tr>
			</thead>
			<tbody id="table_product_body_data">
					<tr style="display: none;">
						<td>HIDDEN</td>
					</tr>
			</tbody>
		</table>

	<!--Modal to Create-->
	<div class="modal fade" id="CRPH1_Modal" tabindex="-1">
              <div class="modal-dialog modal-lg">
                  <div class="modal-content">
                    <div class="modal-header modal-header_form">
                      <h5 class="modal-title">Add Dipstick Inventoty</h5>
					  <div class="btn-group" role="group" aria-label="Basic outlined example">		
						<button type="button" class="btn btn-danger bi bi-x-circle form_button_icon" data-bs-dismiss="modal"></button>
					  </div>
                    </div>
                    <div class="modal-body">
					
					  <form class="g-3 needs-validation" id="CRPH8_form">
					  
					  <div class="row mb-2">
						  <label for="ph8_product_idx" class="col-sm-3 col-form-label">Product</label>
						  <div class="col-sm-9">
									<input class="form-control" list="ph8_product_name" name="ph8_product_name" id="ph8_product_idx" required autocomplete="off">
									<datalist id="ph8_product_name">
										@foreach ($product_data as $product_data_cols)
											<span style="font-family: DejaVu Sans; sans-serif;">
											<option label="&#8369; {{$product_data_cols->product_price}} | {{$product_data_cols->product_name}}" data-id="{{$product_data_cols->product_id}}" data-price="{{$product_data_cols->product_price}}" value="{{$product_data_cols->product_name}}">
											</span>
										@endforeach
									</datalist>
									<span class="valid-feedback" id="ph8_product_idxError"></span>
							</div>	
						</div>	
						
						<div class="row mb-2">
						  <label for="ph8_beginning_inventory" class="col-sm-3 col-form-label">Beginning Inventory</label>
						  <div class="col-sm-9">
							  <input type="number" class="form-control" placeholder="" aria-label="ph8_beginning_inventory" aria-describedby="basic-addon1" name="ph8_beginning_inventory" id="ph8_beginning_inventory" required step=".01" >
							  <span class="valid-feedback" id="ph8_beginning_inventoryError"></span>						  
						  </div>
						</div>	
						
						<div class="row mb-2">
						  <label for="ph8_sales_in_liters" class="col-sm-3 col-form-label">Sales in Liters</label>
						  <div class="col-sm-9">
							  <input type="number" class="form-control" placeholder="" aria-label="ph8_sales_in_liters" aria-describedby="basic-addon1" name="ph8_sales_in_liters" id="ph8_sales_in_liters" required step=".01" >
							  <span class="valid-feedback" id="ph8_sales_in_litersError"></span>						  
						  </div>
						</div>	
						
						<div class="row mb-2">
						  <label for="ph8_ugt_pumping" class="col-sm-3 col-form-label">UGT Pumping(CM & L)</label>
						  <div class="col-sm-9">
							  <input type="number" class="form-control" placeholder="" aria-label="Username" aria-describedby="basic-addon1" name="ph8_ugt_pumping" id="ph8_ugt_pumping" step=".01">
							  <span class="valid-feedback" id="ph8_ugt_pumpingError"></span>						  
						  </div>
						</div>

						<div class="row mb-2">
						  <label for="ph8_dipstick_delivery" class="col-sm-3 col-form-label" title="Manual Price">Delivery</label>
						  <div class="col-sm-9">
							  <input type="number" class="form-control" placeholder="" aria-label="Username" aria-describedby="basic-addon1" name="ph8_dipstick_delivery" id="ph8_dipstick_delivery" step=".01"  >
							  <span class="valid-feedback" id="ph8_dipstick_deliveryError"></span>						  
						  </div>
						</div>		

						<div class="row mb-2">
						  <label for="closing_inventory" class="col-sm-3 col-form-label">Ending Inventory</label>
						  <div class="col-sm-9">
							  <input type="number" class="form-control" placeholder="" aria-label="closing_inventory" aria-describedby="basic-addon1" name="closing_inventory" id="closing_inventory" required step=".01" >
							  <span class="valid-feedback" id="closing_inventoryError"></span>						  
						  </div>
						</div>							
						
						<!--<div class="row mb-2">
						  <label class="col-sm-3 col-form-label">Total Amount</label>
						  <div class="col-sm-9">
								<span style="font-family: DejaVu Sans; sans-serif;">&#8369;</span> <span id="TotalAmount">0.00</span>
						  </div>
						</div>-->
						
						<div class="modal-footer modal-footer_form">
						  <button type="submit" class="btn btn-success btn-sm bi bi-save-fill form_button_icon" id="save-CRPH8"> Submit</button>
						  <button type="reset" class="btn btn-primary btn-sm bi bi-backspace-fill form_button_icon" id="clear-CRPH8-save"> Reset</button>					  
						</div>
						
						</form><!-- End Multi Columns Form -->
						
                  </div>
                </div>
             </div>
		</div>		
		
	<!--Modal to Upadate-->
	<div class="modal fade" id="Update_CRPH1_Modal" tabindex="-1">
              <div class="modal-dialog modal-lg">
                  <div class="modal-content">
                    <div class="modal-header modal-header_form">
                      <h5 class="modal-title">Update Product</h5>
					  <div class="btn-group" role="group" aria-label="Basic outlined example">		
						<button type="button" class="btn btn-danger bi bi-x-circle form_button_icon" data-bs-dismiss="modal"></button>
					  </div>
                    </div>
                    <div class="modal-body">
					
					  <form class="g-3 needs-validation" id="CRPH1_form_edit">
					  
					  <div class="row mb-2">
						  <label for="update_product_idx" class="col-sm-3 col-form-label">PRODUCT</label>
						  <div class="col-sm-9">
									<input class="form-control" list="update_product_name" name="update_product_name" id="update_product_idx" required autocomplete="off" onchange="UpdateTotalAmount()">
									<datalist id="update_product_name">
										@foreach ($product_data as $product_data_cols)
											<span style="font-family: DejaVu Sans; sans-serif;">
											<option label="&#8369; {{$product_data_cols->product_price}} | {{$product_data_cols->product_name}}" data-id="{{$product_data_cols->product_id}}" data-price="{{$product_data_cols->product_price}}" value="{{$product_data_cols->product_name}}">
											</span>
										@endforeach
									</datalist>
									<span class="valid-feedback" id="update_product_idxError"></span>
							</div>	
						</div>	
						
						<div class="row mb-2">
						  <label for="update_beginning_inventory" class="col-sm-3 col-form-label">BEGINNING</label>
						  <div class="col-sm-9">
							  <input type="number" class="form-control" placeholder="" aria-label="Username" aria-describedby="basic-addon1" name="update_beginning_inventory" id="update_beginning_inventory" required step=".01" onchange="UpdateTotalAmount()" >
							  <span class="valid-feedback" id="update_beginning_inventoryError"></span>						  
						  </div>
						</div>	
						
						<div class="row mb-2">
						  <label for="update_sales_in_liters" class="col-sm-3 col-form-label">CLOSING</label>
						  <div class="col-sm-9">
							  <input type="number" class="form-control" placeholder="" aria-label="Username" aria-describedby="basic-addon1" name="update_sales_in_liters" id="update_sales_in_liters" required step=".01" onchange="UpdateTotalAmount()" >
							  <span class="valid-feedback" id="update_sales_in_litersError"></span>						  
						  </div>
						</div>	
						
						<div class="row mb-2">
						  <label for="update_ugt_pumping" class="col-sm-3 col-form-label">ugt_pumping</label>
						  <div class="col-sm-9">
							  <input type="number" class="form-control" placeholder="" aria-label="Username" aria-describedby="basic-addon1" name="update_ugt_pumping" id="update_ugt_pumping" step=".01" onchange="UpdateTotalAmount()" >
							  <span class="valid-feedback" id="update_ugt_pumpingError"></span>						  
						  </div>
						</div>

						<div class="row mb-2">
						  <label for="update_dipstick_delivery" class="col-sm-3 col-form-label" title="Msnual Price">PUMP PRICE</label>
						  <div class="col-sm-9">
							  <input type="number" class="form-control" placeholder="" aria-label="Username" aria-describedby="basic-addon1" name="update_dipstick_delivery" id="update_dipstick_delivery" step=".01" onchange="UpdateTotalAmount()" >
							  <span class="valid-feedback" id="update_dipstick_deliveryError"></span>						  
						  </div>
						</div>						
						<!---->
						<div class="row mb-2">
						  <label class="col-sm-3 col-form-label">Total Amount</label>
						  <div class="col-sm-9">
								<span style="font-family: DejaVu Sans; sans-serif;">&#8369;</span> <span id="UpdateTotalAmount">0.00</span>
						  </div>
						</div>
						
						<div class="modal-footer modal-footer_form">
						  <button type="submit" class="btn btn-success btn-sm bi bi-save-fill form_button_icon" id="update-CRPH1"> Submit</button>
						  <button type="reset" class="btn btn-primary btn-sm bi bi-backspace-fill form_button_icon" id="clear-CRPH1-update"> Reset</button>  
						</div>
					</form><!-- End Multi Columns Form -->
                  </div>
                </div>
             </div>
	</div>
	
		<!-- CRP1 Delete Modal-->
    <div class="modal fade" id="CRPH1DeleteModal" tabindex="-1" role="dialog" aria-hidden="true">
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
				PRODUCT: <span id="CRPH1_delete_product_idx"></span><br>
				BEGINNING: <span id="CRPH1_delete_beginning_inventory"></span><br>
				CLOSING: <span id="CRPH1_delete_sales_in_liters"></span><br>
				ugt_pumping: <span id="CRPH1_delete_ugt_pumping"></span><br>
				SALES IN LITERS: <span id="CRPH1_delete_product_order_quantity"></span><br>			
				PUMP PRICE: <span id="CRPH1_delete_dipstick_delivery"></span><br>
				PESO SALES: <span id="CRPH1_delete_order_total_amount"></span><br>
				</div>	
                <div class="modal-footer footer_modal_bg">
                    
					<button type="button" class="btn btn-danger" data-bs-dismiss="modal" id="deleteCRPH1Confirmed" value=""><i class="bi bi-trash3 form_button_icon"></i> Delete</button>
					<button type="button" class="btn btn-primary" data-bs-dismiss="modal"><i class="bi bi-x-circle form_button_icon"></i> Cancel</button>
                  
                </div>
            </div>
        </div>
    </div>	
					
