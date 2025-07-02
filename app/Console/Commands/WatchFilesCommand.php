<?php

namespace App\Console\Commands;

use App\Events\FileCreated;
use App\Events\FileDeleted;
use App\Events\FileModified;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class WatchFilesCommand extends Command
{
    protected $signature = 'watch:files {directory?}';
    protected $description = 'Watch a directory for file system changes using PHP';

    protected $previousFiles = [];

    public function handle()
    {
        $directory = $this->argument('directory') ?? config('watcher.directory');
        if (!is_dir($directory)) {
            $this->error("Directory {$directory} does not exist.");
            return 1;
        }

        Log::channel('watcher')->info("Watching directory: {$directory}");
        $this->previousFiles = $this->getFiles($directory);

        while (true) {
            $this->pollDirectory($directory);
            sleep(config('watcher.poll_interval', 2));
        }
    }

    protected function pollDirectory(string $directory)
    {
        $currentFiles = $this->getFiles($directory);
        // Detect deleted files
        foreach ($this->previousFiles as $file => $mtime) {
            if (!isset($currentFiles[$file])) {
                Log::channel('watcher')->info("File deleted: {$file}");
                FileDeleted::dispatch($file);
            }
        }
        // Detect created and modified files
        foreach ($currentFiles as $file => $mtime) {
            if (!isset($this->previousFiles[$file])) {
                Log::channel('watcher')->info("File created: {$file}");
                FileCreated::dispatch($file);
            } elseif ($this->previousFiles[$file] !== $mtime) {
                Log::channel('watcher')->info("File modified: {$file}");
                FileModified::dispatch($file);
            }
        }



        $this->previousFiles = $currentFiles;
    }

    protected function getFiles(string $directory): array
    {
        $files = [];
        $disk = Storage::disk('watched');
        foreach ($disk->allFiles() as $relativePath) {
            $fullPath = $directory . '/' . $relativePath;
            if (is_file($fullPath)) {
                $files[$fullPath] = filemtime($fullPath);
            }
        }
        return $files;
    }
}