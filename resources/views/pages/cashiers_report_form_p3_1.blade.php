	<!--Part 3-->	
	<div align="right">
		<button type="button" class="btn btn-success new_item bi bi-plus-circle" data-bs-toggle="modal" data-bs-target="#CRPH3_1_Modal"></button>
	</div>
	<br>						
	<table class="table" id="">
		<thead>
			<tr class='report'>
				<th style="text-align:center !important;">#</th>
				<th style="text-align:center !important;">Name</th>
				<th style="text-align:center !important;">Date</th>
				<th style="text-align:center !important;">Time</th>
				<th style="text-align:center !important;">Amount</th>
				<th style="text-align:center !important;" colspan='2'>Action</th>
			</tr>
		</thead>		
		<tbody id="table_product_data_msc_1">
			<tr style="display: none;">
				<td>HIDDEN</td></tr>
			</tbody>	
	</table>
							
	<!--Modal to Create Other Sales-->
	<div class="modal fade" id="CRPH3_1_Modal" tabindex="-1">
              <div class="modal-dialog modal-lg">
                  <div class="modal-content">
                    <div class="modal-header modal-header_form">
                      <h5 class="modal-title">Create Cash Out</h5>
					  <div class="btn-group" role="group" aria-label="Basic outlined example">		
						
						<button type="button" class="btn btn-danger bi bi-x-circle form_button_icon" data-bs-dismiss="modal"></button>
					  </div>
                    </div>
                    <div class="modal-body">
					
					  <form class="g-3 needs-validation" id="CRPH3_1_form">
					  
					  <div class="row mb-2">
						  <label for="co_name_cash_out" class="col-sm-3 col-form-label">Name</label>
						  <div class="col-sm-9">
								<input class="form-control" name="co_name_cash_out" id="co_name_cash_out" required autocomplete="off">
								<span class="valid-feedback" id="co_name_cash_outError"></span>
						  </div>	
						</div>	
									
						<div class="row mb-2">
						  <label for="date_cashout" class="col-sm-3 col-form-label">Date</label>
						  <div class="col-sm-9">
							  <input type="date" class="form-control" placeholder=""  name="date_cashout" id="date_cashout" value="<?=date('Y-m-d');?>">
							  <span class="valid-feedback" id="date_cashoutError"></span>						  
						  </div>
						</div>	
						
						<div class="row mb-2">
						  <label for="time_cash_out" class="col-sm-3 col-form-label">Time</label>
						  <div class="col-sm-9">
							  <input type="time" class="form-control" placeholder="" name="time_cash_out" id="time_cash_out" value="<?=date('H:i');?>">
							  <span class="valid-feedback" id="time_cash_outError"></span>						  
						  </div>
						</div>	

						<div class="row mb-2">
						  <label for="cashout_amount" class="col-sm-3 col-form-label">Amount</label>
						  <div class="col-sm-9">
							  <input type="number" class="form-control" placeholder="" name="cashout_amount" id="cashout_amount" step=".01" required>
							  <span class="valid-feedback" id="cashout_amountError"></span>						  
						  </div>
						</div>							
						
						<div class="modal-footer modal-footer_form">
						  <button type="submit" class="btn btn-success btn-sm bi bi-save-fill form_button_icon" id="save-CRPH3_1"> Submit</button>
						  <button type="reset" class="btn btn-primary btn-sm bi bi-backspace-fill form_button_icon" id="clear-CRPH3_1-save"> Reset</button>					  
						</div>
					</form><!-- End Multi Columns Form -->
                  </div>
                </div>
             </div>				
	</div>
	
	<!--Modal to Create Other Sales-->
	<div class="modal fade" id="Update_CRPH3_1_Modal" tabindex="-1">
              <div class="modal-dialog modal-lg">
                  <div class="modal-content">
                    <div class="modal-header modal-header_form">
                      <h5 class="modal-title">Update Cash Out</h5>
					  <div class="btn-group" role="group" aria-label="Basic outlined example">		
						
						<button type="button" class="btn btn-danger bi bi-x-circle form_button_icon" data-bs-dismiss="modal"></button>
					  </div>
                    </div>
                    <div class="modal-body">
					
					  <form class="g-3 needs-validation" id="CRPH3_1_form_edit">
					  
					  <div class="row mb-2">
						  <label for="update_co_name_cash_out" class="col-sm-3 col-form-label">Name</label>
						  <div class="col-sm-9">
								<input class="form-control" name="update_co_name_cash_out" id="update_co_name_cash_out" required autocomplete="off">
								<span class="valid-feedback" id="update_co_name_cash_outError"></span>
						  </div>	
						</div>	
									
						<div class="row mb-2">
						  <label for="update_date_cashout" class="col-sm-3 col-form-label">Date</label>
						  <div class="col-sm-9">
							  <input type="date" class="form-control" placeholder=""  name="update_date_cashout" id="update_date_cashout">
							  <span class="valid-feedback" id="update_date_cashoutError"></span>						  
						  </div>
						</div>	
						
						<div class="row mb-2">
						  <label for="update_time_cash_out" class="col-sm-3 col-form-label">Time</label>
						  <div class="col-sm-9">
							  <input type="time" class="form-control" name="update_time_cash_out" id="update_time_cash_out" value="<?=date('H:i');?>">
							  <span class="valid-feedback" id="update_time_cash_outError"></span>						  
						  </div>
						</div>	

						<div class="row mb-2">
						  <label for="update_cashout_amount" class="col-sm-3 col-form-label">Amount</label>
						  <div class="col-sm-9">
							  <input type="number" class="form-control" placeholder="" name="update_cashout_amount" id="update_cashout_amount" step=".01" onchange="UpdateTotalAmount_PH3()" >
							  <span class="valid-feedback" id="update_cashout_amountError"></span>						  
						  </div>
						</div>							
						
						<div class="modal-footer modal-footer_form">
						  <button type="submit" class="btn btn-success btn-sm bi bi-save-fill form_button_icon" id="update-CRPH3_1"> Submit</button>
						  <button type="reset" class="btn btn-primary btn-sm bi bi-backspace-fill form_button_icon" id="clear-CRPH3_1-save"> Reset</button>					  
						</div>
					</form><!-- End Multi Columns Form -->
                  </div>
                </div>
             </div>				
	</div>
	
	<!-- CRP3 Delete Modal-->
    <div class="modal fade" id="CRPH3DeleteModal_1" tabindex="-1" role="dialog" aria-hidden="true">
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
				NAME: <span id="delete_co_name_cash_out"></span><br>
				DATE: <span id="delete_date_cashout"></span><br>
				TIME: <span id="delete_time_cash_out"></span><br>
				AMOUNT: <span id="delete_cashout_amount"></span><br>
				</div>	
                <div class="modal-footer footer_modal_bg">
                    
					<button type="button" class="btn btn-danger" data-bs-dismiss="modal" id="deleteCRPH3Confirmed_1" value=""><i class="bi bi-trash3 form_button_icon"></i> Delete</button>
					<button type="button" class="btn btn-primary" data-bs-dismiss="modal"><i class="bi bi-x-circle form_button_icon"></i> Cancel</button>
                  
                </div>
            </div>
        </div>

        </div>							  
