<?php

namespace App\Jobs;

use App\Repositories\RefBankRepository;
use App\Repositories\RefProjectRepository;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SyncSapRefBank implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $logName = 'SyncSapRefBank';
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
            $bankRepo = new RefBankRepository();
            $data = $bankRepo->syncSAPData();
            Log::debug($this->logName .'.handle, ' . count($data) . ' bank data synchronized successfully');
            $projectRepo = new RefProjectRepository();
            $data = $projectRepo->syncSAPData();
            Log::debug($this->logName .'.handle, ' . count($data) . ' project data synchronized successfully');
        } catch (Exception $e) {
            // Log::debug($this->logName .'.handle, error sync data failed');
            Log::error($e->getMessage());
        }
    }
}
