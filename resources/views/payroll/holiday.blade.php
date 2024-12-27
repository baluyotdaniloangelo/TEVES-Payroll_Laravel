@extends('layouts.layout')  
@section('content')  
			<!-- Page Wrapper -->
            <div class="page-wrapper">
			
				<!-- Page Content -->
                <div class="content container-fluid">
				
					<!-- Page Header -->
					<div class="page-header">
						<div class="row align-items-center">
							<div class="col">
								<h3 class="page-title">Holiday</h3>
								<ul class="breadcrumb">
									<li class="breadcrumb-item"><a href="#">Dashboard</a></li>
									<li class="breadcrumb-item active">Holiday</li>
								</ul>
							</div>
							<div class="col-auto float-end ms-auto">
								<a href="#" class="btn add-btn" data-bs-toggle="modal" data-bs-target="#holiday_details_modal" onclick="ResetHolidayForm()"><i class="fa-solid fa-plus"></i> Add Holiday</a>
								
							</div>
						</div>
					</div>
					
					</style>
					<div class="row">
						<div class="col-md-12">
							<div class="table-responsive">
								<table class="table dataTable display nowrap cell-border" id="getholidayList" width="100%" cellspacing="0">
											<thead>
												<tr>
													<th class="all">#</th>
													<th class="all">Date</th>
													<th class="all">Description</th>
													<th class="all">Type</th>
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
				<!-- /Page Content -->
				<!-- Branch Modal Modal -->
				<div id="holiday_details_modal" class="modal custom-modal fade" data-bs-backdrop="static" role="dialog">
					<div class="modal-dialog modal-dialog-centered modal-lg" role="document">
						<div class="modal-content">
							<div class="modal-header">
								<h5 class="modal-title" id="modal_title_action_holiday">Add Holiday</h5>
								<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
									<span aria-hidden="true">&times;</span>
								</button>
							</div>
							<div class="modal-body">
								 <form class="g-2 needs-validation" id="HolidayForm">
								
									<div class="row">
									
										<div class="col-sm-12">
											<div class="input-block mb-2">
												<label class="col-form-label">Date <span class="text-danger">*</span></label>
												<input class="form-control" type="date" id="holiday_date" name="holiday_date" required>
												<span class="valid-feedback" id="holiday_dateError" title="Required"></span>
											</div>
										</div>
										
										<div class="col-sm-12">
											<div class="input-block mb-2">
												<label class="col-form-label">Description <span class="text-danger">*</span></label>
												<input class="form-control" type="text" id="holiday_description" name="holiday_description" required>
												<span class="valid-feedback" id="holiday_descriptionError" title="Required"></span>
											</div>
										</div>
										
										<div class="col-sm-12">
											<div class="input-block mb-2">
												<label class="col-form-label">Type <span class="text-danger">*</span></label>
													<select class="form-select form-control" name="holiday_type" id="holiday_type">
														<option value="Regular Holiday">Regular Holiday</option>
														<option value="Special Non-Working Holiday">Special Non-Working Holiday</option>
														<option value="Special Working Holiday">Special Working Holiday</option>
													</select>
												<span class="valid-feedback" id="holiday_typeError" title="Required"></span>
											</div>
										</div>
									</div>	
										
									<div class="submit-section">
										<button class="btn btn-primary submit-btn" id="submit_holiday_details" value="0">Submit</button>
									</div>
									
								</form>
							</div>
						</div>
					</div>
				</div>
				<!-- Branch Modal  -->		
				
				
				
				<!-- Delete Branch Modal -->
				<div class="modal custom-modal fade" id="HolidayDeleteModal" role="dialog">
					<div class="modal-dialog modal-dialog-centered">
						<div class="modal-content">
							<div class="modal-body">
								<div class="form-header">
									<h3>Delete Holiday</h3>
									<p>Are you sure want to delete?</p>
								</div>
								<div class="modal-btn delete-action">
									<div class="row">
									<div class="card text-center">
								<div class="card-header border-bottom-0 pb-0">
									<span class="ms-auto shadow-lg fs-17"></span>
								</div>
								<div class="card-body pt-1">
									<!--<span class="avatar avatar-xl avatar-rounded me-2 mb-2">
										<img src="assets/img/avatar/avatar-7.jpg" alt="img">
									</span>-->
									<div class="fw-semibold fs-16"><span id="delete_holiday_complete_name"></span></div>
								</div>
							</div>
									</div>
									
									<div class="row">
										<button type="submit" class="col-6 btn btn-primary continue-btn" id="deleteHolidayConfirmed">Delete</button>
										<div class="col-6">
											<a href="javascript:void(0);" data-bs-dismiss="modal" class="btn btn-primary cancel-btn">Cancel</a>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				
				<!-- /Delete Branch Modal -->
				
				<!-- Successful Action -->
				<div id="success-alert-modal" class="modal fade" tabindex="-1" role="dialog" >
					<div class="modal-dialog modal-lg">
						<div class="modal-content modal-filled bg-success">
							<div class="modal-body p-4">
								<div class="text-center">
									<i class="dripicons-checkmark h1 text-white"></i>
										<h4 class="mt-2 text-white" id="modal_success_details">!</h4>
											<!--<p class="mt-3 text-white">Cras mattis consectetur purus sit amet fermentum. Cras justo odio, dapibus ac facilisis in, egestas eget quam.</p>-->
											<button type="button" class="btn btn-light my-2" data-bs-dismiss="modal">Continue</button>
									</div>
								</div>
								</div><!-- /.modal-content -->
					</div><!-- /.modal-dialog -->
				</div>
				
				<!-- Succeful Action End -->
				
				
				
				
				</div>
				
				
				
			<!-- /Page Wrapper -->
			


@endsection