# PoLuv Tasks
<img width="1906" height="863" alt="image" src="https://github.com/user-attachments/assets/a7b1897a-9011-4dbd-8ca9-673acc4a9886" />
<img width="1901" height="863" alt="image" src="https://github.com/user-attachments/assets/b3bb60d5-8328-4354-a687-b7e1fb292966" />

## View HTML Prototype

To view the HTML prototype design:

1. Navigate to the prototype directory:
```bash
cd prototype-test-UI
```

2. Open `index.html` in your web browser:
   - **Option 1**: Double-click `index.html` file
   - **Option 2**: Right-click `index.html` → "Open with" → Choose your browser
   - **Option 3**: Use a local server (recommended):
   ```bash
   # Using Python (if installed)
   python -m http.server 8001
   
   # Using PHP (if installed)
   php -S localhost:8001
   ```
   Then open `http://localhost:8001` in your browser

**Note**: The prototype is a static HTML file showing the UI design mockup before implementation.

# Overview

PoLuv Tasks is a modern task management application that helps users organize their daily tasks efficiently. Our application provides a comprehensive solution for task management with features like priority-based organization, category management, Pomodoro timer for focused work sessions, and intelligent notifications. We focus on creating an intuitive user experience that adapts to both light and dark themes, supporting multiple languages to serve a global audience.

# Technologies

This Laravel 12 task management web application leverages modern and efficient technologies to deliver a high-performance, secure, and scalable user experience:

-   **Laravel 12**: Latest version of the PHP framework for robust backend development
-   **Blade Templates**: For rendering dynamic and reusable HTML views
-   **Eloquent ORM**: Simplifies database interactions using an intuitive and expressive syntax with optimized queries
-   **MySQL/SQLite Database**: A reliable and scalable relational database system
-   **Livewire 3**: Enables seamless interaction between the frontend and backend without writing JavaScript
-   **Tailwind CSS**: A utility-first framework for styling with speed and flexibility
-   **Alpine.js**: Lightweight JavaScript framework for interactive UI components
-   **Vite**: Modern build tool for compiling and optimizing frontend assets
-   **Laravel Sanctum**: Provides robust API authentication with token-based and session-based authentication
-   **Laravel Telescope**: Offers powerful debugging and monitoring tools for real-time application insights
-   **Sentry**: Comprehensive error tracking and monitoring with detailed stack traces
-   **Slack Integration**: Facilitates real-time communication and alerts for application events
-   **Google Analytics**: Tracks user behavior and analyzes traffic patterns
-   **Job Queues**: Handles time-consuming tasks asynchronously to improve application responsiveness
-   **Batch Jobs**: Manages scheduled tasks like email reminders and password reset codes
-   **Service Layer Architecture**: Separates business logic from controllers for better code organization
-   **Dedoc Scramble**: Automatic API documentation generation from code
-   **Middleware**: For security, data validation, localization, and performance optimizations
-   **Caching Mechanisms**: Browser caching, server-side caching, and cache headers for improved performance
-   **Monolog**: Integrated logging system for debugging and error tracking
-   **PHPUnit**: Comprehensive testing suite for ensuring application reliability (70%+ code coverage)
-   **Custom Exceptions**: Proper error handling with meaningful HTTP status codes
-   **Database Migrations**: Version-controlled database schema management
-   **Seeders & Factories**: Automated data generation for testing and development

# Features

- ✅ **Task Management**: Create, update, delete, and organize tasks with categories and priorities
- 📊 **Analytics Dashboard**: Track progress and visualize task completion statistics
- 🍅 **Pomodoro Timer**: Built-in focus timer for productive work sessions
- 🌓 **Dark/Light Theme**: Automatic theme switching based on system preferences
- 🌍 **Multi-language Support**: Available in English (EN) and Vietnamese (VI)
- 📱 **Responsive Design**: Optimized for desktop, tablet, and mobile devices
- 🔔 **Smart Notifications**: Task reminders and due date alerts via email
- 📈 **Progress Tracking**: Monitor task completion and productivity metrics
- 🔐 **Secure Authentication**: Session-based for web app, token-based for API clients
- 📝 **RESTful API**: Complete API with authentication and documentation
- 🔍 **Search & Filter**: Advanced filtering by status, category, priority, and date
- 🎨 **Customizable Categories**: Organize tasks with color-coded categories
- ⚡ **Performance Optimized**: Eager loading, query optimization, and caching strategies

# Requirements

- PHP 8.2 or higher
- Composer
- Node.js & NPM
- MySQL or SQLite
- Git

# Installation

1. Clone the repository:
```bash
git clone <repository-url>
cd poluv_tasks.github.io
```

