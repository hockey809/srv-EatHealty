<?php namespace App\Http\Controllers\Auth;

use App\User;
use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;

class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Registration & Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users, as well as the
    | authentication of existing users. By default, this controller uses
    | a simple trait to add these behaviors. Why don't you explore it?
    |
    */

    use AuthenticatesAndRegistersUsers, ThrottlesLogins;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'logout']);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        
        return Validator::make($data, [
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|confirmed|min:6',
            'activation_code' => 'required|exists:users,confirmation_code,confirmed,0',
        ]);
    }

    public function validatorActivation(array $data)
    {
       
        return Validator::make($data, [
            'email' => 'required|email|max:255|unique:users,email,'.$data['activation_code'] .',confirmation_code',
            'password' => 'required|confirmed|min:6',
            'activation_code' => 'required|exists:users,confirmation_code,confirmed,0',
        ]);
    }
    
    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);
    }
    
    public function activateClient(array $data) 
    {
        
        if($user = User::where('confirmation_code', $data['activation_code'])
            ->where('confirmed', 0)->first()) { 
            
            $user->email = $data['email'];
            $user->password = bcrypt($data['password']);
            $user->confirmed = 1;
            $user->save();
            
            return $user;
        }
        
        return false;
    }
}
