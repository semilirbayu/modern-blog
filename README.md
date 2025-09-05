# ModernBlog

A modern Laravel + Vue.js blogging platform with API-first architecture, optimized for Railway production deployment.

## Project Overview

ModernBlog is a comprehensive blogging platform built with Laravel 11 as the backend API and Vue.js 3 for the frontend applications. The project follows an API-first design pattern with complete separation between backend and frontend.

## Technology Stack

- **Backend**: Laravel 11 with PHP 8.2+
- **Database**: MySQL 8.0+
- **Cache**: Redis for sessions and application caching
- **Authentication**: Laravel Sanctum for API tokens
- **Development Environment**: Laravel Sail (Docker)
- **Production Environment**: Railway Platform
- **Frontend**: Vue.js 3 with Composition API
- **Styling**: Tailwind CSS
- **Build Tools**: Vite
- **Testing**: PHPUnit with comprehensive test suite

## Architecture

This project implements a REST API backend that serves:
1. **Admin Dashboard** - Vue.js SPA for content management
2. **Public Blog** - Multi-page Vue.js application for content consumption

The application is fully optimized for production deployment with:
- Laravel optimization commands
- Redis caching and session management
- Health check monitoring
- Comprehensive testing suite
- Railway deployment automation

### Database Schema

- **users** - User accounts with authentication
- **categories** - Blog post categories with slugs
- **posts** - Blog posts with status, relationships, and publishing dates
- **personal_access_tokens** - Laravel Sanctum API tokens
- **cache** - Database cache table for fallback caching
- **jobs** - Queue job processing
- **failed_jobs** - Failed job tracking

## Installation & Setup

### Prerequisites

- Docker and Docker Compose
- PHP 8.2+ (for local development without Docker)
- Composer

### Using Laravel Sail (Recommended)

1. **Install Dependencies**
   ```bash
   composer install
   ```

2. **Set up Environment**
   ```bash
   cp .env.example .env
   ```

3. **Start Docker Services**
   ```bash
   ./vendor/bin/sail up -d
   ```

4. **Generate Application Key**
   ```bash
   ./vendor/bin/sail artisan key:generate
   ```

5. **Run Database Migrations**
   ```bash
   ./vendor/bin/sail artisan migrate
   ```

6. **Optional: Seed Database**
   ```bash
   ./vendor/bin/sail artisan db:seed
   ```

### Local Development (Without Docker)

1. **Install Dependencies**
   ```bash
   composer install
   ```

2. **Environment Setup**
   ```bash
   cp .env.example .env
   # Configure your database settings in .env
   ```

3. **Generate Application Key**
   ```bash
   php artisan key:generate
   ```

4. **Run Migrations**
   ```bash
   php artisan migrate
   ```

5. **Start Development Server**
   ```bash
   php artisan serve
   ```

## Railway Production Deployment

### Quick Deployment

1. **Connect Railway to GitHub**
   - Create a new project in Railway
   - Connect your GitHub repository
   - Railway will automatically detect the Nixpacks configuration

2. **Add Environment Variables**
   Configure these variables in Railway dashboard:
   ```
   APP_ENV=production
   APP_DEBUG=false
   APP_KEY=(generate using: php artisan key:generate --show)
   APP_URL=${RAILWAY_STATIC_URL}
   
   # Database (automatically provided by Railway MySQL service)
   DB_HOST=${MYSQLHOST}
   DB_PORT=${MYSQLPORT}  
   DB_DATABASE=${MYSQLDATABASE}
   DB_USERNAME=${MYSQLUSER}
   DB_PASSWORD=${MYSQLPASSWORD}
   
   # Redis (automatically provided by Railway Redis service)
   REDIS_HOST=${REDISHOST}
   REDIS_PASSWORD=${REDISPASSWORD}
   REDIS_PORT=${REDISPORT}
   
   # Production optimizations
   CACHE_DRIVER=redis
   SESSION_DRIVER=redis
   QUEUE_CONNECTION=database
   LOG_LEVEL=error
   SESSION_SECURE_COOKIES=true
   TRUSTED_PROXIES=*
   ```

