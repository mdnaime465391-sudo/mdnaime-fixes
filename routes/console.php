<?php

use App\Support\ContentCache;
use Illuminate\Support\Facades\Artisan;

Artisan::command('portfolio:refresh-cache', function () {
    ContentCache::all();
    $this->info('Public content cache cleared.');
})->purpose('Clear the cached public page payloads');
