<?php

namespace App\Http\Controllers;

use JWTAuth;
use App\Models\Customer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MobileWebviewController extends Controller
{
    
    protected $customer;
    
    public function __construct() 
    {
        $this->customer = new Customer;
    }

    /*
     * dashboard (Tablo) from drawer
     */
    public function dashboard(Request $request)
    {
        
        // BUILD WEB VIEW
        $data = $this->customer->buildAPIWebView($request->get('user_id'));

        return view('customers.mobile_dashboard',['data' => $data]);
        
    }
    
    /*
     * show view for CRUD items(foods)
     */
    public function add(Request $request)
    {
        
        // BUILD WEB VIEW
        $data = $this->customer->buildAPIWebView($request->get('user_id'));

        return view('customers.mobile_crud_items',['data' => $data]);
        
    }
    
}
