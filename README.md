# PoLuv Tasks

A modern task management application built with Laravel and Livewire.

## 🚀 Features

- ✅ Task management with categories and priorities
- 📊 Analytics and reporting
- 🍅 Pomodoro timer for focus sessions
- 🌓 Dark/Light theme support
- 🌍 Multi-language support (EN/VI)
- 📱 Responsive design
- 🔔 Task notifications
- 📈 Progress tracking

## 🆕 New Integrations

### 📊 Google Analytics
Track user behavior and analyze traffic patterns.

### 💬 Slack Notifications
Real-time error alerts and notifications to your team.

### 🔍 Sentry Error Tracking
Comprehensive error monitoring with detailed stack traces.

## 🧪 Testing Integrations

**📖 Xem hướng dẫn đầy đủ:** [TEST_INTEGRATIONS_GUIDE.md](TEST_INTEGRATIONS_GUIDE.md)

**Quick test URLs (chỉ hoạt động khi `APP_ENV=local`):**

## 📋 Requirements

- PHP 8.2 or higher
- Composer
- Node.js & NPM
- SQLite (or other database)

## 🛠️ Installation

1. Clone the repository:
```bash
git clone <repository-url>
cd poluv_tasks.github.io
```

2. Install dependencies:
```bash
composer install
npm install
```

3. Setup environment:
```bash
cp .env.example .env
php artisan key:generate
```

4. Configure database:
```bash
touch database/database.sqlite
php artisan migrate
```

5. Build assets:
```bash
npm run build
```

6. Start development server:
```bash
php artisan serve
```

## ⚙️ Configuration

### Basic Configuration
Edit `.env` file for basic settings (database, app name, etc.)

### Integrations Configuration
See [ENV_CONFIGURATION.md](ENV_CONFIGURATION.md) for:
- Google Analytics setup
- Slack webhook configuration
- Sentry DSN setup

## 🧪 Testing Integrations

### Test từ Browser (Dễ nhất) ⭐

**1. Test Slack:**
```
http://localhost:8000/test/slack        → Gửi message test
http://localhost:8000/test/slack-error  → Tạo lỗi (gửi đến Slack + Sentry)
```

**2. Test Sentry:**
```
http://localhost:8000/test/sentry          → Gửi message test
http://localhost:8000/test/sentry-exception → Tạo exception (auto-capture)
```

**3. Test Google Analytics:**
```
http://localhost:8000/test/ga  → Track page view (xem GA Realtime)
```

### Test bằng Tinker (Chi tiết hơn)

```bash
php artisan tinker
```

**Test Slack:**
```php
>>> \App\Facades\Slack::info('🧪 Test từ Tinker');
>>> \App\Facades\Slack::error('Test error', new \Exception('Test exception'));
>>> \App\Facades\Slack::success('Test thành công!');
```

**Test Sentry:**
```php
>>> \Sentry\captureMessage('🧪 Test Sentry từ Tinker');
>>> throw new \Exception('Test exception cho Sentry');
```

**Kiểm tra cấu hình:**
```php
>>> config('services.google_analytics.tracking_id');  // Kiểm tra GA ID
>>> config('logging.channels.slack.url');  // Kiểm tra Slack Webhook
>>> config('sentry.dsn');  // Kiểm tra Sentry DSN
```

### Kiểm tra kết quả:

- **Slack**: Vào Slack workspace → Channel đã cấu hình → Xem message
- **Sentry**: Vào https://sentry.io → Issues → Xem events
- **Google Analytics**: Vào https://analytics.google.com → Realtime → Xem visits/events

### ⚠️ Lưu ý

- Route `/test-integrations` chỉ hoạt động khi `APP_ENV` ≠ `production`
- Nếu không thấy route, kiểm tra `.env`: `APP_ENV=local`
- Đảm bảo đã cấu hình API keys trong `.env` trước khi test

## 📚 Documentation

- [Quick Start Guide](QUICK_START_INTEGRATIONS.md) - Get started in 5 minutes
- [Integration Summary](INTEGRATION_SUMMARY.md) - Overview of all integrations
- [Google Analytics Setup](GOOGLE_ANALYTICS_SETUP.md) - Detailed GA setup guide
- [Slack Setup](SLACK_SETUP.md) - Slack notification guide
- [Sentry Setup](SENTRY_SETUP.md) - Error tracking setup
- [Environment Configuration](ENV_CONFIGURATION.md) - All environment variables

## 🔧 Development

### Clear caches:
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan optimize:clear
```

### Run development server:
```bash
composer run dev
```

This will start:
- Laravel development server
- Queue worker
- Vite dev server

## 📦 Production Deployment

1. Set environment to production:
```env
APP_ENV=production
APP_DEBUG=false
```

2. Configure integrations:
```env
GOOGLE_ANALYTICS_TRACKING_ID=G-XXXXXXXXXX
LOG_SLACK_WEBHOOK_URL=https://hooks.slack.com/...
SENTRY_LARAVEL_DSN=https://xxx@xxx.ingest.sentry.io/xxx
```

3. Optimize application:
```bash
composer install --optimize-autoloader --no-dev
php artisan config:cache
php artisan route:cache
php artisan view:cache
npm run build
```

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## 📝 License

This project is open-sourced software licensed under the MIT license.

## 🆘 Support

If you encounter any issues:
1. Check the documentation files
2. Visit `/test-integrations` to diagnose integration issues
3. Check Laravel logs in `storage/logs/laravel.log`
4. Review Sentry dashboard for errors

## 🎯 Roadmap

- [ ] Email notifications
- [ ] Task templates
- [ ] Team collaboration features
- [ ] Mobile app
- [ ] API documentation
- [ ] Advanced analytics

---

Made with ❤️ by PoLuv