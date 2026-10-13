<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;

class CronController extends Controller
{
    public function resetData()
    {
        ini_set('max_execution_time', 300);
        $this->copyFiles();
        Artisan::call('migrate:fresh');
        Artisan::call('db:seed');
        $this->moduleSeed();
        Artisan::call('optimize:clear');
        
    }

    public function copyFiles()
    {
        if (
            File::isWritable(public_path('uploads')))
        {
            File::cleanDirectory(public_path('uploads'));
            File::copyDirectory(public_path('backup_files_for_cron/uploads'), public_path('uploads'));
        } else {
            Log::info("Don't have write permission !");
        }
    }

    protected function moduleSeed()
    {
        foreach ($this->modulesName() as $module) {
            Artisan::call('module:seed ' . $module);
        }
    }

    protected function modulesName()
    {
        $moduels = [];

        foreach (\Nwidart\Modules\Facades\Module::getOrdered() as $module) {
            array_push($moduels, $module->getName());
        }
        return $moduels;
    }
}
