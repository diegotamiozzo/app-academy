<x-layouts.app>
    <div class="flex h-full w-full flex-1 flex-col gap-6 rounded-xl p-4 md:p-6">
        <header class="flex flex-col gap-2">
            <p class="text-sm font-medium text-sky-600 dark:text-sky-400">Área do aluno</p>
            <h1 class="text-3xl font-semibold text-zinc-900 dark:text-white">Minha aprendizagem</h1>
            <p class="text-sm text-zinc-600 dark:text-zinc-400">
                Acesse os cursos publicados para {{ $companyName ?? 'sua empresa' }}.
            </p>
        </header>

        <flux:card>
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex flex-col gap-1">
                    <h2 class="text-lg font-semibold text-zinc-900 dark:text-white">Meus cursos</h2>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400">
                        Acesse os cursos publicados e liberados para você.
                    </p>
                </div>
                <flux:button variant="primary" :href="route('user.courses.index')" wire:navigate>
                    Ver cursos
                </flux:button>
            </div>
        </flux:card>
    </div>
</x-layouts.app>
