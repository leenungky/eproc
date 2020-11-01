<?php
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;


class ClearStorage extends Seeder
{
    public function run()
    {
        $directories = [
            'public/vendor',
            'public/tender',
        ];
        foreach($directories as $dir)
            if(Storage::exists($dir)){
                Storage::deleteDirectory($dir);
                Storage::makeDirectory($dir);
            }
        }
}
