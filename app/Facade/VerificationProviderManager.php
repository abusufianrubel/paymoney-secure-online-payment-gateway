<?php

namespace App\Facade;

use Illuminate\Support\Facades\Facade;

/**
 * @method static VerificationContractInstance find(string $alias)
 * @method static array get()
 * @method static array all()
 * @method static void add()
 * @method static array names()
 *
 * @see \App\libraries\VerificationProviderManager
 */

class VerificationProviderManager extends Facade
{
    protected static  function getFacadeAccessor()
    {
        return 'verificationprovidermanager';
    }
}
