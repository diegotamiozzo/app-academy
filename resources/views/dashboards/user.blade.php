<x-layouts.app>
    <div class="flex h-full w-full flex-1 flex-col gap-6 rounded-xl p-4 md:p-6">
        <header class="flex flex-col gap-2">
            <p class="text-sm font-medium text-sky-600 dark:text-sky-400">Área do aluno</p>
            <h1 class="text-3xl font-semibold text-zinc-900 dark:text-white">Minha aprendizagem</h1>
            <p class="text-sm text-zinc-600 dark:text-zinc-400">
                Acesse os cursos publicados para {{ $companyName ?? 'sua empresa' }}.
            </p>
        </header>

        <div class="grid gap-4 md:grid-cols-2">
            <flux:card>
                <h2 class="text-lg font-semibold text-zinc-900 dark:text-white">Meus cursos</h2>
                <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">
                    Os cursos disponibilizados pela sua empresa aparecerão aqui.
                </p>
            </flux:card>

            <flux:card>
                <h2 class="text-lg font-semibold text-zinc-900 dark:text-white">Meu progresso</h2>
                <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">
                    Acompanhe suas aulas concluídas e continue de onde parou.
                </p>
            </flux:card>
        </div>
    </div>
</x-layouts.app>
