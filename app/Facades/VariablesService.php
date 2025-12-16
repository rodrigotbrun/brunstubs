<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \App\Services\VariablesService
 */
class VariablesService extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \App\Services\VariablesService::class;
    }
}
