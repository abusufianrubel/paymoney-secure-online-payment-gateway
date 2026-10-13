<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\PreventRequestsDuringMaintenance as Middleware;

class PreventRequestsDuringMaintenance extends Middleware
{
    /**
     * The URIs that should be reachable while maintenance mode is enabled.
     *
     * @var array
     */
    protected $except = [];

    /**
     * Allow in-app upgrade API calls while maintenance mode is on (phase 2 runs over HTTP).
     *
     * @return array<int, string>
     */
    public function getExcludedPaths(): array
    {
        return array_merge(parent::getExcludedPaths(), \Modules\Upgrader\Entities\UpgradeManager::getMaintenanceExceptUris());
    }
}
