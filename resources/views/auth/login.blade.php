@extends('layouts.app')
@section('content')

    <div class="container">

      <section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
        <div class="container">
          <div class="row justify-content-center">
            <div class="col-lg-4 col-md-6 d-flex flex-column align-items-center justify-content-center">

             <!--  <div class="d-flex justify-content-center py-4">
                <a href="index.html" class="logo d-flex align-items-center w-auto">
                  <img src="{{asset('client_logo/pngegg.png')}}" alt="">
                  <span class="d-none d-lg-block">Centralize Automatic Meter Reading</span>
                </a>
              </div>End Logo -->

              <div class="card mb-3">

                <div class="card-body">

                  <div class="pt-4 pb-2">
				  
				  <div class="d-flex justify-content-center py-4">
					<img src="{{asset('client_logo/logo-r.png')}}" alt="" style="width:120px;">
				  </div>
                  
				  <h5 class="card-title text-center pb-0 fs-6" style="font-weight:bold !important; padding:0px !important; color: #00000;">Centralized Automated Meter Reading</h5>
                  </div>
									@if(Session::has('success'))
										
										<div class="bg-success text-white shadow">
											
                                            {{Session::get('success')}}
											
											
										</div>
										
									@endif
									
									@if(Session::has('fail'))
										
											
											
                                           
										<div class="alert alert-danger bg-danger text-light border-0 alert-dismissible fade show" role="alert">
                 {{Session::get('fail')}}
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>	
										
									@endif			
									
				  <form class="row g-3 needs-validation" action="{{route('login-user')}}" method="POST">
                    @csrf								
                    <div class="col-12">
                      <label for="yourUsername" class="form-label">Username</label>
					  
                       <input type="Text" class="form-control form-control-user" id="user_name" name="user_name" placeholder="" style="text-align:center;" value="{{old('user_name')}}">
					   <span class="text-danger">@error('user_name') {{$message}} @enderror</span>
                      
                    </div>

                    <div class="col-12">
                      <label for="yourPassword" class="form-label">Password</label>
                      <input type="password" class="form-control form-control-user" id="InputPassword" name="InputPassword" placeholder="" style="text-align:center;" value="{{old('InputPassword')}}">
					  <span class="text-danger">@error('InputPassword') {{$message}} @enderror</span>
                    </div>
					
					
                    <div class="col-12">
					<button class="btn btn-primary w-100" type="submit">Login</button>
                      
                    </div>
					<br>
					<!---->
					<div class="col-12">
                      <p class="small mb-0" align="center"><a href="{{ route('passwordreset') }}">Reset Password</a></p>
                    </div>
					
					
					
                  </form>

                </div>
              </div>

         
            </div>
          </div>
        </div>

      </section>

    </div>

@endsection