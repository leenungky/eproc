<?php

namespace App\Helpers;

use App\Models\TenderPermission;
use App\TenderWorkflowHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class App
{
    public static function CanTenderManagement()
    {
        $isAllow = false;
        $tenderPermissions = DB::table('permissions')->where('name','like','tender%')
            ->pluck('name');
        if (Gate::any($tenderPermissions->toArray())) {
            $isAllow = true;
        }

        $tenderProcessPermissions = TenderPermission::pluck('name');
        if(TenderWorkflowHelper::has($tenderProcessPermissions->toArray())){
            $isAllow = true;
        }
        return $isAllow;
    }

    public static function transFb($id, $fallback, $parameters = [], $domain = 'messages', $locale = null)
    {
        return ($id === ($translation = trans($id, $parameters, $domain, $locale))) ? $fallback : $translation;
    }

    public static function baseName($filename)
    {
        return (!empty($filename)) ? basename($filename) : '';
    }

    public static function getClassName($string, $capitalizeFirstCharacter = true)
    {
        $str = str_replace(' ', '', ucwords(str_replace('_', ' ', $string)));
        if (!$capitalizeFirstCharacter) {
            $str[0] = strtolower($str[0]);
        }
        return $str;
    }
}
