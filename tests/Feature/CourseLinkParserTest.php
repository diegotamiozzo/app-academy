<?php

use App\Services\CourseLinkParser;

it('extracts IDs and constructs safe embed URLs for supported providers', function () {
    $parser = new CourseLinkParser;

    expect($parser->slidesId('https://docs.google.com/presentation/d/1234567890/edit?usp=sharing'))
        ->toBe('1234567890')
        ->and($parser->slidesEmbedUrl('https://docs.google.com/presentation/d/1234567890/edit'))
        ->toBe('https://docs.google.com/presentation/d/1234567890/embed')
        ->and($parser->youtubeVideoId('https://youtu.be/abcdefghijk'))
        ->toBe('abcdefghijk')
        ->and($parser->youtubeEmbedUrl('https://www.youtube.com/watch?v=abcdefghijk'))
        ->toBe('https://www.youtube-nocookie.com/embed/abcdefghijk')
        ->and($parser->driveFileId('https://drive.google.com/file/d/AbCdEfGhIj/view?usp=sharing'))
        ->toBe('AbCdEfGhIj')
        ->and($parser->drivePreviewUrlFromId('AbCdEfGhIj'))
        ->toBe('https://drive.google.com/file/d/AbCdEfGhIj/preview')
        ->and($parser->driveDownloadUrlFromId('AbCdEfGhIj'))
        ->toBe('https://drive.google.com/uc?export=download&id=AbCdEfGhIj');
});

it('rejects unsupported hosts, insecure URLs, malformed paths, and invalid video IDs', function () {
    $parser = new CourseLinkParser;

    expect($parser->slidesId('https://docs.google.com.evil.example/presentation/d/1234567890/edit'))
        ->toBeNull()
        ->and($parser->slidesId('http://docs.google.com/presentation/d/1234567890/edit'))
        ->toBeNull()
        ->and($parser->slidesId('https://drive.google.com/file/d/1234567890/view'))
        ->toBeNull()
        ->and($parser->driveFileId('https://drive.google.com/open?id=1234567890'))
        ->toBeNull()
        ->and($parser->youtubeVideoId('https://youtube.com/watch?v=short'))
        ->toBeNull()
        ->and($parser->youtubeVideoId('https://youtube.com.evil.example/watch?v=abcdefghijk'))
        ->toBeNull();
});
