<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="flex min-h-screen flex-col bg-white text-zinc-800 antialiased dark:bg-zinc-800 dark:text-zinc-100">
        <header class="border-b border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            <div class="mx-auto flex w-full max-w-7xl items-center justify-between gap-3 px-4 py-4 sm:gap-6 sm:px-6">
                <a href="{{ route('home') }}" aria-label="Optimize Academy — início">
                    <x-app-logo class="h-auto w-28 text-zinc-900 dark:text-white sm:w-40" />
                </a>

                <nav class="flex shrink-0 items-center gap-2 text-xs sm:gap-4 sm:text-sm" aria-label="Acesso à plataforma">
                    @auth
                        @if (Route::has('dashboard'))
                            <a
                                href="{{ route('dashboard') }}"
                                class="text-zinc-600 hover:text-zinc-900 dark:text-zinc-300 dark:hover:text-white"
                            >
                                Acessar Painel
                            </a>
                        @endif
                    @else
                        @if (Route::has('login'))
                            <a
                                href="{{ route('login') }}"
                                class="text-zinc-600 hover:text-zinc-900 dark:text-zinc-300 dark:hover:text-white"
                            >
                                Entrar
                            </a>
                        @endif

                        @if (Route::has('register'))
                            <a
                                href="{{ route('register') }}"
                                class="rounded-lg bg-zinc-800 px-3 py-1.5 font-medium text-white hover:bg-zinc-700 sm:px-4 sm:py-2 dark:bg-white dark:text-zinc-900 dark:hover:bg-zinc-200"
                            >
                                Criar conta
                            </a>
                        @endif
                    @endauth
                </nav>
            </div>
        </header>

        <main class="mx-auto flex w-full max-w-7xl flex-1 flex-col px-6">
            <section class="max-w-3xl py-20 sm:py-28" aria-labelledby="academy-title">
                <p class="mb-4 text-sm font-medium text-zinc-500 dark:text-zinc-400">Seu próximo passo começa aqui</p>
                <h1 id="academy-title" class="text-4xl leading-tight font-semibold tracking-tight text-zinc-900 sm:text-5xl dark:text-white">
                    Optimize Academy
                    <span class="block text-zinc-600 dark:text-zinc-300">Conhecimento que move você.</span>
                </h1>
                <p class="mt-6 max-w-2xl text-base leading-7 text-zinc-600 sm:text-lg dark:text-zinc-300">
                    Cursos e capacitação online para desenvolver novas habilidades e abrir novas possibilidades na sua jornada profissional.
                </p>

                <div class="mt-8 flex flex-wrap gap-3">
                    @auth
                        @if (Route::has('dashboard'))
                            <a
                                href="{{ route('dashboard') }}"
                                class="inline-flex items-center justify-center rounded-lg bg-zinc-800 px-5 py-3 text-sm font-medium text-white hover:bg-zinc-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-zinc-500 dark:bg-white dark:text-zinc-900 dark:hover:bg-zinc-200"
                            >
                                Acessar Painel
                            </a>
                        @endif
                    @else
                        @if (Route::has('login'))
                            <a
                                href="{{ route('login') }}"
                                class="inline-flex items-center justify-center rounded-lg bg-zinc-800 px-5 py-3 text-sm font-medium text-white hover:bg-zinc-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-zinc-500 dark:bg-white dark:text-zinc-900 dark:hover:bg-zinc-200"
                            >
                                Entrar na Plataforma
                            </a>
                        @endif

                        @if (Route::has('register'))
                            <a
                                href="{{ route('register') }}"
                                class="inline-flex items-center justify-center rounded-lg border border-zinc-300 px-5 py-3 text-sm font-medium text-zinc-700 hover:bg-zinc-50 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-zinc-500 dark:border-zinc-600 dark:text-zinc-200 dark:hover:bg-zinc-700"
                            >
                                Criar Conta
                            </a>
                        @endif
                    @endauth
                </div>
            </section>

            <section class="grid gap-8 border-t border-zinc-200 py-8 sm:grid-cols-3 dark:border-zinc-700" aria-label="Aprendizado e desenvolvimento">
                <div>
                    <svg class="mb-4 size-6 text-zinc-600 dark:text-zinc-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <rect x="3" y="4" width="18" height="13" rx="2"/>
                        <path d="M8 21h8M12 17v4m-2-13 5 3-5 3V8Z"/>
                    </svg>
                    <h2 class="mb-2 text-base font-semibold text-zinc-900 dark:text-white">Aprenda online</h2>
                    <p class="text-sm leading-6 text-zinc-600 dark:text-zinc-300">Conhecimento acessível para acompanhar a sua jornada.</p>
                </div>
                <div>
                    <svg class="mb-4 size-6 text-zinc-600 dark:text-zinc-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M12 6c-3-2-6-2-9-1v15c3-1 6-1 9 1 3-2 6-2 9-1V5c-3-1-6-1-9 1Zm0 0v15"/>
                    </svg>
                    <h2 class="mb-2 text-base font-semibold text-zinc-900 dark:text-white">Desenvolva habilidades</h2>
                    <p class="text-sm leading-6 text-zinc-600 dark:text-zinc-300">Amplie seus conhecimentos e encontre novas perspectivas.</p>
                </div>
                <div>
                    <svg class="mb-4 size-6 text-zinc-600 dark:text-zinc-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M5 19 19 5M5 5h14v14"/>
                    </svg>
                    <h2 class="mb-2 text-base font-semibold text-zinc-900 dark:text-white">Vá além</h2>
                    <p class="text-sm leading-6 text-zinc-600 dark:text-zinc-300">Transforme o aprendizado em um novo passo na sua evolução.</p>
                </div>
            </section>
        </main>

        <footer class="border-t border-zinc-200 py-6 text-xs text-zinc-500 dark:border-zinc-700 dark:text-zinc-400">
            <div class="mx-auto w-full max-w-7xl px-6">
                <p>&copy; {{ date('Y') }} Optimize Academy Todos os direitos reservados.</p>
            </div>
        </footer>

        @fluxScripts
    </body>
</html>