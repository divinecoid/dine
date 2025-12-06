<?php

namespace App\Models\v1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'trx_payments';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'mdx_store_id',
        'mdx_brand_id',
        'trx_order_id',
        'payment_number',
        'amount',
        'payment_method',
        'customer_name',
        'customer_phone',
        'notes',
        'reference_number',
        'xendit_payment_id',
        'xendit_qr_code',
        'status',
        'paid_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'paid_at' => 'datetime',
        ];
    }

    /**
     * Generate unique payment number.
     * Format: PAY-YYYYMMDD-XXXX
     */
    public static function generatePaymentNumber(): string
    {
        $date = now()->format('Ymd');
        $prefix = 'PAY';
        
        // Get the last payment number for today
        $lastPayment = static::whereDate('created_at', today())
            ->where('payment_number', 'like', "{$prefix}-{$date}-%")
            ->orderBy('payment_number', 'desc')
            ->first();
        
        if ($lastPayment) {
            // Extract sequence number
            $parts = explode('-', $lastPayment->payment_number);
            $sequence = isset($parts[2]) ? (int)$parts[2] : 0;
            $sequence++;
        } else {
            $sequence = 1;
        }
        
        return sprintf('%s-%s-%04d', $prefix, $date, $sequence);
    }

    /**
     * Get the store that owns the payment.
     */
    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class, 'mdx_store_id');
    }

    /**
     * Get the brand that owns the payment.
     */
    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class, 'mdx_brand_id');
    }

    /**
     * Get the order for this payment.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'trx_order_id');
    }
}
