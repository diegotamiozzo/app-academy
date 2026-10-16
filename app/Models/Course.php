<?php

namespace App\Models;

use App\Enums\CourseStatus;
use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
    use BelongsToCompany, SoftDeletes;

    protected $fillable = [
        'company_id',
        'created_by',
        'title',
        'description',
        'status',
        'material_title',
        'material_drive_id',
        'material_allow_download',
    ];

    protected function casts(): array
    {
        return [
            'status' => CourseStatus::class,
            'material_allow_download' => 'boolean',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function modules(): HasMany
    {
        return $this->hasMany(CourseModule::class)->orderBy('position');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot(['granted_by', 'granted_at']);
    }
}
