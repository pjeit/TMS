<?php
namespace App\Helper;

class ClearCache
{
    public static function clear(){
        \Artisan::call('cache:clear');
        \Artisan::call('route:clear');
        \Artisan::call('optimize:clear');
        \Artisan::call('storage:link');
    }
    
} ?>