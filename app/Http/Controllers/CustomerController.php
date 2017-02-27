<?php 

namespace App\Http\Controllers;

use DB;
use Auth;
use Lang;
use View;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CustomerController extends Controller
{
    
    protected $customer;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(\App\Models\Customer $customer)
    {
        $this->middleware('auth',['only' => ['index']]);
        
        $this->customer = $customer;
    }
    
    public function index()
    {
        $data['customers'] = $this->customer->orderBy('created_at','desc')->get();
        
        return View::make('dashboard',['data' => $data]);
    }
       
    public function show($id)
    {
        $data['item'] = $this->customer->find($id);
        $data['foods'] = DB::table('food')->get();
                
        return View::make('customers.view',['data' => $data]);
    }
    
    public function create()
    {        
        return View::make('customers.new_customer');
        
    }
    
    public function store(Request $request)
    {    
        return $this->customer->saveNewEntry($request->all());
                
    }
    
    public function destroy($id)
    {        
        return $this->customer->deleteCustomer($id);
    }
}
