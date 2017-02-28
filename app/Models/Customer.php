<?php 

namespace App\Models;

use DB;
use Auth;
use Lang;
use \Input;
use Session;
use Redirect;
use Validator;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use \Illuminate\Database\Eloquent\SoftDeletes;



class Customer extends Model
{
    use SoftDeletes;
    
    protected $table = 'users';
    
    public function __construct()
    {
        parent::__construct();

    }
    
    public function foods()
    {
        return $this->hasMany('App\Models\FoodCustomer', 'user_id');
    }
    
    public function saveNewEntry($data)
    {

        // validator 
        $validator = $this->registerValidator($data);
        if($validator->fails()) 
        {
            Session::flash('validation_errors',$validator->errors()->all());

            return back()->withInput();
                          
        }
        
        
        try { 
            $this->user_type = 2;
            $this->first_name = $data['first_name'];
            $this->middle_name = $data['middle_name'];
            $this->last_name = $data['last_name'];
            $this->gender = $data['gender'];
            $this->birthdate = Carbon::createFromFormat('d/m/Y', $data['birthdate']);
            $this->phone_1 = $data['phone_1'];
            $this->phone_2 = $data['phone_2'];
            $this->email = $this->username = $data['email'];
            $this->password = bcrypt($data['password']);

            $this->save();
        
        } catch (\Exception $ex) {
           
            Session::flash('validation_errors',[Lang::get('common.error_messages.record_save_fail')]);

            return back()->withInput();
        }
        
        Session::flash('success',Lang::get('common.success_messages.record_saved_success'));
        
        return redirect('/');
        
    }
    
    public function updateEntry($entry, $data)
    {
       
        // validator 
        $validator = $this->updateValidator($data);
        if($validator->fails()) 
        {
            Session::flash('validation_errors',$validator->errors()->all());

            return back()->withInput();
                          
        }
        
        try { 
            $entry->first_name = $data['first_name'];
            $entry->middle_name = $data['middle_name'];
            $entry->last_name = $data['last_name'];
            $entry->birthdate = Carbon::createFromFormat('d/m/Y', $data['birthdate']);
            $entry->gender = $data['gender'];
            $entry->phone_1 = $data['phone_1'];
            $entry->phone_2 = $data['phone_2'];
            $entry->email = $this->username = $data['email'];
            if(isset($data['password'])) {
                $entry->password = bcrypt($data['password']);
            }

            $entry->save();
        
        } catch (\Exception $ex) {
                      
            Session::flash('validation_errors',[Lang::get('common.error_messages.record_save_fail')]);

            return back()->withInput();
        }
        
        Session::flash('success',Lang::get('common.success_messages.record_saved_success'));
        
        return redirect('/');
        
    }
    
    
    public function deleteCustomer($id)
    {    
        $this->find($id)->delete();
                
        Session::flash('success',Lang::get('common.success_messages.record_removed_success'));
        
        return redirect('/');
    }
    
    public function buildAPIWebView($clientId)
    {
        
        //BUILD WEB VIEW FOR MOBILE APP
        $data['item'] = $this->with('data')->find($clientId);
       
        //get current subscriptions
        $data['active_subscriptions'] = $data['item']->getSubscriptions('viewonly');
        foreach($data['active_subscriptions'] as $key => $subscription) {
            $data['item']->getStatusHtml($subscription, 'viewonly');
        }
        
        //GET NUMBER OF PAST SUBSCRIPTIONS
        $data['totalsubscriptionscount'] = $this->getTotalSubscriptionsCount($clientId) - count($data['active_subscriptions']);

        return $data;
    }   
    
    private function registerValidator($data)
    {
        $rules = [
            'first_name'        => 'required|alpha_dash',
            'last_name'         => 'required|alpha_dash',
            'email'             => 'required|email|unique:users,email',
            'password'          => 'required|confirmed|min:6'     
        ];

        $messages = [
            'email.required'           => Lang::get('pages.users.email_required'),
            'email.email'              => Lang::get('pages.users.email_email'),
            'email.unique'             => Lang::get('pages.users.email_unique'),
            'password.required'        => Lang::get('pages.users.password_required'),
            'password.confirmed'       => Lang::get('pages.users.password_confirmed'),
            'password.min'             => Lang::get('pages.users.password_min'),
            'first_name.required'      => Lang::get('pages.users.first_name_required'),
            'last_name.required'       => Lang::get('pages.users.last_name_required'),
            'first_name.alpha_dash'    => Lang::get('pages.users.first_name_alpha_dash'),
            'last_name.alpha_dash'     => Lang::get('pages.users.last_name_alpha_dash'),
        ];

        return Validator::make($data, $rules, $messages);        

    }
    
    private function updateValidator($data)
    {
        $rules = [
            'first_name'        => 'required|alpha_dash',
            'last_name'         => 'required|alpha_dash',
            'email'             => 'required|email',
            'password'          => 'confirmed|min:6'     
        ];

        $messages = [
            'email.required'           => Lang::get('pages.users.email_required'),
            'email.email'              => Lang::get('pages.users.email_email'),
            'password.confirmed'       => Lang::get('pages.users.password_confirmed'),
            'password.min'             => Lang::get('pages.users.password_min'),
            'first_name.required'      => Lang::get('pages.users.first_name_required'),
            'last_name.required'       => Lang::get('pages.users.last_name_required'),
            'first_name.alpha_dash'    => Lang::get('pages.users.first_name_alpha_dash'),
            'last_name.alpha_dash'     => Lang::get('pages.users.last_name_alpha_dash'),
        ];

        return Validator::make($data, $rules, $messages);        

    }
}
