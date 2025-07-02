<?php


return [
    'directory' => env('WATCHER_DIRECTORY', storage_path('app/watched')),
    'log_channel' => env('WATCHER_LOG_CHANNEL', 'watcher'),
    'poll_interval' => env('WATCHER_POLL_INTERVAL', 1), // Seconds between polls
];