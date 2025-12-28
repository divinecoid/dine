<?php

namespace App\Models\v1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'trx_orders';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'mdx_store_id',
        'mdx_brand_id',
        'mdx_table_id',
        'order_number',
        'order_type',
        'customer_name',
        'customer_phone',
        'customer_email',
        'delivery_address',
        'delivery_latitude',
        'delivery_longitude',
        'status',
        'subtotal',
        'discount_amount',
        'tax_amount',
        'service_charge',
        'total_amount',
        'payment_status',
        'payment_method',
        'notes',
        'admin_notes',
        'ordered_at',
        'confirmed_at',
        'prepared_at',
        'completed_at',
        'cancelled_at',
        'closed_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'delivery_latitude' => 'decimal:8',
            'delivery_longitude' => 'decimal:8',
            'subtotal' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'service_charge' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'ordered_at' => 'datetime',
            'confirmed_at' => 'datetime',
            'prepared_at' => 'datetime',
            'completed_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'closed_at' => 'datetime',
        ];
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            // Auto-generate order number if not provided
            if (empty($order->order_number)) {
                $order->order_number = static::generateOrderNumber();
            }

            // Set ordered_at if not provided
            if (empty($order->ordered_at)) {
                $order->ordered_at = now();
            }

            // Set brand_id from store if not provided
            if (empty($order->mdx_brand_id) && !empty($order->mdx_store_id)) {
                $store = Store::find($order->mdx_store_id);
                if ($store) {
                    $order->mdx_brand_id = $store->mdx_brand_id;
                }
            }
        });

        static::updating(function ($order) {
            // Auto-update status timestamps
            if ($order->isDirty('status')) {
                $now = now();
                switch ($order->status) {
                    case 'confirmed':
                        if (empty($order->confirmed_at)) {
                            $order->confirmed_at = $now;
                        }
                        break;
                    case 'preparing':
                        if (empty($order->prepared_at)) {
                            $order->prepared_at = $now;
                        }
                        break;
                    case 'completed':
                        if (empty($order->completed_at)) {
                            $order->completed_at = $now;
                        }
                        break;
                    case 'cancelled':
                        if (empty($order->cancelled_at)) {
                            $order->cancelled_at = $now;
                        }
                        break;
                }
            }
        });
    }

    /**
     * Generate unique order number.
     */
    public static function generateOrderNumber(): string
    {
        $prefix = 'ORD';
        $date = date('Ymd');

        // Get last order number for today
        $lastOrder = static::whereDate('created_at', today())
            ->where('order_number', 'like', "{$prefix}-{$date}-%")
            ->orderBy('order_number', 'desc')
            ->first();

        if ($lastOrder) {
            // Extract sequence number
            $parts = explode('-', $lastOrder->order_number);
            $sequence = (int) end($parts) + 1;
        } else {
            $sequence = 1;
        }

        return sprintf('%s-%s-%04d', $prefix, $date, $sequence);
    }

    /**
     * Get the store that owns the order.
     */
    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class, 'mdx_store_id');
    }

    /**
     * Get the brand that owns the order.
     */
    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class, 'mdx_brand_id');
    }

    /**
     * Get the table for the order.
     */
    public function table(): BelongsTo
    {
        return $this->belongsTo(Table::class, 'mdx_table_id');
    }

    /**
     * Get the order details.
     */
    public function orderDetails(): HasMany
    {
        return $this->hasMany(OrderDetail::class, 'trx_order_id');
    }

    /**
     * Get the payments for this order.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'trx_order_id');
    }

    /**
     * Calculate and update order totals.
     */
    public function calculateTotals(): void
    {
        $subtotal = $this->orderDetails()->sum('subtotal');

        $this->subtotal = $subtotal;
        $this->total_amount = $subtotal
            - $this->discount_amount
            + $this->tax_amount
            + $this->service_charge;

        $this->save();
    }

    /**
     * Check if order can be cancelled.
     */
    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['pending', 'confirmed', 'preparing']);
    }

    /**
     * Check if order is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Check if order is paid.
     */
    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
    }

    /**
     * Scope a query to filter by store.
     */
    public function scopeForStore($query, int $storeId)
    {
        return $query->where('mdx_store_id', $storeId);
    }

    /**
     * Scope a query to filter by brand.
     */
    public function scopeForBrand($query, int $brandId)
    {
        return $query->where('mdx_brand_id', $brandId);
    }

    /**
     * Scope a query to filter by status.
     */
    public function scopeWithStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to filter by payment status.
     */
    public function scopeWithPaymentStatus($query, string $paymentStatus)
    {
        return $query->where('payment_status', $paymentStatus);
    }

    /**
     * Scope a query to filter by order type.
     */
    public function scopeWithOrderType($query, string $orderType)
    {
        return $query->where('order_type', $orderType);
    }

    /**
     * Scope a query to filter by date range.
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('ordered_at', [$startDate, $endDate]);
    }

    /**
     * Check if order is closed.
     */
    public function isClosed(): bool
    {
        return $this->closed_at !== null;
    }

    /**
     * Scope a query to exclude closed orders.
     */
    public function scopeNotClosed($query)
    {
        return $query->whereNull('closed_at');
    }

    /**
     * Scope a query to only include closed orders.
     */
    public function scopeClosed($query)
    {
        return $query->whereNotNull('closed_at');
    }

    /**
     * Scope a query to filter orders accessible by a user.
     */
    public function scopeAccessibleBy($query, $user)
    {
        $storeIds = $user->getAccessibleStoreIds();
        return $query->whereIn('mdx_store_id', $storeIds);
    }

    /**
     * Check if all orders for a table can be closed.
     * All orders must be paid and completed.
     */
    public static function canCloseTable(int $tableId): bool
    {
        $orders = static::where('mdx_table_id', $tableId)
            ->whereNull('closed_at')
            ->where('status', '!=', 'cancelled')
            ->get();

        if ($orders->isEmpty()) {
            return false; // No orders to close
        }

        // Check if all orders are paid and completed
        foreach ($orders as $order) {
            if ($order->payment_status !== 'paid' || $order->status !== 'completed') {
                return false;
            }
        }

        return true;
    }

    /**
     * Close all orders for a table.
     */
    public static function closeTableOrders(int $tableId): int
    {
        return static::where('mdx_table_id', $tableId)
            ->whereNull('closed_at')
            ->where('status', '!=', 'cancelled')
            ->update(['closed_at' => now()]);
    }
}

