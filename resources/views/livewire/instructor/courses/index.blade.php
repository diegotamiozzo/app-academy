<?php

use App\Enums\CourseStatus;
use App\Enums\Role;
use App\Models\Course;
use App\Models\CourseModule;
use App\Models\User;
use App\Services\CourseLinkParser;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.app')] class extends Component {
    public ?int $editingCourseId = null;

    public string $title = '';

    public string $description = '';

    public string $status = 'draft';

    public string $material_title = '';

    public string $material_url = '';

    public bool $material_allow_download = false;

    public array $modules = [];

    public array $selectedUsers = [];

    public string $userSearch = '';

    public ?string $statusMessage = null;

    public function mount(): void
    {
        Gate::authorize('viewAny', Course::class);
        $this->modules = [$this->blankModule()];
    }

    #[Computed]
    public function courses(): Collection
    {
        return Course::query()
            ->withCount(['modules', 'users'])
            ->where('created_by', auth()->id())
            ->latest()
            ->get();
    }

    #[Computed]
    public function availableUsers(): Collection
    {
        return User::query()
            ->where('company_id', auth()->user()->company_id)
            ->where('role', Role::User->value)
            ->when($this->userSearch !== '', function ($query) {
                $query->where(function ($query) {
                    $query->where('name', 'like', '%'.$this->userSearch.'%')
                        ->orWhere('email', 'like', '%'.$this->userSearch.'%');
                });
            })
            ->orderBy('name')
            ->get(['id', 'name', 'email']);
    }

    #[Computed]
    public function modulePreviewUrls(): array
    {
        $parser = new CourseLinkParser;

        return array_map(fn ($module) => is_array($module) ? [
            'slides' => $parser->slidesEmbedUrl($module['slides_url'] ?? null),
            'youtube' => $parser->youtubeEmbedUrl($module['youtube_url'] ?? null),
        ] : [
            'slides' => null,
            'youtube' => null,
        ], $this->modules);
    }

    #[Computed]
    public function materialPreviewUrl(): ?string
    {
        $parser = new CourseLinkParser;
        $id = $parser->driveFileId($this->material_url);

        return $id === null ? null : $parser->drivePreviewUrlFromId($id);
    }

    public function startCreating(): void
    {
        Gate::authorize('create', Course::class);
        $this->resetForm();
        $this->resetValidation();
    }

    public function editCourse(int $courseId, CourseLinkParser $parser): void
    {
        $course = Course::query()->findOrFail($courseId);
        Gate::authorize('update', $course);

        $this->editingCourseId = $course->id;
        $this->title = $course->title;
        $this->description = $course->description ?? '';
        $this->status = $course->status->value;
        $this->material_title = $course->material_title ?? '';
        $this->material_url = $course->material_drive_id === null
            ? ''
            : $parser->driveViewUrlFromId($course->material_drive_id);
        $this->material_allow_download = $course->material_allow_download;
        $this->modules = $course->modules->map(fn (CourseModule $module) => [
            'title' => $module->title,
            'slides_url' => $parser->slidesEditUrlFromId($module->slides_id),
            'youtube_url' => $module->youtube_id === null
                ? ''
                : $parser->youtubeWatchUrlFromId($module->youtube_id),
        ])->all();
        $this->selectedUsers = $course->users()->pluck('users.id')->map(fn ($id) => (int) $id)->all();
        $this->userSearch = '';
        $this->statusMessage = null;
        $this->resetValidation();
    }

    public function addModule(): void
    {
        $this->modules[] = $this->blankModule();
        unset($this->modulePreviewUrls);
    }

    public function removeModule(int $index): void
    {
        if (count($this->modules) <= 1 || ! array_key_exists($index, $this->modules)) {
            return;
        }

        array_splice($this->modules, $index, 1);
        unset($this->modulePreviewUrls);
    }

    public function moveModule(int $index, string $direction): void
    {
        $targetIndex = $direction === 'up' ? $index - 1 : $index + 1;

        if (! in_array($direction, ['up', 'down'], true)
            || ! array_key_exists($index, $this->modules)
            || ! array_key_exists($targetIndex, $this->modules)) {
            return;
        }

        [$this->modules[$index], $this->modules[$targetIndex]] = [
            $this->modules[$targetIndex],
            $this->modules[$index],
        ];

        unset($this->modulePreviewUrls);
    }

    public function selectAllUsers(): void
    {
        $this->selectedUsers = $this->availableUsers->pluck('id')->map(fn ($id) => (int) $id)->all();
    }

    public function save(CourseLinkParser $parser): void
    {
        $user = auth()->user();
        $companyId = $user->company_id;

        $validated = $this->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:10000'],
            'status' => ['required', Rule::enum(CourseStatus::class)],
            'material_title' => ['nullable', 'string', 'max:255'],
            'material_url' => [
                'nullable',
                'string',
                'max:2048',
                function (string $attribute, mixed $value, \Closure $fail) use ($parser): void {
                    if ($value !== null && $value !== '' && $parser->driveFileId($value) === null) {
                        $fail('Informe um link válido de arquivo PDF do Google Drive.');
                    }
                },
            ],
            'material_allow_download' => ['boolean'],
            'modules' => ['required', 'array', 'min:1'],
            'modules.*.title' => ['required', 'string', 'max:255'],
            'modules.*.slides_url' => [
                'required',
                'string',
                'max:2048',
                function (string $attribute, mixed $value, \Closure $fail) use ($parser): void {
                    if ($parser->slidesId($value) === null) {
                        $fail('Informe um link válido do Google Slides.');
                    }
                },
            ],
            'modules.*.youtube_url' => [
                'nullable',
                'string',
                'max:2048',
                function (string $attribute, mixed $value, \Closure $fail) use ($parser): void {
                    if ($value !== null && $value !== '' && $parser->youtubeVideoId($value) === null) {
                        $fail('Informe um link válido do YouTube.');
                    }
                },
            ],
            'selectedUsers' => ['array'],
            'selectedUsers.*' => [
                'required',
                'integer',
                'distinct',
                Rule::exists('users', 'id')
                    ->where('company_id', $companyId)
                    ->where('role', Role::User->value),
            ],
        ]);

        $course = DB::transaction(function () use ($parser, $validated, $user): Course {
            if ($this->editingCourseId === null) {
                Gate::authorize('create', Course::class);
                $course = new Course([
                    'company_id' => $user->company_id,
                    'created_by' => $user->id,
                ]);
            } else {
                $course = Course::query()->findOrFail($this->editingCourseId);
                Gate::authorize('update', $course);
            }

            $materialId = $parser->driveFileId($validated['material_url'] ?? null);
            $course->fill([
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'status' => $validated['status'],
                'material_title' => $materialId === null
                    ? null
                    : ($validated['material_title'] ?: 'Material complementar'),
                'material_drive_id' => $materialId,
                'material_allow_download' => $materialId !== null && $validated['material_allow_download'],
            ]);
            $course->save();

            $course->modules()->delete();

            foreach ($validated['modules'] as $position => $module) {
                $course->modules()->create([
                    'company_id' => $course->company_id,
                    'title' => $module['title'],
                    'position' => $position + 1,
                    'slides_id' => $parser->slidesId($module['slides_url']),
                    'youtube_id' => $parser->youtubeVideoId($module['youtube_url'] ?? null),
                ]);
            }

            $access = collect($validated['selectedUsers'] ?? [])
                ->mapWithKeys(fn (int $userId) => [
                    $userId => [
                        'granted_by' => $user->id,
                        'granted_at' => now(),
                    ],
                ])
                ->all();

            $course->users()->sync($access);

            return $course;
        });

        $this->resetForm();
        $this->resetValidation();
        $this->statusMessage = 'Curso salvo com sucesso.';
        unset($this->courses);
    }

    private function blankModule(): array
    {
        return [
            'title' => '',
            'slides_url' => '',
            'youtube_url' => '',
        ];
    }

    private function resetForm(): void
    {
        $this->reset([
            'editingCourseId',
            'title',
            'description',
            'status',
            'material_title',
            'material_url',
            'material_allow_download',
            'selectedUsers',
            'userSearch',
        ]);
        $this->modules = [$this->blankModule()];
    }
}; ?>

