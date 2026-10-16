<?php

namespace App\Services;

class CourseLinkParser
{
    public function slidesId(?string $url): ?string
    {
        $path = $this->pathForHost($url, ['docs.google.com']);

        if ($path === null || ! preg_match('~^/presentation/d/([A-Za-z0-9_-]{10,})(?:/(?:edit|present|embed))?/?$~', $path, $matches)) {
            return null;
        }

        return $matches[1];
    }

    public function driveFileId(?string $url): ?string
    {
        $path = $this->pathForHost($url, ['drive.google.com']);

        if ($path === null || ! preg_match('~^/file/d/([A-Za-z0-9_-]{10,})(?:/(?:view|edit|preview))?/?$~', $path, $matches)) {
            return null;
        }

        return $matches[1];
    }

    public function youtubeVideoId(?string $url): ?string
    {
        $parts = $this->partsForHosts($url, ['youtube.com', 'www.youtube.com', 'm.youtube.com', 'youtu.be', 'www.youtu.be']);

        if ($parts === null) {
            return null;
        }

        $path = $parts['path'] ?? '';
        $videoId = null;

        if (in_array(strtolower($parts['host']), ['youtu.be', 'www.youtu.be'], true)) {
            $videoId = trim($path, '/');
        } elseif ($path === '/watch') {
            parse_str($parts['query'] ?? '', $query);
            $videoId = $query['v'] ?? null;
        } elseif (preg_match('~^/(?:embed|shorts|live)/([A-Za-z0-9_-]{11})/?$~', $path, $matches)) {
            $videoId = $matches[1];
        }

        return is_string($videoId) && preg_match('/^[A-Za-z0-9_-]{11}$/D', $videoId)
            ? $videoId
            : null;
    }

    public function slidesEmbedUrl(?string $url): ?string
    {
        $id = $this->slidesId($url);

        return $id === null ? null : $this->slidesEmbedUrlFromId($id);
    }

    public function slidesEmbedUrlFromId(string $id): string
    {
        return 'https://docs.google.com/presentation/d/'.$id.'/embed';
    }

    public function slidesEditUrlFromId(string $id): string
    {
        return 'https://docs.google.com/presentation/d/'.$id.'/edit';
    }

    public function youtubeEmbedUrl(?string $url): ?string
    {
        $id = $this->youtubeVideoId($url);

        return $id === null ? null : $this->youtubeEmbedUrlFromId($id);
    }

    public function youtubeEmbedUrlFromId(string $id): string
    {
        return 'https://www.youtube-nocookie.com/embed/'.$id;
    }

    public function youtubeWatchUrlFromId(string $id): string
    {
        return 'https://www.youtube.com/watch?v='.$id;
    }

    public function drivePreviewUrlFromId(string $id): string
    {
        return 'https://drive.google.com/file/d/'.$id.'/preview';
    }

    public function driveDownloadUrlFromId(string $id): string
    {
        return 'https://drive.google.com/uc?export=download&id='.$id;
    }

    public function driveViewUrlFromId(string $id): string
    {
        return 'https://drive.google.com/file/d/'.$id.'/view';
    }

    /**
     * @param  list<string>  $allowedHosts
     * @return array{scheme: string, host: string, path?: string, query?: string}|null
     */
    private function partsForHosts(?string $url, array $allowedHosts): ?array
    {
        if ($url === null || filter_var($url, FILTER_VALIDATE_URL) === false) {
            return null;
        }

        $parts = parse_url($url);

        if (! is_array($parts)
            || strtolower($parts['scheme'] ?? '') !== 'https'
            || ! in_array(strtolower($parts['host'] ?? ''), $allowedHosts, true)
            || isset($parts['user'])
            || isset($parts['pass'])
            || isset($parts['port'])) {
            return null;
        }

        return $parts;
    }

    /**
     * @param  list<string>  $allowedHosts
     */
    private function pathForHost(?string $url, array $allowedHosts): ?string
    {
        $parts = $this->partsForHosts($url, $allowedHosts);

        return $parts['path'] ?? null;
    }
}
