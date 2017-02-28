<?php

namespace App\Http\Controllers;

use JWTAuth;
use App\Models\Food;
use App\Models\Customer;
use App\Models\FoodCustomer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MobileCustomerController extends Controller
{
    
    use api\ApiHelper;
    
    protected $request;
    protected $user_id;
    protected $customer;
    protected $food_customer;


    public function __construct(Request $request) 
    {
        $this->request = $request->all();
        $this->user_id = $request->get('user_id');
        $this->customer = new Customer;
        $this->food_customer = new FoodCustomer;
    }

    /*
     * dashboard (Tablo) from drawer
     */
    public function asd(Request $request)
    {
        
        // BUILD WEB VIEW
        $data = $this->customer->buildAPIResponse($request->get('user_id'));

        return view('customers.mobile_dashboard',['data' => $data]);
        
    }
    
    public function dashboard($from = 0, $to = 2147483647)
    {
        
        $foods = $this->food_customer->where('user_id','=',$this->user_id)->skip($from)->take($to)->orderBy('created_at','desc')->get()->toJson();
        
        return $this->respondSuccess($foods);
        
    }
    
    public function getFood($foodId)
    {
              
        return $this->respondSuccess($this->food_customer->where('user_id','=',$this->user_id)->where('id','=',$foodId)->get()->toJson());
        
    }
    
    public function addFoodToCustomer(Food $food)
    {
        
        try {
            $this->request['user_id'] = 305;
            $add = $food->addFoodToCustomer($this->request);
            
            if ( ! $add['status']) { 
                throw new Exception();
            }
            
        } catch (Exception $ex) {
            return $this->setApiStatusCode(105)->setStatusCode(200)->respond('Failed to insert food for customer');
        }
        
        return $this->setApiStatusCode(200)->setStatusCode(200)->respond('Success');
        
    }
    
    public function updateFood($foodCustomerId)
    {
        try { 
            $this->food_customer->find($id)->update([
                'amount' => $this->request['amount'],
                'created_at' => $this->request['created_at'],
                'updated_at' => $this->request['created_at']
            ]);
        } catch (Exception $ex) {
            return $this->setApiStatusCode(106)->setStatusCode(200)->respond('Failed to insert food for customer');
        }
        
        return $this->setApiStatusCode(200)->setStatusCode(200)->respond('Success');

    }
    
    public function deleteFood($foodCustomerId)
    {
        try { 
            $this->food_customer->find($id)->delete();
        } catch (Exception $ex) {
            return $this->setApiStatusCode(107)->setStatusCode(200)->respond('Failed to delete food for customer');
        }
        
        return $this->setApiStatusCode(200)->setStatusCode(200)->respond('Success');

    }
    
}
