# API v1 Documentation

Dokumentasi lengkap untuk API version 1.

**Base URL:** `/api/v1`

## Brands API

### Get All Brands
```
GET /api/v1/brands
```

**Query Parameters:**
- `is_active` (boolean, optional) - Filter by active status
- `search` (string, optional) - Search by name
- `order_by` (string, optional, default: `created_at`) - Order by column
- `order_dir` (string, optional, default: `desc`) - Order direction (asc/desc)
- `per_page` (integer, optional, default: 15) - Items per page

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "McDonald's",
      "slug": "mcdonalds",
      "description": "...",
      "logo": "...",
      "is_active": true,
      "created_at": "...",
      "updated_at": "..."
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 1,
    "per_page": 15,
    "total": 1
  }
}
```

### Get Single Brand
```
GET /api/v1/brands/{brand}
```

**Response:** Brand dengan stores dan categories

### Create Brand
```
POST /api/v1/brands
```

**Body:**
```json
{
  "name": "McDonald's",
  "slug": "mcdonalds",
  "description": "Fast food restaurant",
  "logo": "path/to/logo.jpg",
  "is_active": true
}
```

### Update Brand
```
PUT /api/v1/brands/{brand}
PATCH /api/v1/brands/{brand}
```

### Delete Brand
```
DELETE /api/v1/brands/{brand}
```

### Restore Brand
```
POST /api/v1/brands/{id}/restore
```

---

## Stores API

### Get All Stores
```
GET /api/v1/stores
```

**Query Parameters:**
- `brand_id` (integer, optional) - Filter by brand
- `is_active` (boolean, optional) - Filter by active status
- `search` (string, optional) - Search by name
- `order_by` (string, optional, default: `created_at`)
- `order_dir` (string, optional, default: `desc`)
- `per_page` (integer, optional, default: 15)

**Response:** Stores dengan brand relationship

### Get Single Store
```
GET /api/v1/stores/{store}
```

**Response:** Store dengan brand dan menus relationships

### Create Store
```
POST /api/v1/stores
```

**Body:**
```json
{
  "mdx_brand_id": 1,
  "name": "McDonald's Jakarta",
  "slug": "mcdonalds-jakarta",
  "description": "...",
  "address": "Jl. Sudirman No. 1",
  "phone": "081234567890",
  "email": "jakarta@mcdonalds.com",
  "latitude": -6.2088,
  "longitude": 106.8456,
  "image": "path/to/image.jpg",
  "is_active": true
}
```

### Update Store
```
PUT /api/v1/stores/{store}
PATCH /api/v1/stores/{store}
```

### Delete Store
```
DELETE /api/v1/stores/{store}
```

### Restore Store
```
POST /api/v1/stores/{id}/restore
```

### Get Available Menus for Store
```
GET /api/v1/stores/{store}/available-menus
```

**Response:** List of menus that are available in this store

---

## Categories API

### Get All Categories
```
GET /api/v1/categories
```

**Query Parameters:**
- `brand_id` (integer, optional) - Filter by brand
- `is_active` (boolean, optional) - Filter by active status
- `search` (string, optional) - Search by name
- `order_by` (string, optional, default: `sort_order`)
- `order_dir` (string, optional, default: `asc`)
- `per_page` (integer, optional, default: 15)

**Response:** Categories dengan brand relationship

### Get Single Category
```
GET /api/v1/categories/{category}
```

**Response:** Category dengan brand dan menus relationships

### Create Category
```
POST /api/v1/categories
```

**Body:**
```json
{
  "mdx_brand_id": 1,
  "name": "Makanan",
  "slug": "makanan",
  "description": "Category untuk makanan",
  "image": "path/to/image.jpg",
  "is_active": true,
  "sort_order": 1
}
```

**Note:** Slug harus unique per brand.

### Update Category
```
PUT /api/v1/categories/{category}
PATCH /api/v1/categories/{category}
```

### Delete Category
```
DELETE /api/v1/categories/{category}
```

### Restore Category
```
POST /api/v1/categories/{id}/restore
```

---

## Menus API

### Get All Menus
```
GET /api/v1/menus
```

**Query Parameters:**
- `is_active` (boolean, optional) - Filter by active status
- `is_available` (boolean, optional) - Filter by global availability
- `store_id` (integer, optional) - Filter menus available in store
- `category_id` (integer, optional) - Filter by category
- `search` (string, optional) - Search by name
- `order_by` (string, optional, default: `sort_order`)
- `order_dir` (string, optional, default: `asc`)
- `with` (string, optional) - Comma-separated relationships to load (e.g., `categories,stores`)
- `per_page` (integer, optional, default: 15)

**Examples:**
- Get all menus available in store 1: `GET /api/v1/menus?store_id=1`
- Get menus in category 1: `GET /api/v1/menus?category_id=1`
- Get menus with relationships: `GET /api/v1/menus?with=categories,stores`

### Get Single Menu
```
GET /api/v1/menus/{menu}
```

**Response:** Menu dengan categories dan stores relationships

### Create Menu
```
POST /api/v1/menus
```

**Body:**
```json
{
  "name": "Nasi Goreng",
  "slug": "nasi-goreng",
  "description": "Nasi goreng spesial",
  "price": 25000,
  "image": "path/to/image.jpg",
  "is_available": true,
  "is_active": true,
  "sort_order": 1,
  "category_ids": [1, 2]
}
```

**Note:** `category_ids` is optional array of category IDs to attach.

### Update Menu
```
PUT /api/v1/menus/{menu}
PATCH /api/v1/menus/{menu}
```

**Body:** Same as create, all fields are optional

### Delete Menu
```
DELETE /api/v1/menus/{menu}
```

### Restore Menu
```
POST /api/v1/menus/{id}/restore
```

### Get Stores Where Menu is Available
```
GET /api/v1/menus/{menu}/available-stores
```

**Response:** List of stores where this menu is available

### Check Menu Availability in Store
```
GET /api/v1/menus/{menu}/stores/{store}/check-availability
```

**Response:**
```json
{
  "success": true,
  "data": {
    "is_available": true,
    "stock_quantity": 10,
    "out_of_stock_reason": null,
    "min_stock_threshold": 1
  }
}
```

### Update Menu Availability in Store
```
PUT /api/v1/menus/{menu}/stores/{store}/availability
PATCH /api/v1/menus/{menu}/stores/{store}/availability
```

**Body:**
```json
{
  "is_available": false,
  "stock_quantity": 0,
  "out_of_stock_reason": "Stok habis, menunggu restock",
  "min_stock_threshold": 1
}
```

**Note:** 
- Jika `stock_quantity` dan `min_stock_threshold` disediakan, `is_available` akan auto-set berdasarkan perbandingan keduanya.
- Jika stock_quantity >= min_stock_threshold, maka is_available akan menjadi true.

---

## Error Responses

### Validation Error (422)
```json
{
  "success": false,
  "message": "Validation error",
  "errors": {
    "field_name": ["Error message"]
  }
}
```

### Not Found (404)
```json
{
  "message": "No query results for model [App\\Models\\v1\\Brand] {id}"
}
```

### Server Error (500)
```json
{
  "success": false,
  "message": "Failed to create menu",
  "error": "Error message"
}
```

---

## Complete Example Workflow

### 1. Create Brand
```bash
POST /api/v1/brands
{
  "name": "McDonald's",
  "slug": "mcdonalds",
  "is_active": true
}
```

### 2. Create Store for Brand
```bash
POST /api/v1/stores
{
  "mdx_brand_id": 1,
  "name": "McDonald's Jakarta",
  "address": "Jl. Sudirman No. 1",
  "is_active": true
}
```

### 3. Create Categories for Brand
```bash
POST /api/v1/categories
{
  "mdx_brand_id": 1,
  "name": "Makanan",
  "slug": "makanan",
  "is_active": true
}
```

### 4. Create Menu with Categories
```bash
POST /api/v1/menus
{
  "name": "Big Mac",
  "slug": "big-mac",
  "price": 45000,
  "is_available": true,
  "is_active": true,
  "category_ids": [1]
}
```

### 5. Add Menu to Store with Stock
```bash
PUT /api/v1/menus/1/stores/1/availability
{
  "is_available": true,
  "stock_quantity": 50,
  "min_stock_threshold": 5
}
```

### 6. Set Menu Out of Stock in Store
```bash
PUT /api/v1/menus/1/stores/1/availability
{
  "is_available": false,
  "stock_quantity": 0,
  "out_of_stock_reason": "Stok habis"
}
```

### 7. Get Available Menus for Store
```bash
GET /api/v1/stores/1/available-menus
```

### 8. Check Menu Availability
```bash
GET /api/v1/menus/1/stores/1/check-availability
```

---

## Notes

- Semua endpoints menggunakan route model binding, jadi bisa menggunakan ID atau slug (jika diimplementasikan)
- Soft deletes digunakan, jadi delete hanya melakukan soft delete
- Restore endpoint menggunakan ID karena route model binding tidak work dengan soft deleted models
- Pagination default adalah 15 items per page
- Semua timestamps menggunakan format ISO 8601

