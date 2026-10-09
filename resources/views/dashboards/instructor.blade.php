<x-layouts.app>
    <div class="flex h-full w-full flex-1 flex-col gap-6 rounded-xl p-4 md:p-6">
        <header class="flex flex-col gap-2">
            <p class="text-sm font-medium text-emerald-600 dark:text-emerald-400">Área do instrutor</p>
            <h1 class="text-3xl font-semibold text-zinc-900 dark:text-white">Painel do instrutor</h1>
            <p class="text-sm text-zinc-600 dark:text-zinc-400">
                Gerencie os cursos e acompanhe o aprendizado da equipe de {{ $companyName ?? 'sua empresa' }}.
            </p>
        </header>

        <div class="grid gap-4 md:grid-cols-3">
            <flux:card>
                <h2 class="text-lg font-semibold text-zinc-900 dark:text-white">Cursos</h2>
                <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">
                    Crie cursos e organize módulos e aulas para os colaboradores da sua empresa.
                </p>
            </flux:card>

            <flux:card>
                <h2 class="text-lg font-semibold text-zinc-900 dark:text-white">Materiais</h2>
                <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">
                    Prepare materiais de apoio vinculados ao conteúdo dos cursos.
                </p>
            </flux:card>

            <flux:card>
                <h2 class="text-lg font-semibold text-zinc-900 dark:text-white">Progresso da equipe</h2>
                <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">
                    Acompanhe o andamento dos colaboradores da sua empresa.
                </p>
            </flux:card>
        </div>

        <flux:card>
            <h2 class="text-lg font-semibold text-zinc-900 dark:text-white">Seu espaço de trabalho</h2>
            <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">
                Os recursos de cursos, materiais e acompanhamento ficarão disponíveis aqui conforme os módulos
                da plataforma forem habilitados.
            </p>
        </flux:card>
    </div>
</x-layouts.app>
