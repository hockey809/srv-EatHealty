<?php

namespace App\Http\Controllers;

use JWTAuth;
use App\Models\Food;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MobileFoodController extends Controller
{
    
    use api\ApiHelper;
        
    protected $food;

    public function __construct() 
    {
        $this->food = new Food;
    }

    public function listData()
    {        
        return $this->setApiStatusCode(200)->setStatusCode(200)->respond($this->food->get()->toJson());

    }
    
}
