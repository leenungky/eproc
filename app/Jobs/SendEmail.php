<?php

namespace App\Jobs;

use App\Mail\QueuingMail;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $logName = 'SendEmail';
    private $to = '';
    private $cc = '';

    protected $details;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($details)
    {
        $this->details = $details;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            if(is_array($this->details['email'])){
                foreach($this->details['email'] as $to){
                    if(!empty($to)){
                        $mail = Mail::to($this->to($to));
                    }
                    if(isset($this->details['cc']) && !empty($this->details['cc'])){
                        $mail->cc($this->cc($this->details['cc']));
                    }
                    $mail->send($this->details['mailable']);
                    Log::debug($this->logName .'.handle, mail sending', ['to' => $this->to, 'cc' => $this->cc, 'subject' => $this->details['mailable']->subject]);
                }
            }else if(is_string($this->details['email'])){
                if(!empty($this->details['email'])){
                    $mail = Mail::to($this->to($this->details['email']));
                }
                if(isset($this->details['cc']) && !empty($this->details['cc'])){
                    $mail->cc($this->cc($this->details['cc']));
                }
                $mail->send($this->details['mailable']);
                Log::debug($this->logName .'.handle, mail sending', ['to' => $this->to, 'cc' => $this->cc, 'subject' => $this->details['mailable']->subject]);
            }
        } catch (Exception $e) {
            Log::error($this->logName .'.handle, mail error sending', ['to' => $this->to, 'cc' => $this->cc, 'subject' => $this->details['mailable']->subject]);
            Log::error($e);
        }
    }

    private function to($to)
    {
        if(App::environment('local')){
            Log::debug($this->logName .'.handle, mail original ',['to' => $to]);
            $this->to = config('mail.from.address');
        }else{
            $this->to = $to;
        }
        return explode(',', $this->to);
    }

    private function cc($cc)
    {
        if(App::environment('local')){
            Log::debug($this->logName .'.handle, mail original ', ['cc' => $cc]);
            $this->cc = [config('mail.from.address')];
        }else{
            $this->cc = $cc;
        }
        return $this->cc;
    }
}