<div class="mx-auto flex w-full max-w-7xl flex-col gap-6 p-4 md:p-6">
    <header class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div class="flex flex-col gap-2">
            <p class="text-sm font-medium text-emerald-600 dark:text-emerald-400">Área do instrutor</p>
            <h1 class="text-3xl font-semibold text-zinc-900 dark:text-white">Cursos</h1>
            <p class="text-sm text-zinc-600 dark:text-zinc-400">Crie cursos e libere o conteúdo para sua equipe.</p>
        </div>
        <flux:button variant="primary" icon="plus" wire:click="startCreating">Novo curso</flux:button>
    </header>

    @if ($statusMessage)
        <flux:callout variant="success">{{ $statusMessage }}</flux:callout>
    @endif

    <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_minmax(24rem,1fr)]">
        <section class="flex flex-col gap-4">
            <h2 class="text-xl font-semibold text-zinc-900 dark:text-white">Seus cursos</h2>

            @forelse ($this->courses as $course)
                <flux:card class="flex flex-col gap-4">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div class="flex flex-col gap-1">
                            <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">{{ $course->title }}</h3>
                            <p class="text-sm text-zinc-600 dark:text-zinc-400">
                                {{ $course->modules_count }} módulos · {{ $course->users_count }} alunos liberados
                            </p>
                        </div>
                        <flux:badge :color="$course->status === CourseStatus::Published ? 'green' : 'zinc'">
                            {{ $course->status === CourseStatus::Published ? 'Publicado' : 'Rascunho' }}
                        </flux:badge>
                    </div>
                    @if ($course->description)
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ $course->description }}</p>
                    @endif
                    <div>
                        <flux:button size="sm" variant="filled" wire:click="editCourse({{ $course->id }})">
                            Editar curso
                        </flux:button>
                    </div>
                </flux:card>
            @empty
                <flux:card>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400">Você ainda não criou cursos.</p>
                </flux:card>
            @endforelse
        </section>

        <section class="flex flex-col gap-4">
            <h2 class="text-xl font-semibold text-zinc-900 dark:text-white">
                {{ $editingCourseId ? 'Editar curso' : 'Criar curso' }}
            </h2>

            <form wire:submit="save" class="flex flex-col gap-5">
                <flux:card class="flex flex-col gap-4">
                    <flux:input wire:model="title" label="Título do curso" required />
                    <flux:textarea wire:model="description" label="Descrição" rows="3" />
                    <flux:select wire:model="status" label="Status">
                        <flux:select.option value="draft">Rascunho</flux:select.option>
                        <flux:select.option value="published">Publicado</flux:select.option>
                    </flux:select>
                    <flux:error name="title" />
                    <flux:error name="status" />
                </flux:card>

                <flux:card class="flex flex-col gap-4">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex flex-col gap-1">
                            <h3 class="font-semibold text-zinc-900 dark:text-white">Módulos</h3>
                            <p class="text-sm text-zinc-600 dark:text-zinc-400">O Slides é obrigatório; o vídeo é opcional.</p>
                        </div>
                        <flux:button size="sm" variant="filled" icon="plus" wire:click="addModule" type="button">
                            Adicionar
                        </flux:button>
                    </div>

                    @foreach ($modules as $index => $module)
                        <div wire:key="course-module-{{ $index }}" class="flex flex-col gap-3 rounded-lg border border-zinc-200 p-4 dark:border-zinc-700">
                            <div class="flex items-center justify-between gap-3">
                                <h4 class="font-medium text-zinc-900 dark:text-white">Módulo {{ $index + 1 }}</h4>
                                <div class="flex gap-2">
                                    <flux:button size="sm" variant="ghost" icon="arrow-up" aria-label="Mover módulo para cima" wire:click="moveModule({{ $index }}, 'up')" type="button" />
                                    <flux:button size="sm" variant="ghost" icon="arrow-down" aria-label="Mover módulo para baixo" wire:click="moveModule({{ $index }}, 'down')" type="button" />
                                    <flux:button size="sm" variant="ghost" icon="trash" aria-label="Remover módulo" wire:click="removeModule({{ $index }})" type="button" />
                                </div>
                            </div>

                            <flux:input wire:model="modules.{{ $index }}.title" label="Título do módulo" required />
                            <flux:input
                                wire:model.live.debounce.500ms="modules.{{ $index }}.slides_url"
                                label="Link do Google Slides"
                                type="url"
                                placeholder="https://docs.google.com/presentation/d/..."
                                required
                            />
                            <flux:error name="modules.{{ $index }}.title" />
                            <flux:error name="modules.{{ $index }}.slides_url" />
                            <p class="text-sm text-amber-700 dark:text-amber-300">
                                O Slides precisa permitir a visualização pelo link para abrir dentro da plataforma; quem obtiver o ID ainda pode acessar o arquivo no Google.
                            </p>

                            @if (($this->modulePreviewUrls[$index]['slides'] ?? null) !== null)
                                <iframe
                                    class="aspect-video w-full rounded-md border border-zinc-200 dark:border-zinc-700"
                                    src="{{ $this->modulePreviewUrls[$index]['slides'] }}"
                                    title="Prévia do Google Slides — módulo {{ $index + 1 }}"
                                    loading="lazy"
                                    referrerpolicy="strict-origin-when-cross-origin"
                                    sandbox="allow-scripts allow-same-origin"
                                    allowfullscreen
                                ></iframe>
                            @endif

                            <flux:input
                                wire:model.live.debounce.500ms="modules.{{ $index }}.youtube_url"
                                label="Link do YouTube (opcional)"
                                type="url"
                                placeholder="https://youtu.be/..."
                            />
                            <flux:error name="modules.{{ $index }}.youtube_url" />
                            <p class="text-sm text-zinc-600 dark:text-zinc-400">
                                Vídeos não listados são recomendados, mas qualquer pessoa com o link ainda pode assistir.
                            </p>
                            @if (($this->modulePreviewUrls[$index]['youtube'] ?? null) !== null)
                                <iframe
                                    class="aspect-video w-full rounded-md border border-zinc-200 dark:border-zinc-700"
                                    src="{{ $this->modulePreviewUrls[$index]['youtube'] }}"
                                    title="Prévia do vídeo — módulo {{ $index + 1 }}"
                                    loading="lazy"
                                    referrerpolicy="strict-origin-when-cross-origin"
                                    sandbox="allow-scripts allow-same-origin"
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                    allowfullscreen
                                ></iframe>
                            @endif
                        </div>
                    @endforeach
                    <flux:error name="modules" />
                </flux:card>

                <flux:card class="flex flex-col gap-4">
                    <div class="flex flex-col gap-1">
                        <h3 class="font-semibold text-zinc-900 dark:text-white">Material complementar (opcional)</h3>
                        <p class="text-sm text-amber-700 dark:text-amber-300">
                            O Drive pode permitir downloads mesmo quando o botão estiver desativado aqui. Revise as permissões de compartilhamento do PDF.
                        </p>
                    </div>
                    <flux:input wire:model="material_title" label="Título do material" />
                    <flux:input
                        wire:model.live.debounce.500ms="material_url"
                        label="Link do PDF no Google Drive"
                        type="url"
                        placeholder="https://drive.google.com/file/d/..."
                    />
                    <flux:error name="material_url" />
                    @if ($this->materialPreviewUrl)
                        <iframe
                            class="aspect-video w-full rounded-md border border-zinc-200 dark:border-zinc-700"
                            src="{{ $this->materialPreviewUrl }}"
                            title="Prévia do material complementar"
                            loading="lazy"
                            referrerpolicy="strict-origin-when-cross-origin"
                            sandbox="allow-scripts allow-same-origin"
                        ></iframe>
                    @endif
                    <flux:checkbox wire:model="material_allow_download" label="Exibir botão para baixar o PDF" />
                    <p class="text-sm text-zinc-600 dark:text-zinc-400">
                        O PDF ficará disponível para os alunos desde o início do curso.
                    </p>
                </flux:card>

                <flux:card class="flex flex-col gap-4">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div class="flex flex-col gap-1">
                            <h3 class="font-semibold text-zinc-900 dark:text-white">Alunos com acesso</h3>
                            <p class="text-sm text-zinc-600 dark:text-zinc-400">Somente usuários da sua empresa podem ser selecionados.</p>
                        </div>
                        <flux:button size="sm" variant="filled" wire:click="selectAllUsers" type="button">
                            Selecionar todos
                        </flux:button>
                    </div>
                    <flux:input wire:model.live.debounce.300ms="userSearch" label="Buscar aluno" placeholder="Nome ou e-mail" />
                    <div class="flex max-h-64 flex-col gap-2 overflow-y-auto">
                        @forelse ($this->availableUsers as $availableUser)
                            <label wire:key="course-user-{{ $availableUser->id }}" class="flex items-center gap-3 rounded-md p-2 hover:bg-zinc-100 dark:hover:bg-zinc-700">
                                <input
                                    type="checkbox"
                                    wire:model="selectedUsers"
                                    value="{{ $availableUser->id }}"
                                    class="size-4 rounded border-zinc-300 text-indigo-600 focus:ring-indigo-500"
                                />
                                <span class="flex flex-col">
                                    <span class="text-sm font-medium text-zinc-900 dark:text-white">{{ $availableUser->name }}</span>
                                    <span class="text-xs text-zinc-600 dark:text-zinc-400">{{ $availableUser->email }}</span>
                                </span>
                            </label>
                        @empty
                            <p class="text-sm text-zinc-600 dark:text-zinc-400">Nenhum aluno encontrado nesta empresa.</p>
                        @endforelse
                    </div>
                    <flux:error name="selectedUsers" />
                    @foreach ($errors->get('selectedUsers.*') as $messages)
                        @foreach ($messages as $message)
                            <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @endforeach
                    @endforeach
                </flux:card>

                <div class="flex flex-wrap justify-end gap-3">
                    <flux:button type="button" variant="ghost" wire:click="startCreating">Limpar</flux:button>
                    <flux:button type="submit" variant="primary">Salvar curso</flux:button>
                </div>
            </form>
        </section>
    </div>
</div>
