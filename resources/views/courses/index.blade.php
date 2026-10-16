<x-layouts.app>
    <div class="mx-auto flex w-full max-w-6xl flex-col gap-6 p-4 md:p-6">
        <header class="flex flex-col gap-2">
            <p class="text-sm font-medium text-sky-600 dark:text-sky-400">Área do aluno</p>
            <h1 class="text-3xl font-semibold text-zinc-900 dark:text-white">Meus cursos</h1>
            <p class="text-sm text-zinc-600 dark:text-zinc-400">Cursos publicados e liberados para você.</p>
        </header>

        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            @forelse ($courses as $course)
                <flux:card class="flex h-full flex-col gap-4">
                    <div class="flex flex-col gap-2">
                        <h2 class="text-xl font-semibold text-zinc-900 dark:text-white">{{ $course->title }}</h2>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">
                            {{ $course->modules_count }} {{ $course->modules_count === 1 ? 'módulo' : 'módulos' }}
                        </p>
                    </div>
                    @if ($course->description)
                        <p class="text-sm text-zinc-700 dark:text-zinc-300">{{ $course->description }}</p>
                    @endif
                    <div class="mt-auto">
                        <flux:button variant="primary" :href="route('user.courses.show', $course)" wire:navigate>
                            Acessar curso
                        </flux:button>
                    </div>
                </flux:card>
            @empty
                <flux:card class="md:col-span-2 xl:col-span-3">
                    <p class="text-sm text-zinc-600 dark:text-zinc-400">
                        Ainda não há cursos publicados e liberados para você.
                    </p>
                </flux:card>
            @endforelse
        </div>
    </div>
</x-layouts.app>
