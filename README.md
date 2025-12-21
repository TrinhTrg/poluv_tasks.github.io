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

# Performance Optimizations

The application implements various performance optimizations:

- **Database Query Optimization**: Eager loading to prevent N+1 queries
- **Caching**: Task and category caching for improved response times
- **Bulk Operations**: Batch updates to reduce database queries
- **Frontend Optimization**: Single-pass filtering and lazy loading
- **Asset Optimization**: Code splitting, minification, and compression
- **Cache Headers**: Proper ETag and Cache-Control headers for static resources

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
