<x-layouts.app>
    <div class="flex h-full w-full flex-1 flex-col gap-6 rounded-xl p-4 md:p-6">
        <header class="flex flex-col gap-2">
            <p class="text-sm font-medium text-emerald-600 dark:text-emerald-400">Área do instrutor</p>
            <h1 class="text-3xl font-semibold text-zinc-900 dark:text-white">Painel do instrutor</h1>
            <p class="text-sm text-zinc-600 dark:text-zinc-400">
                Gerencie os cursos e acompanhe o aprendizado da equipe de {{ $companyName ?? 'sua empresa' }}.
            </p>
        </header>

        <flux:card>
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex flex-col gap-1">
                    <h2 class="text-lg font-semibold text-zinc-900 dark:text-white">Gerencie seus cursos</h2>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400">
                        Crie cursos, organize módulos e materiais e libere o acesso para alunos da sua empresa.
                    </p>
                </div>
                <flux:button variant="primary" :href="route('instructor.courses.index')" wire:navigate>
                    Abrir cursos
                </flux:button>
            </div>
        </flux:card>
    </div>
</x-layouts.app>
