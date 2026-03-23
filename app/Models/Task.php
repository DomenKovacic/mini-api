<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
    // pove Laravelu kateri fieldi so varni za assignment

        'title',
        'description',
        'status',
        'priority',
        'due_date',
        'external_reference',
        'metadata',
    ];

    protected function casts(): array
    {
        // pove Laravelu naj tretira due_date kot date ter metadato kot array
        return [
            'due_date' => 'date',
            'metadata' => 'array',
        ];
    }
}