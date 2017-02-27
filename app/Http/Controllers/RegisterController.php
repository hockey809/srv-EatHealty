<?php namespace App\Http\Controllers;

use User;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    
    public function activation(Request $request)
    {
        
        if($request->get('method') == 'email') {
            $activate = new ActivationController(new  EmailController()); 
        } elseif($request->get('method') == 'sms') { 
            $activate = new ActivationController(new  SMSController()); 
        }
        
        return $activate->sendActivationCode($request->get('value'));

    }
}
