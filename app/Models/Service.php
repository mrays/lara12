<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'product',
        'domain',
        'price',
        'billing_cycle',
        'registration_date',
        'due_date',
        'ip',
        'status',
    ];

    protected $casts = [
        'registration_date' => 'date',
        'due_date' => 'date',
        'price' => 'decimal:2',
    ];

    // hubungan: Service milik 1 Client
    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