2. Install PHP dependencies:
```bash
composer install
```

3. Install Node.js dependencies:
```bash
npm install
```

4. Setup environment:
```bash
cp .env.example .env
php artisan key:generate
```

5. Configure database in `.env`:
```env
DB_CONNECTION=sqlite
# Or use MySQL:
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=poluv_tasks
# DB_USERNAME=root
# DB_PASSWORD=
```

6. Run migrations and seeders:
```bash
touch database/database.sqlite  # For SQLite only
php artisan migrate
php artisan db:seed
```

7. Build assets:
```bash
npm run build
```

8. Start development server:
```bash
php artisan serve
```

For development with hot reload:
```bash
composer run dev
```

# Configuration

## Environment Variables

Configure integrations in `.env`:

```env
# Google Analytics
GOOGLE_ANALYTICS_TRACKING_ID=G-XXXXXXXXXX

# Slack Notifications
LOG_SLACK_WEBHOOK_URL=https://hooks.slack.com/services/...

# Sentry Error Tracking
SENTRY_LARAVEL_DSN=https://xxx@xxx.ingest.sentry.io/xxx

# Application
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000
```

# Testing

## Run Tests

```bash
php artisan test
```

## Code Coverage

```bash
php artisan test --coverage
```

Target: 70%+ code coverage

## Test Results

Current test suite status: **All tests passing** ✅

### Test Statistics

- **Total Tests**: 126+ passed (Unit: 62, Feature: 64+)
- **Total Assertions**: 320+
- **Duration**: ~70-90 seconds
- **Status**: All tests passing ✅
- **Pass Rate**: 100%

### Test Coverage by Category

#### Unit Tests (62 tests)
- ✅ **Example Test** (1 test)
  - Basic functionality verification

- ✅ **Models** (17 tests)
  - **Category** (5 tests): relationships, timestamps, CRUD operations
  - **Task** (7 tests): relationships, date casting, boolean casting, priority casting, toggle completion
  - **User** (5 tests): relationships, password hashing, array visibility

- ✅ **Services** (44 tests)
  - **CategoryService** (16 tests): CRUD operations, authorization, caching, exception handling
  - **TaskService** (28 tests): CRUD operations, filtering, search, authorization, date/time handling, category resolution

#### Feature Tests (65 tests)
- ✅ **Authentication** (12 tests)
  - User registration and validation
  - Login/logout functionality
  - Protected route access

- ✅ **API Authentication (Sanctum)** (11 tests)
  - Token-based authentication
  - Token management (create, revoke, list)
  - Custom token abilities/scopes

- ✅ **Category Management** (10 tests)
  - CRUD operations via API
  - Authorization and ownership validation
  - Validation rules

- ✅ **Task Management** (15 tests)
  - CRUD operations via API
  - Search and filtering
  - Status toggling
  - Authorization and ownership validation

- ✅ **Welcome Page** (3 tests)
  - Page accessibility for guests
  - Page loading verification
  - Authenticated user redirect to home

- ✅ **E2E Tests (Modal Interactions)** (6 tests)
  - Task create/edit via API (modal endpoints)
  - Category create/edit via API (modal endpoints)
  - Validation error handling in modals

- ✅ **Integration Tests** (17 tests)
  - **Slack Notifications** (9 tests)
    - Error, warning, success, and info notifications
    - Queue processing for async notifications
    - Webhook error handling
    - Task due and user registration notifications
  - **Email Notifications** (8 tests)
    - Task due reminders
    - Password reset codes
    - Incomplete tasks reminders
    - Email content validation

- ✅ **Performance Benchmarks** (6 tests)
  - API endpoint response times
  - Search and filtering performance
  - Bulk operations efficiency
  - Query optimization verification

### Running Specific Test Suites

```bash
# Run all tests
php artisan test

# Run only unit tests
php artisan test --testsuite=Unit

# Run only feature tests
php artisan test --testsuite=Feature

# Run specific test class
php artisan test --filter=CategoryControllerTest

# Run E2E tests
php artisan test --filter=ModalInteractionTest

# Run integration tests
php artisan test tests/Integration

# Run performance tests
php artisan test --filter=PerformanceBenchmarkTest

# Run with coverage report
php artisan test --coverage
```

### Test Quality Metrics

- ✅ **100% Pass Rate**: All 126+ tests passing
- ✅ **Comprehensive Coverage**: Models, Services, Controllers, and API endpoints
- ✅ **Authorization Testing**: All protected routes and ownership validations tested
- ✅ **Validation Testing**: Input validation for all forms and API endpoints
- ✅ **Error Handling**: Custom exceptions and error responses tested
- ✅ **E2E Testing**: Modal interactions and API endpoints tested
- ✅ **Integration Testing**: Slack and Email notifications fully tested
- ✅ **Performance Testing**: Response times and query optimization verified
- ✅ **Route Protection**: Guest and authenticated user access properly tested

