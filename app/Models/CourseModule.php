<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseModule extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'course_id',
        'company_id',
        'title',
        'position',
        'slides_id',
        'youtube_id',
    ];

    protected function casts(): array
    {
        return [
            'position' => 'integer',
        ];
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
}
