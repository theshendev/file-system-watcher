<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class BaconIpsumService
{

    public function getRandomText(): string
    {
            $response = Http::timeout(10)
                ->withOptions(['verify' => false])
                ->get('https://baconipsum.com/api/?type=meat-and-filler&sentences=1');
            return $response->json()[0];

    }
}