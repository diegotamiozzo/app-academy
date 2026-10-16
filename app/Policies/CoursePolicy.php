<?php

namespace App\Policies;

use App\Enums\CourseStatus;
use App\Enums\Role;
use App\Models\Course;
use App\Models\User;

class CoursePolicy
{
    public function before(User $user): ?bool
    {
        return $user->role === Role::Admin ? true : null;
    }

    public function viewAny(User $user): bool
    {
        return in_array($user->role, [Role::Instructor, Role::User], true)
            && $user->company_id !== null;
    }

    public function view(User $user, Course $course): bool
    {
        if ($user->role === Role::Instructor) {
            return $user->company_id === $course->company_id
                && $course->created_by === $user->id;
        }

        return $user->role === Role::User
            && $user->company_id === $course->company_id
            && $course->status === CourseStatus::Published
            && $course->users()->whereKey($user->id)->exists();
    }

    public function create(User $user): bool
    {
        return $user->role === Role::Instructor && $user->company_id !== null;
    }

    public function update(User $user, Course $course): bool
    {
        return $user->role === Role::Instructor
            && $user->company_id === $course->company_id
            && $course->created_by === $user->id;
    }

    public function delete(User $user, Course $course): bool
    {
        return $this->update($user, $course);
    }
}
