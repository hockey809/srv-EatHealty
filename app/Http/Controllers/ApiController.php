<?php namespace App\Http\Controllers;

use DB;
use Auth;
use Lang;
use Config;
use JWTAuth;
use Response;
use App\User;
use Validator;
use App\Models\Customer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

/* 
 * 200 - success
 * 
 * 101 - phone number not found
 * 102 - failed to send verification SMS
 * 103 - failed basic HTTP credentials 
 * 104 - more than one account with SMS/Emil
 * 105 - failed to create token 
 * 106 - client already activated 
 * 107 - register validation error(s)
 * 108 - failed to activate client
 * 109 - incorrect login credentials
 * 110 - invalid token
 * 111 - missing token 
 * 112 - bad token, must relogin (usually blacklisted)
 * 113 - trying to refresh token twice
 * 
 */
//use \App\Http\Controllers\api\UserTransformer;

class ApiController extends Controller
{
    
    use api\ApiHelper;
            
    protected $customer;

    public function __construct(Customer $customer)
    {
        $this->customer = $customer;
        
//        $this->middleware('jwt.auth',['only' => ['get']]);
        
    }
        

    /**
     * Register new client and send email activation link
     *
     * @return \Illuminate\Http\Response
     */
   public function register(Request $request)
    {
        
        //check if basic HTTP auth pass
        if(Auth::onceBasic('username')) 
        {
            return $this->setApiStatusCode(101)->respondFailedCredentials('Failed basic HTTP credentials');
        }
          
        // validator 
        $validator = $this->registerValidator($request->all());
        if($validator->fails()) 
        {
            return $this->setApiStatusCode(102)->setStatusCode(200)->respond(implode(',',$validator->errors()->all()));
        }
               
        
        $activationCode = md5(uniqid(time(), true));
       
        // save code and user data in DB 
        try { 
            $this->customer->email = $this->customer->username = $request->get('email');
            $this->customer->confirmation_code = $activationCode;
            $this->customer->password = bcrypt($request->get('password'));
            $this->customer->confirmed = 0;        
            $this->customer->save();
        } catch (\Exception $ex) {
            return $this->setApiStatusCode(104)->setStatusCode(200)->respond('Failed to register new user, please try again later');   
        }
        
        // send email 
        $job = new \App\Jobs\SendConfirmationEmail($this->customer);  
        $this->dispatch($job->onQueue('emails'));                        
        
        return $this->setApiStatusCode(200)->respondSuccess(Lang::get('pages.users.successful_registration',['email' => $request->get('email') ]));        
        
    }

    
    /**
     * Login once to generate a token. Use the token for subsequent requests 
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        
        return $this->trylogin($credentials);
        
    }

    private function trylogin($credentials) 
    {  
       
        try {
            if(Auth::attempt($credentials)) {               
                return $this->setApiStatusCode(200)->respondSuccess(JWTAuth::fromUser(Auth::user(), [
                    'full_name' => Auth::user()->first_name . ' ' . Auth::user()->last_name, 
                    'phone' => (isset(Auth::user()->phone_1) ? Auth::user()->phone_1: Auth::user()->phone_2 ),
                    'email' => Auth::user()->email] ));
            } else { 
                return $this->setApiStatusCode(103)->setStatusCode(200)->respond('Incorrect login credentials');
            }
        } catch (JWTException $e) {
            return $this->setApiStatusCode(120)->setStatusCode(200)->respond('Failed to create token');
        }  
        
    }          
    
    private function registerValidator($data)
    {
        $rules = [
            'email'             => 'required|email|unique',
            'password'          => 'required|confirmed|min:6'     
        ];

        $messages = [
            'email.required'           => Lang::get('pages.users.email_required'),
            'email.email'              => Lang::get('pages.users.email_email'),
            'email.unique'             => Lang::get('pages.users.email_unique'),
            'password.required'        => Lang::get('pages.users.password_required'),
            'password.confirmed'       => Lang::get('pages.users.password_confirmed'),
            'password.min'             => Lang::get('pages.users.password_min'),
        ];

        return Validator::make($data, $rules, $messages);        

    }
   
}
