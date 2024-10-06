@extends('layouts.layout')  
@section('content')  

<main id="main" class="main">	
    <section class="section">	  
          <div class="card">
		  
			  <div class="card">
			  
				<div class="card-header ">
				  <h5 class="card-title">&nbsp;{{ $title }}</h5>
					<div class="d-flex justify-content-end" id="product_option"></div>				  
				  </div>
				</div>			  
		 
            <div class="card-body">			
				<div class="p-d3">
									<div class="table-responsive">
										<table class="table dataTable display nowrap cell-border" id="getProductList" width="100%" cellspacing="0">
											<thead>
												<tr>
													<th>#</th>
													<th>Product Name</th>
													<th>Default Price</th>
													<th>Unit of Measurement</th>
													<th>Action</th>
												</tr>
											</thead>				
											
											<tbody>
												
											</tbody>
											
										</table>
									</div>		
				</div>									
                   
            </div>
          </div>

	<!-- Site Delete Modal-->
    <div class="modal fade" id="ProductDeleteModal" tabindex="-1" role="dialog" aria-hidden="true">
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
				Product: <span id="confirm_delete_product_name"></span><br>
				Price: <span id="confirm_delete_product_price"></span><br>
				
				</div>
                <div class="modal-footer footer_modal_bg">
                    
					<button type="button" class="btn btn-danger" data-bs-dismiss="modal" id="deleteProductConfirmed" value=""><i class="bi bi-trash3 form_button_icon"></i> Delete</button>
					<button type="button" class="btn btn-primary" data-bs-dismiss="modal"><i class="bi bi-x-circle form_button_icon"></i> Cancel</button>
                  
                </div>
            </div>
        </div>
    </div>	

	<!--Modal to Create Product-->
	<div class="modal fade" id="CreateProductModal" tabindex="-1">
              <div class="modal-dialog modal-lg">
                  <div class="modal-content">
                    <div class="modal-header modal-header_form">
                      <h5 class="modal-title">Create Product</h5>
					  <div class="btn-group" role="group" aria-label="Basic outlined example">
						<button type="button" class="btn btn-danger bi bi-x-circle form_button_icon" data-bs-dismiss="modal"></button>
					  </div>
                    </div>
                    <div class="modal-body">
					
					  <form class="g-2 needs-validation" id="ProductformNew">
					  
						<div class="row mb-2">
						  <label for="product_name" class="col-sm-3 col-form-label">Product Name</label>
						  <div class="col-sm-9">
							<input type="text" class="form-control" name="product_name" id="product_name" value="" required>
							<span class="valid-feedback" id="product_nameError" title="Required"></span>
						  </div>
						</div>
						
						<div class="row mb-2">
						  <label for="product_price" class="col-sm-3 col-form-label">Default Price</label>
						  <div class="col-sm-9">
							<input type="number" class="form-control " name="product_price" id="product_price" value="" required>
							<span class="valid-feedback" id="product_priceError"></span>
						  </div>
						</div>						
							
						<div class="row mb-2">
						  <label for="product_unit_measurement" class="col-sm-3 col-form-label">Unit of Measurement</label>
						  <div class="col-sm-9">
							<select class="form-control form-select " name="product_unit_measurement" id="product_unit_measurement" required>				
									<option value="L">Liter | L</option>
									<option value="mL">Milliliter | mL</option>
									<option value="g">Gram | g</option>
									<option value="kg">Kilo | kg</option>
									<option value="PC">Pieces | PC</option>
									<option value="oz">Ounce | oz</option>
									<option value="Pail">Pail</option>
									<option value="Drum">Drum</option>
									<option value="Case">Case</option>
							</select>
							<span class="valid-feedback" id="product_unit_measurementError"></span>
						  </div>
						</div>
							
						</div>
						
                    <div class="modal-footer modal-footer_form">
						
						  <button type="submit" class="btn btn-success btn-sm bi bi-save-fill form_button_icon" id="save-product"> Submit</button>
						  <button type="reset" class="btn btn-primary btn-sm bi bi-backspace-fill form_button_icon" id="clear-product"> Reset</button>
						  
					</div>
					
					</form><!-- End Multi Columns Form -->
                  </div>
                </div>
             </div>
	
	
	<!--Modal to Upadate Site-->
	<div class="modal fade" id="UpdateProductModal" tabindex="-1">
              <div class="modal-dialog modal-lg">
                  <div class="modal-content">
                    <div class="modal-header modal-header_form">
                      <h5 class="modal-title">Update Product</h5>
					  <div class="btn-group" role="group" aria-label="Basic outlined example">		
						<button type="button" class="btn btn-danger bi bi-x-circle form_button_icon" data-bs-dismiss="modal"></button>
					  </div>
                    </div>
                    <div class="modal-body">
					
					  <form class="g-2 needs-validation" id="ProductformEdit">
					  
						<div class="row mb-2">
						  <label for="update_product_name" class="col-sm-3 col-form-label">Product Name</label>
						  <div class="col-sm-9">
							<input type="text" class="form-control" name="update_product_name" id="update_product_name" value="" required>
							<span class="valid-feedback" id="update_product_nameError" title="Required"></span>
						  </div>
						</div>
						
						<div class="row mb-2">
						  <label for="update_product_price" class="col-sm-3 col-form-label">Default Price</label>
						  <div class="col-sm-9">
							<input type="number" class="form-control " name="update_product_price" id="update_product_price" value="" required>
							<span class="valid-feedback" id="update_product_priceError"></span>
						  </div>
						</div>					
								
						<div class="row mb-2">
						  <label for="update_product_unit_measurement" class="col-sm-3 col-form-label">Unit of Measurement</label>
						  <div class="col-sm-9">
							<select class="form-control form-select " name="update_product_unit_measurement" id="update_product_unit_measurement" required>
									<option value="L">Liter | L</option>
									<option value="mL">Milliliter | mL</option>
									<option value="g">Gram | g</option>
									<option value="kg">Kilo | kg</option>
									<option value="PC">Pieces | PC</option>
									<option value="oz">Ounce | oz</option>
									<option value="Pail">Pail</option>
									<option value="Drum">Drum</option>
									<option value="Case">Case</option>
							</select>
							<span class="valid-feedback" id="update_product_unit_measurementError"></span>
						  </div>
						</div>
						
						</div>
						
                    <div class="modal-footer modal-footer_form">
						
						  <button type="submit" class="btn btn-success btn-sm bi bi-save-fill form_button_icon" id="update-product"> Submit</button>
						  <button type="reset" class="btn btn-primary btn-sm bi bi-backspace-fill form_button_icon" id="clear-product"> Reset</button>
						  
					</div>
					</form><!-- End Multi Columns Form -->
                  </div>
                </div>
             </div>
			 
