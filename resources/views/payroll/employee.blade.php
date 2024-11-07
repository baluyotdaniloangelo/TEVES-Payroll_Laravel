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
								<h3 class="page-title">Employee</h3>
								<ul class="breadcrumb">
									<li class="breadcrumb-item"><a href="admin-dashboard.html">Dashboard</a></li>
									<li class="breadcrumb-item active">Employee</li>
								</ul>
							</div>
							<div class="col-auto float-end ms-auto">
								<a href="#" class="btn add-btn" data-bs-toggle="modal" data-bs-target="#add_employee"><i class="fa-solid fa-plus"></i> Add Employee</a>
								<!--<div class="view-icons">
									<a href="employees.html" class="grid-view btn btn-link"><i class="fa fa-th"></i></a>
									<a href="employees-list.html" class="list-view btn btn-link active"><i class="fa-solid fa-bars"></i></a>
								</div>-->
							</div>
						</div>
					</div>
					<!-- /Page Header -->
					
					<!-- Search Filter
					<div class="row filter-row">
						<div class="col-sm-6 col-md-3">  
							<div class="input-block mb-3 form-focus">
								<input type="text" class="form-control floating">
								<label class="focus-label">Employee ID</label>
							</div>
						</div>
						<div class="col-sm-6 col-md-3">  
							<div class="input-block mb-3 form-focus">
								<input type="text" class="form-control floating">
								<label class="focus-label">Employee Name</label>
							</div>
						</div>
						<div class="col-sm-6 col-md-3"> 
							<div class="input-block mb-3 form-focus select-focus">
								<select class="select floating"> 
									<option>Select Designation</option>
									<option>Web Developer</option>
									<option>Web Designer</option>
									<option>Android Developer</option>
									<option>Ios Developer</option>
								</select>
								<label class="focus-label">Designation</label>
							</div>
						</div>
						<div class="col-sm-6 col-md-3">  
							<a href="#" class="btn btn-success w-100"> Search </a>  
						</div>     
                    </div> -->
					<!-- /Search Filter -->
					<style>
					.dt-scroll-body{
						min-height:400px !important;
					}
					</style>
					<div class="row">
						<div class="col-md-12">
							<div class="table-responsive">
								<table class="table dataTable display nowrap cell-border"" id="EmployeeListDatatable" width="100%" cellspacing="0">
											<thead>
												<tr>
													<th class="all">#</th>
										 			<th class="all" title="Employee Number">Employee Number</th>
													<th class="all" title="Employee Name">Employee Name</th>
													<th class="all" title="Employee Name">Employee Name</th>
													<th class="all" title="Employee Name">Employee Name</th>
													<th class="all" title="Employee Name">Employee Name</th>
													<th>Branch</th>
													<th>Department</th>			
													<th>Position</th>				
													<th class="all">Status</th>
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
				
				<!-- Add Employee Modal -->
				<div id="add_employee" class="modal custom-modal fade" role="dialog">
					<div class="modal-dialog modal-dialog-centered modal-xl" role="document">
						<div class="modal-content">
							<div class="modal-header">
								<h5 class="modal-title">Add Employee</h5>
								<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
									<span aria-hidden="true">&times;</span>
								</button>
							</div>
							<div class="modal-body">
								 <form class="g-2 needs-validation" id="CreateCompanyform">
								<!--
								'employee_number',
								'employee_last_name',
								'employee_first_name',
								'employee_middle_name',
								'employee_extension_name',
								'employee_birthday',
								'employee_position',
								'employee_picture',
								'employee_phone',
								'employee_email',
								'branch_idx',
								'department_idx',
								'time_in',
								'break_time',
								'time_out',
								'restday_monday',
								'restday_tuesday',
								'restday_wednesday',
								'restday_thursday',
								'restday_friday',
								'restday_saturday',
								'restday_sunday',
								-->
								
									<div class="row">
										<div class="col-sm-3">
											<div class="input-block mb-3">
												<label class="col-form-label">Last Name <span class="text-danger">*</span></label>
												<input class="form-control" type="text" id="employee_last_name" name="employee_last_name" required>
											</div>
										</div>
										<div class="col-sm-3">
											<div class="input-block mb-3">
												<label class="col-form-label">First Name</label>
												<input class="form-control" type="text" id="employee_first_name" name="employee_first_name" required>
											</div>
										</div>
										<div class="col-sm-3">
											<div class="input-block mb-3">
												<label class="col-form-label">Middle Name</label>
												<input class="form-control" type="text" id="employee_middle_name" name="employee_middle_name">
											</div>
										</div>
										<div class="col-sm-3">
											<div class="input-block mb-3">
												<label class="col-form-label">Name Extention</label>
												<input class="form-control" type="text" id="employee_extension_name" name="employee_extension_name">
											</div>
										</div>
										<div class="col-sm-4">  
											<div class="input-block mb-3">
												<label class="col-form-label">Birth Date <span class="text-danger">*</span></label>
												<div class="cal-icon"><input class="form-control datetimepicker" type="text" id="employee_birthday" name="employee_birthday" required></div>
											</div>
										</div>
										<div class="col-sm-4">
											<div class="input-block mb-3">
												<label class="col-form-label">Email <span class="text-danger">*</span></label>
												<input class="form-control" type="email" id="" name="" id="employee_email" name="employee_email">
											</div>
										</div>
										<div class="col-sm-4">
											<div class="input-block mb-3">
												<label class="col-form-label">Phone </label>
												<input class="form-control" type="text" id="employee_phone" name="employee_phone">
											</div>
										</div>
										<div class="col-sm-6">  
											<div class="input-block mb-3">
												<label class="col-form-label">Employee ID <span class="text-danger">*</span></label>
												<input type="text" class="form-control" id="employee_number" name="employee_number">
											</div>
										</div>
										
										<div class="col-sm-6">  
											<div class="input-block mb-3">
												<label class="col-form-label">Position <span class="text-danger">*</span></label>
												<input type="text" class="form-control" id="employee_position" name="employee_position">
											</div>
										</div>
										<div class="col-sm-6">
											<div class="input-block mb-3">
												<label class="col-form-label">Company/Branch</label>
												<select class="select" id="branch_idx" name="branch_idx">
													<option value="">Global Technologies</option>
													<option value="1">Delta Infotech</option>
												</select>
											</div>
										</div>
										<div class="col-md-6">
											<div class="input-block mb-3">
												<label class="col-form-label">Department <span class="text-danger">*</span></label>
												<select class="select" id="department_idx" name="department_idx">
													<option>Select Department</option>
													<option>Web Development</option>
													<option>IT Management</option>
													<option>Marketing</option>
												</select>
											</div>
										</div>
										
										<div class="col-sm-4">  
											<div class="input-block mb-3">
												<label class="col-form-label">Time In <span class="text-danger">*</span></label>
												<div class="cal-icon"><input class="form-control timepicker" type="text" id="time_in" name="time_in" required></div>
											</div>
										</div>
										<div class="col-sm-4">  
											<div class="input-block mb-3">
												<label class="col-form-label">Breaktime <span class="text-danger">*</span></label>
												<div class="cal-icon"><input class="form-control timepicker" type="text" id="break_time" name="break_time" required></div>
											</div>
										</div>
										<div class="col-sm-4">  
											<div class="input-block mb-3">
												<label class="col-form-label">Time Out <span class="text-danger">*</span></label>
												<div class="cal-icon"><input class="form-control timepicker" type="text" id="time_out" name="time_out" required></div>
											</div>
										</div>
									</div>
									<div class="table-responsive m-t-15">
										<table class="table table-striped custom-table">
											<thead>
												<tr>
													<th>&nbsp;</th>
													<th class="text-center">Sunday</th>
													<th class="text-center">Monday</th>
													<th class="text-center">Tuesday</th>
													<th class="text-center">Wednesday</th>
													<th class="text-center">Thursday</th>
													<th class="text-center">Friday</th>
													<th class="text-center">Saturday</th>
												</tr>
											</thead>
											<tbody>
												<tr>
													<td>Restday</td>
													<td class="text-center">
														<label class="custom_check">
															<input type="checkbox" checked>													
															<span class="checkmark"></span>
													</label>																			
													</td>
													<td class="text-center">
														<label class="custom_check">
															<input type="checkbox" name="rememberme" class="rememberme">
															<span class="checkmark"></span>
														</label>
													</td>
													<td class="text-center">
														<label class="custom_check">
															<input type="checkbox" name="rememberme" class="rememberme">
															<span class="checkmark"></span>
														</label>
													</td>
													<td class="text-center">
														<label class="custom_check">
															<input type="checkbox" name="rememberme" class="rememberme">
															<span class="checkmark"></span>
														</label>
													</td>
													<td class="text-center">
														<label class="custom_check">
															<input type="checkbox" name="rememberme" class="rememberme">
															<span class="checkmark"></span>
														</label>
													</td>
													<td class="text-center">
														<label class="custom_check">
															<input type="checkbox" name="rememberme" class="rememberme">
															<span class="checkmark"></span>
														</label>
													</td>
													<td class="text-center">
														<label class="custom_check">
															<input type="checkbox" checked>													
															<span class="checkmark"></span>
													</label>																			
													</td>
												</tr>
											</tbody>
										</table>
									</div>
									<div class="submit-section">
										<button class="btn btn-primary submit-btn">Submit</button>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
				<!-- /Add Employee Modal -->
				
				<!-- Edit Employee Modal -->
				<div id="edit_employee" class="modal custom-modal fade" role="dialog">
					<div class="modal-dialog modal-dialog-centered modal-lg" role="document">
						<div class="modal-content">
							<div class="modal-header">
								<h5 class="modal-title">Edit Employee</h5>
								<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
									<span aria-hidden="true">&times;</span>
								</button>
							</div>
							<div class="modal-body">
								<form>
									<div class="row">
										<div class="col-sm-6">
											<div class="input-block mb-3">
												<label class="col-form-label">First Name <span class="text-danger">*</span></label>
												<input class="form-control" value="John" type="text">
											</div>
										</div>
										<div class="col-sm-6">
											<div class="input-block mb-3">
												<label class="col-form-label">Last Name</label>
												<input class="form-control" value="Doe" type="text">
											</div>
										</div>
										<div class="col-sm-6">
											<div class="input-block mb-3">
												<label class="col-form-label">Username <span class="text-danger">*</span></label>
												<input class="form-control" value="johndoe" type="text">
											</div>
										</div>
										<div class="col-sm-6">
											<div class="input-block mb-3">
												<label class="col-form-label">Email <span class="text-danger">*</span></label>
												<input class="form-control" value="johndoe@example.com" type="email">
											</div>
										</div>
										<div class="col-sm-6">
											<div class="input-block mb-3">
												<label class="col-form-label">Password</label>
												<input class="form-control" value="johndoe" type="password">
											</div>
										</div>
										<div class="col-sm-6">
											<div class="input-block mb-3">
												<label class="col-form-label">Confirm Password</label>
												<input class="form-control" value="johndoe" type="password">
											</div>
										</div>
										<div class="col-sm-6">  
											<div class="input-block mb-3">
												<label class="col-form-label">Employee ID <span class="text-danger">*</span></label>
												<input type="text" value="FT-0001" readonly class="form-control floating">
											</div>
										</div>
										<div class="col-sm-6">  
											<div class="input-block mb-3">
												<label class="col-form-label">Joining Date <span class="text-danger">*</span></label>
												<div class="cal-icon"><input class="form-control datetimepicker" type="text"></div>
											</div>
										</div>
										<div class="col-sm-6">
											<div class="input-block mb-3">
												<label class="col-form-label">Phone </label>
												<input class="form-control" value="9876543210" type="text">
											</div>
										</div>
										<div class="col-sm-6">
											<div class="input-block mb-3">
												<label class="col-form-label">Company</label>
												<select class="select">
													<option>Global Technologies</option>
													<option>Delta Infotech</option>
													<option selected>International Software Inc</option>
												</select>
											</div>
										</div>
										<div class="col-md-6">
											<div class="input-block mb-3">
												<label class="col-form-label">Department <span class="text-danger">*</span></label>
												<select class="select">
													<option>Select Department</option>
													<option>Web Development</option>
													<option>IT Management</option>
													<option>Marketing</option>
												</select>
											</div>
										</div>
										<div class="col-md-6">
											<div class="input-block mb-3">
												<label class="col-form-label">Designation <span class="text-danger">*</span></label>
												<select class="select">
													<option>Select Designation</option>
													<option>Web Designer</option>
													<option>Web Developer</option>
													<option>Android Developer</option>
												</select>
											</div>
										</div>
									</div>
									<div class="table-responsive m-t-15">
										<table class="table table-striped custom-table">
											<thead>
												<tr>
													<th>Module Permission</th>
													<th class="text-center">Read</th>
													<th class="text-center">Write</th>
													<th class="text-center">Create</th>
													<th class="text-center">Delete</th>
													<th class="text-center">Import</th>
													<th class="text-center">Export</th>
												</tr>
											</thead>
											<tbody>
												<tr>
													<td>Holidays</td>
													<td class="text-center">
														<label class="custom_check">
															<input type="checkbox" checked>													
															<span class="checkmark"></span>
													</label>																			
													</td>
													<td class="text-center">
														<label class="custom_check">
															<input type="checkbox" name="rememberme" class="rememberme">
															<span class="checkmark"></span>
														</label>
													</td>
													<td class="text-center">
														<label class="custom_check">
															<input type="checkbox" name="rememberme" class="rememberme">
															<span class="checkmark"></span>
														</label>
													</td>
													<td class="text-center">
														<label class="custom_check">
															<input type="checkbox" name="rememberme" class="rememberme">
															<span class="checkmark"></span>
														</label>
													</td>
													<td class="text-center">
														<label class="custom_check">
															<input type="checkbox" name="rememberme" class="rememberme">
															<span class="checkmark"></span>
														</label>
													</td>
													<td class="text-center">
														<label class="custom_check">
															<input type="checkbox" name="rememberme" class="rememberme">
															<span class="checkmark"></span>
														</label>
													</td>
												</tr>
												<tr>
													<td>Leaves</td>
													<td class="text-center">
														<label class="custom_check">
															<input type="checkbox" checked>													
															<span class="checkmark"></span>
													</label>																			
													</td>
													<td class="text-center">
														<label class="custom_check">
															<input type="checkbox" checked>													
															<span class="checkmark"></span>
													</label>																			
													</td>
													<td class="text-center">
														<label class="custom_check">
															<input type="checkbox" checked>													
															<span class="checkmark"></span>
													</label>																			
													</td>
													<td class="text-center">
														<label class="custom_check">
															<input type="checkbox" name="rememberme" class="rememberme">
															<span class="checkmark"></span>
														</label>
													</td>
													<td class="text-center">
														<label class="custom_check">
															<input type="checkbox" name="rememberme" class="rememberme">
															<span class="checkmark"></span>
														</label>
													</td>
													<td class="text-center">
														<label class="custom_check">
															<input type="checkbox" name="rememberme" class="rememberme">
															<span class="checkmark"></span>
														</label>
													</td>
												</tr>
												<tr>
													<td>Clients</td>
													<td class="text-center">
														<label class="custom_check">
															<input type="checkbox" checked>													
															<span class="checkmark"></span>
													</label>																			
													</td>
													<td class="text-center">
														<label class="custom_check">
															<input type="checkbox" checked>													
															<span class="checkmark"></span>
													</label>																			
													</td>
													<td class="text-center">
														<label class="custom_check">
															<input type="checkbox" checked>													
															<span class="checkmark"></span>
													</label>																			
													</td>
													<td class="text-center">
														<label class="custom_check">
															<input type="checkbox" name="rememberme" class="rememberme">
															<span class="checkmark"></span>
														</label>
													</td>
													<td class="text-center">
														<label class="custom_check">
															<input type="checkbox" name="rememberme" class="rememberme">
															<span class="checkmark"></span>
														</label>
													</td>
													<td class="text-center">
														<label class="custom_check">
															<input type="checkbox" name="rememberme" class="rememberme">
															<span class="checkmark"></span>
														</label>
													</td>
												</tr>
												<tr>
													<td>Projects</td>
													<td class="text-center">
														<label class="custom_check">
															<input type="checkbox" checked>													
															<span class="checkmark"></span>
													</label>																			
													</td>
													<td class="text-center">
														<label class="custom_check">
															<input type="checkbox" name="rememberme" class="rememberme">
															<span class="checkmark"></span>
														</label>
													</td>
													<td class="text-center">
														<label class="custom_check">
															<input type="checkbox" name="rememberme" class="rememberme">
															<span class="checkmark"></span>
														</label>
													</td>
													<td class="text-center">
														<label class="custom_check">
															<input type="checkbox" name="rememberme" class="rememberme">
															<span class="checkmark"></span>
														</label>
													</td>
													<td class="text-center">
														<label class="custom_check">
															<input type="checkbox" name="rememberme" class="rememberme">
															<span class="checkmark"></span>
														</label>
													</td>
													<td class="text-center">
														<label class="custom_check">
															<input type="checkbox" name="rememberme" class="rememberme">
															<span class="checkmark"></span>
														</label>
													</td>
												</tr>
												<tr>
													<td>Tasks</td>
													<td class="text-center">
														<label class="custom_check">
															<input type="checkbox" checked>													
															<span class="checkmark"></span>
													</label>																			
													</td>
													<td class="text-center">
														<label class="custom_check">
															<input type="checkbox" checked>													
															<span class="checkmark"></span>
													</label>																			
													</td>
													<td class="text-center">
														<label class="custom_check">
															<input type="checkbox" checked>													
															<span class="checkmark"></span>
													</label>																			
													</td>
													<td class="text-center">
														<label class="custom_check">
															<input type="checkbox" checked>													
															<span class="checkmark"></span>
													</label>																			
													</td>
													<td class="text-center">
														<label class="custom_check">
															<input type="checkbox" name="rememberme" class="rememberme">
															<span class="checkmark"></span>
														</label>
													</td>
													<td class="text-center">
														<label class="custom_check">
															<input type="checkbox" name="rememberme" class="rememberme">
															<span class="checkmark"></span>
														</label>
													</td>
												</tr>
												<tr>
													<td>Chats</td>
													<td class="text-center">
														<label class="custom_check">
															<input type="checkbox" checked>													
															<span class="checkmark"></span>
													</label>																			
													</td>
													<td class="text-center">
														<label class="custom_check">
															<input type="checkbox" checked>													
															<span class="checkmark"></span>
													</label>																			
													</td>
													<td class="text-center">
														<label class="custom_check">
															<input type="checkbox" checked>													
															<span class="checkmark"></span>
													</label>																			
													</td>
													<td class="text-center">
														<label class="custom_check">
															<input type="checkbox" checked>													
															<span class="checkmark"></span>
													</label>																			
													</td>
													<td class="text-center">
														<label class="custom_check">
															<input type="checkbox" name="rememberme" class="rememberme">
															<span class="checkmark"></span>
														</label>
													</td>
													<td class="text-center">
														<label class="custom_check">
															<input type="checkbox" name="rememberme" class="rememberme">
															<span class="checkmark"></span>
														</label>
													</td>
												</tr>
												<tr>
													<td>Assets</td>
													<td class="text-center">
														<label class="custom_check">
															<input type="checkbox" checked>													
															<span class="checkmark"></span>
													</label>																			
													</td>
													<td class="text-center">
														<label class="custom_check">
															<input type="checkbox" checked>													
															<span class="checkmark"></span>
													</label>																			
													</td>
													<td class="text-center">
														<label class="custom_check">
															<input type="checkbox" checked>													
															<span class="checkmark"></span>
													</label>																			
													</td>
													<td class="text-center">
														<label class="custom_check">
															<input type="checkbox" checked>													
															<span class="checkmark"></span>
													</label>																			
													</td>
													<td class="text-center">
														<label class="custom_check">
															<input type="checkbox" name="rememberme" class="rememberme">
															<span class="checkmark"></span>
														</label>
													</td>
													<td class="text-center">
														<label class="custom_check">
															<input type="checkbox" name="rememberme" class="rememberme">
															<span class="checkmark"></span>
														</label>
													</td>
												</tr>
												<tr>
													<td>Timing Sheets</td>
													<td class="text-center">
														<label class="custom_check">
															<input type="checkbox" checked>													
															<span class="checkmark"></span>
													</label>																			
													</td>
													<td class="text-center">
														<label class="custom_check">
															<input type="checkbox" checked>													
															<span class="checkmark"></span>
													</label>																			
													</td>
													<td class="text-center">
														<label class="custom_check">
															<input type="checkbox" checked>													
															<span class="checkmark"></span>
													</label>																			
													</td>
													<td class="text-center">
														<label class="custom_check">
															<input type="checkbox" checked>													
															<span class="checkmark"></span>
													</label>																			
													</td>
													<td class="text-center">
														<label class="custom_check">
															<input type="checkbox" name="rememberme" class="rememberme">
															<span class="checkmark"></span>
														</label>
													</td>
													<td class="text-center">
														<label class="custom_check">
															<input type="checkbox" name="rememberme" class="rememberme">
															<span class="checkmark"></span>
														</label>
													</td>
												</tr>
											</tbody>
										</table>
									</div>
									<div class="submit-section">
										<button class="btn btn-primary submit-btn">Save</button>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
				<!-- /Edit Employee Modal -->
				
				<!-- Delete Employee Modal -->
				<div class="modal custom-modal fade" id="delete_employee" role="dialog">
					<div class="modal-dialog modal-dialog-centered">
						<div class="modal-content">
							<div class="modal-body">
								<div class="form-header">
									<h3>Delete Employee</h3>
									<p>Are you sure want to delete?</p>
								</div>
								<div class="modal-btn delete-action">
									<div class="row">
										<div class="col-6">
											<a href="javascript:void(0);" class="btn btn-primary continue-btn">Delete</a>
										</div>
										<div class="col-6">
											<a href="javascript:void(0);" data-bs-dismiss="modal" class="btn btn-primary cancel-btn">Cancel</a>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<!-- /Delete Employee Modal -->
				
            </div>
			<!-- /Page Wrapper -->
			


@endsection