<?php

namespace App\Console\Commands;

use App\TenderParameter;
use App\TenderWorkflowHelper;
use Illuminate\Console\Command;

class RefreshTenderWorkflow extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tender-workflow:refresh';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tender Workflow Refresh';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $helper = new TenderWorkflowHelper;
        $tenders = TenderParameter::whereIn('status',['draft','announcement','active'])->get();

        $bar = $this->output->createProgressBar($tenders->count());
        $bar->start();
        foreach($tenders as $tender){
            $helper->restartWorkflow($tender->tender_number);
            $bar->advance();
        }
        $bar->finish();
        echo PHP_EOL;
    }
}
