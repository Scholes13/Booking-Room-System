# Technology Stack

## Backend
- **PHP 8.2+** - Core language requirement
- **Laravel 11.31** - Primary framework
- **MySQL/Database** - Data persistence (configured via Laravel)

## Frontend
- **Blade Templates** - Server-side rendering
- **Tailwind CSS 3.4** - Utility-first CSS framework
- **Vite 6.0** - Build tool and dev server
- **JavaScript ES6+** - Client-side functionality
- **Flatpickr** - Date/time picker component
- **Tabulator Tables** - Advanced data tables

## Key Libraries
- **DomPDF** - PDF generation for reports
- **Maatwebsite Excel** - Excel import/export functionality
- **Laravel Tinker** - REPL for debugging
- **Doctrine DBAL** - Database abstraction layer
- **OpenSpout** - Spreadsheet reading/writing

## Development Tools
- **Laravel Pint** - Code style fixer
- **PHPUnit** - Testing framework
- **Laravel Sail** - Docker development environment
- **Laravel Pail** - Log viewer
- **Faker** - Test data generation

## Common Commands

### Development
```bash
# Start development environment (all services)
composer dev

# Individual services
php artisan serve          # Start Laravel server
npm run dev               # Start Vite dev server
php artisan queue:listen  # Start queue worker
php artisan pail          # View logs

# Database
php artisan migrate       # Run migrations
php artisan db:seed       # Seed database
```

### Build & Deploy
```bash
npm run build            # Build assets for production
composer install --no-dev --optimize-autoloader
php artisan config:cache # Cache configuration
php artisan route:cache  # Cache routes
php artisan view:cache   # Cache views
```

### Testing & Quality
```bash
php artisan test         # Run PHPUnit tests
./vendor/bin/pint        # Fix code style
php artisan tinker       # Interactive shell
```