<?php

use App\Enums\CourseStatus;
use App\Enums\Role;
use App\Models\Company;
use App\Models\Course;
use App\Models\CourseModule;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Http;
use Livewire\Volt\Volt;

it('lets an instructor publish a course with ordered modules and same-company student access', function () {
    $company = Company::create(['name' => 'Academy One']);
    $instructor = User::factory()->create([
        'company_id' => $company->id,
        'role' => Role::Instructor,
    ]);
    $student = User::factory()->create([
        'company_id' => $company->id,
        'role' => Role::User,
    ]);

    $this->actingAs($instructor);

    Volt::test('instructor.courses.index')
        ->set('title', 'Secure Course')
        ->set('description', 'Course overview')
        ->set('status', CourseStatus::Published->value)
        ->set('modules', [
            [
                'title' => 'Introduction',
                'slides_url' => 'https://docs.google.com/presentation/d/1234567890/edit',
                'youtube_url' => 'https://youtu.be/abcdefghijk',
            ],
            [
                'title' => 'Advanced topics',
                'slides_url' => 'https://docs.google.com/presentation/d/0987654321/edit',
                'youtube_url' => '',
            ],
        ])
        ->set('material_title', 'Course PDF')
        ->set('material_url', 'https://drive.google.com/file/d/AbCdEfGhIj/view')
        ->set('material_allow_download', true)
        ->set('selectedUsers', [$student->id])
        ->assertSee('https://docs.google.com/presentation/d/1234567890/embed')
        ->assertSee('https://www.youtube-nocookie.com/embed/abcdefghijk')
        ->assertSee('https://drive.google.com/file/d/AbCdEfGhIj/preview')
        ->call('save')
        ->assertHasNoErrors()
        ->assertSee('Curso salvo com sucesso.');

    $course = Course::query()->where('title', 'Secure Course')->firstOrFail();

    expect($course->company_id)->toBe($company->id)
        ->and($course->created_by)->toBe($instructor->id)
        ->and($course->status)->toBe(CourseStatus::Published)
        ->and($course->material_drive_id)->toBe('AbCdEfGhIj')
        ->and($course->material_allow_download)->toBeTrue();

    $this->assertDatabaseHas('course_modules', [
        'course_id' => $course->id,
        'company_id' => $company->id,
        'title' => 'Introduction',
        'position' => 1,
        'slides_id' => '1234567890',
        'youtube_id' => 'abcdefghijk',
    ]);

    $this->assertDatabaseHas('course_modules', [
        'course_id' => $course->id,
        'title' => 'Advanced topics',
        'position' => 2,
        'slides_id' => '0987654321',
        'youtube_id' => null,
    ]);

    $this->assertDatabaseHas('course_user', [
        'course_id' => $course->id,
        'user_id' => $student->id,
        'granted_by' => $instructor->id,
    ]);
});

it('rejects unsupported links before creating a course', function () {
    $company = Company::create(['name' => 'Academy One']);
    $instructor = User::factory()->create([
        'company_id' => $company->id,
        'role' => Role::Instructor,
    ]);

    $this->actingAs($instructor);

    Volt::test('instructor.courses.index')
        ->set('title', 'Invalid Course')
        ->set('modules', [[
            'title' => 'Introduction',
            'slides_url' => 'https://docs.google.com.evil.example/presentation/d/1234567890/edit',
            'youtube_url' => '',
        ]])
        ->call('save')
        ->assertHasErrors(['modules.0.slides_url']);

    $this->assertDatabaseCount('courses', 0);
});

