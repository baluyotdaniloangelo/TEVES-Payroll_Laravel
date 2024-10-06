	<!--Part 1-->
	
	<div align="right">
			<button type="button" class="btn btn-success new_item bi bi-plus-circle" data-bs-toggle="modal" data-bs-target="#CRPH7_Modal"></button>
	</div>
	<br>
		<table class="table" id="">
			<thead>
					<tr class='report'>
						<th style="text-align:center !important;">Product</th>
						<th style="text-align:center !important;">Tank Name</th>
						<th style="text-align:center !important;">Tank Capacity</th>
						<th style="text-align:center !important;">Beginning Inventory</th>
						<th style="text-align:center !important;">Sales in Liters</th>
						<th style="text-align:center !important;">Delivery</th>
						<th style="text-align:center !important;">Ending Inventory</th>
						<th style="text-align:center !important;">Book Stock</th>
						<th style="text-align:center !important;">Variance</th>
						<th style="text-align:center !important;" colspan="2">ACTION</th>
					</tr>
			</thead>
			<tbody id="table_product_dipstick_inventory_body_data">
					<tr style="display: none;">
						<td>HIDDEN</td>
					</tr>
			</tbody>
		</table>

		<div class="modal fade" id="CRPH7_Modal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title">Add Product Dipstick Inventory</h5>
                      <button type="button" class="btn btn-danger bi bi-x-circle form_button_icon" data-bs-dismiss="modal"></button>
                    </div>
                    
					<div class="modal-body">
						<form class="g-3 needs-validation" id="CRPH7_form">		
						
							<div class="row mb-2">
							<label for="product_idx" class="col-sm-4 col-form-label">PRODUCT</label>
							<div class="col-sm-8">
									<input class="form-control" list="product_list_inventory" name="product_name_dipstick_inventory" id="product_idx_dipstick_inventory" required autocomplete="off" onchange="LoadProductTank('dipstick')">
									
									<span class="valid-feedback" id="product_idx_dipstick_inventoryError"></span>
							</div>	
							</div>
						
							<div class="row mb-2">
							  <label for="product_tank_idx_inventor" class="col-sm-4 col-form-label">TANK</label>
							  <div class="col-sm-8">
										<input class="form-control" list="product_tank_list" name="product_tank_name_dipstick_inventory" id="product_tank_idx_dipstick_inventory" required autocomplete="off">
										<span class="valid-feedback" id="product_tank_idx_dipstick_inventoryError"></span>
								</div>	
							</div>	
						
							<div class="row mb-2">
							  <label for="beginning_reading_dipstick_inventory" class="col-sm-4 col-form-label">BEGINNING INVENTORY</label>
							  <div class="col-sm-8">
								  <input type="number" class="form-control" placeholder="" name="beginning_dipstick_inventory" id="beginning_dipstick_inventory" required step=".01" onchange="dipstick_inventory_tank_compute()" >
								  <span class="valid-feedback" id="beginning_dipstick_inventoryError"></span>						  
							  </div>
							</div>	
							
							<div class="row mb-2">
							  <label for="sales_in_liters_dipstick_inventory" class="col-sm-4 col-form-label">SALES IN LITERS</label>
							  <div class="col-sm-8">
								  <input type="number" class="form-control" placeholder="" name="sales_in_liters_dipstick_inventory" id="sales_in_liters_dipstick_inventory" required step=".01" onchange="dipstick_inventory_tank_compute()" >
								  <span class="valid-feedback" id="sales_in_liters_dipstick_inventoryError"></span>						  
							  </div>
							</div>	
							
							<div class="row mb-2">
							  <label for="ugt_pumping_dipstick_inventory" class="col-sm-4 col-form-label">UGT PUMPING</label>
							  <div class="col-sm-8">
								  <input type="number" class="form-control" placeholder="" name="ugt_pumping_dipstick_inventory" id="ugt_pumping_dipstick_inventory" required step=".01" onchange="dipstick_inventory_tank_compute()" >
								  <span class="valid-feedback" id="ugt_pumping_dipstick_inventoryError"></span>						  
							  </div>
							</div>	
							
							<div class="row mb-2">
							  <label for="delivery_dipstick_inventory" class="col-sm-4 col-form-label">DELIVERY</label>
							  <div class="col-sm-8">
								  <input type="number" class="form-control" placeholder="" name="delivery_dipstick_inventory" id="delivery_dipstick_inventory" required step=".01" onchange="dipstick_inventory_tank_compute()" >
								  <span class="valid-feedback" id="delivery_dipstick_inventoryError"></span>						  
							  </div>
							</div>

							<div class="row mb-2">
							  <label for="ending_dipstick_inventory" class="col-sm-4 col-form-label" title="Manual Price">ENDING INVENTORY</label>
							  <div class="col-sm-8">
								  <input type="number" class="form-control" placeholder="" name="ending_dipstick_inventory" id="ending_dipstick_inventory" required step=".01" onchange="dipstick_inventory_tank_compute()" >
								  <span class="valid-feedback" id="ending_dipstick_inventoryError"></span>						  
							  </div>
							</div>						
							
							<div class="row mb-2">
							  <label class="col-sm-4 col-form-label">BOOK STOCK</label>
							  <div class="col-sm-8">
									 <span id="TotalBookStock_dipstick_inventory">0.00</span>
							  </div>
							</div>
							
							<div class="row mb-2">
							  <label class="col-sm-4 col-form-label">VARIANCE</label>
							  <div class="col-sm-8">
									 <span id="TotalVariance_dipstick_inventory">0.00</span>
							  </div>
							</div>
						
						
					</div>
					
                    <div class="modal-footer">
							<button type="submit" class="btn btn-success btn-sm bi bi-save-fill form_button_icon" id="save-CRPH7"> Submit</button>
						    <button type="reset" class="btn btn-primary btn-sm bi bi-backspace-fill form_button_icon" id="clear-CRPH7-save"> Reset</button>
                    </div>
					</form>
                </div>
            </div>
        </div>

		<div class="modal fade" id="Update_CRPH7_Modal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title">Update Product Dipstick Inventory</h5>
                      <button type="button" class="btn btn-danger bi bi-x-circle form_button_icon" data-bs-dismiss="modal"></button>
                    </div>
                    
					<div class="modal-body">
						<form class="g-3 needs-validation" id="update_CRPH7_form">		
						
							<div class="row mb-2">
							<label for="update_product_idx_dipstick_inventory" class="col-sm-4 col-form-label">PRODUCT</label>
							<div class="col-sm-8">
									<input class="form-control" list="product_list_inventory" name="update_product_name_dipstick_inventory" id="update_product_idx_dipstick_inventory" required autocomplete="off" onchange="LoadProductTank_Update('dipstick')">
									
									<span class="valid-feedback" id="update_product_idx_dipstick_inventoryError"></span>
							</div>	
							</div>
						
							<div class="row mb-2">
							  <label for="update_product_tank_idx_inventor" class="col-sm-4 col-form-label">TANK</label>
							  <div class="col-sm-8">
										<input class="form-control" list="update_product_tank_list" name="update_product_tank_name_dipstick_inventory" id="update_product_tank_idx_dipstick_inventory" required autocomplete="off">
										<span class="valid-feedback" id="update_product_tank_idx_dipstick_inventoryError"></span>
								</div>	
							</div>	
						
							<div class="row mb-2">
							  <label for="update_beginning_reading_dipstick_inventory" class="col-sm-4 col-form-label">BEGINNING INVENTORY</label>
							  <div class="col-sm-8">
								  <input type="number" class="form-control" placeholder="" name="update_beginning_dipstick_inventory" id="update_beginning_dipstick_inventory" required step=".01" onchange="update_dipstick_inventory_compute()" >
								  <span class="valid-feedback" id="update_beginning_dipstick_inventoryError"></span>						  
							  </div>
							</div>	
							
							<div class="row mb-2">
							  <label for="update_sales_in_liters_dipstick_inventory" class="col-sm-4 col-form-label">SALES IN LITERS</label>
							  <div class="col-sm-8">
								  <input type="number" class="form-control" placeholder="" name="update_sales_in_liters_dipstick_inventory" id="update_sales_in_liters_dipstick_inventory" required step=".01" onchange="update_dipstick_inventory_compute()" >
								  <span class="valid-feedback" id="update_sales_in_liters_dipstick_inventoryError"></span>						  
							  </div>
							</div>	
							
							<div class="row mb-2">
							  <label for="update_ugt_pumping_dipstick_inventory" class="col-sm-4 col-form-label">UGT PUMPING</label>
							  <div class="col-sm-8">
								  <input type="number" class="form-control" placeholder="" name="update_ugt_pumping_dipstick_inventory" id="update_ugt_pumping_dipstick_inventory" required step=".01" onchange="update_dipstick_inventory_compute()" >
								  <span class="valid-feedback" id="update_ugt_pumping_dipstick_inventoryError"></span>						  
							  </div>
							</div>	
							
							<div class="row mb-2">
							  <label for="update_delivery_dipstick_inventory" class="col-sm-4 col-form-label">DELIVERY</label>
							  <div class="col-sm-8">
								  <input type="number" class="form-control" placeholder="" name="update_delivery_dipstick_inventory" id="update_delivery_dipstick_inventory" required step=".01" onchange="update_dipstick_inventory_compute()" >
								  <span class="valid-feedback" id="update_delivery_dipstick_inventoryError"></span>						  
							  </div>
							</div>

							<div class="row mb-2">
							  <label for="update_ending_dipstick_inventory" class="col-sm-4 col-form-label" title="Manual Price">ENDING INVENTORY</label>
							  <div class="col-sm-8">
								  <input type="number" class="form-control" placeholder="" name="update_ending_dipstick_inventory" id="update_ending_dipstick_inventory" required step=".01" onchange="update_dipstick_inventory_compute()" >
								  <span class="valid-feedback" id="update_ending_dipstick_inventoryError"></span>						  
							  </div>
							</div>						
							
							<div class="row mb-2">
							  <label class="col-sm-4 col-form-label">BOOK STOCK</label>
							  <div class="col-sm-8">
									 <span id="update_TotalBookStock_dipstick_inventory">0.00</span>
							  </div>
							</div>
							
							<div class="row mb-2">
							  <label class="col-sm-4 col-form-label">VARIANCE</label>
							  <div class="col-sm-8">
									 <span id="update_TotalVariance_dipstick_inventory">0.00</span>
							  </div>
							</div>
						
						
					</div>
					
                    <div class="modal-footer">
							<button type="submit" class="btn btn-success btn-sm bi bi-save-fill form_button_icon" id="update-CRPH7"> Submit</button>
						    <button type="reset" class="btn btn-primary btn-sm bi bi-backspace-fill form_button_icon" id="clear-CRPH7-update"> Reset</button>
                    </div>
					</form>
                </div>
            </div>
        </div>			  
	<!--Data List for Product Tank-->
	<datalist id="product_list_dipstick_inventory">
		
	</datalist>		

	<!--Data List for Product Tank-->
	<datalist id="product_tank_list_dipstick_inventory">
		
	</datalist>		
	

	<!-- CRP1 Delete Modal-->
    <div class="modal fade" id="CRPH7DeleteModal" tabindex="-1" role="dialog" aria-hidden="true">
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
				Product: <span id="delete_product_idx_dipstick_inventory"></span><br>
				Tank: <span id="delete_product_tank_idx_dipstick_inventory"></span><br>
				Beginning Inventory: <span id="delete_beginning_dipstick_inventory"></span><br>
				Sales in liters: <span id="delete_sales_in_liters_dipstick_inventory"></span><br>
				UGT Pumping: <span id="delete_ugt_pumping"></span><br>
				Delivery: <span id="delete_delivery_dipstick_inventory"></span><br>
				Ending Inventory: <span id="delete_ending_dipstick_inventory"></span><br>				
				Book Stock: <span id="CRPH7_delete_TotalBookStock"></span><br>
				Variance: <span id="CRPH7_delete_TotalVariance"></span><br>
				</div>	
				
                <div class="modal-footer footer_modal_bg">
                    
					<button type="button" class="btn btn-danger" data-bs-dismiss="modal" id="deleteCRPH7Confirmed" value=""><i class="bi bi-trash3 form_button_icon"></i> Delete</button>
					<button type="button" class="btn btn-primary" data-bs-dismiss="modal"><i class="bi bi-x-circle form_button_icon"></i> Cancel</button>
                  
                </div>
            </div>
        </div>
    </div>	
					
