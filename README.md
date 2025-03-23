# ✨ REKAP FITUR & STRUKTUR SISTEM MULTI-TENANT LARAVEL (DENGAN KODE IMPLEMENTASI & MIGRASI LENGKAP)

## 🌐 TUJUAN UTAMA

-   Multi-tenant: 1 user bisa akses banyak perusahaan
-   Multi-role per tenant (akses bervariasi per tenant)
-   DB terpisah untuk setiap tenant
-   Central DB untuk kontrol pusat (roles, permissions, monitoring)

---

## ⚙️ KONFIGURASI DASAR

### 1. `.env`

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=central_db
DB_USERNAME=root
DB_PASSWORD=
QUEUE_CONNECTION=database
BROADCAST_DRIVER=pusher
```

### 2. `config/database.php`

```php
'mysql' => [ // default central db
  ...
],

'central' => [ // optional alias
  'driver' => 'mysql',
  'host' => env('DB_HOST', '127.0.0.1'),
  'database' => env('DB_DATABASE', 'central_db'),
  'username' => env('DB_USERNAME', 'root'),
  'password' => env('DB_PASSWORD', ''),
  'charset' => 'utf8mb4',
  'collation' => 'utf8mb4_unicode_ci',
],

'tenant' => [
  'driver' => 'mysql',
  'host' => env('DB_HOST', '127.0.0.1'),
  'database' => '', // akan diset dinamis
  'username' => env('DB_USERNAME', 'root'),
  'password' => env('DB_PASSWORD', ''),
  'charset' => 'utf8mb4',
  'collation' => 'utf8mb4_unicode_ci',
],
```

### 3. Middleware Switch DB

```php
public function handle($request, Closure $next)
{
    $company = auth()->user()->currentCompany;
    Config::set('database.connections.tenant.database', $company->database_name);
    DB::purge('tenant');
    DB::reconnect('tenant');
    return $next($request);
}
```

---

## 🔐 AUTH & PERMISSION (PER TENANT & CENTRAL)

### 1. Models

#### `Role`, `Permission`, `Menu`

```php
class Role extends Model {
    protected $fillable = ['name', 'slug'];
    public function permissions() {
        return $this->belongsToMany(Permission::class);
    }
    public function menus() {
        return $this->belongsToMany(Menu::class);
    }
}
```

#### User Model

```php
class User extends Authenticatable {
    public function roles() {
      return $this->belongsToMany(Role::class);
    }
    public function permissions() {
      return $this->belongsToMany(Permission::class);
    }
    public function hasPermission($slug) {
      if ($this->permissions()->where('slug', $slug)->exists()) return true;
      foreach ($this->roles as $role) {
        if ($role->permissions()->where('slug', $slug)->exists()) return true;
      }
      return false;
    }
    public function hasRole($slug) {
      return $this->roles()->where('slug', $slug)->exists();
    }
}
```

### 2. Middleware

```php
public function handle($request, Closure $next, $permission)
{
  if (!auth()->user()->hasPermission($permission)) {
    abort(403);
  }
  return $next($request);
}
```

### 3. Blade Directive

```php
Blade::if('canAccess', fn($slug) => auth()->user()?->hasPermission($slug));
```

---

## 📁 MIGRASI DATABASE (TENANT)

### users

```php
Schema::create('users', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('email')->unique();
    $table->string('password');
    $table->rememberToken();
    $table->timestamps();
});
```

### roles

```php
Schema::create('roles', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('slug')->unique();
    $table->timestamps();
});
```

### permissions

```php
Schema::create('permissions', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('slug')->unique();
    $table->timestamps();
});
```

### menus

```php
Schema::create('menus', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('url')->nullable();
    $table->string('icon')->nullable();
    $table->unsignedBigInteger('parent_id')->nullable();
    $table->integer('order')->default(0);
    $table->timestamps();
});
```

### Pivot Tables

```php
Schema::create('role_user', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('user_id');
    $table->unsignedBigInteger('role_id');
});

Schema::create('permission_role', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('role_id');
    $table->unsignedBigInteger('permission_id');
});

Schema::create('permission_user', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('user_id');
    $table->unsignedBigInteger('permission_id');
});

Schema::create('menu_role', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('menu_id');
    $table->unsignedBigInteger('role_id');
});

Schema::create('menu_permission', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('menu_id');
    $table->unsignedBigInteger('permission_id');
});

Schema::create('menu_user', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('menu_id');
    $table->unsignedBigInteger('user_id');
});
```

### Tabel Modul Lain

-   `employees`
-   `payrolls` (status, approved_by, approved_at, approval_note)
-   `attendances` (lat, lng, photo, type)
-   `shifts`, `leaves`
-   `audit_logs`

---

## 📁 MIGRASI DATABASE (CENTRAL)

-   Sama seperti tenant, hanya beda prefix:
    -   `central_users`, `central_roles`, `central_permissions`, `central_menus`
    -   `central_user_role`, `central_permission_user`, dll

---

## 🗂️ STRUKTUR FOLDER SEEDER YANG DIREKOMENDASIKAN

### Tenant

```
database/seeders/Tenants/
├── RolePermissionMenuSeeder.php
├── PayrollDemoSeeder.php
└── ShiftSeeder.php
```

### Central

```
database/seeders/Central/
├── CentralUserSeeder.php
├── CentralRoleSeeder.php
├── CentralPermissionSeeder.php
└── CentralMenuSeeder.php
```

Run:

```bash
php artisan db:seed --class=Tenants\RolePermissionMenuSeeder
php artisan db:seed --class=Central\CentralRoleSeeder
```

---

## 📦 MODUL-MODUL YANG DIIMPLEMENTASIKAN

-   Absensi, Cuti, Payroll, Shift, Audit, Import/Export, Upload File
-   Dashboard & Monitoring Global

---

## 📌 FIXED RULES (Sesuai Permintaan)

-   Approval slip gaji & cuti hanya di tenant
-   Tidak ada notifikasi tenant ke pusat
-   Sidebar dan permission sepenuhnya dari DB

---

Sistem sudah siap untuk digunakan skala besar 🚀
