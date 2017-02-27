<?php namespace App\Http\Controllers\api;

use Response;

/**
 * Description of ApiResponses
 *
 * @author Boko
 */
trait ApiHelper {
    
    protected $statusCode = 200; 
    protected $ApiStatusCode = 200;
    
    public function getStatusCode()
    {    
        return $this->statusCode;    
    }    
        
    public function setStatusCode($code)
    {    
        $this->statusCode = $code;
        
        return $this;
    }
    
    public function getApiStatusCode()
    {    
        return $this->ApiStatusCode;    
    }    
        
    public function setApiStatusCode($code)
    {    
        $this->ApiStatusCode = $code;
        
        return $this;
    }
    
    public function respond($data, $headers = [])
    {
        
        $content = [
                'data' => $data,
                'http_status_code'  =>  $this->getStatusCode(),
                'api_status_code'   =>  $this->getApiStatusCode()
            ];
        
        $headers['X-Content-Length'] = mb_strlen(serialize($content), '8bit');
//        $headers['Transfer-Encoding'] = 'chunked';
        
        return Response::json($content, $this->getStatusCode(), $headers);
        
    }
    
    public function respondWithRefreshToken($data, $newToken, $headers = [])
    {
        
        $content = [
                'data' => $data,
                'http_status_code'  =>  $this->getStatusCode(),
                'api_status_code'   =>  $this->getApiStatusCode(),
                'new_token'         =>  $newToken
            ];
     
        $headers['X-Content-Length'] = mb_strlen(serialize($content), '8bit');
         
        return Response::json($content, $this->getStatusCode(), $headers);
        
    }
    public function respondNotFound($message = 'Not found')            
    {
        
        return $this->setStatusCode(200)->respond($message);
        
    }   
    
    public function respondFailedCredentials($message = 'Failed credentials')
    {
        
        return $this->setStatusCode(422)->respond($message);
        
    }
    
    public function respondSuccess($data, $headers = [])
    {
        return $this->setStatusCode(200)->respond($data, $headers);
    }
    
    
}
