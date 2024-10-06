<?php
  
namespace App\Mail;
   
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
  
class MyTestMail extends Mailable
{
    use Queueable, SerializesModels;
  
    // public $details;
	
   
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct((
	
		 string $title, 
		 string $body,
		 string $name,
		 string $user_name,
		 string $user_password
		
		)
    {
		$this->title = $title;
		$this->body = $body;
		$this->name = $name;
		$this->user_name = $user_name;
		$this->user_password = $user_password;
    
    }
   
    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $title = $this->title;
		$body = $this->body;
		$name = $this->name;
		$user_name = $this->user_name;
		
		$user_password = $this->user_password;
		$temporary_password = $this->generateRandomString();
		
		if($user_password==''){
			$user_new_password = $user_password;
		}
		else{
			$user_new_password = $temporary_password;
		}
		
		
		
		
        return $this->view('emails.welcome', compact('title', 'body', 'name', 'user_name', 'user_new_password'));	
		
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