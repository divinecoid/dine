# Handling Menu Availability Per Store

Dokumen ini menjelaskan bagaimana menangani ketersediaan menu per store, termasuk handling ketika menu out of stock di store tertentu.

## Struktur Database

### 1. Tabel `mdx_menu_store` (Pivot Table)

Tabel ini menghubungkan menu dengan store dan menyimpan informasi availability per store:

- `mdx_menu_id`: Foreign key ke menu
- `mdx_store_id`: Foreign key ke store
- `is_available`: Boolean - apakah menu tersedia di store ini (default: true)
- `stock_quantity`: Integer nullable - jumlah stok per store
- `out_of_stock_reason`: Text nullable - alasan ketika out of stock
- `min_stock_threshold`: Integer nullable - threshold minimum stok untuk auto-set is_available

### 2. Tabel `mdx_menus`

- `is_available`: Boolean - availability global menu
- `is_active`: Boolean - status aktif menu

**Logic Priority:**
1. Menu harus `is_active = true` dan `is_available = true` (global)
2. Menu harus ada di pivot table `mdx_menu_store` dengan `is_available = true` untuk store tertentu

## Cara Handling Out of Stock

### 1. Set Menu Out of Stock di Store Tertentu

```php
use App\Models\Menu;
use App\Models\Store;

// Method 1: Set is_available = false
$menu = Menu::find(1);
$store = Store::find(1);

$menu->stores()->updateExistingPivot($store->id, [
    'is_available' => false,
    'out_of_stock_reason' => 'Stok habis, menunggu restock',
]);

// Method 2: Update berdasarkan stock_quantity
$menu->stores()->updateExistingPivot($store->id, [
    'stock_quantity' => 0,
    'is_available' => false,
    'out_of_stock_reason' => 'Stok habis',
]);
```

### 2. Auto-Set is_available Berdasarkan Stock Quantity

```php
// Service method untuk auto-update availability berdasarkan stock
public function updateAvailabilityFromStock($menuId, $storeId)
{
    $pivot = DB::table('mdx_menu_store')
        ->where('mdx_menu_id', $menuId)
        ->where('mdx_store_id', $storeId)
        ->first();

    if (!$pivot) {
        return;
    }

    $minThreshold = $pivot->min_stock_threshold ?? 1;
    $isAvailable = ($pivot->stock_quantity ?? 0) >= $minThreshold;

    DB::table('mdx_menu_store')
        ->where('mdx_menu_id', $menuId)
        ->where('mdx_store_id', $storeId)
        ->update([
            'is_available' => $isAvailable,
        ]);
}
```

### 3. Query Menu yang Tersedia di Store Tertentu

```php
use App\Models\Menu;
use App\Models\Store;

// Get semua menu yang tersedia di store tertentu
$store = Store::find(1);

$availableMenus = Menu::where('is_active', true)
    ->where('is_available', true) // Global availability
    ->whereHas('stores', function ($query) use ($store) {
        $query->where('mdx_stores.id', $store->id)
              ->where('mdx_menu_store.is_available', true);
    })
    ->with(['stores' => function ($query) use ($store) {
        $query->where('mdx_stores.id', $store->id)
              ->select('mdx_menu_store.is_available', 'mdx_menu_store.stock_quantity');
    }])
    ->get();

// Atau menggunakan query builder
$availableMenus = DB::table('mdx_menus')
    ->join('mdx_menu_store', 'mdx_menus.id', '=', 'mdx_menu_store.mdx_menu_id')
    ->where('mdx_menus.is_active', true)
    ->where('mdx_menus.is_available', true)
    ->where('mdx_menu_store.mdx_store_id', $storeId)
    ->where('mdx_menu_store.is_available', true)
    ->select('mdx_menus.*', 'mdx_menu_store.stock_quantity', 'mdx_menu_store.out_of_stock_reason')
    ->get();
```

### 4. Query Menu yang Out of Stock di Store Tertentu

```php
// Get menu yang out of stock di store tertentu
$outOfStockMenus = Menu::where('is_active', true)
    ->whereHas('stores', function ($query) use ($storeId) {
        $query->where('mdx_stores.id', $storeId)
              ->where('mdx_menu_store.is_available', false);
    })
    ->with(['stores' => function ($query) use ($storeId) {
        $query->where('mdx_stores.id', $storeId)
              ->select('mdx_menu_store.is_available', 'mdx_menu_store.stock_quantity', 'mdx_menu_store.out_of_stock_reason');
    }])
    ->get();
```

