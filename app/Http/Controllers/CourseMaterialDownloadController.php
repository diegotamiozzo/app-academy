<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Client\Response as ClientResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CourseMaterialDownloadController extends Controller
{
    public function __invoke(Course $course): StreamedResponse
    {
        Gate::authorize('view', $course);

        abort_unless($course->material_drive_id !== null && $course->material_allow_download, 404);

        $upstream = Http::withOptions(['stream' => true])
            ->get('https://drive.google.com/uc', [
                'export' => 'download',
                'id' => $course->material_drive_id,
            ]);

        abort_unless(
            $upstream->successful() && $this->isPdf($upstream),
            502,
            'O material não pôde ser baixado do Google Drive.'
        );

        $stream = $upstream->getBody()->detach();

        abort_unless(is_resource($stream), 502, 'O material não pôde ser baixado do Google Drive.');

        $filename = (Str::slug($course->material_title ?: $course->title) ?: 'material').'.pdf';

        return response()->streamDownload(function () use ($stream): void {
            try {
                fpassthru($stream);
            } finally {
                fclose($stream);
            }
        }, $filename, [
            'Content-Type' => 'application/pdf',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    private function isPdf(ClientResponse $response): bool
    {
        return str_starts_with(
            strtolower($response->header('Content-Type', '')),
            'application/pdf'
        );
    }
}
