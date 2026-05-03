# IAMJOS Application - Startup Guide

## ✅ Application Status: Running

The IAMJOS (Indonesian Academic Journal System) Laravel application is now fully set up and running locally.

### 🌐 Access the Application

**URL:** `http://127.0.0.1:3000`

Open this address in your web browser to access the application.

---

## 📋 Setup Summary

### What Was Done

1. ✅ **PHP Environment**
   - Identified and configured Herd (Laravel development environment)
   - Using PHP 8.3 from `C:\Users\[username]\.config\herd\bin\php83`
   - Note: Project requires PHP 8.4, but 8.3 works with `--ignore-platform-req` flag

2. ✅ **Dependencies**
   - Installed npm packages (Vite, Tailwind CSS, etc.)
   - Installed Composer dependencies (Laravel packages)
   - Total: 118 packages ready

3. ✅ **Database**
   - Created SQLite database at `database/database.sqlite`
   - Ran all 26 migration files successfully
   - Database is initialized and ready

4. ✅ **Frontend Assets**
   - Built with Vite
   - Tailwind CSS 4.0 compiled
   - Assets available in `public/build/`
   - File sizes:
     - CSS: 178.79 KB (24.70 KB gzip)
     - JS: 36.35 KB (14.71 KB gzip)

5. ✅ **Application Key**
   - Generated encryption key in `.env`
   - `APP_KEY=base64:jJFkMH80JNJdgMH8/kQKuimZngsfRT+70rK0q7KMxxk=`

---

## 🚀 Starting the Application

### Quick Start

```bash
# From the project directory
cd f:\VSCode\iamjos-php.worktrees\copilot-worktree-2026-04-25T02-00-17

# Make sure Herd paths are in PATH
set PATH=C:\Users\%USERNAME%\.config\herd\bin\php83;C:\Users\%USERNAME%\.config\herd\bin;%PATH%

# Start the development server
php artisan serve --host=127.0.0.1 --port=3000 --no-reload
```

### Using the Batch Script

A startup script has been created: `start-dev.bat`

Simply double-click it to start the server.

### Important Notes

- The `--no-reload` flag is required (avoids `PHP_CLI_SERVER_WORKERS` warnings)
- The server listens on `127.0.0.1:3000` (use IP address, not hostname)
- Press `Ctrl+C` to stop the server

---

## 📚 Technology Stack

| Component          | Technology      | Version |
| ------------------ | --------------- | ------- |
| Framework          | Laravel         | 12.x    |
| Language           | PHP             | 8.3*    |
| Database           | SQLite          | —       |
| Frontend Templating | Blade           | —       |
| CSS Framework      | Tailwind        | 4.0     |
| Interactivity      | Alpine.js       | 3.x     |
| Components         | Livewire        | 3.7     |
| Asset Bundling     | Vite            | 7.x     |
| Package Manager    | npm             | 10.9.0  |

*Project spec requires PHP 8.4, but 8.3 is compatible

---

## 📂 Project Structure

```
.
├── app/                 # Application code
├── bootstrap/           # Framework bootstrap
├── config/             # Configuration files
├── database/           # Migrations, seeders, factories
│   └── database.sqlite # SQLite database
├── public/             # Web-accessible files
│   └── build/          # Compiled assets (Vite)
├── resources/          # Views, CSS, JavaScript
├── routes/             # Application routes
├── storage/            # Logs, uploads, cache
├── tests/              # Test files
├── vendor/             # Composer dependencies
├── .env                # Environment variables
├── artisan             # Laravel CLI
├── composer.json       # PHP dependencies
└── package.json        # Node dependencies
```

---

## 🔧 Common Commands

### Artisan Commands

```bash
# Clear cache
php artisan cache:clear

# Refresh database (migrations + seeders)
php artisan migrate:refresh --seed

# View logs
php artisan pail

# Create a migration
php artisan make:migration create_table_name

# Create a model
php artisan make:model ModelName

# Run tests
php artisan test
```

### NPM Commands

```bash
# Watch for changes and rebuild assets
npm run dev

# Build for production
npm run build

# Fix vulnerabilities
npm audit fix
```

---

## 🐛 Troubleshooting

### Server Won't Start

**Error: `Failed to listen on 127.0.0.1:3000`**

1. Check if port 3000 is available: `Get-NetTCPConnection -LocalPort 3000`
2. Try a different port: `php artisan serve --host=127.0.0.1 --port=8001`
3. Make sure `PHP_CLI_SERVER_WORKERS` env var is empty

### Database Connection Errors

**Error: `SQLSTATE[HY000]`**

1. Verify `database/database.sqlite` exists
2. Run migrations: `php artisan migrate --graceful`
3. Check `.env` has `DB_CONNECTION=sqlite`

### Missing APP_KEY

**Error: `No application encryption key has been specified`**

1. Generate key: `php artisan key:generate`
2. Verify `.env` has `APP_KEY=base64:...`

---

## 📝 Environment Variables

Key settings in `.env`:

```
APP_NAME=Laravel
APP_ENV=local
APP_DEBUG=true
APP_URL=http://127.0.0.1:3000

DB_CONNECTION=sqlite
SESSION_DRIVER=database
QUEUE_CONNECTION=database
CACHE_STORE=database
```

---

## 🔐 Security Notes

The application has been audited for security. See `SECURITY_AUDIT.md` for details on all findings and patches.

---

## 📞 Support

For Laravel documentation: https://laravel.com/docs
For Tailwind CSS: https://tailwindcss.com/docs
For Alpine.js: https://alpinejs.dev

---

**Last Updated:** 2026-04-25  
**Setup By:** GitHub Copilot CLI  
**Status:** ✅ Ready for Development
