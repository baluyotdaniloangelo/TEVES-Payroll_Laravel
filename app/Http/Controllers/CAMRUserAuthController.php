<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Hash;
use Session;
use Validator;

class CAMRUserAuthController extends Controller
{
    public function login(){
		$title = 'Login';
        return view("auth.login", compact('title'));
    }

    public function passwordreset(){
		$title = 'Reset Password';
        return view("auth.reset", compact('title'));
    }
	
    public function loginUser(Request $request){ 
		
		$request->validate([
            'user_name'=>'required|min:1|max:50', 
            'InputPassword'=>'required|min:6|max:50'
        ]);
		
        $user = User::where('user_name', '=', $request->user_name)->first();
		if ($user){
			if(Hash::check($request->InputPassword,$user->user_password)){
				$request->session()->put('loginID', $user->user_id);
				return redirect('site');
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
			Session::pull('site_current_tab');
			return redirect('/');
		}
    }
}
