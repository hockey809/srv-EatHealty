<?php

namespace App\Http\Middleware;

use Closure;

use JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class JWTCheckMiddleware
{
    use \App\Http\Controllers\api\ApiHelper;
        
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if( ! $current_token = JWTAuth::getToken()) {
            return $this->setApiStatusCode(122)->setStatusCode(200)->respond('Missing token');
            // TODO: send error page 
        }
        
        try { 
            JWTAuth::authenticate($current_token);
        } catch (TokenInvalidException $ex) {
            return $this->setApiStatusCode(121)->setStatusCode(200)->respond('Invalid token');
            // TODO: send error page 
            
        } catch (TokenExpiredException $ex ) {
            try { 
                $newToken = JWTAuth::refresh($current_token);
            } catch (\Exception $ex) { //already refreshed, blacklisted
                return $this->setApiStatusCode(123)->setStatusCode(200)->respond('Cant refresh token, please relogin');
                // TODO: send error page 
            }
             
            $request->attributes->add(['new_token' => $newToken]);
                        
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $ex) {
            return $this->setApiStatusCode(124)->setStatusCode(200)->respond('Bad token, please relogin');
            // TODO: send error page 
        }
        
        isset($newToken) ? $clientId = JWTAuth::toUser($newToken)->id : $clientId = JWTAuth::toUser($current_token)->id;
        
        $request->attributes->add(['user_id' => $clientId]);
        
        return $next($request);
    }
}
