<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\PagesController;
use App\Http\Controllers\ApplicantController;
use App\Mail\TestMail;
use App\Traits\AccessLog;
use Illuminate\Support\Facades\Mail;

class ProcessEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use AccessLog;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    private $to;
    private $cc = null;
    private $arrdata; 
    public function __construct($to, $cc = null, $arrdata)
    {
        $this->to = $to;
        $this->cc = $cc;
        $this->arrdata = $arrdata;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->cc == null){
            $this->maillog("===========email send==============. email :".json_encode($this->to).", arrdata: ".json_encode($this->arrdata));
            Mail::to($this->to)->send(new TestMail($this->arrdata));
        }else{
            $this->maillog("===========email send==============. email :".json_encode($this->to).", cc: ".json_encode($this->cc).", arrdata: ".json_encode($this->arrdata));
            Mail::to($this->to)->cc($this->cc)->send(new TestMail($this->arrdata));
        }
    }
}
