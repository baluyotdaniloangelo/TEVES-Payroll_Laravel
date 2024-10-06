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
									<table class="table dataTable display nowrap cell-border" id="getPurchaseOrderList" width="100%" cellspacing="0">
											<thead>
												<tr>
													<th class="all">#</th>
													<th class="all">Date</th>
													<th class="all">Control Number</th>
													<th class="all">Supplier</th>
													<th class="all">Sales Order #</th>
													<th class="none">Total Payable</th>
													<th class="all">Delivery Status</th>
													<th class="all">Payment Status</th>
													<th class="all">Action</th>
												</tr>
											</thead>				
											
											<tbody>
												
											</tbody>
											
											
										</table>
									</div>		
				</div>									
                   
            </div>
          </div>


	<!--Modal to Create Purchase Order-->
	<div class="modal fade" id="CreatePurchaseOrderModal" tabindex="-1">
              <div class="modal-dialog">
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
										<div class="col-md-12">
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

										<div class="col-md-12">
										  <label for="purchase_order_date" class="form-label">Date</label>
										  <input type="date" class="form-control" id="purchase_order_date" name="purchase_order_date" value="<?=date('Y-m-d');?>">
										</div>
										
									</div>

									<hr>
									
									<div class="row">

										<div class="col-md-12">
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
											<div class="col-md-12">
												<label for="purchase_order_net_percentage" class="form-label">Net Value</label>
												<input type="number" class="form-control" id="purchase_order_net_percentage" name="purchase_order_net_percentage">
											</div>
									</div>
									
									<hr>
									
									<div class="row">
											<div class="col-md-12">
												<label for="purchase_order_less_percentage" class="form-label">Less Value</label>
												<input type="number" class="form-control" id="purchase_order_less_percentage" name="purchase_order_less_percentage">
											</div>
									</div>
								
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

	<!-- Sales Order Delete Modal-->
    <div class="modal fade" id="PurchaseOrderDeleteModal" tabindex="-1" role="dialog" aria-hidden="true">
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

	<!-- View Payment Gallery Modal-->
    <div class="modal fade" id="ViewPaymentGalery" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content" style="height: 900px;">
                <div class="modal-header header_modal_bg">
                    <h5 class="modal-title" id="exampleModalLabel">Payment Information</h5>
 					<button type="button" class="btn btn-danger bi bi-x-circle form_button_icon" data-bs-dismiss="modal"></button>
                </div>
				
				<br>
				
					<div id="carouselExampleCaptions" class="carousel slide" data-bs-ride="carousel" align="center">
					
					<div class="carousel-indicators">
				
					</div>
					
					<div class="carousel-inner" style="height: 700px;">
					 
					</div>
					
					
					<button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="prev">
					  <span class="carousel-control-prev-icon" aria-hidden="true" style="background-color: magenta; border-radius: 15px;"></span>
					  <span class="visually-hidden" >Previous</span>
					</button>
					<button class="carousel-control-next" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="next">
					  <span class="carousel-control-next-icon" aria-hidden="true" style="background-color: magenta; border-radius: 15px;"></span>
					  <span class="visually-hidden">Next</span>
					</button>

					</div>
					
				<br>
				
            </div>
        </div>
    </div>	

  </section>
</main>
@endsection