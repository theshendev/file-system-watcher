<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class MemeApiService
{
    public function getRandomMemeUrl(): string
    {
        $response = Http::timeout(10)
            ->withOptions(['verify' => false]) // Disable SSL verification (development only)
            ->get('https://meme-api.com/gimme');
        return $response->json()['url'];
    }
}