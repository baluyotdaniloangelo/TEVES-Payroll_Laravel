<?php

namespace App\Http\Controllers;

use App\Mail\ResetPassword;
use App\Mail\MyTestMail;

use Illuminate\Support\Facades\Mail;
use App\Models\User;
use Illuminate\Http\Request;

class EmailController extends Controller
{
    public function sendWelcomeEmail()
    {
        $title = 'Welcome to the laracoding.com example email';
        $body = 'Thank you for participating!';

        Mail::to('daniloangelo.baluyot@dec.com.ph')->send(new ResetPassword($title, $body));

        return "Email sent successfully!";
    }
	
	
	 public function sendTemporaryPasswordtoEmail(Request $request)
    {
		
		//ALTER TABLE `user_tb` ADD COLUMN `user_email_address` VARCHAR(100) NULL DEFAULT NULL AFTER `user_access`;
		
		$request->validate(
						['user_email_address'    		=> 'required',], 
						['user_email_address.required' 	=> 'Email Address is Required']
					);
		
		$user = User::where('user_email_address', '=', $request->user_email_address)->first();
		
		if ($user){
			
			if($request->user_email_address == $user->user_email_address){
				
						$title 			= 'Teves Billing System Portal: Password Reset';
						$body 			= 'Your password has been changed successfully. Please use below password to log in.';
						$user_id 		= $user->user_id;
						$name 			= $user->user_real_name;
						$user_name 		= $user->user_name;
						$user_password 	= '';

						Mail::to($user->user_email_address)->send(new ResetPassword($title, $body, $name, $user_id, $user_name, $user_password));

						return response()->json(['success'=>'Email sent successfully!']);
							
			}else{
				
				//return 'Incorrect Email';
				return response()->json(['success'=>'Incorrect Email!']);
			
			}
			
		}else{
			
				//return 'Email Not Found';
				return response()->json(['success'=>'Email Not Found!']);
		
		}
		
    }
	
}