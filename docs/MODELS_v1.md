# Models v1 Documentation

Dokumentasi untuk semua model di folder `app/Models/v1/`.

## Brand Model

**File:** `app/Models/v1/Brand.php`  
**Table:** `mdx_brands`

### Attributes
- `name` - string
- `slug` - string (nullable, unique)
- `description` - text (nullable)
- `logo` - string (nullable)
- `is_active` - boolean (default: true)

### Relationships
- `stores()` - HasMany: Semua store yang dimiliki brand
- `categories()` - HasMany: Semua kategori yang dimiliki brand

### Scopes
- `active()` - Filter hanya brand yang aktif

### Example Usage

```php
use App\Models\v1\Brand;

// Get all active brands
$brands = Brand::active()->get();

// Get brand with stores and categories
$brand = Brand::with(['stores', 'categories'])->find(1);

// Create brand
$brand = Brand::create([
    'name' => 'McDonald\'s',
    'slug' => 'mcdonalds',
    'is_active' => true,
]);
```

## Store Model

**File:** `app/Models/v1/Store.php`  
**Table:** `mdx_stores`

### Attributes
- `mdx_brand_id` - foreign key to brands
- `name` - string
- `slug` - string (nullable, unique)
- `description` - text (nullable)
- `address` - text (nullable)
- `phone` - string (nullable)
- `email` - string (nullable)
- `latitude` - decimal(10,8) (nullable)
- `longitude` - decimal(11,8) (nullable)
- `image` - string (nullable)
- `is_active` - boolean (default: true)

### Relationships
- `brand()` - BelongsTo: Brand yang memiliki store ini
- `menus()` - BelongsToMany: Semua menu di store ini (dengan pivot data: is_available, stock_quantity, out_of_stock_reason, min_stock_threshold)
- `availableMenus()` - BelongsToMany: Hanya menu yang tersedia di store ini

### Scopes
- `active()` - Filter hanya store yang aktif

### Example Usage

```php
use App\Models\v1\Store;

// Get all active stores
$stores = Store::active()->get();

// Get store with brand and available menus
$store = Store::with(['brand', 'availableMenus'])->find(1);

// Get menu availability info for a store
$store = Store::find(1);
$menu = $store->menus()->where('mdx_menus.id', 1)->first();
$isAvailable = $menu->pivot->is_available;
$stockQuantity = $menu->pivot->stock_quantity;

// Set menu out of stock
$store->menus()->updateExistingPivot(1, [
    'is_available' => false,
    'stock_quantity' => 0,
    'out_of_stock_reason' => 'Stok habis',
]);
```

## Category Model

**File:** `app/Models/v1/Category.php`  
**Table:** `mdx_categories`

### Attributes
- `mdx_brand_id` - foreign key to brands
- `name` - string
- `slug` - string (nullable)
- `description` - text (nullable)
- `image` - string (nullable)
- `is_active` - boolean (default: true)
- `sort_order` - integer (default: 0)

### Relationships
- `brand()` - BelongsTo: Brand yang memiliki kategori ini
- `menus()` - BelongsToMany: Semua menu dalam kategori ini

### Scopes
- `active()` - Filter hanya kategori yang aktif
- `ordered()` - Order by sort_order ascending

### Example Usage

```php
use App\Models\v1\Category;

// Get all active categories ordered by sort_order
$categories = Category::active()->ordered()->get();

// Get category with menus
$category = Category::with('menus')->find(1);

// Get categories for a specific brand
$categories = Category::where('mdx_brand_id', 1)->active()->ordered()->get();
```

## Menu Model

**File:** `app/Models/v1/Menu.php`  
**Table:** `mdx_menus`

### Attributes
- `name` - string
- `slug` - string (nullable, unique)
- `description` - text (nullable)
- `price` - decimal(10,2)
- `image` - string (nullable)
- `is_available` - boolean (default: true) - Global availability
- `is_active` - boolean (default: true)
- `sort_order` - integer (default: 0)

### Relationships
- `categories()` - BelongsToMany: Semua kategori yang memiliki menu ini
- `stores()` - BelongsToMany: Semua store yang memiliki menu ini (dengan pivot data: is_available, stock_quantity, out_of_stock_reason, min_stock_threshold)
- `availableStores()` - BelongsToMany: Hanya store dimana menu ini tersedia

### Methods
- `isAvailableInStore(int $storeId): bool` - Check apakah menu tersedia di store tertentu

### Scopes
- `active()` - Filter hanya menu yang aktif dan available
- `availableInStore(int $storeId)` - Filter menu yang tersedia di store tertentu

### Example Usage

```php
use App\Models\v1\Menu;

// Get all active menus
$menus = Menu::active()->get();

// Check if menu is available in a specific store
$menu = Menu::find(1);
$isAvailable = $menu->isAvailableInStore(1);

// Get menu with categories and stores
$menu = Menu::with(['categories', 'stores'])->find(1);

// Get menu available in specific store
$menus = Menu::availableInStore(1)->get();

// Get stores where menu is available
$menu = Menu::find(1);
$availableStores = $menu->availableStores()->get();

// Update menu availability in store
$menu->stores()->updateExistingPivot(1, [
    'is_available' => false,
    'stock_quantity' => 0,
    'out_of_stock_reason' => 'Stok habis',
]);

// Get stock info for menu in a store
$menu = Menu::find(1);
$storePivot = $menu->stores()->where('mdx_stores.id', 1)->first()->pivot;
$stockQuantity = $storePivot->stock_quantity;
$isAvailable = $storePivot->is_available;
```

## Relationship Summary

```
Brand
├── hasMany Stores
└── hasMany Categories

Store
├── belongsTo Brand
└── belongsToMany Menus (through mdx_menu_store)

Category
├── belongsTo Brand
└── belongsToMany Menus (through mdx_category_menu)

Menu
├── belongsToMany Categories (through mdx_category_menu)
└── belongsToMany Stores (through mdx_menu_store)
```

## Complete Example: Get Available Menus for a Store

```php
use App\Models\v1\Store;

$store = Store::find(1);

// Method 1: Using availableMenus relationship
$availableMenus = $store->availableMenus()->get();

// Method 2: Using Menu scope
$availableMenus = Menu::availableInStore($store->id)
    ->with(['categories'])
    ->get();

// Method 3: Manual query with pivot data
$availableMenus = $store->menus()
    ->wherePivot('is_available', true)
    ->where('mdx_menus.is_active', true)
    ->where('mdx_menus.is_available', true)
    ->withPivot(['stock_quantity', 'out_of_stock_reason'])
    ->get();
```

## Complete Example: Get Menus by Brand and Category

```php
use App\Models\v1\Brand;
use App\Models\v1\Category;

$brand = Brand::find(1);
$category = Category::where('mdx_brand_id', $brand->id)->first();

// Get menus in category for brand
$menus = $category->menus()
    ->active()
    ->get();

// Get menus available in all stores of a brand
$brand = Brand::with(['stores.menus' => function ($query) {
    $query->wherePivot('is_available', true);
}])->find(1);
```

