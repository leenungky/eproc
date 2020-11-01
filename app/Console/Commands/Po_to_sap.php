<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Repositories\PoRepository;
use App\Traits\AccessLog;

class Po_to_sap extends Command
{
    use AccessLog;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'po:sap';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Po submit to sap';

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
        $this->poToSapListLog("====================== mulai schedule =============================");
        $repo = new PoRepository();
        $repo->commandSap();
        $this->poToSapListLog("====================== end schedule =============================");
        return 0;
    }
}