it('rejects granting course access to a user from another company', function () {
    $company = Company::create(['name' => 'Academy One']);
    $otherCompany = Company::create(['name' => 'Academy Two']);
    $instructor = User::factory()->create([
        'company_id' => $company->id,
        'role' => Role::Instructor,
    ]);
    $otherStudent = User::factory()->create([
        'company_id' => $otherCompany->id,
        'role' => Role::User,
    ]);

    $this->actingAs($instructor);

    Volt::test('instructor.courses.index')
        ->set('title', 'Company Course')
        ->set('modules', [[
            'title' => 'Introduction',
            'slides_url' => 'https://docs.google.com/presentation/d/1234567890/edit',
            'youtube_url' => '',
        ]])
        ->set('selectedUsers', [$otherStudent->id])
        ->call('save')
        ->assertHasErrors(['selectedUsers.0']);

    $this->assertDatabaseCount('courses', 0);
});

it('shows only published assigned courses to students and guards course content by tenant and grant', function () {
    $company = Company::create(['name' => 'Academy One']);
    $otherCompany = Company::create(['name' => 'Academy Two']);
    $instructor = User::factory()->create([
        'company_id' => $company->id,
        'role' => Role::Instructor,
    ]);
    $student = User::factory()->create([
        'company_id' => $company->id,
        'role' => Role::User,
    ]);
    $admin = User::factory()->create([
        'company_id' => null,
        'role' => Role::Admin,
    ]);

    $this->actingAs($instructor);
    $published = Course::create([
        'company_id' => $company->id,
        'created_by' => $instructor->id,
        'title' => 'Published and assigned',
        'status' => CourseStatus::Published,
        'material_title' => 'Workbook',
        'material_drive_id' => 'AbCdEfGhIj',
    ]);
    CourseModule::create([
        'course_id' => $published->id,
        'company_id' => $company->id,
        'title' => 'Module one',
        'position' => 1,
        'slides_id' => '1234567890',
        'youtube_id' => 'abcdefghijk',
    ]);
    $published->users()->attach($student->id, [
        'granted_by' => $instructor->id,
        'granted_at' => now(),
    ]);

    $draft = Course::create([
        'company_id' => $company->id,
        'created_by' => $instructor->id,
        'title' => 'Draft course',
        'status' => CourseStatus::Draft,
    ]);
    $draft->users()->attach($student->id, [
        'granted_by' => $instructor->id,
        'granted_at' => now(),
    ]);

    $unassigned = Course::create([
        'company_id' => $company->id,
        'created_by' => $instructor->id,
        'title' => 'Unassigned course',
        'status' => CourseStatus::Published,
    ]);

    $this->actingAs($admin);
    $crossCompanyCourse = Course::create([
        'company_id' => $otherCompany->id,
        'created_by' => null,
        'title' => 'Another company course',
        'status' => CourseStatus::Published,
    ]);

    $this->actingAs($student)
        ->get(route('user.courses.index'))
        ->assertOk()
        ->assertSee('Published and assigned')
        ->assertDontSee('Draft course')
        ->assertDontSee('Unassigned course')
        ->assertDontSee('Another company course');

    $this->get(route('user.courses.show', $published))
        ->assertOk()
        ->assertSee('https://docs.google.com/presentation/d/1234567890/embed')
        ->assertSee('https://www.youtube-nocookie.com/embed/abcdefghijk')
        ->assertSee('referrerpolicy="strict-origin-when-cross-origin"', false)
        ->assertSee('sandbox="allow-scripts allow-same-origin"', false)
        ->assertSee('https://drive.google.com/file/d/AbCdEfGhIj/preview')
        ->assertDontSee('Baixar PDF')
        ->assertDontSee('target="_blank"', false)
        ->assertHeader(
            'Content-Security-Policy',
            "frame-src 'self' https://docs.google.com https://drive.google.com https://www.youtube-nocookie.com"
        );

    $published->update(['material_allow_download' => true]);

    Http::preventStrayRequests();
    Http::fake([
        'https://drive.google.com/uc*' => Http::response(
            '%PDF-1.7 sample PDF',
            200,
            ['Content-Type' => 'application/pdf']
        ),
    ]);

    $this->get(route('user.courses.show', $published))
        ->assertOk()
        ->assertSee('Baixar PDF')
        ->assertSee(route('user.courses.material.download', $published), false)
        ->assertDontSee('href="https://drive.google.com/uc?export=download', false);

    $this->get(route('user.courses.material.download', $published))
        ->assertOk()
        ->assertDownload('workbook.pdf')
        ->assertStreamedContent('%PDF-1.7 sample PDF')
        ->assertHeader('Content-Type', 'application/pdf')
        ->assertHeader('X-Content-Type-Options', 'nosniff');

    $this->get(route('user.courses.show', $unassigned))
        ->assertForbidden();

    $this->get(route('user.courses.show', $crossCompanyCourse))
        ->assertNotFound();

    $this->actingAs($admin)
        ->get(route('user.courses.show', $crossCompanyCourse))
        ->assertOk();
});

