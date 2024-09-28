<?php

namespace mlusas\LaravelLoomUpdates\Commands;

use Illuminate\Console\Command;
use mlusas\LaravelLoomUpdates\Models\LoomUrl;
use Illuminate\Support\Facades\Config;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use Carbon\Carbon;

class ListLoomUrls extends Command
{
    protected $signature = 'loom:list {--timeframe= : The timeframe to filter URLs (day, week, month)}';
    protected $description = 'List all Loom URLs in the codebase';

    protected $urlsFromDatabase = [];
    protected $urlsFromFiles = [];
    protected $allUrls = [];

    public function handle()
    {
        $this->info('Starting Loom URL scan...');
        $timeframe = $this->option('timeframe');

        $this->collectUrls($timeframe);
        $this->mergeUrls();

        $this->info('Found ' . count($this->allUrls) . ' URLs');
        $this->displayUrls($this->allUrls, 'Loom URLs');
    }

    public function getAllUrls($timeframe = null)
    {
        $this->collectUrls($timeframe);
        $this->mergeUrls();
        return $this->allUrls;
    }

    protected function collectUrls($timeframe)
    {
        if ($this->useDatabaseStorage()) {
            $this->urlsFromDatabase = $this->getLoomUrlsFromDatabase($timeframe);
        }
        $this->scanDirectories($timeframe);
    }

    protected function mergeUrls()
    {
        $this->allUrls = array_merge($this->urlsFromDatabase, $this->urlsFromFiles);
    }

    protected function useDatabaseStorage()
    {
        return Config::get('loom-updates.use_database', true);
    }

    protected function getLoomUrlsFromDatabase($timeframe = null)
    {
        $query = LoomUrl::query();

        if ($timeframe) {
            $date = match ($timeframe) {
                'day' => Carbon::now()->subDay(),
                'week' => Carbon::now()->subWeek(),
                'month' => Carbon::now()->subMonth(),
                default => null,
            };

            if ($date) {
                $query->where('date', '>=', $date);
            }
        }

        return $query->get()->toArray();
    }

    protected function scanDirectories($timeframe)
    {
        $directories = Config::get('loom-updates.scan_directories', [app_path(), resource_path()]);

        foreach ($directories as $directory) {
            $this->processDirectory($directory, $timeframe);
        }
    }

    protected function processDirectory($directory, $timeframe)
    {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $file) {
            if ($this->isValidFile($file)) {
                $this->processFile($file, $directory, $timeframe);
            }
        }
    }

    protected function isValidFile($file)
    {
        return $file->isFile() && in_array($file->getExtension(), Config::get('loom-updates.file_extensions', ['php', 'blade.php']));
    }

    protected function processFile($file, $directory, $timeframe)
    {
        $content = file_get_contents($file->getRealPath());
        preg_match_all('/@loom\s+(https:\/\/www\.loom\.com\/share\/[a-zA-Z0-9]+(\?sid=[a-zA-Z0-9-]+)?)\s+(\d{4}-\d{2}-\d{2})(?:\s+(\S+))?(?:\s+"([^"\n]*)")?\s*$/m', $content, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $url = $this->createUrlArray($match, $file, $directory);
            $this->addUrlToAppropriateArray($url, $timeframe);
        }
    }

    protected function createUrlArray($match, $file, $directory)
    {
        return [
            'url' => $match[1],
            'file_path' => $this->getRelativePath($directory, $file->getRealPath()),
            'line_number' => $this->findLineNumber($file->getRealPath(), $match[0]),
            'date' => $match[3] ?? '',
            'author' => $match[4] ?? '',
            'tag' => isset($match[5]) ? str_replace('"', '', trim($match[5])) : '',
            'title' => '', // Only available via database storage
        ];
    }

    protected function addUrlToAppropriateArray($url, $timeframe)
    {
        if ($this->isWithinTimeframe($url['date'], $timeframe)) {
            $this->urlsFromFiles[] = $url;
        }
    }

    protected function getRelativePath($from, $to)
    {
        $from = rtrim($from, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        $to = rtrim($to, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        $relativePath = str_replace($from, '', $to);
        return rtrim($relativePath, DIRECTORY_SEPARATOR);
    }

    protected function findLineNumber($filePath, $match)
    {
        $lines = file($filePath);
        foreach ($lines as $index => $line) {
            if (strpos($line, $match) !== false) {
                return $index + 1;
            }
        }
        return null;
    }

    protected function isWithinTimeframe($date, $timeframe)
    {
        if (!$timeframe) {
            return true;
        }

        $date = Carbon::parse($date);
        $now = Carbon::now();

        return match ($timeframe) {
            'day' => $date->isAfter($now->copy()->subDay(2)),
            'week' => $date->isAfter($now->copy()->subDays(8)),
            'month' => $date->isAfter($now->copy()->subDays(32)),
            default => true,
        };
    }

    protected function displayUrls($urls, $title)
    {
        $this->info($title);

        if (count($urls) > 0) {
            $this->table(
                ['URL', 'File', 'Date', 'Author', 'Tag'],
                collect($urls)->map(function ($url) {
                    return [
                        $url['url'],
                        $url['file_path'] . ":" . $url['line_number'],
                        $url['date'] ?? 'N/A',
                        $url['author'] ?? 'N/A',
                        $url['tag'] ?? 'N/A',
                    ];
                })
            );
        } else {
            $this->info('No Loom URLs found.');
        }
    }
}
