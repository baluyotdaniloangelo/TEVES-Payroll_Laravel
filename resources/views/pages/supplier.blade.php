@extends('layouts.layout')  
@section('content')  

<main id="main" class="main">	
    <section class="section">	  
          <div class="card">
		  
			  <div class="card">
			  
				<div class="card-header ">
				  <h5 class="card-title">&nbsp;{{ $title }}</h5>
					<div class="d-flex justify-content-end" id="supplier_option"></div>				  
				  </div>
				</div>			  
		 
            <div class="card-body">			
				<div class="p-d3">
									
										<table class="table dataTable display nowrap cell-border" id="getsupplierList" width="100%" cellspacing="0">
											<thead>
												<tr>
													<th>#</th>
													<th class="all">Supplier Name</th>
													<th>Address</th>
													<th class="all">TIN</th>
													<th title='Default Value' class="none">Discount(Less)</th>
													<th class="none">Net</th>
													<th class="none">Vat</th>
													<th class="none">Withhoding Tax</th>
													<th class="none">Payment Terms</th>
													<th class="all">Action</th>
												</tr>
											</thead>				
											
											<tbody>
												
											</tbody>
											
										</table>
											
				</div>									
                   
            </div>
          </div>

	<!-- supplier Delete Modal-->
    <div class="modal fade" id="supplierDeleteModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header header_modal_bg">
                    <h5 class="modal-title" id="exampleModalLabel"></h5>
 					<div class="btn-sm btn-warning btn-circle bi bi-exclamation-circle btn_icon_modal"></div>
                </div>

                <div class="modal-body warning_modal_bg" id="modal-body">
				Are you sure you want to Delete This supplier?<br>
				</div>
				<div align="left"style="margin: 10px;">
				Supplier: <span id="confirm_delete_supplier_name"></span><br>
				Address: <span id="confirm_delete_supplier_address"></span><br>
				TIN: <span id="confirm_delete_supplier_tin"></span><br>
				</div>
                <div class="modal-footer footer_modal_bg">
                    
					<button type="button" class="btn btn-danger" data-bs-dismiss="modal" id="deletesupplierConfirmed" value=""><i class="bi bi-trash3 form_button_icon"></i> Delete</button>
					<button type="button" class="btn btn-primary" data-bs-dismiss="modal"><i class="bi bi-x-circle form_button_icon"></i> Cancel</button>
                  
                </div>
            </div>
        </div>
    </div>	
	
	<!--Modal to Create supplier-->
	<div class="modal fade" id="CreatesupplierModal" tabindex="-1">
              <div class="modal-dialog modal-lg">
                  <div class="modal-content">
                    <div class="modal-header modal-header_form">
                      <h5 class="modal-title">Create Supplier</h5>
					  <div class="btn-group" role="group" aria-label="Basic outlined example">	
						<button type="button" class="btn btn-danger bi bi-x-circle form_button_icon" data-bs-dismiss="modal"></button>
					  </div>
                    </div>
                    <div class="modal-body">
					
					  <form class="g-2 needs-validation" id="supplierformNew">
					  
						<div class="row mb-2">
						  <label for="supplier_name" class="col-sm-3 col-form-label">Supplier Name</label>
						  <div class="col-sm-9">
							<input type="text" class="form-control" name="supplier_name" id="supplier_name" value="" required>
							<span class="valid-feedback" id="supplier_nameError" title="Required"></span>
						  </div>
						</div>
						
						<div class="row mb-2">
						  <label for="supplier_address" class="col-sm-3 col-form-label">Address</label>
						  <div class="col-sm-9">
							<input type="text" class="form-control " name="supplier_address" id="supplier_address" value="" required>
							<span class="valid-feedback" id="supplier_addressError"></span>
						  </div>
						</div>

						<div class="row mb-2">
						  <label for="supplier_tin" class="col-sm-3 col-form-label">TIN</label>
						  <div class="col-sm-9">
							<input type="text" class="form-control " name="supplier_tin" id="supplier_tin" value="" required>
							<span class="valid-feedback" id="supplier_tinError"></span>
						  </div>
						</div>						
						
						<div class="row mb-2">
						  <label for="default_less_percentage" class="col-sm-3 col-form-label">Less/Discount</label>
						  <div class="col-sm-9">
							<input type="text" class="form-control " name="default_less_percentage" id="default_less_percentage" value="">
							<span class="valid-feedback" id="default_less_percentageError"></span>
						  </div>
						</div>

						<div class="row mb-2">
						  <label for="default_net_percentage" class="col-sm-3 col-form-label">Net Value</label>
						  <div class="col-sm-9">
							<input type="text" class="form-control " name="default_net_percentage" id="default_net_percentage" value="">
							<span class="valid-feedback" id="default_net_percentageError"></span>
						  </div>
						</div>
						
						<div class="row mb-2">
						  <label for="default_vat_percentage" class="col-sm-3 col-form-label">VAT Value</label>
						  <div class="col-sm-9">
							<input type="text" class="form-control " name="default_vat_percentage" id="default_vat_percentage" value="">
							<span class="valid-feedback" id="default_vat_percentageError"></span>
						  </div>
						</div>
						
						<div class="row mb-2">
						  <label for="default_withholding_tax_percentage" class="col-sm-3 col-form-label">Withhoding Tax</label>
						  <div class="col-sm-9">
							<input type="text" class="form-control " name="default_withholding_tax_percentage" id="default_withholding_tax_percentage" value="">
							<span class="valid-feedback" id="default_withholding_tax_percentageError"></span>
						  </div>
						</div>
								
						<div class="row mb-2">
						  <label for="default_payment_terms" class="col-sm-3 col-form-label">Payment Terms</label>
						  <div class="col-sm-9">
							<input type="text" class="form-control " name="default_payment_terms" id="default_payment_terms" value="">
							<span class="valid-feedback" id="default_payment_termsError"></span>
						  </div>
						</div>
						
						</div>
						
                    <div class="modal-footer modal-footer_form">
						
						  <button type="submit" class="btn btn-success btn-sm bi bi-save-fill form_button_icon" id="save-supplier"> Submit</button>
						  <button type="reset" class="btn btn-primary btn-sm bi bi-backspace-fill form_button_icon" id="clear-supplier"> Reset</button>
						  
					</div>
					</form><!-- End Multi Columns Form -->
                  </div>
                </div>
             </div>
	
	
	<!--Modal to Upadate supplier-->
	<div class="modal fade" id="UpdatesupplierModal" tabindex="-1">
              <div class="modal-dialog modal-lg">
                  <div class="modal-content">
                    <div class="modal-header modal-header_form">
                      <h5 class="modal-title">Update Supplier</h5>
					  <div class="btn-group" role="group" aria-label="Basic outlined example">		
						<button type="button" class="btn btn-danger bi bi-x-circle form_button_icon" data-bs-dismiss="modal"></button>
					  </div>
                    </div>
                    <div class="modal-body">
					
					  <form class="g-2 needs-validation" id="supplierformEdit">
					  
						<div class="row mb-2">
						  <label for="update_supplier_name" class="col-sm-3 col-form-label">Supplier Name</label>
						  <div class="col-sm-9">
							<input type="text" class="form-control" name="update_supplier_name" id="update_supplier_name" value="" required>
							<span class="valid-feedback" id="update_supplier_nameError" title="Required"></span>
						  </div>
						</div>
						
						<div class="row mb-2">
						  <label for="update_supplier_address" class="col-sm-3 col-form-label">Address</label>
						  <div class="col-sm-9">
							<input type="text" class="form-control " name="update_supplier_address" id="update_supplier_address" value="" required>
							<span class="valid-feedback" id="update_supplier_addressError"></span>
						  </div>
						</div>

						<div class="row mb-2">
						  <label for="update_supplier_tin" class="col-sm-3 col-form-label">TIN</label>
						  <div class="col-sm-9">
							<input type="text" class="form-control " name="update_supplier_tin" id="update_supplier_tin" value="" required>
							<span class="valid-feedback" id="update_supplier_tinError"></span>
						  </div>
						</div>			
						
						<div class="row mb-2">
						  <label for="update_default_less_percentage" class="col-sm-3 col-form-label">Less/Discount</label>
						  <div class="col-sm-9">
							<input type="text" class="form-control " name="update_default_less_percentage" id="update_default_less_percentage" value="">
							<span class="valid-feedback" id="update_default_less_percentageError"></span>
						  </div>
						</div>

						<div class="row mb-2">
						  <label for="update_default_net_percentage" class="col-sm-3 col-form-label">Net Value</label>
						  <div class="col-sm-9">
							<input type="text" class="form-control " name="update_default_net_percentage" id="update_default_net_percentage" value="">
							<span class="valid-feedback" id="update_default_net_percentageError"></span>
						  </div>
						</div>
						
						<div class="row mb-2">
						  <label for="update_default_vat_percentage" class="col-sm-3 col-form-label">VAT Value</label>
						  <div class="col-sm-9">
							<input type="text" class="form-control " name="update_default_vat_percentage" id="update_default_vat_percentage" value="">
							<span class="valid-feedback" id="update_default_vat_percentageError"></span>
						  </div>
						</div>
						
						<div class="row mb-2">
						  <label for="update_default_withholding_tax_percentage" class="col-sm-3 col-form-label">Withhoding Tax</label>
						  <div class="col-sm-9">
							<input type="text" class="form-control " name="update_default_withholding_tax_percentage" id="update_default_withholding_tax_percentage" value="">
							<span class="valid-feedback" id="update_default_withholding_tax_percentageError"></span>
						  </div>
						</div>
								
						<div class="row mb-2">
						  <label for="update_default_payment_terms" class="col-sm-3 col-form-label">Payment Terms</label>
						  <div class="col-sm-9">
							<input type="text" class="form-control " name="update_default_payment_terms" id="update_default_payment_terms" value="">
							<span class="valid-feedback" id="update_default_payment_termsError"></span>
						  </div>
						</div>		
						
						</div>
						
                    <div class="modal-footer modal-footer_form">
						
						  <button type="submit" class="btn btn-success btn-sm bi bi-save-fill form_button_icon" id="update-supplier"> Submit</button>
						  <button type="reset" class="btn btn-primary btn-sm bi bi-backspace-fill form_button_icon" id="clear-supplier"> Reset</button>
						  
					</div>
					</form><!-- End Multi Columns Form -->
                  </div>
                </div>
             </div>
	

    </section>
</main>

@endsection