3. **Add Services**
   - Add MySQL service to your Railway project
   - Add Redis service for caching and sessions

4. **Deploy**
   - Push to your main branch
   - Railway will automatically build and deploy
   - The application will be optimized during deployment using the `optimize:for-production` command

### Manual Deployment with Scripts

Use the provided deployment script:
```bash
./scripts/deploy.sh --verbose
```

### Health Monitoring

The application includes comprehensive health checks:
- Basic health: `GET /health`
- Detailed health: `GET /health/detailed`
- Manual health check: `./scripts/health-check.sh --url YOUR_RAILWAY_URL`

### Production Testing

Run comprehensive production tests:
```bash
./scripts/test-production.sh --base-url YOUR_RAILWAY_URL
```

## API Endpoints

### Health & Monitoring
- `GET /health` - Basic application health check
- `GET /health/detailed` - Detailed system health with component status

### Authentication
- `POST /api/login` - User login
- `POST /api/logout` - User logout (requires auth)
- `GET /api/user` - Get authenticated user (requires auth)

### Posts Management (Protected)
- `GET /api/posts` - List all posts (with pagination, filtering, search)
- `POST /api/posts` - Create new post
- `GET /api/posts/{id}` - Get specific post
- `PUT /api/posts/{id}` - Update post
- `DELETE /api/posts/{id}` - Delete post

### Categories Management (Protected)
- `GET /api/categories` - List all categories
- `POST /api/categories` - Create new category
- `PUT /api/categories/{id}` - Update category
- `DELETE /api/categories/{id}` - Delete category

### Public Endpoints
- `GET /api/public/posts` - List published posts (with pagination)
- `GET /api/public/posts/{slug}` - Get published post by slug
- `GET /api/public/categories` - List all categories
- `GET /api/public/categories/{slug}` - Get category with published posts

## Development Commands

### Using Sail (Local Development)
```bash
# Start services
./vendor/bin/sail up -d

# Stop services
./vendor/bin/sail down

# Run Artisan commands
./vendor/bin/sail artisan migrate
./vendor/bin/sail artisan make:controller PostController

# Run tests
./vendor/bin/sail test

# Access MySQL
./vendor/bin/sail mysql

# View logs
./vendor/bin/sail logs
```

### Production Commands
```bash
# Deploy to production
./scripts/deploy.sh

# Run production tests
./scripts/test-production.sh

# Health check
./scripts/health-check.sh

# Laravel optimization
php artisan optimize:for-production
```

### Standard Laravel Commands
```bash
# Run migrations
php artisan migrate

# Create controllers, models, etc.
php artisan make:controller PostController
php artisan make:model Post

# Run tests
php artisan test
composer test

# Linting and code quality
composer lint
composer analyze

# Clear caches
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Frontend Commands
```bash
# Development server
npm run dev

# Production build
npm run build
npm run build:production

# Code formatting
npm run lint
npm run format

