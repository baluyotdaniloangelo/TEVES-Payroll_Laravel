	<!--Part 4-->	
	<div align="right">
		<button type="button" class="btn btn-success new_item bi bi-plus-circle" data-bs-toggle="modal" data-bs-target="#CRPH4_Modal"></button>
	</div>
	<br>						
	<table class="table" id="table_PH4">
		<thead>
			<tr class='report'>
				<th style="text-align:center !important;">Description</th>
				<th style="text-align:center !important;">Amount</th>
				<th style="text-align:center !important;" colspan='2'>Action</th>
			</tr>
		</thead>		
		<tbody id="table_product_data_PH4">
			<tr style="display: none;">
				<td>HIDDEN</td></tr>
			</tbody>	
	</table>
							
	<!--Modal to Create Other Sales-->
	<div class="modal fade" id="CRPH4_Modal" tabindex="-1">
              <div class="modal-dialog modal-lg">
                  <div class="modal-content">
                    <div class="modal-header modal-header_form">
                      <h5 class="modal-title">Create Other Sales</h5>
					  <div class="btn-group" role="group" aria-label="Basic outlined example">		
						
						<button type="button" class="btn btn-danger bi bi-x-circle form_button_icon" data-bs-dismiss="modal"></button>
					  </div>
                    </div>
                    <div class="modal-body">
					
					  <form class="g-3 needs-validation" id="CRPH4_form">
					  
					  <div class="row mb-2">
						  <label for="description_p4" class="col-sm-3 col-form-label">Description</label>
						  <div class="col-sm-9">
									<input class="form-control" list="description_p4" name="description_p4" id="description_p4" required autocomplete="off">
									<span class="valid-feedback" id="description_p4Error"></span>
									</div>	
						</div>	
						
						<div class="row mb-2">
						  <label for="amount_p4" class="col-sm-3 col-form-label" title="Amount">Amount</label>
						  <div class="col-sm-9">
							  <input type="number" class="form-control" placeholder="" name="amount_p4" id="amount_p4" step=".01" >
							  <span class="valid-feedback" id="amount_p4Error"></span>						  
						  </div>
						</div>						
						
						<div class="modal-footer modal-footer_form">
						  <button type="submit" class="btn btn-success btn-sm bi bi-save-fill form_button_icon" id="save-CRPH4"> Submit</button>
						  <button type="reset" class="btn btn-primary btn-sm bi bi-backspace-fill form_button_icon" id="clear-CRPH4-save"> Reset</button>					  
						</div>
					</form><!-- End Multi Columns Form -->
                  </div>
                </div>
             </div>				
	</div>
	
	<!--Modal to Create Other Sales-->
	<div class="modal fade" id="Update_CRPH4_Modal" tabindex="-1">
              <div class="modal-dialog modal-lg">
                  <div class="modal-content">
                    <div class="modal-header modal-header_form">
                      <h5 class="modal-title">Update Other Sales</h5>
					  <div class="btn-group" role="group" aria-label="Basic outlined example">		
						
						<button type="button" class="btn btn-danger bi bi-x-circle form_button_icon" data-bs-dismiss="modal"></button>
					  </div>
                    </div>
                    <div class="modal-body">
					
					  <form class="g-3 needs-validation" id="CRPH4_form_edit">
					  
					   <div class="row mb-2">
						  <label for="update_description_p4" class="col-sm-3 col-form-label">Description</label>
							<div class="col-sm-9">
									<input class="form-control" list="update_description_p4" name="update_description_p4" id="update_description_p4" required autocomplete="off">
									<span class="valid-feedback" id="update_description_p4Error"></span>
							</div>	
						</div>	
						
						<div class="row mb-2">
						  <label for="update_amount_p4" class="col-sm-3 col-form-label" title="Amount">Amount</label>
						  <div class="col-sm-9">
							  <input type="number" class="form-control" placeholder="" name="update_amount_p4" id="update_amount_p4" step=".01" >
							  <span class="valid-feedback" id="update_amount_p4Error"></span>						  
						  </div>
						</div>						
						
						<div class="modal-footer modal-footer_form">
						  <button type="submit" class="btn btn-success btn-sm bi bi-save-fill form_button_icon" id="update-CRPH4"> Submit</button>
						  <button type="reset" class="btn btn-primary btn-sm bi bi-backspace-fill form_button_icon" id="clear-CRPH4-update"> Reset</button>					  
						</div>
					</form><!-- End Multi Columns Form -->
                  </div>
                </div>
             </div>		
	</div>
	
	<!-- CRP2 Delete Modal-->
    <div class="modal fade" id="CRPH4DeleteModal" tabindex="-1" role="dialog" aria-hidden="true">
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
				Description: <span id="delete_description_p4"></span><br>
				Amount: <span id="delete_amount_p4"></span><br>
				</div>	
                <div class="modal-footer footer_modal_bg">
                    
					<button type="button" class="btn btn-danger" data-bs-dismiss="modal" id="deleteCRPH4Confirmed" value=""><i class="bi bi-trash3 form_button_icon"></i> Delete</button>
					<button type="button" class="btn btn-primary" data-bs-dismiss="modal"><i class="bi bi-x-circle form_button_icon"></i> Cancel</button>
                  
                </div>
            </div>
        </div>
    </div>							  
