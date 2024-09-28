<?php

namespace mlusas\LaravelLoomUpdates\Commands;

use Illuminate\Console\Command;
use mlusas\LaravelLoomUpdates\Models\LoomUrl;
use mlusas\LaravelLoomUpdates\Services\MetaScraper;

class StoreLoomUrls extends Command
{
    protected $signature = 'loom:store';
    protected $description = 'Store all Loom URLs in the database';

    public function handle()
    {
        $this->info('Starting Loom URL scan and storage...');
        $listCommand = new ListLoomUrls();
        $urls = $listCommand->getAllUrls();

        $loomUrls = LoomUrl::all();

        $storedCount = 0;
        foreach ($urls as $url) {
            $existingUrl = $loomUrls->firstWhere('url', $url['url']);

            if (!$existingUrl) {
                $metaData = (new MetaScraper())->scrape($url['url']);

                LoomUrl::create([
                    'url' => $url['url'],
                    'file_path' => $url['file_path'],
                    'line_number' => $url['line_number'],
                    'date' => $url['date'] ?? null,
                    'author' => $url['author'] ?? null,
                    'title' => $metaData['title'] ?? null,
                    'image_url' => $metaData['image_url'] ?? null,
                    'tag' => $url['tag'] ?? null,
                ]);
                $storedCount++;
            }
        }

        $this->info("Loom URLs stored successfully. Stored $storedCount new URLs.");
    }
}
