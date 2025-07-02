<?php

namespace App\Listeners;

use App\Events\FileCreated;
use App\Events\FileModified;
use App\Services\BaconIpsumService;
use Illuminate\Support\Facades\Log;

class AppendText
{
    public function __construct(protected BaconIpsumService $baconIpsumService)
    {
    }

public function handle(FileCreated|FileModified $event)
    {
        if (strtolower(pathinfo($event->filePath, PATHINFO_EXTENSION)) !== 'txt') {
            return;
        }

        Log::channel('watcher')->info("Appending text to: {$event->filePath}");
        try {
            $text = $this->baconIpsumService->getRandomText();
            file_put_contents($event->filePath, "\n$text", FILE_APPEND);
            Log::channel('watcher')->info("Text appended to: {$event->filePath}");
        } catch (\Exception $e) {
            Log::channel('watcher')->error("Failed to append text to {$event->filePath}: {$e->getMessage()}");
        }
    }
}
