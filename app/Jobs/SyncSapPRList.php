<?php

namespace App\Jobs;

use App\Repositories\PRListRepository;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SyncSapPRList implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $logName = 'SyncSapPRList';
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Log::debug($this->logName .'.handle, run job');
        try {
            $listRepo = new PRListRepository();
            $data = $listRepo->syncSAPData();
            Log::debug($this->logName .'.handle, ' . count($data) . ' data synchronized successfully');
            return $data;
        } catch (Exception $e) {
            Log::debug($this->logName .'.handle, error sync data failed');
            Log::error($e);
            throw $e;
        }
    }
}
