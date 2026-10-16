<?php

use App\Enums\CourseStatus;
use App\Enums\Role;
use App\Http\Controllers\CourseMaterialDownloadController;
use App\Models\AccessCode;
use App\Models\Company;
use App\Models\Course;
use App\Models\LicensePurchase;
use App\Models\User;
use App\Services\CourseLinkParser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('dashboard', function (Request $request) {
    return match ($request->user()->role) {
        Role::Admin => redirect()->route('admin'),
        Role::Instructor => redirect()->route('instructor.dashboard'),
        Role::User => redirect()->route('user.dashboard'),
    };
})
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::get('admin', function () {
    return view('dashboards.admin', [
        'companyCount' => Company::count(),
        'userCount' => User::count(),
        'purchaseCount' => LicensePurchase::count(),
        'accessCodeCount' => AccessCode::count(),
    ]);
})
    ->middleware(['auth', 'verified', 'role:admin'])
    ->name('admin');

Route::get('instructor/dashboard', function (Request $request) {
    return view('dashboards.instructor', [
        'companyName' => $request->user()->company?->name,
    ]);
})
    ->middleware(['auth', 'verified', 'role:instructor'])
    ->name('instructor.dashboard');

Route::get('user/dashboard', function (Request $request) {
    return view('dashboards.user', [
        'companyName' => $request->user()->company?->name,
    ]);
})
    ->middleware(['auth', 'verified', 'role:user'])
    ->name('user.dashboard');

Route::middleware(['auth', 'verified', 'role:instructor'])->group(function () {
    Volt::route('instructor/courses', 'instructor.courses.index')
        ->name('instructor.courses.index');
});

Route::middleware(['auth', 'verified', 'role:user'])->group(function () {
    Route::get('user/courses', function (Request $request) {
        $courses = Course::query()
            ->where('status', CourseStatus::Published)
            ->whereHas('users', fn ($query) => $query->whereKey($request->user()->id))
            ->withCount('modules')
            ->latest()
            ->get();

        return view('courses.index', compact('courses'));
    })->name('user.courses.index');

});

Route::get('user/courses/{course}', function (Course $course, CourseLinkParser $linkParser) {
    Gate::authorize('view', $course);

    $modules = $course->modules->map(fn ($module) => [
        'title' => $module->title,
        'slides_url' => $linkParser->slidesEmbedUrlFromId($module->slides_id),
        'youtube_url' => $module->youtube_id === null
            ? null
            : $linkParser->youtubeEmbedUrlFromId($module->youtube_id),
    ]);

    return view('courses.show', [
        'course' => $course,
        'modules' => $modules,
        'materialPreviewUrl' => $course->material_drive_id === null
            ? null
            : $linkParser->drivePreviewUrlFromId($course->material_drive_id),
    ]);
})
    ->middleware(['auth', 'verified'])
    ->whereNumber('course')
    ->name('user.courses.show');

Route::get('user/courses/{course}/material/download', CourseMaterialDownloadController::class)
    ->middleware(['auth', 'verified'])
    ->whereNumber('course')
    ->name('user.courses.material.download');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
});

require __DIR__.'/auth.php';
