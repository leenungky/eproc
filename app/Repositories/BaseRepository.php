<?php

namespace App\Repositories;

use Illuminate\Support\Facades\Storage;

class BaseRepository
{

    protected function removeStorage($filename)
    {
        if (Storage::exists($filename)) {
            Storage::delete($filename);
        }
    }

}
