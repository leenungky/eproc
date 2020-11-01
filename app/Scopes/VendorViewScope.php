<?php

namespace App\Scopes;

use App\Enums\TenderStatusEnum;
use App\Enums\TenderSubmissionEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class VendorViewScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        $user = Auth::user();
        if($user && $user->isVendor()){
            // tampilkan item dengan status new (submitted | draft)
            $builder->where($model->getTable().'.action_status', TenderStatusEnum::ACT_NEW);
        } else {
            // tampilkan item yang sudah disubmit
            $builder->where($model->getTable().'.status', '!=', TenderSubmissionEnum::STATUS_ITEM[1]);
        }
    }
}