<div class="modal fade" id="ProductBranchPriceModal" tabindex="-1">
	
              <div class="modal-dialog modal-xl">
                  <div class="modal-content">
                    <div class="modal-header modal-header_form">
                      <h5 class="modal-title">Product Pricing per Branch</h5>
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
							<div class="fw-bold">Product</div>
							<div id="branch_product_name"></div>
						  </div>
						 
						</li>
						
						<li class="list-group-item d-flex justify-content-between align-items-start">
						  <div class="ms-2 me-auto">
							<div class="fw-bold">Default Price</div>
							<div id="branch_product_price"></div>
						  </div>
						 
						</li>
						  
						<li class="list-group-item d-flex justify-content-between align-items-start">
						  <div class="ms-2 me-auto">
							<div class="fw-bold">Unit of Measurement</div>
							<div id="branch_product_unit"></div>
						  </div>
						 
						</li>
						
					  </ol>					
					
					</div>
					<div class="col-lg-8">
								
								<table class="table" id="branch_pricing_table">
									<thead>
										<tr class='report'>
										<th style="text-align:center !important;">#</th>
										<th style="text-align:left !important;">Branch</th>
										<th style="text-align:center !important;">Price</th>
										</tr>
									</thead>
										
									<tbody id="branch_pricing_table_body_data">
										 <tr style="display: none;"><td>HIDDEN</td></tr>
									</tbody>
									
								</table>
								<div style="color:red;" id="table_paymentxError"></div>
					  
					</div>
						
					</div>
					</div>
					
                    <div class="modal-footer modal-footer_form">
						<button type="submit" class="btn btn-success btn-sm bi bi-save-fill form_button_icon" id="save-product-price-per-branch" value=""> Submit</button>
					</div>
					<!-- End Multi Columns Form -->
                  
				  </div>
                </div>
             </div>
						 
    </section>
</main>
@endsection