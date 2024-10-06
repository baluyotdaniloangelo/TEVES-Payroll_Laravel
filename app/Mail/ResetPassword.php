<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Models\UserAccountModel;
use Hash;

class ResetPassword extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
	
		 string $title, 
		 string $body,
		 string $name,
		 string $user_id,
		 string $user_name,
		 string $user_password
		
		)
    {
		$this->title = $title;
		$this->body = $body;
		$this->name = $name;
		$this->user_id = $user_id;
		$this->user_name = $user_name;
		$this->user_password = $user_password;
    }

    /**
     * Get the message envelope.
     */
     // public function envelope(): Envelope
     // {
         // return new Envelope(
             // subject: 'CAMR - Reset Password',
         // );
     // }

    /**
     * Get the message content definition.
     */
     // public function content(): Content
     // {
         // return new Content(
             // view: 'emails.welcome',
             // with: [
                 // 'title' => $this->title,
                 // 'body' => $this->body,
				 // 'name' => $this->name,
				 // 'user_name' => $this->user_name,
             // ],
         // );
     // }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    // public function attachments(): array
    // {
        // return [];
    // }
	
	public function build()
    {
		
		$title = $this->title;
		$body = $this->body;
		$name = $this->name;
		$user_id = $this->user_id;
		$user_name = $this->user_name;
		
		$user_password = $this->user_password;
		$temporary_password = $this->generateRandomString();
		
		if($user_password!=''){
			$user_new_password = $user_password;
		}
		else{
			
			$user_new_password = $temporary_password;
			
			$UserList = new UserAccountModel();
			$UserList = UserAccountModel::find($user_id);
			// $UserList->user_real_name 			= $request->user_real_name;
			// $UserList->user_name 				= $request->user_name;
			// $UserList->user_email_address 		= $request->user_email_address;
			$UserList->user_password 	= hash::make($user_new_password); 
			$result = $UserList->update();
			
		}
		
        return $this->view('emails.welcome', compact('title', 'body', 'name', 'user_name', 'user_new_password'))->subject($title);	
		
    }
	
	public function generateRandomString($length = 6) {
		
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[random_int(0, $charactersLength - 1)];
		}
		
		return $randomString;
		
	}
}