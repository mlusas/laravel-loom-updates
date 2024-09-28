<?php

namespace mlusas\LaravelLoomUpdates\Commands;

use Illuminate\Console\Command;
use mlusas\LaravelLoomUpdates\Models\LoomUrl;
use Illuminate\Support\Facades\Config;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;

class ListLoomUrls extends Command
{
    protected $signature = 'loom:list {timeframe? : The timeframe to filter URLs (day, week, month)}';
    protected $description = 'List all Loom URLs in the codebase';

    public function handle()
    {
        $timeframe = $this->argument('timeframe');
        $urls = $this->getUrls($timeframe);

        $this->displayUrls($urls, 'Loom URLs');
    }

    public function getUrls($timeframe = null)
    {
        $urls = [];

        if ($this->useDatabaseStorage()) {
            $urls = $this->getLoomUrlsFromDatabase($timeframe)->toArray();
        }

        // Always scan directories, regardless of database storage
        $undatedUrls = [];
        $this->scanDirectories($urls, $undatedUrls, $timeframe);
        $urls = array_merge($urls, $undatedUrls);

        return $urls;
    }

    private function useDatabaseStorage()
    {
        return Config::get('loom-updates.use_database', true);
    }

    private function getLoomUrlsFromDatabase($timeframe = null)
    {
        $query = LoomUrl::query();

        if ($timeframe) {
            $query->where('created_at', '>=', now()->sub($timeframe));
        }

        return $query->get();
    }

    private function scanDirectories(&$urls, &$undatedUrls, $timeframe)
    {
        $directories = Config::get('loom-updates.scan_directories', [app_path(), resource_path()]);
        
        foreach ($directories as $directory) {
            $this->processDirectory($directory, $urls, $undatedUrls, $timeframe);
        }
    }

    private function processDirectory($directory, &$urls, &$undatedUrls, $timeframe)
    {
        $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($directory));
        foreach ($files as $file) {
            if ($file->isFile() && in_array($file->getExtension(), Config::get('loom-updates.file_extensions', ['php', 'blade.php']))) {
                $content = file_get_contents($file->getRealPath());
                preg_match_all('/@loom\s+(https:\/\/www\.loom\.com\/share\/[a-zA-Z0-9]+(\?sid=[a-zA-Z0-9-]+)?)\s+(\S+)\s+(\d{4}-\d{2}-\d{2})\s+(.*)/', $content, $matches, PREG_SET_ORDER);
                foreach ($matches as $match) {
                    $url = [
                        'url' => $match[1],
                        'file_path' => $this->getRelativePath($directory, $file->getRealPath()),
                        'line_number' => $this->findLineNumber($content, $match[0]),
                        'author' => $match[3],
                        'date' => $match[4],
                        'title' => $match[5],
                    ];
                    
                    if ($this->isWithinTimeframe($url['date'], $timeframe)) {
                        $urls[] = $url;
                    } else {
                        $undatedUrls[] = $url;
                    }
                }
            }
        }
    }

    // Add this new method to the class
    private function getRelativePath($from, $to)
    {
        $from = rtrim($from, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        $to = rtrim($to, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        $relativePath = str_replace($from, '', $to);
        return rtrim($relativePath, DIRECTORY_SEPARATOR);
    }

    private function findLineNumber($content, $match)
    {
        $lines = explode("\n", $content);
        foreach ($lines as $index => $line) {
            if (strpos($line, $match) !== false) {
                return $index + 1;
            }
        }
        return null;
    }

    private function isWithinTimeframe($date, $timeframe)
    {
        if (!$timeframe) {
            return true;
        }
        
        $date = new \DateTime($date);
        $now = new \DateTime();
        
        switch ($timeframe) {
            case 'day':
                return $date >= $now->modify('-1 day');
            case 'week':
                return $date >= $now->modify('-1 week');
            case 'month':
                return $date >= $now->modify('-1 month');
            default:
                return true;
        }
    }

    private function displayUrls($urls, $title)
    {
        $this->info($title);
        
        if (count($urls) > 0) {
            $this->table(
                ['URL', 'File', 'Line', 'Author', 'Date'],
                collect($urls)->map(function ($url) {
                    return [
                        $url['url'],
                        $url['file_path'],
                        $url['line_number'],
                        $url['author'] ?? 'N/A',
                        $url['date'] ?? 'N/A',
                    ];
                })
            );
        } else {
            $this->info('No Loom URLs found.');
        }
    }
}