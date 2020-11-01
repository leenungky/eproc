<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use View;

class TestMail extends Mailable
{
    use Queueable, SerializesModels;

    public $variable;
    
    protected $inputs;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($arrdata)
    {
        $this->inputs = $arrdata;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject($this->inputs->subject ?? 'Test Mail')->view('mail.'.$this->inputs->mailtype)->with([
            'vendor' => $this->inputs,
        ]);
    }
}
