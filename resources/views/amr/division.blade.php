@extends('layouts.layout')  
@section('content')  

<main id="main" class="main">	
    <section class="section">
	
          <div class="card">
		  
			  <div class="card card-12-btm">
			  
				<div class="card-header" style="text-align:center;">
                             
				  
				  <div class="row">
					
						  <div class="col-sm-12">
						  
						  <h5 class="card-title bi bi-person-lines-fill">&nbsp;{{ $title }}</h5>    
						  <!--OPTIONS HERE-->
							<div class="d-flex justify-content-end" id="division_option"></div>
						  </div>
						  
						  
						</div>
				  
				  </div>
				</div>			  
		 
            <div class="card-body">
				<div class="row">
				<div class="p-1" align="">
									<div class="table-responsive">
										<table class="table dataTable display nowrap cell-border" id="divisionList" width="100%" cellspacing="0">
											<thead>
												<tr>
													<th class="all">#</th>
													<th nowrap class="all">Division Code</th>
													<th class="all">Division Name</th>
													<th>Date Created</th>
													<th>Date Updated</th>	
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
          </div>

	<!-- Switch Delete Modal-->
    <div class="modal fade" id="DivisionDeleteModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header header_modal_bg">
                    <h5 class="modal-title" id="exampleModalLabel"></h5>
 					<div class="btn-sm btn-warning btn-circle bi bi-exclamation-circle btn_icon_modal"></div>
                </div>
                <div class="modal-body warning_modal_bg" id="modal-body">
				Are you sure you want to delete this Division?</div>
				<div style="margin:10px;">
					Division Name: <span id="division_name_info_confirm"></span><br>
				</div>
                <div class="modal-footer footer_modal_bg">         
					<button type="button" class="btn btn-danger" data-bs-dismiss="modal" id="deleteDivisionConfirmed" value=""><i class="bi bi-trash3 form_button_icon"></i>Delete</button>
					<button type="button" class="btn btn-primary" data-bs-dismiss="modal"><i class="bi bi-x-circle form_button_icon"></i>Cancel</button>
                </div>
            </div>
        </div>
    </div>	

	<!--Modal to Create Division-->
	<div class="modal fade" id="CreateDivisionModal" tabindex="-1">
           <div class="modal-dialog modal-lg">
                  <div class="modal-content">
                    <div class="modal-header modal-header_form">
                      <h5 class="modal-title">Create Division</h5>
					  <div class="btn-group" role="group" aria-label="Basic outlined example">		
						<button type="button" class="btn btn-danger bi bi-x-circle form_button_icon" data-bs-dismiss="modal"></button>
					  </div>
                    </div>
                    <div class="modal-body">

					  <form class="g-2 needs-validation" id="CreateDivisionform">
						
						<div class="row mb-2">
						  <label for="division_code" class="col-sm-3 col-form-label" title="Division Code">Code:</label>
						  <div class="col-sm-9">
							<input type="text" class="form-control" name="division_code" id="division_code" value="" required>
							<span class="valid-feedback" id="division_codeError" title="Required"></span>
						  </div>
						</div>
					  
						<div class="row mb-2">
						  <label for="division_name" class="col-sm-3 col-form-label" title="Division Name">Name:</label>
						  <div class="col-sm-9">
							<input type="text" class="form-control" name="division_name" id="division_name" value="" required>
							<span class="valid-feedback" id="division_nameError" title="Required"></span>
						  </div>
						</div>
						
						</div>
						
                    <div class="modal-footer modal-footer_form">
						
						  <button type="submit" class="btn btn-success btn-sm bi bi-save-fill form_button_icon" id="save-division"> Submit</button>
						  <button type="submit" class="btn btn-primary btn-sm bi bi-backspace-fill form_button_icon" id="clear-division" onclick="ResetFormDivision()"> Reset</button>
						  
					</div>
					</form><!-- End Multi Columns Form -->
                  </div>
                </div>
                  </div>
	
	<!--Modal to Create Division-->
	<div class="modal fade" id="UpdateDivisionModal" tabindex="-1">
           <div class="modal-dialog modal-lg">
                  <div class="modal-content">
                    <div class="modal-header modal-header_form">
                      <h5 class="modal-title">Update Division</h5>
					  <div class="btn-group" role="group" aria-label="Basic outlined example">		
						<button type="button" class="btn btn-danger bi bi-x-circle form_button_icon" data-bs-dismiss="modal"></button>
					  </div>
                    </div>
                    <div class="modal-body">

					  <form class="g-2 needs-validation" id="UpdateDivisionform">
						
						<div class="row mb-2">
						  <label for="update_division_code" class="col-sm-3 col-form-label" title="Division Code">Code:</label>
						  <div class="col-sm-9">
							<input type="text" class="form-control" name="update_division_code" id="update_division_code" value="" required>
							<span class="valid-feedback" id="update_division_codeError" title="Required"></span>
						  </div>
						</div>
					  
						<div class="row mb-2">
						  <label for="update_division_name" class="col-sm-3 col-form-label">Division Name:</label>
						  <div class="col-sm-9">
							<input type="text" class="form-control " name="update_division_name" id="update_division_name" value="" required>
							<span class="valid-feedback" id="update_division_nameError"></span>
						  </div>
						</div>
						
						</div>
						
                    <div class="modal-footer modal-footer_form">
						
						  <button type="submit" class="btn btn-success btn-sm bi bi-save-fill form_button_icon" id="update-division"> Submit</button>
						  
						  
					</div>
					</form><!-- End Multi Columns Form -->
                  </div>
                </div>
            </div>
	
		<!--Modal to Create Division-->
	<div class="modal fade" id="SiteDivisionAccessModal" tabindex="-1">
           <div class="modal-dialog modal-xl">
                  <div class="modal-content">
				  
                    <div class="modal-header modal-header_form">
                      <h5 class="modal-title">Division Site Access</h5>
					  
					  <div class="btn-group" role="group" aria-label="Basic outlined example">		
						<button type="button" class="btn btn-danger bi bi-x-circle form_button_icon" data-bs-dismiss="modal"></button>
					  </div>
					  
                    </div>
					
                    <div class="modal-body">
						<div class="row mb-3">
						
							<div class="col-sm-4">
							
									<ol class="list-group list-group-numbered">
									<li class="list-group-item d-flex justify-content-between align-items-start">
									  <div class="ms-2 me-auto">
										<div class="fw-bold">Name</div>
										<span id="division_real_name_info_site_access"></span>
									  </div>
									 
									</li>
									<li class="list-group-item d-flex justify-content-between align-items-start">
									  <div class="ms-2 me-auto">
										<div class="fw-bold">Divisionname</div>
										<span id="division_name_info_site_access"></span>
									  </div>
									 
									</li>
									<li class="list-group-item d-flex justify-content-between align-items-start">
									  <div class="ms-2 me-auto">
										<div class="fw-bold">Division Type</div>
										<span id="division_type_info_site_access"></span>
									  </div>
									  
									</li>
								  </ol>
							  
							</div>
							
							<div class="col-sm-8">
							
									<div class="table-responsive">
										<table class="table table-bordered dataTable" id="DivisionSiteAccessList" width="100%" cellspacing="0">
											<thead>
												<tr>
													<th class="all"></th>
													<th class="all">#</th>
													<th class="all">Site Name</th>
													<th class="all">Site Code</th>
													<th class="all">Business Entity</th>
												</tr>
											</thead>				
											
											<tbody>
												
											</tbody>
											
											<tfoot>
												<tr>
													<th class="all"></th>
													<th class="all">#</th>
													<th class="all">Site Name</th>
													<th class="all">Site Code</th>
													<th class="all">Business Entity</th>
												</tr>
											</tfoot>
											
										</table>
										
									</div>
							</div>		
	
                  </div>
				  
					
					
                </div>
				
					<div class="modal-footer modal-footer_form">
						
						  <button type="submit" class="btn btn-success btn-sm bi bi-save-fill form_button_icon" id="update-division-site-access"> Submit</button>
						  <!--<button type="reset" class="btn btn-primary btn-sm bi bi-backspace-fill form_button_icon" id="update-clear-division"> Clear Form</button>-->
						  
				    </div>
            </div>
	
    </section>
</main>


@endsection