it('does not request Drive when downloads are disabled and rejects non-PDF responses', function () {
    $company = Company::create(['name' => 'Academy One']);
    $instructor = User::factory()->create([
        'company_id' => $company->id,
        'role' => Role::Instructor,
    ]);
    $student = User::factory()->create([
        'company_id' => $company->id,
        'role' => Role::User,
    ]);

    $this->actingAs($instructor);
    $course = Course::create([
        'company_id' => $company->id,
        'created_by' => $instructor->id,
        'title' => 'Course material',
        'status' => CourseStatus::Published,
        'material_drive_id' => 'AbCdEfGhIj',
        'material_allow_download' => false,
    ]);
    $course->users()->attach($student->id, [
        'granted_by' => $instructor->id,
        'granted_at' => now(),
    ]);

    Http::preventStrayRequests();
    Http::fake([
        'https://drive.google.com/uc*' => Http::response('<html>not a PDF</html>', 200, [
            'Content-Type' => 'text/html',
        ]),
    ]);

    $this->actingAs($student)
        ->get(route('user.courses.material.download', $course))
        ->assertNotFound();

    Http::assertNothingSent();

    $course->update(['material_allow_download' => true]);

    $this->get(route('user.courses.material.download', $course))
        ->assertServerError();
});

it('allows instructors to manage only courses they created', function () {
    $company = Company::create(['name' => 'Academy One']);
    $owner = User::factory()->create([
        'company_id' => $company->id,
        'role' => Role::Instructor,
    ]);
    $colleague = User::factory()->create([
        'company_id' => $company->id,
        'role' => Role::Instructor,
    ]);

    $this->actingAs($owner);
    $course = Course::create([
        'company_id' => $company->id,
        'created_by' => $owner->id,
        'title' => 'Owner course',
        'status' => CourseStatus::Draft,
    ]);

    expect(Gate::forUser($owner)->allows('update', $course))->toBeTrue()
        ->and(Gate::forUser($colleague)->allows('update', $course))->toBeFalse();
});

it('persists modules in the instructor-selected order after reordering', function () {
    $company = Company::create(['name' => 'Academy One']);
    $instructor = User::factory()->create([
        'company_id' => $company->id,
        'role' => Role::Instructor,
    ]);

    $this->actingAs($instructor);

    Volt::test('instructor.courses.index')
        ->set('title', 'Ordered Course')
        ->set('modules', [
            [
                'title' => 'First module',
                'slides_url' => 'https://docs.google.com/presentation/d/1234567890/edit',
                'youtube_url' => '',
            ],
            [
                'title' => 'Second module',
                'slides_url' => 'https://docs.google.com/presentation/d/0987654321/edit',
                'youtube_url' => '',
            ],
        ])
        ->call('moveModule', 1, 'up')
        ->call('save')
        ->assertHasNoErrors();

    $course = Course::query()->where('title', 'Ordered Course')->firstOrFail();

    expect($course->modules()->orderBy('position')->pluck('title')->all())
        ->toBe(['Second module', 'First module']);
});
