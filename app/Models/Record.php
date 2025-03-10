<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class Record extends Model
{
    /**
     * @var string[]
     */
    protected $fillable = [
        'name', 'date', 'custom_id'
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'date' => 'date'
    ];
}
