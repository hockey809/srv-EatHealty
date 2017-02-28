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
        $this->middleware('auth');
        
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
    
    public function edit($id)
    {    
        $data['item'] = $this->customer->find($id);
        
        return View::make('customers.edit_customer',['data' => $data]);
        
    }
    
    public function store(Request $request)
    {    
        return $this->customer->saveNewEntry($request->all());
                
    }
    
    public function update($id,Request $request)
    {    
        $entry = $this->customer->find($id);
        
        return $this->customer->updateEntry($entry,$request->all());
                        
    }
    
    public function destroy($id)
    {        
        return $this->customer->deleteCustomer($id);
    }
}
