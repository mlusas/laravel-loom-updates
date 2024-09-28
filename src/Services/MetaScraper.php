<?php

namespace mlusas\LaravelLoomUpdates\Services;

use GuzzleHttp\Client;

class MetaScraper
{
    private function extractPreloadImageUrl($html)
    {
        preg_match('/<link rel="preload" href="(https:\/\/cdn\.loom\.com\/sessions\/thumbnails\/[^"]+)" as="image"/', $html, $matches);
        return $matches[1] ?? null;
    }

    public function scrape($url)
    {
        $client = new Client();
        $response = $client->get($url);
        $html = $response->getBody()->getContents();

        $title = $this->extractMetaContent($html, 'og:title') ?? $this->extractTitle($html);
        $imageUrl =  $this->extractPreloadImageUrl($html) ?? $this->extractMetaContent($html, 'og:image');

        return [
            'title' => $title,
            'image_url' => $imageUrl,
        ];
    }

    private function extractMetaContent($html, $property)
    {
        preg_match('/<meta property="' . $property . '" content="([^"]+)"/', $html, $matches);
        return $matches[1] ?? null;
    }

    private function extractTitle($html)
    {
        preg_match('/<title>(.*?)<\/title>/', $html, $matches);
        return $matches[1] ?? null;
    }
}