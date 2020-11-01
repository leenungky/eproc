<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use View;

class QueuingMail extends Mailable
{
    use Queueable, SerializesModels;

    public $variable;

    protected $inputs;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($inputs)
    {
        $this->inputs = $inputs;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        // return $this->subject($this->inputs->subject ?? 'Test Mail')
        // ->view('mail.'.$this->inputs->mailtype)->with($this->inputs->view_data);
        $mailable = $this->subject($this->inputs->subject ?? 'Test Mail')
            ->view('mail.'.$this->inputs->mailtype)
            ->with($this->inputs->view_data);
        if(isset($this->inputs->attachment) && !empty($this->inputs->attachment)){
            Log::debug('attachment : ', $this->inputs->attachment);
            $mailable = $mailable->attach($this->inputs->attachment['file'], $this->inputs->attachment['detail']);
        }
        return $mailable;
    }
}
