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
        'lapin_kg',
        'poulet_goliath_kg',
        'deliveries',
        'optional_prepayment',
        'status',
        'receipt_url',
        'confirmation_sent',
        'submitted_at',
        'ip_address'
    ];

    protected $casts = [
        'deliveries' => 'array',
        'optional_prepayment' => 'decimal:2',
        'confirmation_sent' => 'boolean',
        'submitted_at' => 'datetime'
    ];
}
