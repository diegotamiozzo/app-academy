<x-layouts.app>
    <div class="flex h-full w-full flex-1 flex-col gap-6 rounded-xl p-4 md:p-6">
        <header class="flex flex-col gap-2">
            <p class="text-sm font-medium text-indigo-600 dark:text-indigo-400">Administração da plataforma</p>
            <h1 class="text-3xl font-semibold text-zinc-900 dark:text-white">Painel do administrador</h1>
            <p class="text-sm text-zinc-600 dark:text-zinc-400">
                Visão geral das empresas, licenças e usuários da Optimize Academy.
            </p>
        </header>

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <flux:card>
                <p class="text-sm text-zinc-600 dark:text-zinc-400">Empresas</p>
                <p class="mt-2 text-3xl font-semibold text-zinc-900 dark:text-white">{{ $companyCount }}</p>
            </flux:card>

            <flux:card>
                <p class="text-sm text-zinc-600 dark:text-zinc-400">Usuários</p>
                <p class="mt-2 text-3xl font-semibold text-zinc-900 dark:text-white">{{ $userCount }}</p>
            </flux:card>

            <flux:card>
                <p class="text-sm text-zinc-600 dark:text-zinc-400">Compras de licenças</p>
                <p class="mt-2 text-3xl font-semibold text-zinc-900 dark:text-white">{{ $purchaseCount }}</p>
            </flux:card>

            <flux:card>
                <p class="text-sm text-zinc-600 dark:text-zinc-400">Códigos de acesso</p>
                <p class="mt-2 text-3xl font-semibold text-zinc-900 dark:text-white">{{ $accessCodeCount }}</p>
            </flux:card>
        </div>

        <flux:card>
            <h2 class="text-lg font-semibold text-zinc-900 dark:text-white">Gestão da plataforma</h2>
            <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">
                Este espaço reúne as ferramentas administrativas para gerenciar empresas, compras de licenças,
                códigos de acesso, usuários e cursos de todas as empresas.
            </p>
        </flux:card>
    </div>
</x-layouts.app>