### 5. Restock Menu di Store Tertentu

```php
public function restockMenu($menuId, $storeId, $quantity, $minThreshold = 1)
{
    DB::table('mdx_menu_store')
        ->where('mdx_menu_id', $menuId)
        ->where('mdx_store_id', $storeId)
        ->update([
            'stock_quantity' => $quantity,
            'is_available' => $quantity >= $minThreshold,
            'out_of_stock_reason' => null, // Clear reason when restocked
        ]);
}
```

### 6. Check Availability Menu di Store Tertentu

```php
public function isMenuAvailableInStore($menuId, $storeId): bool
{
    // Check global availability
    $menu = Menu::find($menuId);
    if (!$menu || !$menu->is_active || !$menu->is_available) {
        return false;
    }

    // Check store-specific availability
    $pivot = DB::table('mdx_menu_store')
        ->where('mdx_menu_id', $menuId)
        ->where('mdx_store_id', $storeId)
        ->first();

    if (!$pivot) {
        return false; // Menu tidak ada di store ini
    }

    return $pivot->is_available === 1 || $pivot->is_available === true;
}
```

## Scenario: Menu Out of Stock di Store A, Tapi Tersedia di Store B

```php
// Menu ID 1 out of stock di Store 1
$menu = Menu::find(1);
$store1 = Store::find(1);
$store2 = Store::find(2);

// Set out of stock di Store 1
$menu->stores()->updateExistingPivot($store1->id, [
    'is_available' => false,
    'stock_quantity' => 0,
    'out_of_stock_reason' => 'Stok habis',
]);

// Pastikan tersedia di Store 2
$menu->stores()->syncWithoutDetaching([
    $store2->id => [
        'is_available' => true,
        'stock_quantity' => 10,
    ],
]);

// Query: Menu 1 tersedia di store mana saja?
$storesWithMenu = Store::whereHas('menus', function ($query) use ($menu) {
    $query->where('mdx_menus.id', $menu->id)
          ->where('mdx_menu_store.is_available', true);
})->get();
```

## Best Practices

1. **Always check both levels**: Global (`mdx_menus.is_available`) dan per-store (`mdx_menu_store.is_available`)
2. **Use transactions**: Saat update stock, gunakan database transaction untuk konsistensi
3. **Auto-sync stock**: Implementasi logic untuk auto-update `is_available` ketika `stock_quantity` berubah
4. **Logging**: Simpan log ketika menu di-set out of stock untuk tracking
5. **Notifications**: Kirim notifikasi ke admin ketika stock habis atau mencapai threshold minimum

## Example: Complete Service Class

```php
namespace App\Services;

use App\Models\Menu;
use App\Models\Store;
use Illuminate\Support\Facades\DB;

class MenuAvailabilityService
{
    public function setOutOfStock($menuId, $storeId, $reason = null)
    {
        return DB::table('mdx_menu_store')
            ->where('mdx_menu_id', $menuId)
            ->where('mdx_store_id', $storeId)
            ->update([
                'is_available' => false,
                'stock_quantity' => 0,
                'out_of_stock_reason' => $reason,
            ]);
    }

    public function updateStock($menuId, $storeId, $quantity, $minThreshold = 1)
    {
        return DB::table('mdx_menu_store')
            ->where('mdx_menu_id', $menuId)
            ->where('mdx_store_id', $storeId)
            ->update([
                'stock_quantity' => $quantity,
                'is_available' => $quantity >= $minThreshold,
                'out_of_stock_reason' => $quantity >= $minThreshold ? null : 'Stok tidak mencukupi',
            ]);
    }

    public function getAvailableMenusForStore($storeId)
    {
        return Menu::where('is_active', true)
            ->where('is_available', true)
            ->whereHas('stores', function ($query) use ($storeId) {
                $query->where('mdx_stores.id', $storeId)
                      ->where('mdx_menu_store.is_available', true);
            })
            ->with(['stores' => function ($query) use ($storeId) {
                $query->where('mdx_stores.id', $storeId);
            }])
            ->get();
    }
}
```

