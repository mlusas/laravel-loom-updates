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
        $listCommand = new ListLoomUrls();
        $urls = $listCommand->getUrls();

        foreach ($urls as $url) {
            $existingUrl = LoomUrl::where('url', $url['url'])->first();
    
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
                    'tag' => $url['title'],
                ]);
            }
        }

        $this->info('Loom URLs stored successfully.');
    }
}