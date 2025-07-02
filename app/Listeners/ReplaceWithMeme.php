<?php

namespace App\Listeners;

use App\Events\FileDeleted;
use App\Services\MemeApiService;
use Illuminate\Support\Facades\Log;

class ReplaceWithMeme
{
    public function __construct(protected MemeApiService $memeApiService)
    {
    }

public function handle(FileDeleted $event)
{
    Log::channel('watcher')->info("Replacing deleted file: {$event->filePath}");
    try {
        $memeUrl = $this->memeApiService->getRandomMemeUrl();
        $memeContent = file_get_contents($memeUrl);
        $newFile = dirname($event->filePath) . '/' . uniqid('meme_') . '.jpg';
        file_put_contents($newFile, $memeContent);
        Log::channel('watcher')->info("Meme saved: {$newFile}");
    } catch (\Exception $e) {
        Log::channel('watcher')->error("Failed to replace {$event->filePath} with meme: {$e->getMessage()}");
    }
}
}
