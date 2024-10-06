@extends('layouts.layout')  
@section('content')  

<main id="main" class="main">	
    <section class="section">	  
          <div class="card">
		  
			  <div class="card">
			  
				<div class="card-header ">
				  <h5 class="card-title">&nbsp;{{ $title }}</h5>
					<div class="d-flex justify-content-end" id="branch_option"></div>				  
				  </div>
				</div>			  
		 
            <div class="card-body">			
				<div class="p-d3">
									<div class="table-responsive">
									<table class="table dataTable display nowrap cell-border" id="getbranchList" width="100%" cellspacing="0">
											<thead>
												<tr>
													<th class="all">#</th>
													<th class="all">Branch Code</th>
													<th class="all">Branch Name</th>
													<th>Branch Initial</th>
													<th class="all">TIN</th>
													<th title='Default Value' class="none">Address:</th>
													<th class="none">Contact Number:</th>
													<th class="none">Owner:</th>
													<th class="none">Owner Position/Title:</th>
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

	<!-- Branch Delete Modal-->
    <div class="modal fade" id="BranchDeleteModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header header_modal_bg">
                    <h5 class="modal-title" id="exampleModalLabel"></h5>
 					<div class="btn-sm btn-warning btn-circle bi bi-exclamation-circle btn_icon_modal"></div>
                </div>

                <div class="modal-body warning_modal_bg" id="modal-body">
				Are you sure you want to Delete This Branch?<br>
				</div>
				<div align="left"style="margin: 10px;">
				Branch Code: <span id="confirm_delete_branch_code"></span><br>
				Branch Name: <span id="confirm_delete_branch_name"></span><br>
				TIN: <span id="confirm_delete_branch_tin"></span><br>
				</div>
                <div class="modal-footer footer_modal_bg">
                    
					<button type="button" class="btn btn-danger" data-bs-dismiss="modal" id="deleteBranchConfirmed" value=""><i class="bi bi-trash3 form_button_icon"></i> Delete</button>
					<button type="button" class="btn btn-primary" data-bs-dismiss="modal"><i class="bi bi-x-circle form_button_icon"></i> Cancel</button>
                  
                </div>
            </div>
        </div>
    </div>	
	
	<!--Modal to Create Branch-->
	<div class="modal fade" id="CreateBranchModal" tabindex="-1">
              <div class="modal-dialog modal-lg">
                  <div class="modal-content">
                    <div class="modal-header modal-header_form">
                      <h5 class="modal-title">Create Branch</h5>
					  <div class="btn-group" role="group" aria-label="Basic outlined example">	
						<button type="button" class="btn btn-danger bi bi-x-circle form_button_icon" data-bs-dismiss="modal"></button>
					  </div>
                    </div>
                    <div class="modal-body">

					  <form class="g-2 needs-validation" id="BranchformNew">
					  
						<div class="row mb-2">
						  <label for="branch_code" class="col-sm-3 col-form-label">Branch Code</label>
						  <div class="col-sm-9">
							<input type="text" class="form-control" name="branch_code" id="branch_code" value="" required>
							<span class="valid-feedback" id="branch_codeError" title="Required"></span>
						  </div>
						</div>
						
						<div class="row mb-2">
						  <label for="branch_name" class="col-sm-3 col-form-label">Branch Name</label>
						  <div class="col-sm-9">
							<input type="text" class="form-control " name="branch_name" id="branch_name" value="" required>
							<span class="valid-feedback" id="branch_nameError"></span>
						  </div>
						</div>

						<div class="row mb-2">
						  <label for="branch_name" class="col-sm-3 col-form-label">Branch Initial</label>
						  <div class="col-sm-9">
							<input type="text" class="form-control " name="branch_initial" id="branch_initial" value="" required>
							<span class="valid-feedback" id="branch_initialError"></span>
						  </div>
						</div>
						
						<div class="row mb-2">
						  <label for="branch_tin" class="col-sm-3 col-form-label">TIN</label>
						  <div class="col-sm-9">
							<input type="text" class="form-control " name="branch_tin" id="branch_tin" value="" required>
							<span class="valid-feedback" id="branch_tinError"></span>
						  </div>
						</div>

						<div class="row mb-2">
						  <label for="branch_address" class="col-sm-3 col-form-label">Address</label>
						  <div class="col-sm-9">
							<input type="text" class="form-control " name="branch_address" id="branch_address" value="">
							<span class="valid-feedback" id="branch_addressError"></span>
						  </div>
						</div>

						<div class="row mb-2">
						  <label for="branch_contact_number" class="col-sm-3 col-form-label">Contact Number</label>
						  <div class="col-sm-9">
							<input type="text" class="form-control " name="branch_contact_number" id="branch_contact_number" value="">
							<span class="valid-feedback" id="branch_contact_numberError"></span>
						  </div>
						</div>
						
						<div class="row mb-2">
						  <label for="branch_owner" class="col-sm-3 col-form-label">Owner</label>
						  <div class="col-sm-9">
							<input type="text" class="form-control " name="branch_owner" id="branch_owner" value="">
							<span class="valid-feedback" id="branch_ownerError"></span>
						  </div>
						</div>
						
						<div class="row mb-2">
						  <label for="branch_owner_title" class="col-sm-3 col-form-label">Position/Title</label>
						  <div class="col-sm-9">
							<input type="text" class="form-control " name="branch_owner_title" id="branch_owner_title" value="">
							<span class="valid-feedback" id="branch_owner_titleError"></span>
						  </div>
						</div>
						
						</div>
						
                    <div class="modal-footer modal-footer_form">
						  <button type="submit" class="btn btn-success btn-sm bi bi-save-fill form_button_icon" id="save-branch"> Submit</button>
						  <button type="reset" class="btn btn-primary btn-sm bi bi-backspace-fill form_button_icon" id="clear-branch"> Reset</button>
					</div>
					</form><!-- End Multi Columns Form -->
                  </div>
                </div>
             </div>
	
	
	<!--Modal to Upadate Branch-->
	<div class="modal fade" id="UpdateBranchModal" tabindex="-1">
              <div class="modal-dialog modal-lg">
                  <div class="modal-content">
                    <div class="modal-header modal-header_form">
                      <h5 class="modal-title">Update Branch</h5>
					  <div class="btn-group" role="group" aria-label="Basic outlined example">		
						<button type="button" class="btn btn-danger bi bi-x-circle form_button_icon" data-bs-dismiss="modal"></button>
					  </div>
                    </div>
                    <div class="modal-body">
					
					  <form class="g-2 needs-validation" id="BranchformEdit">
					  
						<div class="row mb-2">
						  <label for="update_branch_code" class="col-sm-3 col-form-label">Branch Code</label>
						  <div class="col-sm-9">
							<input type="text" class="form-control" name="update_branch_code" id="update_branch_code" value="" required>
							<span class="valid-feedback" id="update_branch_codeError" title="Required"></span>
						  </div>
						</div>
						
						<div class="row mb-2">
						  <label for="update_branch_name" class="col-sm-3 col-form-label">Branch Name</label>
						  <div class="col-sm-9">
							<input type="text" class="form-control " name="update_branch_name" id="update_branch_name" value="" required>
							<span class="valid-feedback" id="update_branch_nameError"></span>
						  </div>
						</div>
						
						<div class="row mb-2">
						  <label for="branch_name" class="col-sm-3 col-form-label">Branch Initial</label>
						  <div class="col-sm-9">
							<input type="text" class="form-control " name="update_branch_initial" id="update_branch_initial" value="" required>
							<span class="valid-feedback" id="update_branch_initialError"></span>
						  </div>
						</div>
						
						<div class="row mb-2">
						  <label for="update_branch_tin" class="col-sm-3 col-form-label">TIN</label>
						  <div class="col-sm-9">
							<input type="text" class="form-control " name="update_branch_tin" id="update_branch_tin" value="" required>
							<span class="valid-feedback" id="update_branch_tinError"></span>
						  </div>
						</div>						
								
						<div class="row mb-2">
						  <label for="update_branch_address" class="col-sm-3 col-form-label">Address</label>
						  <div class="col-sm-9">
							<input type="text" class="form-control " name="update_branch_address" id="update_branch_address" value="">
							<span class="valid-feedback" id="update_branch_addressError"></span>
						  </div>
						</div>

						<div class="row mb-2">
						  <label for="update_branch_contact_number" class="col-sm-3 col-form-label">Contract Number</label>
						  <div class="col-sm-9">
							<input type="text" class="form-control " name="update_branch_contact_number" id="update_branch_contact_number" value="">
							<span class="valid-feedback" id="update_branch_contact_numberError"></span>
						  </div>
						</div>
						
						<div class="row mb-2">
						  <label for="update_branch_owner" class="col-sm-3 col-form-label">Owner</label>
						  <div class="col-sm-9">
							<input type="text" class="form-control " name="update_branch_owner" id="update_branch_owner" value="">
							<span class="valid-feedback" id="update_branch_ownerError"></span>
						  </div>
						</div>
						
						<div class="row mb-2">
						  <label for="update_branch_owner_title" class="col-sm-3 col-form-label">Position/Title</label>
						  <div class="col-sm-9">
							<input type="text" class="form-control " name="update_branch_owner_title" id="update_branch_owner_title" value="">
							<span class="valid-feedback" id="update_branch_owner_titleError"></span>
						  </div>
						</div>
								
						</div>
						
                    <div class="modal-footer modal-footer_form">
						
						  <button type="submit" class="btn btn-success btn-sm bi bi-save-fill form_button_icon" id="update-branch"> Submit</button>
						    
					</div>
					</form><!-- End Multi Columns Form -->
                  </div>
                </div>
             </div>
	

    </section>
</main>

@endsection