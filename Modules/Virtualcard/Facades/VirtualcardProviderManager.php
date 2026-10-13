<?php

namespace Modules\Virtualcard\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static array get()
 *
 * @see \Modules\Virtualcard\Manager\ProviderManager
 */

class VirtualcardProviderManager extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'virtualcardprovidermanager';
    }
}
