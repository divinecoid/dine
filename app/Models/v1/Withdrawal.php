<?php

namespace App\Models\v1;

use Illuminate\Database\Eloquent\Model;

class Withdrawal extends Model
{
    protected $fillable = [
        'user_id',
        'mdx_store_id',
        'mdx_bank_account_id',
        'amount',
        'status',
        'admin_notes',
        'proof_image',
        'processed_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'processed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function store()
    {
        return $this->belongsTo(\App\Models\v1\Store::class, 'mdx_store_id');
    }

    public function bankAccount()
    {
        return $this->belongsTo(\App\Models\v1\BankAccount::class, 'mdx_bank_account_id');
    }
}
