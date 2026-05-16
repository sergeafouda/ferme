<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Preorder extends Model
{
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'project_note',
        'total_kg',
        'fruit_kg',
        'puree_kg',
        'deliveries',
        'optional_prepayment',
        'status',
        'submitted_at',
        'ip_address'
    ];

    protected $casts = [
        'deliveries' => 'array',
        'optional_prepayment' => 'decimal:2',
        'submitted_at' => 'datetime'
    ];
}
