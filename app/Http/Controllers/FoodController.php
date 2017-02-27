<?php

namespace App\Http\Controllers;

use View;
use Illuminate\Http\Request;
 
class FoodController extends Controller
{
    
    protected $timestamps = true;
    
    public function __construct() 
    {
        $this->middleware('auth');
        
        $this->food = new \App\Models\Food();
    }
    
    public function index() 
    {
        $data['foods'] = $this->food->get();
        
        return View::make('foods.index',['data' => $data]);
    }
    
    public function create()
    {
        return View::make('foods.new_food');
    }
    
    public function store(Request $request)
    {

        return $this->food->saveNewEntry($request->all());
        
    }
    
    public function edit($id) 
    {
    
        $data['item'] = $this->food->find($id);
        
        return View::make('foods.edit_food',['data' => $data]);
    }
            
    public function update($id, Request $request)
    {
        $entry = $this->food->find($id);
        
        return $this->food->updateEntry($entry,$request->all());
    }
    
    public function addFoodToCustomer(Request $request)
    {
        
        return $this->food->addFoodToCustomer($request->all());
               
        
    }
}
