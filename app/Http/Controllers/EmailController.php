<?php namespace App\Http\Controllers;

use Mail;
use App\User;
use App\Jobs\SendActivationEmail;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Interfaces\ActivationInterface;

class EmailController extends Controller implements ActivationInterface 
{
    
    public function sendActivationCode(User $user, $to, $code)
    {        
        $job = new SendActivationEmail($user,$to,$code);
                
        $this->dispatch($job->onQueue('emails'));
           
        return ['status' => true, 'msg' => ''];
        
    }
    
    public function check($data) 
    {
        return User::where('email', $data)->where('user_type', User::TYPE_CUSTOMER)->get();
        
    }
}
