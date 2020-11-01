<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     * PT Abyor International - Voproc Trademark Registration and Copyright Recordal
     *
     * @var array
     */
    protected $except = [
        'logout',
        'login'
    ];
}
