<?php

namespace App\Models\v1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderDetail extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'trx_order_details';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'trx_order_id',
        'mdx_menu_id',
        'quantity',
        'unit_price',
        'discount_amount',
        'subtotal',
        'menu_name',
        'menu_description',
        'notes',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'unit_price' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'subtotal' => 'decimal:2',
        ];
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($orderDetail) {
            // Set menu snapshot if not provided
            if (empty($orderDetail->menu_name) || empty($orderDetail->menu_description)) {
                $menu = Menu::find($orderDetail->mdx_menu_id);
                if ($menu) {
                    $orderDetail->menu_name = $menu->name;
                    $orderDetail->menu_description = $menu->description;
                }
            }

            // Calculate subtotal if not provided
            if (empty($orderDetail->subtotal)) {
                $orderDetail->subtotal = ($orderDetail->unit_price * $orderDetail->quantity) 
                    - $orderDetail->discount_amount;
            }
        });

        static::saved(function ($orderDetail) {
            // Recalculate order totals when detail is saved
            if ($orderDetail->order) {
                $orderDetail->order->calculateTotals();
            }
        });

        static::deleted(function ($orderDetail) {
            // Recalculate order totals when detail is deleted
            if ($orderDetail->order) {
                $orderDetail->order->calculateTotals();
            }
        });
    }

    /**
     * Get the order that owns the detail.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'trx_order_id');
    }

    /**
     * Get the menu for the detail.
     */
    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class, 'mdx_menu_id');
    }

    /**
     * Calculate subtotal.
     */
    public function calculateSubtotal(): float
    {
        return ($this->unit_price * $this->quantity) - $this->discount_amount;
    }

    /**
     * Update subtotal.
     */
    public function updateSubtotal(): void
    {
        $this->subtotal = $this->calculateSubtotal();
        $this->save();
    }
}

