<?php

namespace App\Scopes;

use App\Enums\TenderStatusEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class PublicViewScope implements Scope
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
        if($user && !$user->isVendor()){
            $builder->where($model->getTable().'.action_status', TenderStatusEnum::ACT_NEW);
        } else {
            $builder->where($model->getTable().'.public_status', TenderStatusEnum::PUBLIC_STATUS[2]);
        }
    }
}
