<?php

//API routes
//
Route::post('/api/v1/clients/register', 'ApiController@register');
Route::post('/api/v1/clients/login', 'ApiController@login');

// new registration way
Route::get('/register', 'Auth\AuthController@showRegistrationForm'); 
Route::post('/register/sendemail', 'Auth\AuthController@registerAndSendActivationEmail'); 
Route::get('/register/token/{token}', 'Auth\AuthController@activateViaConfirmationLink'); 

    
// drawer routes
Route::group(['middleware' => 'jwt'], function() {
    Route::get('/api/v1/webview/dashboard', 'MobileWebviewController@dashboard');
    Route::get('/api/v1/webview/add', 'MobileWebviewController@schedule');  
    // more API web views come here
});
//
//end of API routes


// ADMIN BACKEND ROUTES
Route::group(['middleware' => 'web'], function () {
    Route::get('/', 'CustomerController@index');
    Route::get('/customers', 'CustomerController@create');
    Route::post('/customers','CustomerController@store');
    Route::get('/customers/{id}', 'CustomerController@show');
    Route::delete('/customers/{id}', 'CustomerController@destroy');
    
    Route::get('/food','FoodController@index');
    Route::get('/new_food','FoodController@create');
    Route::post('/food','FoodController@store');
    Route::put('/food/{id}','FoodController@update');
    Route::get('/edit_food/{id}','FoodController@edit');
    Route::post('/food/add_to_customer','FoodController@addFoodToCustomer');
    
    // login
    Route::get('/login', 'Auth\AuthController@showLoginForm');
    Route::post('/login','Auth\AuthController@login');
    Route::get('/logout','Auth\AuthController@logout');
    Route::post('/password/email', 'Auth\PasswordController@sendResetLinkEmail');
    Route::post('/password/reset', 'Auth\PasswordController@reset');
    Route::get('/password/reset/{token?}', 'Auth\PasswordController@showResetForm');
    
    
    // NOT USED
//    Route::get('/activate/{code?}', 'Auth\AuthController@showRegistrationForm'); //this is showing registration form with the act code
//    Route::post('/activate/{activate?}', 'Auth\AuthController@register'); //this is POST for registration form(incl activation code)
//    //this is called from modal ajax POST to send registration code 
//    Route::post('/register/sendactivation', 'RegisterController@activation');
    
    
});

