<?php

namespace App\Services\Jp\Facades;

use Illuminate\Support\Facades\Facade as FacadeBase;

class Jp extends FacadeBase
{

    /**
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'Jp';
    }

}
