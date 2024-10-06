@extends('layouts.layout')  
@section('content')  

<main id="main" class="main">	

    <section class="section">
	
		<div class="card">
		
            <div class="card-body">
			
				<h5 class="card-title" align="center">{{ $title }}</h5>
				<div class="row mb-2">
					<div class="col-sm-6">
					
					</div>
					<div class="col-sm-6">
						<div class="d-flex justify-content-end" id="">
							<div class="btn-group" role="group" aria-label="Basic outlined example" style="margin-top: -35px; position: absolute;">
								<a class="btn btn-secondary new_item bi bi-chevron-double-left form_button_icon" href="{{ route('product') }}" title="Back">  
								  <span title="Back to Sales Order List">Back</span>
								</a>
				
							</div>					
						</div>
						
					</div>
				</div>
				
				<hr>
				
				<div class="row mb-2">
				
					<div class="col-sm-4">
						
						<form class="g-3 needs-validation" id="ProductformEdit">
							<div class="row mb-2">
						  <label for="update_product_name" class="col-sm-4 col-form-label">Product Name</label>
						  <div class="col-sm-8">
							<input type="text" class="form-control" name="update_product_name" id="update_product_name" value="" required>
							<span class="valid-feedback" id="update_product_nameError" title="Required"></span>
						  </div>
						</div>
						
						<div class="row mb-2">
						  <label for="update_product_price" class="col-sm-4 col-form-label">Default Price</label>
						  <div class="col-sm-8">
							<input type="number" class="form-control " name="update_product_price" id="update_product_price" value="" required step=".01">
							<span class="valid-feedback" id="update_product_priceError"></span>
						  </div>
						</div>					
								
						<div class="row mb-2">
						  <label for="update_product_unit_measurement" class="col-sm-4 col-form-label">Unit of Measurement</label>
						  <div class="col-sm-8">
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
						
						<div class="row mb-3">
						
							<div class="col-sm-6" align='right'>
								<div id="loading_data_update_so" style="display:none;">
									<div class="spinner-border text-success" role="status">
										<span class="visually-hidden">Loading...</span>
									</div>
								</div>
							</div>
							
							<div class="col-sm-6" align='right'>
								<button type="submit" class="btn btn-success btn-sm bi bi-save-fill form_button_icon" id="update-product"> Update</button>
							</div>
							
						</div>
						
						</form>
						
					</div>
					
					<div class="col-sm-8">
					
					    <ul class="nav nav-tabs" id="myTab" role="tablist">
							<!-- nav-link active -->
							<li class="nav-item" role="presentation">
								<button class="nav-link <?php if($tab=='branchprice') { echo 'active'; } ?>" id="branchprice-tab" data-bs-toggle="tab" data-bs-target="#branchprice" type="button" role="tab" aria-controls="branchprice" aria-selected="true" onclick="loadPricingListPerBranch()" title='Product Prices per branch'>Price per Branch</button>
							</li>
									
							<li class="nav-item" role="presentation">
								<button class="nav-link <?php if($tab=='tank') { echo 'active'; } ?>" id="tank-tab" data-bs-toggle="tab" data-bs-target="#tank" type="button" role="tab" aria-controls="tank" aria-selected="false" tabindex="-1" onclick="LoadProductTank()" title='Tank Information'>Tank</button>
							</li>
									
						</ul>
					
						<div class="tab-content pt-2" id="myTabContent">
						
							<div class="tab-pane fade  <?php if($tab=='branchprice') { echo ' show active'; } ?>"" id="branchprice" role="tabpanel" aria-labelledby="branchprice-tab">
									
									<table class="table table-striped" id="">
									<thead>
									<tr class='report'>
										<th style="text-align:center !important;">Item #</th>
										<th style="text-align:center !important;">Branch</th>
										<th style="text-align:center !important;">Price</th>
									</tr>
									</thead>
										<tbody id="branch_pricing_table_body_data">
												<tr style="display: none;">
													<td>HIDDEN</td>
												</tr>
										</tbody>
									</table>
									<div class="row mb-3">
									<div class="col-sm-6" align='right'>
								<div id="loading_data_update_so" style="display:none;">
									<div class="spinner-border text-success" role="status">
										<span class="visually-hidden">Loading...</span>
									</div>
								</div>
							</div>
							
							<div class="col-sm-6" align='right'>
									<button type="submit" class="btn btn-success btn-sm bi bi-save-fill form_button_icon" id="save-product-price-per-branch" value=""> Save</button>
							</div>	
							
							</div>

							</div>
					
						</div>

						<div class="tab-content pt-2" id="myTabContent">
						
							<div class="tab-pane fade  <?php if($tab=='tank') { echo ' show active'; } ?>"" id="tank" role="tabpanel" aria-labelledby="tank-tab">
									
									<div class="d-flex justify-content-end" id="">
									<div class="btn-group" role="group" aria-label="Basic outlined example" style="margin-top: -58px; position: absolute;">
										<button type="button" class="btn btn-success new_item bi bi-plus-circle form_button_icon" data-bs-toggle="modal" data-bs-target="#AddProductTankModal" id="AddProductTankBTN"></button>
									</div>											
									</div>
									
									<table class="table table-striped" id="">
									<thead>
									<tr class='report'>
										<th style="text-align:center !important;" class="action_column_class">Action</th>
										<th style="text-align:center !important;">Item #</th>
										<th style="text-align:center !important;">Branch</th>
										<th style="text-align:center !important;">Name</th>
										<th style="text-align:center !important;">Capacity</th>
									</tr>
									</thead>
										<tbody id="branch_tank_table_body_data">
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
	
	<!--Moda Product Tank-->
	<div class="modal fade" id="AddProductTankModal" tabindex="-1">
              <div class="modal-dialog modal-lg">
                  <div class="modal-content">
                    <div class="modal-header modal-header_form">
                      <h5 class="modal-title">Add Tank</h5>
					  <div class="btn-group" role="group" aria-label="Basic outlined example">		
						
						<button type="button" class="btn btn-danger bi bi-x-circle form_button_icon" data-bs-dismiss="modal"></button>
					  </div>
                    </div>
                    <div class="modal-body">
					
					  <form class="g-3 needs-validation" id="AddProductTank">
						<div class="row mb-2">
						<div class="col-sm-12">
						
							<div class="form-floating mb-3">
								<select class="form-select form-control" required="" name="branch_idx" id="branch_idx">
									@foreach ($teves_branch as $teves_branch_cols)
											<?php 
											$branch_id = $teves_branch_cols->branch_id;
													
											?>
										<option value="{{$teves_branch_cols->branch_id}}">
												{{$teves_branch_cols->branch_code}}
										</option>
													
									@endforeach
								</select>
								<label for="branch_idx">Branch</label>
								 <span class="valid-feedback" id="branch_idxError"></span>
							</div>
							
						</div>
						</div>


						<div class="row mb-2">
						
						<div class="col-sm-12">
						
						<div class="form-floating mb-3">
						  <input class="form-control" name="tank_name" id="tank_name" required autocomplete="off" placeholder="Date">
						  <label for="tank_name">Name</label>
						  <span class="valid-feedback" id="tank_nameError"></span>
						 </div>
						
						
						</div>
						
						</div>
						
						<div class="row mb-2">
						
						<div class="col-sm-12">
						
							<div class="form-floating mb-3">
								<input type="number" class="form-control" aria-label="" name="tank_capacity" id="tank_capacity" value="" step=".01" placeholder="Capacity">
								<label for="tank_capacity">Capacity</label>
								<span class="valid-feedback" id="tank_capacityError"></span>
							</div>
							
							
						</div>
						</div>
						
						
						</div>
						
                    <div class="modal-footer modal-footer_form">
							<div id="loading_data_add_product" style="display:none;">
							<div class="spinner-border text-success" role="status">
								<span class="visually-hidden">Loading...</span>
							</div>
							</div>
						  <button type="submit" class="btn btn-success btn-sm bi bi-save-fill form_button_icon" id="save-tank"> Save</button>
						  <button type="reset" class="btn btn-primary btn-sm bi bi-backspace-fill form_button_icon" id="clear-save-tank"> Reset</button>					  
					</div>
					</form><!-- End Multi Columns Form -->
                  </div>
				  
                </div>
             </div>	

	<div class="modal fade" id="UpdateProductTankModal" tabindex="-1">
              <div class="modal-dialog modal-lg">
                  <div class="modal-content">
                    <div class="modal-header modal-header_form">
                      <h5 class="modal-title">Add Tank</h5>
					  <div class="btn-group" role="group" aria-label="Basic outlined example">		
						
						<button type="button" class="btn btn-danger bi bi-x-circle form_button_icon" data-bs-dismiss="modal"></button>
					  </div>
                    </div>
                    <div class="modal-body">
					
					  <form class="g-3 needs-validation" id="UpdateProductTank">
						<div class="row mb-2">
						<div class="col-sm-12">
						
							<div class="form-floating mb-3">
								<select class="form-select form-control" required="" name="update_branch_idx" id="update_branch_idx">
									@foreach ($teves_branch as $teves_branch_cols)
											<?php 
											$branch_id = $teves_branch_cols->branch_id;
													
											?>
										<option value="{{$teves_branch_cols->branch_id}}">
												{{$teves_branch_cols->branch_code}}
										</option>
													
									@endforeach
								</select>
								<label for="update_branch_idx">Branch</label>
							</div>
							 <span class="valid-feedback" id="update_branch_idxError"></span>
						</div>
						</div>


						<div class="row mb-2">
						
						<div class="col-sm-12">
						
						<div class="form-floating mb-3">
						  <input class="form-control" name="update_tank_name" id="update_tank_name" required autocomplete="off" placeholder="Date">
						  <label for="tank_name">Name</label>
						 </div>
						<span class="valid-feedback" id="update_tank_nameError"></span>
						
						</div>
						
						</div>
						
						<div class="row mb-2">
						
						<div class="col-sm-12">
						
							<div class="form-floating mb-3">
								<input type="number" class="form-control" aria-label="" name="update_tank_capacity" id="update_tank_capacity" value="" step=".01" placeholder="Capacity">
								<label for="update_tank_capacity">Capacity</label>
							</div>
							<span class="valid-feedback" id="update_tank_capacityError"></span>
							
						</div>
						</div>
						
						
						</div>
						
                    <div class="modal-footer modal-footer_form">
							<div id="loading_data_update_product" style="display:none;">
							<div class="spinner-border text-success" role="status">
								<span class="visually-hidden">Loading...</span>
							</div>
							</div>
						  <button type="submit" class="btn btn-success btn-sm bi bi-save-fill form_button_icon" id="update-tank"> Update</button>
						  <button type="reset" class="btn btn-primary btn-sm bi bi-backspace-fill form_button_icon" id="clear-update-tank"> Reset</button>					  
					</div>
					</form><!-- End Multi Columns Form -->
                  </div>
				  
                </div>
             </div>	


	<!-- Bill Delete Modal-->
    <div class="modal fade" id="TankInfoDeleteModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header header_modal_bg">
                    <h5 class="modal-title" id="exampleModalLabel"></h5>
 					<div class="btn-sm btn-warning btn-circle bi bi-exclamation-circle btn_icon_modal"></div>
                </div>
				
				
                <div class="modal-body warning_modal_bg" id="modal-body">
				Are you sure you want to Delete This Tank?<br>
				</div>
				<div align="left"style="margin: 10px;">
				
				Name: <span id="delete_tank_name"></span><br>
				Capacity: <span id="delete_tank_capacity"></span><br>
				
				</div>
                <div class="modal-footer footer_modal_bg">
					<button type="button" class="btn btn-danger" data-bs-dismiss="modal" id="deleteTankInfoConfirmed" value=""><i class="bi bi-trash3 form_button_icon"></i> Delete</button>
					<button type="button" class="btn btn-primary" data-bs-dismiss="modal"><i class="bi bi-x-circle form_button_icon"></i> Cancel</button>
                  
                </div>
            </div>
        </div>
    </div>
			 			 
    </section>
</main>


											

@endsection

