# API Documentation - DINE ERP

Dokumentasi lengkap untuk API DINE ERP v1.

## Daftar Isi

- [Base URL](#base-url)
- [Authentication](#authentication)
- [Response Format](#response-format)
- [Error Handling](#error-handling)
- [Status Codes](#status-codes)
- [Authentication Endpoints](#authentication-endpoints)
- [Masterdata Endpoints](#masterdata-endpoints)
  - [Brands](#brands)
  - [Stores](#stores)
  - [Categories](#categories)
  - [Menus](#menus)
  - [Bank Accounts](#bank-accounts)
  - [Tables](#tables)
- [Orders Endpoints](#orders-endpoints)

---

## Base URL

```
https://your-domain.com/api/v1
```

---

## Authentication

API menggunakan **Laravel Sanctum** untuk autentikasi berbasis token.

### Cara Menggunakan Token

Setelah login, Anda akan menerima token. Gunakan token tersebut di header setiap request:

```
Authorization: Bearer {your-token}
```

### Role yang Diizinkan

Hanya user dengan role berikut yang dapat mengakses API:
- `brand_owner`
- `store_manager`
- `kasir`

---

## Response Format

Semua response mengikuti format standar:

### Success Response

```json
{
  "success": true,
  "message": "Optional success message",
  "data": {
    // Response data
  },
  "meta": {
    // Pagination metadata (jika ada)
    "current_page": 1,
    "last_page": 10,
    "per_page": 15,
    "total": 150
  }
}
```

### Error Response

```json
{
  "success": false,
  "message": "Error message",
  "errors": {
    // Validation errors (jika ada)
    "field_name": ["Error message"]
  }
}
```

---

## Error Handling

### Validation Error (422)

```json
{
  "success": false,
  "message": "Validation error",
  "errors": {
    "phone": ["Format nomor telepon tidak valid."],
    "password": ["The password field is required."]
  }
}
```

### Unauthorized (401)

```json
{
  "success": false,
  "message": "Unauthenticated."
}
```

### Forbidden (403)

```json
{
  "success": false,
  "message": "Akun Anda tidak memiliki akses ke sistem."
}
```

### Not Found (404)

```json
{
  "success": false,
  "message": "Resource not found"
}
```

---

## Status Codes

| Code | Description |
|------|-------------|
| 200 | Success |
| 201 | Created |
| 401 | Unauthorized |
| 403 | Forbidden |
| 404 | Not Found |
| 422 | Validation Error |
| 500 | Server Error |

---

## Authentication Endpoints

### 1. Login

Login dengan nomor telepon dan password.

**Endpoint:** `POST /auth/login`

**Authentication:** Tidak diperlukan

**Request Body:**
```json
{
  "phone": "081234567890",
  "password": "password123"
}
```

**Response (200):**
```json
{
  "success": true,
  "message": "Login berhasil",
  "data": {
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com",
      "phone": "081234567890",
      "role": "brand_owner",
      "account_type": "CORE"
    },
    "token": "1|xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx",
    "token_type": "Bearer"
  }
}
```

**Error Response (401):**
```json
{
  "success": false,
  "message": "Nomor telepon atau password yang Anda masukkan salah."
}
```

**Error Response (403):**
```json
{
  "success": false,
  "message": "Akun Anda tidak memiliki akses ke sistem."
}
```

---

### 2. Register

Registrasi user baru.

**Endpoint:** `POST /auth/register`

**Authentication:** Tidak diperlukan

**Request Body:**
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "phone": "081234567890",
  "password": "password123",
  "password_confirmation": "password123"
}
```

**Response (201):**
```json
{
  "success": true,
  "message": "Registrasi berhasil",
  "data": {
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com",
      "phone": "081234567890",
      "role": "brand_owner",
      "account_type": null
    },
    "token": "1|xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx",
    "token_type": "Bearer"
  }
}
```

**Validation Rules:**
- `name`: required, string, max:255
- `email`: required, email, unique:users
- `phone`: required, string, regex:/^(\+62|62|0)[0-9]{9,12}$/, unique:users
- `password`: required, string, min:8, confirmed

---

### 3. Get Current User

Mendapatkan informasi user yang sedang login.

**Endpoint:** `GET /auth/me`

**Authentication:** Required

**Response (200):**
```json
{
  "success": true,
  "data": {
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com",
      "phone": "081234567890",
      "role": "brand_owner",
      "account_type": "CORE",
      "mdx_store_id": null
    }
  }
}
```

---

### 4. Logout

Logout dari perangkat saat ini.

**Endpoint:** `POST /auth/logout`

**Authentication:** Required

**Response (200):**
```json
{
  "success": true,
  "message": "Logout berhasil"
}
```

---

### 5. Logout All

Logout dari semua perangkat.

**Endpoint:** `POST /auth/logout-all`

**Authentication:** Required

**Response (200):**
```json
{
  "success": true,
  "message": "Logout dari semua perangkat berhasil"
}
```

---

## Masterdata Endpoints

Semua endpoint masterdata memerlukan autentikasi dan berada di prefix `/masterdata`.

---

### Brands

#### 1. List Brands

Mendapatkan daftar brands.

**Endpoint:** `GET /masterdata/brands`

**Query Parameters:**
- `is_active` (boolean, optional): Filter by status aktif
- `search` (string, optional): Search by name
- `order_by` (string, optional): Column untuk sorting (default: `created_at`)
- `order_dir` (string, optional): Direction sorting - `asc` atau `desc` (default: `desc`)
- `per_page` (integer, optional): Jumlah item per halaman (default: 15)

**Response (200):**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "McDonald's",
      "slug": "mcdonalds",
      "description": "Fast food restaurant",
      "logo": "https://example.com/logo.png",
      "is_active": true,
      "created_at": "2024-01-01T00:00:00.000000Z",
      "updated_at": "2024-01-01T00:00:00.000000Z"
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

---

#### 2. Create Brand

Membuat brand baru.

**Endpoint:** `POST /masterdata/brands`

**Request Body:**
```json
{
  "name": "McDonald's",
  "slug": "mcdonalds",
  "description": "Fast food restaurant",
  "logo": "https://example.com/logo.png",
  "is_active": true
}
```

**Validation Rules:**
- `name`: required, string, max:255
- `slug`: nullable, string, max:255, unique:mdx_brands,slug
- `description`: nullable, string
- `logo`: nullable, string, max:255
- `is_active`: nullable, boolean

**Response (201):**
```json
{
  "success": true,
  "message": "Brand created successfully",
  "data": {
    "id": 1,
    "name": "McDonald's",
    "slug": "mcdonalds",
    "description": "Fast food restaurant",
    "logo": "https://example.com/logo.png",
    "is_active": true,
    "created_at": "2024-01-01T00:00:00.000000Z",
    "updated_at": "2024-01-01T00:00:00.000000Z"
  }
}
```

---

#### 3. Get Brand

Mendapatkan detail brand.

**Endpoint:** `GET /masterdata/brands/{brand}`

**Response (200):**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "McDonald's",
    "slug": "mcdonalds",
    "description": "Fast food restaurant",
    "logo": "https://example.com/logo.png",
    "is_active": true,
    "stores": [],
    "categories": [],
    "created_at": "2024-01-01T00:00:00.000000Z",
    "updated_at": "2024-01-01T00:00:00.000000Z"
  }
}
```

---

#### 4. Update Brand

Update brand.

**Endpoint:** `PUT /masterdata/brands/{brand}` atau `PATCH /masterdata/brands/{brand}`

**Request Body:**
```json
{
  "name": "McDonald's Updated",
  "is_active": false
}
```

**Validation Rules:** (semua field optional untuk PATCH)
- `name`: sometimes, required, string, max:255
- `slug`: nullable, string, max:255, unique:mdx_brands,slug (ignore current)
- `description`: nullable, string
- `logo`: nullable, string, max:255
- `is_active`: nullable, boolean

**Response (200):**
```json
{
  "success": true,
  "message": "Brand updated successfully",
  "data": {
    "id": 1,
    "name": "McDonald's Updated",
    "slug": "mcdonalds",
    "description": "Fast food restaurant",
    "logo": "https://example.com/logo.png",
    "is_active": false,
    "created_at": "2024-01-01T00:00:00.000000Z",
    "updated_at": "2024-01-01T00:00:00.000000Z"
  }
}
```

---

#### 5. Delete Brand

Menghapus brand (soft delete).

**Endpoint:** `DELETE /masterdata/brands/{brand}`

**Response (200):**
```json
{
  "success": true,
  "message": "Brand deleted successfully"
}
```

---

#### 6. Restore Brand

Mengembalikan brand yang telah dihapus.

**Endpoint:** `POST /masterdata/brands/{id}/restore`

**Response (200):**
```json
{
  "success": true,
  "message": "Brand restored successfully",
  "data": {
    "id": 1,
    "name": "McDonald's",
    // ... other fields
  }
}
```

---

### Stores

#### 1. List Stores

Mendapatkan daftar stores.

**Endpoint:** `GET /masterdata/stores`

**Query Parameters:**
- `brand_id` (integer, optional): Filter by brand
- `is_active` (boolean, optional): Filter by status aktif
- `search` (string, optional): Search by name
- `order_by` (string, optional): Column untuk sorting
- `order_dir` (string, optional): Direction sorting
- `per_page` (integer, optional): Jumlah item per halaman

**Response (200):**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "mdx_brand_id": 1,
      "name": "McDonald's Cabang A",
      "slug": "mcdonalds-cabang-a",
      "description": "Store description",
      "address": "Jl. Sudirman No. 1",
      "phone": "02112345678",
      "email": "store@example.com",
      "latitude": "-6.2088",
      "longitude": "106.8456",
      "image": "https://example.com/image.jpg",
      "is_active": true,
      "created_at": "2024-01-01T00:00:00.000000Z",
      "updated_at": "2024-01-01T00:00:00.000000Z"
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

---

#### 2. Create Store

Membuat store baru.

**Endpoint:** `POST /masterdata/stores`

**Request Body:**
```json
{
  "mdx_brand_id": 1,
  "name": "McDonald's Cabang A",
  "slug": "mcdonalds-cabang-a",
  "description": "Store description",
  "address": "Jl. Sudirman No. 1",
  "phone": "02112345678",
  "email": "store@example.com",
  "latitude": "-6.2088",
  "longitude": "106.8456",
  "image": "https://example.com/image.jpg",
  "is_active": true
}
```

**Validation Rules:**
- `mdx_brand_id`: required, exists:mdx_brands,id
- `name`: required, string, max:255
- `slug`: nullable, string, max:255, unique:mdx_stores,slug
- `description`: nullable, string
- `address`: nullable, string
- `phone`: nullable, string, max:255
- `email`: nullable, email, max:255
- `latitude`: nullable, numeric, between:-90,90
- `longitude`: nullable, numeric, between:-180,180
- `image`: nullable, string, max:255
- `is_active`: nullable, boolean

**Response (201):**
```json
{
  "success": true,
  "message": "Store created successfully",
  "data": {
    "id": 1,
    "mdx_brand_id": 1,
    "name": "McDonald's Cabang A",
    // ... other fields
    "brand": {
      "id": 1,
      "name": "McDonald's"
    }
  }
}
```

---

#### 3. Get Store

Mendapatkan detail store.

**Endpoint:** `GET /masterdata/stores/{store}`

**Response (200):**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "mdx_brand_id": 1,
    "name": "McDonald's Cabang A",
    // ... other fields
    "brand": {
      "id": 1,
      "name": "McDonald's"
    },
    "menus": []
  }
}
```

---

#### 4. Update Store

Update store.

**Endpoint:** `PUT /masterdata/stores/{store}` atau `PATCH /masterdata/stores/{store}`

**Request Body:**
```json
{
  "name": "McDonald's Cabang A Updated",
  "is_active": false
}
```

**Response (200):**
```json
{
  "success": true,
  "message": "Store updated successfully",
  "data": {
    // Updated store data
  }
}
```

---

#### 5. Delete Store

Menghapus store (soft delete).

**Endpoint:** `DELETE /masterdata/stores/{store}`

**Response (200):**
```json
{
  "success": true,
  "message": "Store deleted successfully"
}
```

---

#### 6. Restore Store

Mengembalikan store yang telah dihapus.

**Endpoint:** `POST /masterdata/stores/{id}/restore`

**Response (200):**
```json
{
  "success": true,
  "message": "Store restored successfully",
  "data": {
    // Restored store data
  }
}
```

---

#### 7. Get Available Menus

Mendapatkan daftar menu yang tersedia di store.

**Endpoint:** `GET /masterdata/stores/{store}/available-menus`

**Response (200):**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "Big Mac",
      "price": "50000.00",
      "pivot": {
        "is_available": true,
        "stock_quantity": 100,
        "out_of_stock_reason": null,
        "min_stock_threshold": 10
      }
    }
  ]
}
```

---

### Categories

#### 1. List Categories

Mendapatkan daftar categories.

**Endpoint:** `GET /masterdata/categories`

**Query Parameters:**
- `brand_id` (integer, optional): Filter by brand
- `is_active` (boolean, optional): Filter by status aktif
- `search` (string, optional): Search by name
- `order_by` (string, optional): Column untuk sorting (default: `sort_order`)
- `order_dir` (string, optional): Direction sorting (default: `asc`)
- `per_page` (integer, optional): Jumlah item per halaman

**Response (200):**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "mdx_brand_id": 1,
      "name": "Makanan",
      "slug": "makanan",
      "description": "Kategori makanan",
      "image": "https://example.com/image.jpg",
      "is_active": true,
      "sort_order": 1,
      "created_at": "2024-01-01T00:00:00.000000Z",
      "updated_at": "2024-01-01T00:00:00.000000Z"
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

---

#### 2. Create Category

Membuat category baru.

**Endpoint:** `POST /masterdata/categories`

**Request Body:**
```json
{
  "mdx_brand_id": 1,
  "name": "Makanan",
  "slug": "makanan",
  "description": "Kategori makanan",
  "image": "https://example.com/image.jpg",
  "is_active": true,
  "sort_order": 1
}
```

**Validation Rules:**
- `mdx_brand_id`: required, exists:mdx_brands,id
- `name`: required, string, max:255
- `slug`: nullable, string, max:255 (unique per brand)
- `description`: nullable, string
- `image`: nullable, string, max:255
- `is_active`: nullable, boolean
- `sort_order`: nullable, integer, min:0

**Response (201):**
```json
{
  "success": true,
  "message": "Category created successfully",
  "data": {
    "id": 1,
    "mdx_brand_id": 1,
    "name": "Makanan",
    // ... other fields
    "brand": {
      "id": 1,
      "name": "McDonald's"
    }
  }
}
```

---

#### 3. Get Category

Mendapatkan detail category.

**Endpoint:** `GET /masterdata/categories/{category}`

**Response (200):**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "mdx_brand_id": 1,
    "name": "Makanan",
    // ... other fields
    "brand": {
      "id": 1,
      "name": "McDonald's"
    }
  }
}
```

---

#### 4. Update Category

Update category.

**Endpoint:** `PUT /masterdata/categories/{category}` atau `PATCH /masterdata/categories/{category}`

**Request Body:**
```json
{
  "name": "Makanan Updated",
  "is_active": false
}
```

**Response (200):**
```json
{
  "success": true,
  "message": "Category updated successfully",
  "data": {
    // Updated category data
  }
}
```

---

#### 5. Delete Category

Menghapus category (soft delete).

**Endpoint:** `DELETE /masterdata/categories/{category}`

**Response (200):**
```json
{
  "success": true,
  "message": "Category deleted successfully"
}
```

---

#### 6. Restore Category

Mengembalikan category yang telah dihapus.

**Endpoint:** `POST /masterdata/categories/{id}/restore`

**Response (200):**
```json
{
  "success": true,
  "message": "Category restored successfully",
  "data": {
    // Restored category data
  }
}
```

---

### Menus

#### 1. List Menus

Mendapatkan daftar menus.

**Endpoint:** `GET /masterdata/menus`

**Query Parameters:**
- `is_active` (boolean, optional): Filter by status aktif
- `is_available` (boolean, optional): Filter by ketersediaan global
- `store_id` (integer, optional): Filter menu yang tersedia di store tertentu
- `category_id` (integer, optional): Filter by category
- `search` (string, optional): Search by name
- `order_by` (string, optional): Column untuk sorting (default: `sort_order`)
- `order_dir` (string, optional): Direction sorting (default: `asc`)
- `with` (string, optional): Load relationships (comma-separated, e.g., `categories,stores`)
- `per_page` (integer, optional): Jumlah item per halaman

**Response (200):**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "Big Mac",
      "slug": "big-mac",
      "description": "Burger dengan daging sapi",
      "price": "50000.00",
      "image": "https://example.com/image.jpg",
      "is_available": true,
      "is_active": true,
      "sort_order": 1,
      "created_at": "2024-01-01T00:00:00.000000Z",
      "updated_at": "2024-01-01T00:00:00.000000Z"
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

---

#### 2. Create Menu

Membuat menu baru.

**Endpoint:** `POST /masterdata/menus`

**Request Body:**
```json
{
  "name": "Big Mac",
  "slug": "big-mac",
  "description": "Burger dengan daging sapi",
  "price": 50000,
  "image": "https://example.com/image.jpg",
  "is_available": true,
  "is_active": true,
  "sort_order": 1,
  "category_ids": [1, 2]
}
```

**Validation Rules:**
- `name`: required, string, max:255
- `slug`: nullable, string, max:255, unique:mdx_menus,slug
- `description`: nullable, string
- `price`: required, numeric, min:0
- `image`: nullable, string, max:255
- `is_available`: nullable, boolean
- `is_active`: nullable, boolean
- `sort_order`: nullable, integer, min:0
- `category_ids`: nullable, array
- `category_ids.*`: exists:mdx_categories,id

**Response (201):**
```json
{
  "success": true,
  "message": "Menu created successfully",
  "data": {
    "id": 1,
    "name": "Big Mac",
    // ... other fields
    "categories": []
  }
}
```

---

#### 3. Get Menu

Mendapatkan detail menu.

**Endpoint:** `GET /masterdata/menus/{menu}`

**Response (200):**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "Big Mac",
    // ... other fields
    "categories": [],
    "stores": []
  }
}
```

---

#### 4. Update Menu

Update menu.

**Endpoint:** `PUT /masterdata/menus/{menu}` atau `PATCH /masterdata/menus/{menu}`

**Request Body:**
```json
{
  "name": "Big Mac Updated",
  "price": 55000,
  "is_available": false
}
```

**Response (200):**
```json
{
  "success": true,
  "message": "Menu updated successfully",
  "data": {
    // Updated menu data
  }
}
```

---

#### 5. Delete Menu

Menghapus menu (soft delete).

**Endpoint:** `DELETE /masterdata/menus/{menu}`

**Response (200):**
```json
{
  "success": true,
  "message": "Menu deleted successfully"
}
```

---

#### 6. Restore Menu

Mengembalikan menu yang telah dihapus.

**Endpoint:** `POST /masterdata/menus/{id}/restore`

**Response (200):**
```json
{
  "success": true,
  "message": "Menu restored successfully",
  "data": {
    // Restored menu data
  }
}
```

---

#### 7. Get Available Stores

Mendapatkan daftar store yang memiliki menu ini.

**Endpoint:** `GET /masterdata/menus/{menu}/available-stores`

**Response (200):**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "McDonald's Cabang A",
      "pivot": {
        "is_available": true,
        "stock_quantity": 100
      }
    }
  ]
}
```

---

#### 8. Check Menu Availability in Store

Cek ketersediaan menu di store tertentu.

**Endpoint:** `GET /masterdata/menus/{menu}/stores/{store}/check-availability`

**Response (200):**
```json
{
  "success": true,
  "data": {
    "is_available": true,
    "stock_quantity": 100,
    "out_of_stock_reason": null,
    "min_stock_threshold": 10
  }
}
```

---

#### 9. Update Menu Availability in Store

Update ketersediaan menu di store tertentu.

**Endpoint:** `PUT /masterdata/menus/{menu}/stores/{store}/availability` atau `PATCH /masterdata/menus/{menu}/stores/{store}/availability`

**Request Body:**
```json
{
  "is_available": true,
  "stock_quantity": 100,
  "out_of_stock_reason": null,
  "min_stock_threshold": 10
}
```

**Response (200):**
```json
{
  "success": true,
  "message": "Menu availability updated successfully",
  "data": {
    "is_available": true,
    "stock_quantity": 100
  }
}
```

---

### Bank Accounts

#### 1. List Bank Accounts

Mendapatkan daftar bank accounts.

**Endpoint:** `GET /masterdata/bank-accounts`

**Query Parameters:**
- `brand_id` (integer, optional): Filter by brand
- `store_id` (integer, optional): Filter by store
- `is_active` (boolean, optional): Filter by status aktif
- `search` (string, optional): Search by account name atau bank name
- `order_by` (string, optional): Column untuk sorting
- `order_dir` (string, optional): Direction sorting
- `per_page` (integer, optional): Jumlah item per halaman

**Response (200):**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "mdx_brand_id": 1,
      "mdx_store_id": null,
      "account_name": "PT McDonald's Indonesia",
      "account_number": "1234567890",
      "bank_name": "Bank BCA",
      "bank_code": "BCA",
      "branch_name": "Cabang Sudirman",
      "currency": "IDR",
      "balance": "1000000.00",
      "minimum_balance": "100000.00",
      "is_active": true,
      "is_verified": false,
      "notes": "Rekening utama",
      "created_at": "2024-01-01T00:00:00.000000Z",
      "updated_at": "2024-01-01T00:00:00.000000Z"
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

---

#### 2. Create Bank Account

Membuat bank account baru.

**Endpoint:** `POST /masterdata/bank-accounts`

**Request Body:**
```json
{
  "mdx_brand_id": 1,
  "mdx_store_id": null,
  "account_name": "PT McDonald's Indonesia",
  "account_number": "1234567890",
  "bank_name": "Bank BCA",
  "bank_code": "BCA",
  "branch_name": "Cabang Sudirman",
  "currency": "IDR",
  "balance": 1000000,
  "minimum_balance": 100000,
  "is_active": true,
  "notes": "Rekening utama"
}
```

**Validation Rules:**
- `mdx_brand_id`: nullable, required_without:mdx_store_id, exists:mdx_brands,id
- `mdx_store_id`: nullable, required_without:mdx_brand_id, exists:mdx_stores,id
- `account_name`: required, string, max:255
- `account_number`: required, string, max:255
- `bank_name`: required, string, max:255
- `bank_code`: nullable, string, max:255
- `branch_name`: nullable, string, max:255
- `currency`: nullable, string, size:3
- `balance`: nullable, numeric, min:0
- `minimum_balance`: nullable, numeric, min:0
- `is_active`: nullable, boolean
- `notes`: nullable, string

**Note:** Harus menyediakan salah satu dari `mdx_brand_id` atau `mdx_store_id`, tidak boleh keduanya.

**Response (201):**
```json
{
  "success": true,
  "message": "Bank account created successfully",
  "data": {
    "id": 1,
    // ... all fields
  }
}
```

---

#### 3. Get Bank Account

Mendapatkan detail bank account.

**Endpoint:** `GET /masterdata/bank-accounts/{bankAccount}`

**Response (200):**
```json
{
  "success": true,
  "data": {
    "id": 1,
    // ... all fields
  }
}
```

---

#### 4. Update Bank Account

Update bank account.

**Endpoint:** `PUT /masterdata/bank-accounts/{bankAccount}` atau `PATCH /masterdata/bank-accounts/{bankAccount}`

**Request Body:**
```json
{
  "account_name": "PT McDonald's Indonesia Updated",
  "is_active": false
}
```

**Response (200):**
```json
{
  "success": true,
  "message": "Bank account updated successfully",
  "data": {
    // Updated bank account data
  }
}
```

---

#### 5. Delete Bank Account

Menghapus bank account (soft delete).

**Endpoint:** `DELETE /masterdata/bank-accounts/{bankAccount}`

**Response (200):**
```json
{
  "success": true,
  "message": "Bank account deleted successfully"
}
```

---

#### 6. Restore Bank Account

Mengembalikan bank account yang telah dihapus.

**Endpoint:** `POST /masterdata/bank-accounts/{id}/restore`

**Response (200):**
```json
{
  "success": true,
  "message": "Bank account restored successfully",
  "data": {
    // Restored bank account data
  }
}
```

---

#### 7. Get Bank Account Balance

Mendapatkan saldo bank account.

**Endpoint:** `GET /masterdata/bank-accounts/{bankAccount}/balance`

**Response (200):**
```json
{
  "success": true,
  "data": {
    "balance": "1000000.00",
    "minimum_balance": "100000.00",
    "available_balance": "900000.00"
  }
}
```

---

#### 8. Update Bank Account Balance

Update saldo bank account.

**Endpoint:** `POST /masterdata/bank-accounts/{bankAccount}/balance` atau `PUT /masterdata/bank-accounts/{bankAccount}/balance`

**Request Body:**
```json
{
  "balance": 2000000
}
```

**Response (200):**
```json
{
  "success": true,
  "message": "Balance updated successfully",
  "data": {
    "balance": "2000000.00"
  }
}
```

---

### Tables

#### 1. List Tables

Mendapatkan daftar tables.

**Endpoint:** `GET /masterdata/tables`

**Query Parameters:**
- `store_id` (integer, optional): Filter by store
- `is_active` (boolean, optional): Filter by status aktif
- `status` (string, optional): Filter by table status (available, occupied, reserved, maintenance)
- `zone` (string, optional): Filter by zone
- `floor` (integer, optional): Filter by floor
- `search` (string, optional): Search by name atau table_number
- `order_by` (string, optional): Column untuk sorting (default: `sort_order`)
- `order_dir` (string, optional): Direction sorting (default: `asc`)
- `with` (string, optional): Load relationships (comma-separated, e.g., `store,orders`)
- `per_page` (integer, optional): Jumlah item per halaman

**Response (200):**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "mdx_store_id": 1,
      "table_number": 1,
      "name": "Meja 1",
      "capacity": 4,
      "status": "available",
      "zone": "Indoor",
      "floor": 1,
      "notes": null,
      "sort_order": 1,
      "is_active": true,
      "unique_identifier": "550e8400-e29b-41d4-a716-446655440000",
      "created_at": "2024-01-01T00:00:00.000000Z",
      "updated_at": "2024-01-01T00:00:00.000000Z",
      "store": {
        "id": 1,
        "name": "McDonald's Cabang A",
        "brand": {
          "id": 1,
          "name": "McDonald's"
        }
      }
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

---

#### 2. Create Table

Membuat table baru.

**Endpoint:** `POST /masterdata/tables`

**Request Body:**
```json
{
  "mdx_store_id": 1,
  "table_number": 1,
  "name": "Meja 1",
  "capacity": 4,
  "status": "available",
  "zone": "Indoor",
  "floor": 1,
  "notes": null,
  "sort_order": 1,
  "is_active": true
}
```

**Validation Rules:**
- `mdx_store_id`: required, exists:mdx_stores,id
- `table_number`: required, integer, min:1 (unique per store)
- `name`: nullable, string, max:255 (auto-generated jika tidak diisi)
- `capacity`: nullable, integer, min:1
- `status`: required, in:available,occupied,reserved,maintenance
- `zone`: nullable, string, max:255
- `floor`: nullable, integer
- `notes`: nullable, string
- `sort_order`: nullable, integer, min:0
- `is_active`: nullable, boolean

**Note:** `name` akan otomatis di-generate menjadi "Meja {table_number}" jika tidak diisi.

**Response (201):**
```json
{
  "success": true,
  "message": "Table created successfully",
  "data": {
    "id": 1,
    "mdx_store_id": 1,
    "table_number": 1,
    "name": "Meja 1",
    "capacity": 4,
    "status": "available",
    "zone": "Indoor",
    "floor": 1,
    "notes": null,
    "sort_order": 1,
    "is_active": true,
    "unique_identifier": "550e8400-e29b-41d4-a716-446655440000",
    "created_at": "2024-01-01T00:00:00.000000Z",
    "updated_at": "2024-01-01T00:00:00.000000Z",
    "store": {
      "id": 1,
      "name": "McDonald's Cabang A",
      "brand": {
        "id": 1,
        "name": "McDonald's"
      }
    }
  }
}
```

---

#### 3. Get Table

Mendapatkan detail table.

**Endpoint:** `GET /masterdata/tables/{table}`

**Response (200):**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "mdx_store_id": 1,
    "table_number": 1,
    "name": "Meja 1",
    "capacity": 4,
    "status": "available",
    "zone": "Indoor",
    "floor": 1,
    "notes": null,
    "sort_order": 1,
    "is_active": true,
    "unique_identifier": "550e8400-e29b-41d4-a716-446655440000",
    "store": {
      "id": 1,
      "name": "McDonald's Cabang A",
      "brand": {
        "id": 1,
        "name": "McDonald's"
      }
    },
    "orders": []
  }
}
```

---

#### 4. Update Table

Update table.

**Endpoint:** `PUT /masterdata/tables/{table}` atau `PATCH /masterdata/tables/{table}`

**Request Body:**
```json
{
  "status": "occupied",
  "capacity": 6,
  "is_active": false
}
```

**Validation Rules:** (semua field optional untuk PATCH)
- `mdx_store_id`: sometimes, required, exists:mdx_stores,id
- `table_number`: sometimes, required, integer, min:1 (unique per store)
- `name`: nullable, string, max:255
- `capacity`: nullable, integer, min:1
- `status`: sometimes, required, in:available,occupied,reserved,maintenance
- `zone`: nullable, string, max:255
- `floor`: nullable, integer
- `notes`: nullable, string
- `sort_order`: nullable, integer, min:0
- `is_active`: nullable, boolean

**Response (200):**
```json
{
  "success": true,
  "message": "Table updated successfully",
  "data": {
    // Updated table data
  }
}
```

---

#### 5. Delete Table

Menghapus table (soft delete).

**Endpoint:** `DELETE /masterdata/tables/{table}`

**Response (200):**
```json
{
  "success": true,
  "message": "Table deleted successfully"
}
```

---

#### 6. Restore Table

Mengembalikan table yang telah dihapus.

**Endpoint:** `POST /masterdata/tables/{id}/restore`

**Response (200):**
```json
{
  "success": true,
  "message": "Table restored successfully",
  "data": {
    // Restored table data
  }
}
```

---

#### 7. Check if Table Can Be Closed

Cek apakah semua order di table sudah bisa ditutup (semua sudah dibayar dan selesai).

**Endpoint:** `GET /masterdata/tables/{table}/can-close`

**Response (200):**
```json
{
  "success": true,
  "data": {
    "can_close": true,
    "table_id": 1
  }
}
```

**Note:** Table dapat ditutup jika semua order yang belum ditutup sudah memiliki status `completed` dan `payment_status` = `paid`.

---

#### 8. Close Table Orders

Menutup semua order untuk table tertentu.

**Endpoint:** `POST /masterdata/tables/{table}/close-orders`

**Response (200):**
```json
{
  "success": true,
  "message": "Berhasil menutup 3 pesanan untuk meja ini.",
  "data": {
    "closed_count": 3,
    "table_id": 1
  }
}
```

**Error Response (422):**
```json
{
  "success": false,
  "message": "Tidak dapat menutup pesanan. Pastikan semua pesanan sudah dibayar dan selesai."
}
```

---

#### 9. Get Table Orders

Mendapatkan daftar order untuk table tertentu.

**Endpoint:** `GET /masterdata/tables/{table}/orders`

**Query Parameters:**
- `status` (string, optional): Filter by order status
- `payment_status` (string, optional): Filter by payment status
- `closed` (boolean, optional): Filter closed/unclosed orders
- `order_by` (string, optional): Column untuk sorting (default: `ordered_at`)
- `order_dir` (string, optional): Direction sorting (default: `desc`)
- `per_page` (integer, optional): Jumlah item per halaman

**Response (200):**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "order_number": "ORD-20240101-001",
      "status": "completed",
      "payment_status": "paid",
      "total_amount": "116000.00",
      "orderDetails": [
        {
          "id": 1,
          "mdx_menu_id": 1,
          "quantity": 2,
          "unit_price": "50000.00",
          "subtotal": "100000.00",
          "menu": {
            "id": 1,
            "name": "Big Mac"
          }
        }
      ],
      "payments": []
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

---

## Orders Endpoints

Semua endpoint orders memerlukan autentikasi dan berada di prefix `/orders`.

---

### 1. List Orders

Mendapatkan daftar orders.

**Endpoint:** `GET /orders`

**Query Parameters:**
- `store_id` (integer, optional): Filter by store
- `status` (string, optional): Filter by status
- `order_type` (string, optional): Filter by order type (dine_in, takeaway, delivery)
- `date_from` (date, optional): Filter dari tanggal
- `date_to` (date, optional): Filter sampai tanggal
- `search` (string, optional): Search by order number atau customer name
- `order_by` (string, optional): Column untuk sorting
- `order_dir` (string, optional): Direction sorting
- `per_page` (integer, optional): Jumlah item per halaman

**Response (200):**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "order_number": "ORD-20240101-001",
      "mdx_store_id": 1,
      "mdx_brand_id": 1,
      "mdx_table_id": null,
      "order_type": "dine_in",
      "customer_name": "John Doe",
      "customer_phone": "081234567890",
      "status": "pending",
      "subtotal": "100000.00",
      "discount_amount": "0.00",
      "tax_amount": "11000.00",
      "service_charge": "5000.00",
      "total": "116000.00",
      "created_at": "2024-01-01T00:00:00.000000Z",
      "updated_at": "2024-01-01T00:00:00.000000Z"
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

---

### 2. Create Order

Membuat order baru.

**Endpoint:** `POST /orders`

**Request Body:**
```json
{
  "mdx_store_id": 1,
  "mdx_brand_id": 1,
  "mdx_table_id": null,
  "order_type": "dine_in",
  "customer_name": "John Doe",
  "customer_phone": "081234567890",
  "customer_email": "john@example.com",
  "delivery_address": null,
  "delivery_latitude": null,
  "delivery_longitude": null,
  "discount_amount": 0,
  "tax_amount": 0,
  "service_charge": 0,
  "notes": "Tidak pakai acar",
  "admin_notes": null,
  "items": [
    {
      "mdx_menu_id": 1,
      "quantity": 2,
      "unit_price": 50000,
      "discount_amount": 0,
      "notes": "Tidak pakai acar"
    }
  ]
}
```

**Validation Rules:**
- `mdx_store_id`: required, exists:mdx_stores,id
- `mdx_brand_id`: nullable, exists:mdx_brands,id
- `mdx_table_id`: nullable, exists:mdx_tables,id
- `order_type`: nullable, in:dine_in,takeaway,delivery
- `customer_name`: required (jika tidak ada incomplete order), string, max:255
- `customer_phone`: nullable, string, max:255
- `customer_email`: nullable, email, max:255
- `delivery_address`: nullable, required_if:order_type,delivery, string
- `delivery_latitude`: nullable, numeric, between:-90,90
- `delivery_longitude`: nullable, numeric, between:-180,180
- `discount_amount`: nullable, numeric, min:0
- `tax_amount`: nullable, numeric, min:0
- `service_charge`: nullable, numeric, min:0
- `notes`: nullable, string
- `admin_notes`: nullable, string
- `items`: required, array, min:1
- `items.*.mdx_menu_id`: required, exists:mdx_menus,id
- `items.*.quantity`: required, integer, min:1
- `items.*.unit_price`: nullable, numeric, min:0
- `items.*.discount_amount`: nullable, numeric, min:0
- `items.*.notes`: nullable, string

**Response (201):**
```json
{
  "success": true,
  "message": "Order created successfully",
  "data": {
    "id": 1,
    "order_number": "ORD-20240101-001",
    // ... other fields
    "items": []
  }
}
```

---

### 3. Check Incomplete Order

Cek apakah ada incomplete order untuk customer.

**Endpoint:** `GET /orders/check-incomplete`

**Query Parameters:**
- `customer_phone` (string, required): Nomor telepon customer

**Response (200):**
```json
{
  "success": true,
  "data": {
    "has_incomplete_order": true,
    "order": {
      "id": 1,
      "order_number": "ORD-20240101-001",
      // ... order data
    }
  }
}
```

---

### 4. Get Order

Mendapatkan detail order.

**Endpoint:** `GET /orders/{order}`

**Response (200):**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "order_number": "ORD-20240101-001",
    // ... all fields
    "items": [
      {
        "id": 1,
        "mdx_menu_id": 1,
        "quantity": 2,
        "unit_price": "50000.00",
        "subtotal": "100000.00",
        "menu": {
          "id": 1,
          "name": "Big Mac"
        }
      }
    ]
  }
}
```

---

### 5. Update Order

Update order.

**Endpoint:** `PUT /orders/{order}` atau `PATCH /orders/{order}`

**Request Body:**
```json
{
  "status": "processing",
  "notes": "Updated notes"
}
```

**Response (200):**
```json
{
  "success": true,
  "message": "Order updated successfully",
  "data": {
    // Updated order data
  }
}
```

---

### 6. Delete Order

Menghapus order (soft delete).

**Endpoint:** `DELETE /orders/{order}`

**Response (200):**
```json
{
  "success": true,
  "message": "Order deleted successfully"
}
```

---

### 7. Cancel Order

Membatalkan order.

**Endpoint:** `POST /orders/{orderNumber}/cancel`

**Response (200):**
```json
{
  "success": true,
  "message": "Order cancelled successfully",
  "data": {
    // Cancelled order data
  }
}
```

---

### 8. Restore Order

Mengembalikan order yang telah dihapus.

**Endpoint:** `POST /orders/{id}/restore`

**Response (200):**
```json
{
  "success": true,
  "message": "Order restored successfully",
  "data": {
    // Restored order data
  }
}
```

---

### 9. Add Order Item

Menambahkan item ke order.

**Endpoint:** `POST /orders/{order}/items`

**Request Body:**
```json
{
  "mdx_menu_id": 1,
  "quantity": 2,
  "unit_price": 50000,
  "discount_amount": 0,
  "notes": "Tidak pakai acar"
}
```

**Response (201):**
```json
{
  "success": true,
  "message": "Item added successfully",
  "data": {
    "id": 1,
    "mdx_order_id": 1,
    "mdx_menu_id": 1,
    "quantity": 2,
    "unit_price": "50000.00",
    "subtotal": "100000.00"
  }
}
```

---

### 10. Update Order Item

Update item dalam order.

**Endpoint:** `PUT /orders/{order}/items/{orderDetail}` atau `PATCH /orders/{order}/items/{orderDetail}`

**Request Body:**
```json
{
  "quantity": 3,
  "unit_price": 55000
}
```

**Response (200):**
```json
{
  "success": true,
  "message": "Item updated successfully",
  "data": {
    // Updated item data
  }
}
```

---

### 11. Remove Order Item

Menghapus item dari order.

**Endpoint:** `DELETE /orders/{order}/items/{orderDetail}`

**Response (200):**
```json
{
  "success": true,
  "message": "Item removed successfully"
}
```

---

## Catatan Penting

1. **Soft Delete**: Semua operasi delete menggunakan soft delete, data tidak benar-benar dihapus dari database.

2. **Pagination**: Semua endpoint list menggunakan pagination dengan default 15 item per halaman.

3. **Filtering**: Gunakan query parameters untuk filtering dan searching.

4. **Sorting**: Gunakan `order_by` dan `order_dir` untuk sorting.

5. **Relationships**: Gunakan parameter `with` untuk load relationships (jika tersedia).

6. **Phone Number Format**: Format nomor telepon Indonesia yang diterima:
   - `081234567890`
   - `+6281234567890`
   - `6281234567890`

7. **Token Expiration**: Token tidak memiliki expiration time secara default. Gunakan `logout` atau `logout-all` untuk revoke token.

---

## Changelog

### v1.0.0 (2024-12-12)
- Initial API documentation
- Authentication endpoints
- Masterdata CRUD endpoints (Brands, Stores, Categories, Menus, Bank Accounts, Tables)
- Orders endpoints

---

## Support

Untuk pertanyaan atau bantuan, silakan hubungi tim development.

