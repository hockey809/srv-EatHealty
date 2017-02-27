<?php

namespace App\Http\Controllers;

use \Hash;
use \Input;
use App\User;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Redirect;

/**
 * UserController Class
 *
 * Implements actions regarding user management
 */
class UserController extends BaseController
{   
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Displays the form for account creation
     *
     * @return  Illuminate\Http\Response
     */
    public function create()
    {
        return View::make(Config::get('confide::signup_form'));
    }
    
    /**
     * Stores new account
     *
     * @return  Illuminate\Http\Response
     */
    public function store()
    {
        $repo = App::make('UserRepository');
        $user = $repo->signup(Input::all());

        if ($user->id) {
            if (Config::get('confide::signup_email')) {
                Mail::queueOn(
                    Config::get('confide::email_queue'),
                    Config::get('confide::email_account_confirmation'),
                    compact('user'),
                    function ($message) use ($user) {
                        $message
                            ->to($user->email, $user->username)
                            ->subject(Lang::get('confide::confide.email.account_confirmation.subject'));
                    }
                );
            }

            return Redirect::action('UserController@login')
                ->with('notice', Lang::get('confide::confide.alerts.account_created'));
        } else {
            $error = $user->errors()->all(':message');

            return Redirect::action('UserController@create')
                ->withInput(Input::except('password'))
                ->with('error', $error);
        }
    }

    /**
     * Displays the login form
     *
     * @return  Illuminate\Http\Response
     */
    public function login()
    {   
        return view('user.login');
    }

    /**
     * Attempt to do login
     *
     * @return  Illuminate\Http\Response
     */
    public function doLogin()
    {
        $input = Request::all();
        
        if (Auth::attempt(['username' => $input['email'], 'password' => $input['password'], 'deleted_at' => null, 'user_type' => 1]))
        {
            
            $user = Auth::user();
            Session::set('current_user_id',$user->id);
            return Redirect::intended('/dashboard');
        }
        else {   
            Session::flash(
                        'systemMessages', ['error' => Lang::get('pages.login.errors.wrong_credentials')]
                );
            return Redirect::action('UserController@login')
                            ->withInput(Request::except('password'));
        }
    }

    /**
     * Attempt to confirm account with code
     *
     * @param  string $code
     *
     * @return  Illuminate\Http\Response
     */
    public function confirm($code)
    {
        if (Confide::confirm($code)) {
            $notice_msg = Lang::get('confide::confide.alerts.confirmation');

            return Redirect::action('UserController@login')
                ->with('notice', $notice_msg);
        } else {
            $error_msg = Lang::get('confide::confide.alerts.wrong_confirmation');

            return Redirect::action('UserController@login')
                ->with('error', $error_msg);
        }
    }

    /**
     * Displays the forgot password form
     *
     * @return  Illuminate\Http\Response
     */
    public function forgotPassword()
    {
        return View::make(Config::get('confide::forgot_password_form'));
    }

    /**
     * Attempt to send change password link to the given email
     *
     * @return  Illuminate\Http\Response
     */
    public function doForgotPassword()
    {
        if (Confide::forgotPassword(Input::get('email'))) {
            $notice_msg = Lang::get('confide::confide.alerts.password_forgot');
            Session::flash('systemMessages', ['notice' => [$notice_msg]]);
        } else {
            $error_msg = Lang::get('pages.login.errors.wrong_password_forgot');
            Session::flash('systemMessages', ['errors' => [[$error_msg]]]);
        }

        return Redirect::action('UserController@login');
    }

    /**
     * Shows the change password form with the given token
     *
     * @param  string $token
     *
     * @return  Illuminate\Http\Response
     */
    public function resetPassword($token)
    {
        return View::make(Config::get('confide::reset_password_form'))
            ->with('token', $token);
    }

    /**
     * Attempt change password of the user
     *
     * @return  Illuminate\Http\Response
     */
    public function doResetPassword()
    {
        $repo = App::make('UserRepository');
        $input = array(
            'token'                 => Input::get('token'),
            'password'              => Input::get('password'),
            'password_confirmation' => Input::get('password_confirmation'),
        );

        // By passing an array with the token, password and confirmation
        if ($repo->resetPassword($input)) {
            $notice_msg = Lang::get('confide::confide.alerts.password_reset');

            return Redirect::action('UserController@login')
                ->with('notice', $notice_msg);
        } else {
            $error_msg = Lang::get('confide::confide.alerts.wrong_password_reset');

            return Redirect::action('UserController@resetPassword', array('token' => $input['token']))
                ->withInput()
                ->with('error', $error_msg);
        }
    }

    /**
     * shows the profile edit view
     *
     * @return $this
     */
    public function profile()
    {
        // get the current user info
        $user = Auth::user();
        $data['item'] = [
            'email'       => $user->email,
            'first_name'  => $user->first_name,
            'middle_name' => $user->middle_name,
            'last_name'   => $user->last_name,
            'phone_1'     => $user->phone_1,
            'phone_2'     => $user->phone_2,
        ];

        return View::make('user/profile')->with('data', $data);
    }

    /**
     * saves the edited profile
     */
    public function saveProfile()
    {
        $input = Input::all();
        $user = User::find(Auth::user()->id);
        $user->user_type = User::TYPE_ADMIN;
        $user->email = $input['email'];
        $user->first_name = $input['first_name'];
        $user->middle_name = $input['middle_name'];
        $user->last_name = $input['last_name'];
        $user->phone_1 = $input['phone_1'];
        $user->phone_2 = $input['phone_2'];

        try {
            // check if old password matches
            if (!empty($input['old_password']) || !empty($input['new_password']) || !empty($input['repeat_password'])) {
                $hashCheck = Hash::check($input['old_password'], $user->getAuthPassword());
                if (!$hashCheck) {
                    Session::flash(
                        'systemMessages',
                        ['errors' => [[Lang::get('pages.users.old_password_incorrect')]]]
                    );

                    return Redirect::action('UserController@profile')->withInput();
                }

                if ($input['new_password'] !== $input['repeat_password']) {
                    Session::flash(
                        'systemMessages',
                        ['errors' => [[Lang::get('pages.users.passwords_dont_match')]]]
                    );

                    return Redirect::action('UserController@profile')->withInput();
                }

                $user->password = Hash::make($input['new_password']);
            }

            $user->save();

            // upload the new avatar if any
            $bm = new \BaseModel();
            $filename = $bm->uploadImage('user', $user->id, Input::file('avatar'));

            // update the user record with the new avatar
            if (!empty($filename)) {
                $user->avatar = $filename;
                $user->save();
            }

            Session::flash('systemMessages', ['success' => [Lang::get('pages.profile.saved_success')]]);
        } catch (\Exceptions\ValidateException $e) {
            Session::flash('systemMessages', ['errors' => $e->getErrors()]);
        }

        return Redirect::back()->withInput();
    }

    /**
     * Log the user out of the application.
     *
     * @return  Illuminate\Http\Response
     */
    public function logout()
    {
        Auth::logout();

        return Redirect::action('DashboardController@dashboard');
    }
}
