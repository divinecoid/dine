<?php

namespace App\Models\v1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Table extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'mdx_tables';

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($table) {
            // Auto-generate unique identifier if not provided
            if (empty($table->unique_identifier)) {
                $table->unique_identifier = (string) \Illuminate\Support\Str::uuid();
            }
        });
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'mdx_store_id',
        'table_number',
        'name',
        'capacity',
        'status',
        'zone',
        'floor',
        'notes',
        'sort_order',
        'is_active',
        'unique_identifier',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'capacity' => 'integer',
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the store that owns the table.
     */
    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class, 'mdx_store_id');
    }

    /**
     * Get the orders for the table.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'mdx_table_id');
    }

    /**
     * Scope a query to only include active tables.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to filter by status.
     */
    public function scopeWithStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to filter by zone.
     */
    public function scopeInZone($query, string $zone)
    {
        return $query->where('zone', $zone);
    }

    /**
     * Find table by unique identifier.
     */
    public static function findByUniqueIdentifier(string $uniqueIdentifier): ?self
    {
        return static::where('unique_identifier', $uniqueIdentifier)->first();
    }

    /**
     * Scope a query to filter tables accessible by a user.
     */
    public function scopeAccessibleBy($query, $user)
    {
        $storeIds = $user->getAccessibleStoreIds();
        return $query->whereIn('mdx_store_id', $storeIds);
    }
}

