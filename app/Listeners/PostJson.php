<?php

namespace App\Listeners;

use App\Events\FileCreated;
use App\Events\FileModified;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PostJson
{
    public function handle(FileCreated|FileModified $event)
    {
        if (strtolower(pathinfo($event->filePath, PATHINFO_EXTENSION)) !== 'json') {
            return;
        }
        Log::channel('watcher')->info("Posting JSON: {$event->filePath}");
        try {
            $response = Http::attach('file', file_get_contents($event->filePath), basename($event->filePath))
                ->post('https://fswatcher.requestcatcher.com/');
            Log::channel('watcher')->info("JSON posted: {$event->filePath}, Status: {$response->status()}");
        } catch (\Exception $e) {
            Log::channel('watcher')->error("Failed to post JSON {$event->filePath}: {$e->getMessage()}");
        }
}
}