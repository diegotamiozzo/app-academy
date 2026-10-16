<x-layouts.app>
    <article class="mx-auto flex w-full max-w-5xl flex-col gap-8 p-4 md:p-6">
        <header class="flex flex-col gap-3">
            <a href="{{ route('user.courses.index') }}" class="text-sm text-indigo-600 hover:underline dark:text-indigo-400" wire:navigate>
                ← Voltar aos meus cursos
            </a>
            <h1 class="text-3xl font-semibold text-zinc-900 dark:text-white">{{ $course->title }}</h1>
            @if ($course->description)
                <p class="text-zinc-600 dark:text-zinc-400">{{ $course->description }}</p>
            @endif
        </header>

        <section class="flex flex-col gap-8" aria-label="Módulos do curso">
            @foreach ($modules as $index => $module)
                <section class="flex flex-col gap-4">
                    <h2 class="text-xl font-semibold text-zinc-900 dark:text-white">
                        {{ $index + 1 }}. {{ $module['title'] }}
                    </h2>
                    <iframe
                        class="aspect-video w-full rounded-lg border border-zinc-200 bg-black dark:border-zinc-700"
                        src="{{ $module['slides_url'] }}"
                        title="Apresentação: {{ $module['title'] }}"
                        loading="lazy"
                        referrerpolicy="strict-origin-when-cross-origin"
                        sandbox="allow-scripts allow-same-origin"
                        allowfullscreen
                    ></iframe>
                    @if ($module['youtube_url'])
                        <iframe
                            class="aspect-video w-full rounded-lg border border-zinc-200 bg-black dark:border-zinc-700"
                            src="{{ $module['youtube_url'] }}"
                            title="Vídeo: {{ $module['title'] }}"
                            loading="lazy"
                            referrerpolicy="strict-origin-when-cross-origin"
                            sandbox="allow-scripts allow-same-origin"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                            allowfullscreen
                        ></iframe>
                    @endif
                </section>
            @endforeach
        </section>

        @if ($course->material_drive_id)
            <section class="flex flex-col gap-4 border-t border-zinc-200 pt-6 dark:border-zinc-700">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div class="flex flex-col gap-1">
                        <h2 class="text-xl font-semibold text-zinc-900 dark:text-white">
                            {{ $course->material_title ?: 'Material complementar' }}
                        </h2>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">Material complementar do curso.</p>
                    </div>
                    @if ($course->material_allow_download)
                        <a
                            href="{{ route('user.courses.material.download', $course) }}"
                            class="inline-flex items-center justify-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-500"
                        >
                            Baixar PDF
                        </a>
                    @endif
                </div>
                <iframe
                    class="aspect-video w-full rounded-lg border border-zinc-200 dark:border-zinc-700"
                    src="{{ $materialPreviewUrl }}"
                    title="Prévia: {{ $course->material_title ?: 'Material complementar' }}"
                    loading="lazy"
                    referrerpolicy="strict-origin-when-cross-origin"
                    sandbox="allow-scripts allow-same-origin"
                ></iframe>
            </section>
        @endif
    </article>
</x-layouts.app>