# Clean build files
npm run clean
```

## Project Structure

```
/
├── app/
│   ├── Console/Commands/     # Custom Artisan commands
│   ├── Http/Controllers/     # API controllers
│   └── Models/              # Eloquent models
├── bootstrap/               # Laravel bootstrap files
├── config/                  # Configuration files
├── database/
│   ├── migrations/          # Database migrations (including cache/jobs tables)
│   ├── seeders/             # Database seeders
│   └── factories/           # Model factories for testing
├── docs/                    # Project documentation
├── public/                  # Public web directory
├── resources/
│   ├── css/                 # Stylesheets
│   ├── js/                  # Vue.js frontend applications
│   └── views/               # Blade templates
├── routes/
│   ├── api.php              # API routes
│   └── web.php              # Web routes (includes health checks)
├── scripts/                 # Deployment and maintenance scripts
│   ├── deploy.sh            # Production deployment script
│   ├── test-production.sh   # Production testing script
│   └── health-check.sh      # Health monitoring script
├── storage/                 # Application storage
├── tests/
│   ├── Feature/             # Feature/integration tests
│   └── Unit/                # Unit tests
├── vendor/                  # Composer dependencies
├── .env.example             # Environment template (includes production vars)
├── .env.production          # Production environment template
├── railway.json             # Railway deployment configuration
├── nixpacks.toml            # Nixpacks build configuration
├── Procfile                 # Process file for Railway
├── phpunit.xml              # PHPUnit configuration
├── vite.config.js           # Vite build configuration (optimized)
├── package.json             # Node.js dependencies and scripts
├── composer.json            # PHP dependencies and scripts
└── README.md                # This file
```

## Production Features

### ✅ Deployment Automation
- Railway platform integration with auto-deployment
- Nixpacks configuration for consistent builds
- Environment variable management
- Automated optimization during deployment

### ✅ Performance Optimization  
- Redis caching for database queries and sessions
- Laravel route, config, and view caching
- Frontend asset optimization with Vite
- Database query optimization and eager loading

### ✅ Monitoring & Health Checks
- Comprehensive health check endpoints
- Database connectivity monitoring  
- Cache system validation
- Storage accessibility checks
- Automated alerting capabilities

### ✅ Testing & Quality Assurance
- Complete PHPUnit test suite (Unit + Feature tests)
- API endpoint testing with authentication
- Database transaction testing
- Health check validation
- Production environment testing scripts

### ✅ Security & Best Practices
- Laravel Sanctum for API authentication
- HTTPS enforcement in production
- Secure cookie configuration
- Rate limiting on authentication endpoints
- Proper error handling and logging

## Troubleshooting

### Common Railway Deployment Issues

**Build Failures:**
- Ensure PHP version compatibility (8.1+)
- Check Node.js version (18+)
- Verify environment variables are set correctly

**Database Connection Issues:**
- Verify MySQL service is added and connected
- Check database environment variables
- Ensure migrations ran successfully

**Redis Connection Issues:**
- Verify Redis service is added and connected  
- Check Redis environment variables
- Try fallback to database cache if needed

**Application Not Starting:**
- Check application logs in Railway dashboard
- Verify APP_KEY is generated and set
- Ensure storage directories have proper permissions

### Performance Monitoring

Monitor your application using:
- Health check endpoints (`/health`, `/health/detailed`)
- Railway application metrics
- Laravel log files in Railway dashboard
- Custom monitoring scripts (`./scripts/health-check.sh`)

## Documentation

For detailed architecture and requirements, see:
- `docs/modermblog-architecture.md` - Complete system architecture
- `docs/modermblog-prd.md` - Product requirements document  
- `docs/blogging-project-brief.md` - Original project brief

## Contributing

This project follows Laravel and Vue.js best practices as outlined in the architecture documentation. When contributing:

1. **Code Quality**
   - Follow PSR-12 coding standards
   - Use Laravel Pint for code formatting (`composer lint`)
   - Write comprehensive tests for new features
   - Maintain test coverage above 80%

2. **API Development**
   - Follow established API patterns
   - Maintain proper RESTful conventions
   - Use Laravel Sanctum for authentication
   - Implement proper validation and error handling

3. **Database Changes**
   - Create migrations for all schema changes
   - Update model factories and seeders
   - Test migrations in both directions (up/down)
   - Document relationship changes

4. **Frontend Integration** 
   - Maintain API-first architecture
   - Ensure proper CORS configuration
   - Test with both admin and public frontends
   - Optimize asset builds for production

5. **Production Deployment**
   - Test changes with production scripts
   - Verify health checks pass
   - Update environment configuration if needed
   - Monitor application performance after deployment

## License

MIT License

---

**Ready for Production Deployment** 🚀

This ModernBlog application is fully configured and optimized for Railway deployment with comprehensive monitoring, testing, and automation. The application includes all necessary production features for a robust blogging platform.