@extends('layouts.app')
@section('content')
   <body class="account-page">
	
		<!-- Main Wrapper -->
        <div class="main-wrapper">
			<div class="account-content">
				<div class="container">
				
					<!-- Account Logo -->
					<div class="account-logo">
						<img src="assets/img/logo2.png" alt="">
					</div>
					<!-- /Account Logo -->
					
					<div class="account-box">
						<div class="account-wrapper">
							<h3 class="account-title">Login</h3>
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
							<!-- Account Form -->
							<!-- <form action="admin-dashboard.html">-->
							<form class="row g-3 needs-validation" action="{{route('login-user')}}" method="POST">
							@csrf
								<div class="input-block mb-4">
									<label class="col-form-label">Username</label>
									<input type="Text" class="form-control form-control-user" id="user_name" name="user_name" placeholder="" style="text-align:center;" value="{{old('user_name')}}">
									<span class="text-danger">@error('user_name') {{$message}} @enderror</span>
								</div>
								<div class="input-block mb-4">
									<div class="row align-items-center">
										<div class="col">
											<label class="col-form-label">Password</label>
										</div>
										<div class="col-auto">
											<a class="text-muted" href="{{ route('passwordreset') }}">Forgot password?</a>
										</div>
									</div>
									<div class="position-relative">
										
									    <input type="password" class="form-control form-control-user" id="InputPassword" name="InputPassword" placeholder="" style="text-align:center;" value="{{old('InputPassword')}}">
									    <span class="text-danger">@error('InputPassword') {{$message}} @enderror</span>
									
									</div>
								</div>
								<div class="input-block mb-4 text-center">
									<button class="btn btn-primary account-btn" type="submit">Login</button>
								</div>
								<div class="account-footer">
									<!--<p>Don't have an account yet? <a href="register.html">Register</a></p>-->
								</div>
							</form>
							<!-- /Account Form -->
							
						</div>
					</div>
				</div>
			</div>
        </div>
		<!-- /Main Wrapper -->
		
		<!-- jQuery -->
       <script src="assets/js/jquery-3.7.1.min.js"></script>
		
		<!-- Bootstrap Core JS -->
        <script src="assets/js/bootstrap.bundle.min.js"></script>
		
		<!-- Custom JS -->
		<script src="assets/js/app.js"></script>
		
    </body>
@endsection