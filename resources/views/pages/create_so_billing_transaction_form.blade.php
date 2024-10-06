@extends('layouts.layout')  
@section('content')  

<main id="main" class="main">	
    <section class="section">	  
          <div class="card">
		  
			  <div class="card">
			  
				<div class="card-header ">
				  <h5 class="card-title">&nbsp;{{ $title }}</h5>
					<div class="d-flex justify-content-end" id="billing_option"></div>				  
				  </div>
				</div>			  
		 
            <div class="card-body">			
				<div class="p-3">
								
	<!--Part 1-->
	<div class="row mb-2">
		<div class="col-lg-4">
						<form class="g-3 needs-validation" id="SOBillingformUpdate">
							  
								<div class="row mb-2">
								  <label for="order_date" class="col-sm-3 col-form-label">Date</label>
								  <div class="col-sm-9">
									<input type="date" class="form-control" name="so_order_date" id="so_order_date" value="<?=date('Y-m-d');?>" required>
									<span class="valid-feedback" id="so_order_dateError" title="Required"></span>
								  </div>
								</div>
								
								<div class="row mb-2">
								  <label for="order_time" class="col-sm-3 col-form-label">Time</label>
								  <div class="col-sm-9">
									<input type="time" class="form-control " name="so_order_time" id="so_order_time" value="<?=date('H:i');?>" required>
									<span class="valid-feedback" id="so_order_timeError"></span>
								  </div>
								</div>	
								
								<div class="row mb-2">
								  <label for="order_po_number" class="col-sm-3 col-form-label">SO Number</label>
								  <div class="col-sm-9">
									<input type="text" class="form-control " name="so_order_po_number" id="so_order_po_number" value="" required>
									<span class="valid-feedback" id="so_order_po_numberError"></span>
								  </div>
								</div>
								
								<div class="row mb-2">
								  <label for="client_idx" class="col-sm-3 col-form-label">Client</label>
								  <div class="col-sm-9">
									<input class="form-control" list="so_client_name" name="so_client_name" id="so_client_idx" required autocomplete="off" value="">
										<datalist id="so_client_name">
											@foreach ($client_data as $client_data_cols)
												<option label="{{$client_data_cols->client_name}}" data-id="{{$client_data_cols->client_id}}" value="{{$client_data_cols->client_name}}">
											@endforeach
										</datalist>
									<span class="valid-feedback" id="so_client_idxError"></span>
								  </div>
								</div>
								
								<div class="row mb-2">
								  <label for="plate_no" class="col-sm-3 col-form-label">Plate Number</label>
								  <div class="col-sm-9">
									<input type="text" class="form-control " name="so_plate_no" id="so_plate_no" value="" required list="so_plate_no_list">
									<datalist id="so_plate_no_list">
										@foreach ($plate_no as $plate_no_cols)
											<option value="{{$plate_no_cols->plate_no}}">
										@endforeach
									  </datalist>
									<span class="valid-feedback" id="plate_noError"></span>
								  </div>
								</div>	
								
								<div class="row mb-2">
								
								  <label for="drivers_name" class="col-sm-3 col-form-label">Drivers Name</label>
								  <div class="col-sm-9">
									<input type="text" class="form-control " name="so_drivers_name" id="so_drivers_name" value="" required list="so_drivers_list">
									<datalist id="drivers_list">
										@foreach ($drivers_name as $drivers_name_cols)
											<option value="{{$drivers_name_cols->drivers_name}}">
										@endforeach
									  </datalist>
									<span class="valid-feedback" id="drivers_nameError"></span>
								  </div>
								  
								</div>
								
							<div class="row mb-3">
							  <label class="col-sm-3 col-form-label"></label>
							  <div class="col-sm-9" align='right'>
							  <div id="loading_data" style="display:none;">
										<div class="spinner-border text-success" role="status">
											<span class="visually-hidden">Loading...</span>
										</div>
										</div>
								<button type="submit" class="btn btn-success btn-sm bi bi-save-fill form_button_icon" id="update-so-billing-transaction" title='Update SO information'> Update</button>
							  </div>
							</div>
							
			</form>
	</div>
								
				</div>									         
            </div>
          </div>

    </section>
</main>


@endsection

