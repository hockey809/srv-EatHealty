<?php namespace App\Jobs;

use Mail;
use App\User;
use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendActivationEmail extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    protected $user;
    protected $email;
    protected $activationCode;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(User $user, $email, $activationCode)
    {
        $this->user = $user;
        $this->email = $email;
        $this->activationCode = $activationCode;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $data = [];
        $data['code'] = $this->activationCode;
        $data['first_name'] = $this->user->first_name;
        $data['last_name'] = $this->user->last_name;
        
        Mail::send('auth.emails.activationcode', ['data' => $data], function ($m) {
            $m->to($this->email, $this->user->first_name . ' ' . $this->user->last_name)->subject('Fitsys.co registration code');
        });
        
    }
    
    /**
     * Handle a job failure.
     *
     * @return void
     */
    public function failed()
    {
        $data = [];
        $data['code'] = $this->activationCode;
        $data['email'] = $this->email;
        
        Mail::send('auth.emails.failedactivationemail', ['data' => $data], function ($m) {
            $m->to('b.borisov9@gmail.com', 'Borislav Borisov')->subject('Fitsys.co failed activation email');
        });
    }
}
