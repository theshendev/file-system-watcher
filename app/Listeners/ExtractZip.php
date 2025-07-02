<?php

namespace App\Listeners;

use App\Events\FileCreated;
use Illuminate\Support\Facades\Log;
use ZipArchive;

class ExtractZip
{
    public function handle(FileCreated $event)
    {
        if (strtolower(pathinfo($event->filePath, PATHINFO_EXTENSION)) !== 'zip') {
            return;
        }

        Log::channel('watcher')->info("Extracting ZIP: {$event->filePath}");
        try {
            $zip = new ZipArchive;
            if ($zip->open($event->filePath) === true) {
                $zip->extractTo(dirname($event->filePath));
                $zip->close();
                Log::channel('watcher')->info("ZIP extracted: {$event->filePath}");
            } else {
                Log::channel('watcher')->error("Failed to open ZIP: {$event->filePath}");
            }
        } catch (\Exception $e) {
            Log::channel('watcher')->error("Failed to extract ZIP {$event->filePath}: {$e->getMessage()}");
        }
    }
}