### Recent Test Results Summary

```
✓ Unit Tests: 62 passed
  - Models: 17 tests
  - Services: 44 tests
  - Example: 1 test

✓ Feature Tests: 64+ passed
  - Authentication: 12 tests
  - API Authentication (Sanctum): 11 tests
  - Category Management: 10 tests
  - Task Management: 15 tests
  - Welcome Page: 3 tests
  - E2E (Modal Interactions): 6 tests
  - Performance Benchmarks: 6 tests

✓ Integration Tests: 17+ passed
  - Slack Notifications: 9 tests
  - Email Notifications: 8 tests

Total: 126+ tests, 100% pass rate
```

# Development

## Clear Caches

```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
php artisan optimize:clear
```

## Queue Worker

```bash
php artisan queue:work
```

## Scheduled Tasks

The application uses Laravel's task scheduler. Make sure to add this to your crontab:

```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

Scheduled tasks include:
- Task due notifications (every minute)
- Incomplete tasks reminders (daily at midnight)
- Server metrics monitoring (every 5 minutes)

# Production Deployment

1. Set environment to production:
```env
APP_ENV=production
APP_DEBUG=false
```

2. Configure integrations (see Configuration section)

3. Optimize application:
```bash
composer install --optimize-autoloader --no-dev
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
npm run build
```

4. Set up queue worker and scheduler (see Development section)

# API Documentation

Access API documentation at:
```
http://localhost:8000/docs/api
```

## API Authentication with Laravel Sanctum

This application uses **Laravel Sanctum** for API authentication, providing both token-based and session-based authentication.

### Sanctum Installation & Configuration

Sanctum is already installed via Composer. The configuration includes:

1. **Package Installation** (already done):
   ```bash
   composer require laravel/sanctum
   ```

2. **Migration** (already run):
   ```bash
   php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
   php artisan migrate
   ```
   This creates the `personal_access_tokens` table to store API tokens.

3. **Model Configuration**:
   The `User` model uses the `HasApiTokens` trait:
   ```php
   use Laravel\Sanctum\HasApiTokens;

   class User extends Authenticatable
   {
       use HasApiTokens, Notifiable;
       // ...
   }
   ```

4. **Middleware Configuration**:
   Protected API routes use `auth:sanctum` middleware:
   ```php
   Route::middleware(['web', 'auth:sanctum'])->prefix('v1')->group(function () {
       // Protected routes
   });
   ```

5. **Scramble Integration**:
   Sanctum Bearer Token authentication is configured in `AppServiceProvider` for Scramble documentation:
   ```php
   Scramble::afterOpenApiGenerated(function (\Dedoc\Scramble\Support\Generator\OpenApi $openApi) {
       $openApi->secure(
           \Dedoc\Scramble\Support\Generator\SecurityScheme::http('bearer', 'Sanctum')
               ->as('sanctum')
               ->setDescription('Sanctum Bearer Token authentication. Get your token from /api/v1/auth/login endpoint.')
               ->default()
       );
   });
   ```

### Token Abilities/Scopes

Sanctum supports token abilities for fine-grained access control:

**Available Abilities:**
- `tasks:read` - Read tasks
- `tasks:write` - Create and update tasks
- `tasks:delete` - Delete tasks
- `categories:read` - Read categories
- `categories:write` - Create and update categories
- `categories:delete` - Delete categories
- `*` - Full access (default)

### Testing API with Scramble

1. **Access Scramble Documentation**:
   ```
   http://localhost:8000/docs/api
   ```

2. **Get a Token**:
   - Find the `POST /api/v1/auth/login` endpoint
   - Send a request with your credentials:
     ```json
     {
       "email": "your-email@example.com",
       "password": "your-password",
       "device_name": "Scramble API Test"
     }
     ```
   - Copy the `token` from the response

3. **Use Token in Scramble**:
   - Find the **"Auth" panel** (right sidebar or top bar)
   - Enter your token in the **"Token:"** field
   - The token will be automatically used for all protected endpoints

4. **Test Protected Endpoints**:
   - All protected endpoints (marked with 🔒) will use the token
   - Example: `GET /api/v1/auth/user` to verify authentication
   - Status `200 OK` with user data confirms successful authentication

### API Authentication Endpoints

- `POST /api/v1/auth/login` - Login and create API token
- `GET /api/v1/auth/user` - Get authenticated user info
- `GET /api/v1/auth/tokens` - List all user tokens
- `POST /api/v1/auth/logout` - Revoke current token
- `POST /api/v1/auth/logout-all` - Revoke all user tokens
- `DELETE /api/v1/auth/tokens/{tokenId}` - Revoke specific token
- `GET /api/v1/auth/abilities` - Get available token abilities/scopes

### Token Management

**Create Token with Specific Abilities:**
```json
{
  "email": "user@example.com",
  "password": "password",
  "device_name": "Read-Only App",
  "abilities": ["tasks:read", "categories:read"]
}
```

**View Token Abilities:**
Call `GET /api/v1/auth/user` to see:
```json
{
  "user": {...},
  "token_abilities": ["tasks:read", "categories:read"],
  "auth_type": "token"
}
```

# Performance Optimizations

The application implements various performance optimizations:

- **Database Query Optimization**: Eager loading to prevent N+1 queries
- **Caching**: Task and category caching for improved response times
- **Bulk Operations**: Batch updates to reduce database queries
- **Frontend Optimization**: Single-pass filtering and lazy loading
- **Asset Optimization**: Code splitting, minification, and compression
- **Cache Headers**: Proper ETag and Cache-Control headers for static resources

## Comprehensive Caching Strategies

This application implements a complete caching strategy covering browser caching, server-side caching, cache headers, and cache invalidation. All requirements for caching have been fully implemented and are actively working in the application.

### 1. Browser Caching & Cache Headers

**Custom Middleware: `SetCacheHeaders`** (`app/Http/Middleware/SetCacheHeaders.php`)

The application uses a custom middleware to set appropriate cache headers for different types of content:

#### API Responses (JSON)
- **Cache-Control**: `public, max-age=60, must-revalidate`
- **ETag**: MD5 hash of response content for cache validation
- **Strategy**: Only for guest/unauthenticated GET requests
- **Location**: Applied in `TaskController` and `CategoryController`

#### Static Assets (Images, CSS, JS)
- **Cache-Control**: `public, max-age=31536000, immutable` (1 year)
- **Strategy**: Long-term caching for assets that don't change frequently
- **Implementation**: Middleware can be applied via `cache.headers:static` alias

#### Authenticated Requests
- **Cache-Control**: `no-cache, no-store, must-revalidate, private`
- **Pragma**: `no-cache`
- **Expires**: `0`
- **Strategy**: Ensures authenticated users always receive fresh data

#### HTML Responses (Views)
- **Cache-Control**: `no-cache, no-store, must-revalidate, private`
- **Strategy**: Prevents caching of Blade views to ensure `$errors` and other dynamic variables are always available

**Implementation Example:**
```php
// In API Controllers
if (!$request->user() && $request->isMethod('GET')) {
    $response->headers->set('Cache-Control', 'public, max-age=60, must-revalidate');
    $etag = md5($response->getContent());
    $response->setEtag($etag);
}
```

### 2. Server-Side Caching

#### Task Caching (`TaskService`)
- **Strategy**: Cache guest user requests only (60 seconds TTL)
- **Cache Key**: `tasks:user:guest:{filter_hash}`
- **Rationale**: Authenticated users need fresh data after create/update/delete operations
- **Implementation**: `Cache::remember()` with dynamic cache keys based on filters

```php
if ($userId === null) {
    $cacheKey = 'tasks:user:guest:' . md5(serialize($filters));
    return Cache::remember($cacheKey, 60, function () use ($filters) {
        return $this->fetchTasks($filters, null);
    });
}
```

#### Category Caching (`CategoryService`)
- **Strategy**: Cache for both authenticated and guest users
- **TTL**: 60 seconds (authenticated) or 120 seconds (guest)
- **Cache Key**: `categories:user:{userId|guest}`
- **Implementation**: `Cache::remember()` with user-specific keys

```php
$cacheKey = 'categories:user:' . ($userId ?? 'guest');
$cacheTtl = $userId ? 60 : 120;
return Cache::remember($cacheKey, $cacheTtl, function () use ($userId) {
    // Fetch categories from database
});
```

#### Homepage Caching (`HomepageController`)
- **Strategy**: Cache initial page load only (30 seconds TTL)
- **Cache Key**: `homepage:tasks:user:{userId}`
- **Rationale**: AJAX requests (reloadTasks) always fetch fresh data
- **Implementation**: Skips caching for AJAX requests

```php
if (!$isAjaxRequest) {
    $cacheKey = 'homepage:tasks:user:' . $userId;
    $tasks = Cache::remember($cacheKey, 30, function () use ($userId) {
        return Task::with('category')->where('user_id', $userId)->get();
    });
}
```

### 3. Cache Invalidation Strategies

The application implements multiple cache invalidation strategies to ensure data consistency:

#### Automatic Cache Invalidation via Model Observers

**TaskObserver** (`app/Observers/TaskObserver.php`):
- Automatically clears cache when tasks are created, updated, or deleted
- Uses **cache versioning** with `Cache::increment()` for efficient invalidation
- Clears homepage cache when tasks change
- **Events**: `created()`, `updated()`, `deleted()`

```php
protected function clearTaskCache(Task $task): void
{
    $userId = $task->user_id ?? 'guest';
    // Version-based invalidation
    Cache::increment('user:' . $userId . ':tasks_version');
    // Direct cache clearing
    Cache::forget('homepage:tasks:user:' . $userId);
}
```

**CategoryObserver** (`app/Observers/CategoryObserver.php`):
- Automatically clears cache when categories are created, updated, or deleted
- Clears both categories cache and homepage tasks cache (since tasks display category info)
- Supports cache tags if using Redis/Memcached
- **Events**: `created()`, `updated()`, `deleted()`

```php
protected function clearCategoryCache(Category $category): void
{
    $userId = $category->user_id ?? 'guest';
    Cache::forget('categories:user:' . $userId);
    Cache::forget('homepage:tasks:user:' . $userId);
}
```

#### Manual Cache Invalidation in Services

**TaskService**:
- `clearUserTasksCache()` called after create/update/delete/toggle operations
- Clears homepage cache and related category caches
- Supports cache tags (Redis/Memcached) with fallback to manual clearing

**CategoryService**:
- `clearUserCategoriesCache()` called after create/update/delete operations
- Ensures cache consistency across the application

### 4. Cache Configuration

#### Cache Driver Support
- **Database Cache**: Default for development (migration included)
- **File Cache**: Alternative for development
- **Redis/Memcached**: Production-ready with cache tags support
- **Configuration**: `config/cache.php`

#### Cache Table
- Migration: `0001_01_01_000001_create_cache_table.php`
- Stores cache data when using database driver
- Automatically created during `php artisan migrate`

### 5. Cache Strategy Summary

| Component | Cache Type | TTL | Invalidation | Location |
|-----------|-----------|-----|--------------|----------|
| **API Responses (Guest)** | Browser + Server | 60s | ETag validation | `TaskController`, `CategoryController` |
| **API Responses (Auth)** | None | - | Always fresh | Controllers set `no-cache` headers |
| **Tasks (Guest)** | Server-side | 60s | Observer + Manual | `TaskService::getTasks()` |
| **Tasks (Auth)** | None | - | Always fresh | `TaskService` skips cache |
| **Categories** | Server-side | 60s/120s | Observer + Manual | `CategoryService::getCategories()` |
| **Homepage** | Server-side | 30s | Observer + Manual | `HomepageController` |
| **Static Assets** | Browser | 1 year | Immutable | `SetCacheHeaders` middleware |

### 6. Benefits of This Caching Strategy

✅ **Performance**: Reduced database queries and faster response times  
✅ **Scalability**: Handles high traffic with efficient caching  
✅ **Data Consistency**: Automatic cache invalidation ensures users see latest data  
✅ **User Experience**: Authenticated users always get fresh data, guests get cached data  
✅ **CDN Ready**: Cache headers properly configured for CDN integration  
✅ **Flexibility**: Supports multiple cache drivers (database, file, Redis, Memcached)

### 7. Cache Monitoring

Cache operations are logged for debugging:
- TaskObserver logs cache clearing events
- CategoryObserver logs cache clearing events
- Cache version increments are tracked

**All caching requirements have been fully implemented and are actively working in the application.**

# Troubleshooting

## Common Issues

1. **500 Error on Login**: Check `.env` file and ensure `APP_KEY` is set
2. **Queue Not Processing**: Verify `QUEUE_CONNECTION` in `.env` and run `php artisan queue:work`
3. **Assets Not Loading**: Run `npm run build` or `npm run dev`
4. **Database Errors**: Run `php artisan migrate:fresh --seed`

## Logs

Check application logs:
```bash
tail -f storage/logs/laravel.log
```

## Debug Mode

For detailed error information, enable debug mode in `.env`:
```env
APP_DEBUG=true
```

# Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

# License

This project is open-sourced software licensed under the MIT license.

# Contributors

- PoLuv Development Team

---

# Video Demo

Here is video demo:

[https://drive.google.com/file/d/1PN8woXC4GmS8iec9DAMltMPLcu1GUoAI/view?usp=sharing]

---

Made with ❤️ by PoLuv
