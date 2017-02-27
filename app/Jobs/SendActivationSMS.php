<?php namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendActivationSMS extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    const SMS_SEND_URL = 'https://bsms.voicecom.bg/smsapi/bsms/sendsms/?sid=1433';
    const SMS_STATUS_URL = 'https://bsms.voicecom.bg/smsapi/bsms/dlr/?sid=1433';
    
    protected $phone;
    protected $message;
    
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($phone, $message)
    {
        $this->phone = $phone;
        $this->message = $message;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle($phone, $message)
    {
        $client = new \GuzzleHttp\Client();
        $response = $client->get(self::SMS_SEND_URL . '/&id=1152&msisdn='.$this->phone.'&priority=1&text=' . $this->message);

        return $response->getBody()->getContent();
        
    }
}
