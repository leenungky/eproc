<?php

use Illuminate\Database\Seeder;
use App\Repositories\RefBankRepository;
use App\Repositories\RefProjectRepository;

class SapSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $bankRepo = new RefBankRepository();
        $bankRepo->syncSAPData();
        $projectRepo = new RefProjectRepository();
        $projectRepo->syncSAPData();
    }
}
