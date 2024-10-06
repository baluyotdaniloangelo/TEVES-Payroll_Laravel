<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Activitylog\Models\Activity;
use Hash;
use Session;
use Validator;

class UserAuthController extends Controller
{
    public function login(){
        return view("auth.login");
    }

	public function passwordreset(){
		$title = 'Reset Password';
        return view("auth.reset", compact('title'));
    }
	
    public function loginUser(Request $request){ 
		
		$request->validate([
            'user_name'=>'required|min:1|max:12', 
            'InputPassword'=>'required|min:6|max:20'
        ]);
		
        $user = User::where('user_name', '=', $request->user_name)->first();
		if ($user){
			if(Hash::check($request->InputPassword,$user->user_password)){
				
				$request->session()->put('loginID', $user->user_id);
				$request->session()->put('UserType', $user->user_type);
				
				return redirect('billing');
				
			}else{
				return back()->with('fail', 'Incorrect Password');
			}
		}else{
			return back()->with('fail', 'This Username is not Registered.');
		}
    }

    public function logout(){
		if(Session::has('loginID')){
			Session::pull('loginID');
			return redirect('/');
		}
    }
}
