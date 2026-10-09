@props([
    'companyName' => 'Optimize Academy',
    'facebook' => 'https://facebook.com',
    'instagram' => 'https://instagram.com',
    'youtube' => 'https://youtube.com',
    'linkedin' => 'https://linkedin.com',
    'maps' => 'https://maps.google.com',
])

<footer class="border-t border-zinc-200 py-8 text-xs text-zinc-500 dark:border-zinc-700 dark:text-zinc-400" {{ $attributes }}>
    <div class="mx-auto flex w-full max-w-7xl flex-col items-center justify-center gap-4 px-6 text-center">
        
        <!-- Ícones das Redes Sociais -->
        <div class="flex items-center gap-5">
            <!-- Instagram -->
            <a href="{{ $instagram }}" target="_blank" rel="noopener noreferrer" aria-label="Instagram" class="transition-colors hover:text-zinc-800 dark:hover:text-white">
                <svg class="size-5 fill-current" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                </svg>
            </a>

            <!-- YouTube -->
            <a href="{{ $youtube }}" target="_blank" rel="noopener noreferrer" aria-label="YouTube" class="transition-colors hover:text-zinc-800 dark:hover:text-white">
                <svg class="size-5 fill-current" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                </svg>
            </a>

            <!-- Facebook -->
            <a href="{{ $facebook }}" target="_blank" rel="noopener noreferrer" aria-label="Facebook" class="transition-colors hover:text-zinc-800 dark:hover:text-white">
                <svg class="size-5 fill-current" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M9 8H6v4h3v12h5V12h3.642L18 8h-4V6.333C14 5.375 14.5 5 15.5 5H18V0h-3.808C10.5 0 9 1.519 9 4.747V8z"/>
                </svg>
            </a>

            <!-- LinkedIn -->
            <a href="{{ $linkedin }}" target="_blank" rel="noopener noreferrer" aria-label="LinkedIn" class="transition-colors hover:text-zinc-800 dark:hover:text-white">
                <svg class="size-5 fill-current" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/>
                </svg>
            </a>

            <!-- Google Maps -->
            <a href="{{ $maps }}" target="_blank" rel="noopener noreferrer" aria-label="Google Maps" class="transition-colors hover:text-zinc-800 dark:hover:text-white">
                <svg class="size-5 fill-current" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 0C7.802 0 4 3.403 4 7.602C4 11.8 7.401 16.598 12 24C16.599 16.598 20 11.8 20 7.602C20 3.403 16.198 0 12 0zm0 11.401a3.799 3.799 0 1 1 0-7.598 3.799 3.799 0 0 1 0 7.598z"/>
                </svg>
            </a>
        </div>

        <!-- Texto de Direitos Reservados -->
        <p>&copy; {{ date('Y') }} {{ $companyName }} | Todos os direitos reservados.</p>
        <p>Desenvolvido por <a href="https://optimize.dev.br/" target="_blank" rel="noopener noreferrer" class="text-blue-500 hover:underline"><strong>optimize.dev</strong></a></p>
    </div>
</footer>