<?php

namespace App\Listeners;


use App\Events\FileCreated;
use App\Events\FileModified;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Encoders\WebpEncoder;
use Intervention\Image\ImageManager;

class OptimizeJpg
{
    public function handle(FileCreated|FileModified $event)
    {
                if (strtolower(pathinfo($event->filePath, PATHINFO_EXTENSION)) !== 'jpg') {
                    return;
                }

        Log::channel('watcher')->info("Optimizing JPG: {$event->filePath}");
        try {
            $manager = new ImageManager(new Driver());
            $image = $manager->read($event->filePath);
            $image = $image->encode(new WebpEncoder(quality: 65));
            $image->save($event->filePath);
            Log::channel('watcher')->info("JPG optimized: {$event->filePath}");
        } catch (\Exception $e) {
            Log::channel('watcher')->error("Failed to optimize JPG {$event->filePath}: {$e->getMessage()}");
        }
        }
}
