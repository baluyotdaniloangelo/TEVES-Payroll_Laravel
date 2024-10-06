@extends('layouts.app')
@section('content')

    <div class="container">

      <section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
        <div class="container">
          <div class="row justify-content-center">
            <div class="col-lg-6 col-md-6 d-flex flex-column align-items-center justify-content-center">


              <div class="card mb-3">

                <div class="card-body">

                  <div class="pt-4 pb-0">
				  
				  <div class="d-flex justify-content-center py-4">
					<img src="{{asset('client_logo/logo-r.png')}}" alt="" style="width:120px;">
				  </div>
                  
				  <h5 class="card-title text-center pb-0 fs-6" style="font-weight:bold !important; padding:0px !important; color: #00000;">Centralized Automated Meter Reading</h5>
                  </div>
							
									
				  <form class="g-2 needs-validation" id="ResetUserPasswordform">
                    					
                    <div class="col-12">
                      <p>Please Enter your Email Address Registered to your CAMR User Account</p>
					  
                      
                    </div>

                    <div class="row mb-2">
						
						<div class="col-sm-12">
							<div class="form-floating mb-3">
							  <input type="email" class="form-control " name="user_email_address" id="user_email_address" value="" required placeholder="Email Address">
							  <label for="meter_multiplier" class="col-sm-3 col-form-label">Email Address</label>
							  <span class="valid-feedback" id="meter_multiplierError"></span>
							 </div>
						</div>
						
					</div>
					
                    <div class="col-12">
					
					
					<button class="btn btn-primary w-100" type="submit" id="check-email">Send</button>
                      
                    </div>
					<br>
					<div class="col-12">
                      <div align="center"><p class="small mb-0">Back to <a href="/">Login</a></p></div>
                    </div>
                  </form>
					
                </div>
              </div>

         
            </div>
          </div>
        </div>

      </section>
	<!-- Success Modal-->
    <div class="modal fade" id="SuccessModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header header_modal_bg">
                    <h5 class="modal-title" id="exampleModalLabel"></h5>
 					<div class="btn-sm btn-warning btn-circle bi bi-check2-circle btn_icon_modal"></div>
                </div>
                <div class="modal-body success_modal_bg" id="modal-body">
				&nbsp;
				</div>
                <div class="modal-footer footer_modal_bg">
					<button type="button" class="btn btn-primary" data-bs-dismiss="modal" id="close_email_confirm"><i class="bi bi-x-circle navbar_icon"></i> Close</button>
                </div>
            </div>
        </div>
    </div>
    </div>
   <!-- Bootstrap core JavaScript-->
   <script src="{{asset('/jquery/jquery-3.6.0.min.js')}}"></script>
   <script type="text/javascript">
	<!--Save New Site-->
	$("#check-email").click(function(event){
			
			event.preventDefault();
			
					/*Reset Warnings*/			
					$('#user_email_addressError').text('');		

			document.getElementById('ResetUserPasswordform').className = "g-3 needs-validation was-validated";
			
			let user_email_address 		= $("input[name=user_email_address]").val();
			
			  $.ajax({
				url: "/reset-password",
				type:"POST",
				data:{
				  user_email_address:user_email_address,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				 
				  if(response) {

					$('#user_email_addressError').text('');		
					
					$('.success_modal_bg').html(response.success);
					$('#SuccessModal').modal('toggle');	
				
					document.getElementById("ResetUserPasswordform").reset();
					
					document.getElementById('ResetUserPasswordform').className = "g-3 needs-validation";
					
					// sleep(5000);
					//window.location.href = "/";
				
				  }
				},
				error: function(error) {
				 console.log(error);	
				 
				if(error.responseJSON.errors.user_real_name=="The user real name has already been taken."){
							  
				  $('#user_email_addressError').html("<b>"+ user_real_name +"</b> has already been taken.");
				  document.getElementById('user_email_addressError').className = "invalid-feedback";
				  document.getElementById('user_real_name').className = "form-control is-invalid";
				  $('#user_real_name').val("");
				  
				}else{
					
				  $('#user_email_addressError').text(error.responseJSON.errors.user_real_name);
				  document.getElementById('user_email_addressError').className = "invalid-feedback";		
				
				}	
				  
				  $('#InvalidModal').modal('toggle');				
				  
				}
			   });
	  });

	$("#close_email_confirm").click(function(event){
			
			event.preventDefault();
			window.location.href = "/";
			
	  });	  
// function sleep(delay) {
    // var start = new Date().getTime();
    // while (new Date().getTime() < start + delay);
// }
</script>
@endsection