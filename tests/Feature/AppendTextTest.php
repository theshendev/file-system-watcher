<?php

namespace Tests\Feature;

use App\Events\FileCreated;
use App\Events\FileModified;
use App\Listeners\AppendText;
use App\Services\BaconIpsumService;
use Illuminate\Container\Attributes\Log;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AppendTextTest extends TestCase
{
    protected $logger;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('watched');
        $this->logger = $this->createMock(Log::class);
        $this->app->instance('log', $this->logger);
    }

    public function test_it_appends_text_to_txt_files_on_creation()
    {
        Storage::disk('watched')->put('test.txt', 'Original content');
        $baconIpsumService = $this->createMock(BaconIpsumService::class);
        $baconIpsumService->method('getRandomText')->willReturn('Bacon ipsum text');

        $this->logger->expects($this->exactly(2))
            ->method('info')
            ->withConsecutive(
                ['Appending text to: ' . storage_path('app/watched-test/test.txt')],
                ['Text appended to: ' . storage_path('app/watched-test/test.txt')]
            );

        $listener = new AppendText($baconIpsumService);
        $listener->handle(new FileCreated(storage_path('app/watched-test/test.txt')));

        $content = Storage::disk('watched')->get('test.txt');
        $this->assertStringContainsString('Bacon ipsum text', $content);
    }

    public function test_it_appends_text_to_txt_files_on_modification()
    {
        Storage::disk('watched')->put('test.txt', 'Original content');
        $baconIpsumService = $this->createMock(BaconIpsumService::class);
        $baconIpsumService->method('getRandomText')->willReturn('Bacon ipsum text');

        $this->logger->expects($this->exactly(2))
            ->method('info')
            ->withConsecutive(
                ['Appending text to: ' . storage_path('app/watched-test/test.txt')],
                ['Text appended to: ' . storage_path('app/watched-test/test.txt')]
            );

        $listener = new AppendText($baconIpsumService);
        $listener->handle(new FileModified(storage_path('app/watched-test/test.txt')));

        $content = Storage::disk('watched')->get('test.txt');
        $this->assertStringContainsString('Bacon ipsum text', $content);
    }

    public function test_it_skips_non_txt_files()
    {
        Storage::disk('watched')->put('test.jpg', 'image');
        $baconIpsumService = $this->createMock(BaconIpsumService::class);
        $baconIpsumService->expects($this->never())->method('getRandomText');

        $this->logger->expects($this->never())->method('info');

        $listener = new AppendText($baconIpsumService);
        $listener->handle(new FileCreated(storage_path('app/watched-test/test.jpg')));
    }

    public function test_it_handles_api_failure()
    {
        Storage::disk('watched')->put('test.txt', 'Original content');
        $baconIpsumService = $this->createMock(BaconIpsumService::class);
        $baconIpsumService->method('getRandomText')->willThrowException(new \Exception('API error'));

        $this->logger->expects($this->once())
            ->method('info')
            ->with('Appending text to: ' . storage_path('app/watched-test/test.txt'));
        $this->logger->expects($this->once())
            ->method('error')
            ->with($this->stringContains('Failed to append text'));

        $listener = new AppendText($baconIpsumService);
        $listener->handle(new FileCreated(storage_path('app/watched-test/test.txt')));
    }
}