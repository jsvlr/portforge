# Portfolio

A multi-user personal portfolio & blog admin panel built with **Laravel 13**, **Filament 5**, **Livewire 4**, and **Pest**. Each registered user gets their own isolated content workspace — posts, post categories, projects, and project roles are automatically scoped to the authenticated owner.

## ✨ Features

### Admin Panel (`/admin`)

- **Authentication**
    - Login, registration, password reset, and user profile
    - Enhanced auth UI (right-side form panel) via `filament-auth-ui-enhancer`
    - Database notifications
    - Email multi-factor authentication (MFA)

- **Blog Management**
    - **Posts** — full CRUD with rich text editor, cover image, tags, publish status (`Published` / `Draft`), views counter, and SEO meta fields
    - **Post Categories** — manage categories with an active/inactive visibility toggle, deduplicated slugs, and post counts

- **Portfolio Management**
    - **Projects** — full CRUD with rich text editor, image gallery (up to 5 images), client name, project role assignment, status (`Published` / `Draft`), start/end dates, and views counter
    - **Project Roles** — reusable roles (e.g. Backend Developer, UI Designer) attached to projects

- **Dashboard Widgets**
    - Stats overviews for total posts, published, drafts, and total views — each with a 7-day trend chart
    - Category statistics

- **Settings**
    - Site-wide general settings via `filament-settings`

### Public Pages

- `/` — Home (Volt page)
- `/projects` — Projects listing (Volt page)

### Multi-User Data Isolation

- The `BelongToUser` trait applies a global `UserScope` to all content models
- Every user **only sees, edits, and deletes their own records**
- New records are automatically assigned to the authenticated user on creation

## 🛠 Tech Stack

| Layer    | Technology                                           |
| -------- | ---------------------------------------------------- |
| Backend  | PHP 8.3, Laravel 13                                  |
| Admin UI | Filament 5.6                                         |
| Frontend | Livewire 4.3, Tailwind CSS 4, Vite 8                 |
| Testing  | Pest 4.7 (feature tests)                             |
| Database | MySQL (default) / SQLite (tests)                     |
| Extras   | Laravel Sanctum, Filament Settings, Auth UI Enhancer |

## 📦 Installation

### Prerequisites

- PHP ^8.3
- Composer
- Node.js & npm
- MySQL (or SQLite for quick setup)

### Steps

```bash
# 1. Clone the repository
git clone https://github.com/jsvlr/my-portfolio.git
cd portfolio

# 2. Install PHP dependencies
composer install

# 3. Set up environment
cp .env.example .env
php artisan key:generate

# 4. Configure database in .env (DB_DATABASE, DB_USERNAME, DB_PASSWORD)
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=portfolio
DB_USERNAME=root
DB_PASSWORD=

# 5. Run migrations
php artisan migrate

# 6. Install & build frontend assets
npm install
npm run build

# 7. Start the development server
php artisan serve
```

### One-Command Setup (alternative)

```bash
composer run setup
```

This runs `composer install`, creates `.env`, generates the app key, migrates, installs npm dependencies, and builds assets.

## 🚀 Development

Run the full dev stack (server, queue, logs, and Vite) concurrently:

```bash
composer run dev
```

This starts:

- `php artisan serve` — web server
- `php artisan queue:listen` — queue worker
- `php artisan pail` — real-time log tailing
- `npm run dev` — Vite dev server

## 🧪 Testing

The project uses **Pest** for feature testing, exercising the full Filament admin panel — CRUD operations, table actions, and multi-user authorization.

```bash
# Run the entire test suite
composer test

# Or directly
php artisan test

# Run a single test file
php artisan test tests/Feature/ProjectTest.php

# Run a filtered test
php artisan test --filter="can create project"
```

### Test Coverage

| Test File              | Verifies                                                                                                          |
| ---------------------- | ----------------------------------------------------------------------------------------------------------------- |
| `PostTest.php`         | Render, view own posts, cannot view others, create, update, cannot update others, delete                          |
| `PostCategoryTest.php` | Render, view own categories, cannot view others, create, update, cannot edit others, cannot delete others, delete |
| `ProjectTest.php`      | Render, view own projects, cannot view others, create, update, cannot edit others, delete, cannot delete others   |
| `ProjectRoleTest.php`  | Project role CRUD and user isolation                                                                              |
| `PagesTest.php`        | Public homepage accessibility                                                                                     |

### Testing Notes

- Tests use the **`RefreshDatabase`**-style behavior via Pest's `TestCase`
- Each test authenticates a freshly factory-created user and asserts both the **happy path** and **user isolation** (i.e., users cannot see/edit/delete other users' records)
- `assertDatabaseHas` / `assertDatabaseMissing` verify actual database state, accounting for Eloquent casts (JSON-encoded arrays, `Y-m-d H:i:s` datetime formats)

## 📁 Project Structure

```
app/
├── Enums/
│   ├── NavigationGroup.php       # Admin navigation grouping
│   ├── PostStatusEnum.php        # Post: Published / Draft
│   └── ProjectStatusEnum.php     # Project: Published / Draft
├── Filament/
│   ├── Pages/
│   │   ├── Auth/Login.php        # Custom login page
│   │   └── Settings/General.php  # Site settings
│   ├── Resources/
│   │   ├── PostCategories/       # Manage categories (table actions)
│   │   ├── Posts/                # Posts CRUD
│   │   ├── ProjectRoles/         # Project roles CRUD
│   │   └── Projects/             # Projects CRUD
│   └── Widgets/
│       ├── PostStats.php         # Dashboard blog stats
│       └── PostCategoryStats.php # Dashboard category stats
├── Models/
│   ├── Post.php
│   ├── PostCategory.php
│   ├── Project.php
│   ├── ProjectRole.php
│   ├── User.php
│   ├── Scopes/UserScope.php      # Global user-isolation scope
│   └── Traits/BelongToUser.php   # Applies UserScope + auto-assigns user_id
└── Providers/
    └── Filament/
        ├── AdminBaseProvider.php # Shared panel config
        └── AdminPanelProvider.php# Admin panel setup

database/
├── factories/                    # PostFactory, ProjectFactory, etc.
├── migrations/                   # Users, posts, categories, projects, roles
└── seeders/

resources/views/pages/            # Public Volt pages (Home, Projects)

routes/web.php                    # Public routes
```

## 🧩 Key Implementation Details

### User Isolation via Global Scope

`app/Models/Traits/BelongToUser.php` is used by `Post`, `PostCategory`, `Project`, and `ProjectRole`:

```php
protected static function booted(): void
{
    static::addGlobalScope(new UserScope);

    static::creating(function ($model) {
        if (auth()->check() && empty($model->user_id)) {
            $model->user_id = auth()->id();
        }
    });
}
```

- **`UserScope`** adds `WHERE user_id = <current user>` to every query, so users can never see (or resolve) other users' records.
- The **`creating` hook** auto-assigns the authenticated user to new records — no need to set `user_id` manually.

### Testing User Isolation

Because of the global scope:

- **Page-based records** (`EditRecord`) throw `ModelNotFoundException` when trying to access another user's record.
- **Table actions** (`EditAction` / `DeleteAction`) silently fail to mount — the action is treated as never clicked. Tests assert this with `assertActionNotMounted()` and verify data integrity with `assertDatabaseMissing()` / `assertDatabaseHas()`.

## 🔗 License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
