<?php

namespace App\Models;

use DB;
use Lang;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Food extends Model
{
    protected $table = 'food';
    
    
    public function saveNewEntry($data)
    {
                
        $this->title = $data['title'];
        $this->units = $data['units'];
        $this->calories_per_unit = $data['calories'];
        $this->save();
        
        return redirect('/food');                
        
    }
    
    public function updateEntry($entry, $data)
    {
        
        $entry->title = $data['title'];
        $entry->units = $data['units'];
        $entry->calories_per_unit = $data['calories'];
        $entry->save();
        
        return redirect('/food');      
    }
    
    public function addFoodToCustomer($data)
    {
        try { 
           
            $food = $this->find($data['food_id']);

            if($food->units == 'abs') { 
                $calories = ceil($data['quantity'] * $food->calories_per_unit);
            } else { 
                $calories = ceil(($data['quantity'] * $food->calories_per_unit ) / 100);
            }
           
            DB::table('customer_food_rel')->insert([
                'user_id' => $data['user_id'],
                'food_id' => $food->id,
                'title' => $food->title,
                'units' => $food->units,
                'amount' => $data['quantity'],
                'calories' => $calories,
                'updated_at' => Carbon::now(),
                'created_at' => Carbon::now(),
            ]);
         
        } catch (Exception $ex) {
            
            return ['status' => false, 'msg' => Lang::get('common.error_messages.record_save_fail')];
        }
        
        return ['status' => true, 'msg' => ''];
    }
}
