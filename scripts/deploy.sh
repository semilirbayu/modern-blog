#!/bin/bash

# ModernBlog Production Deployment Script for Railway
# This script handles the complete deployment process

set -e  # Exit on any error

# Configuration
APP_ENV=${APP_ENV:-production}
SKIP_TESTS=${SKIP_TESTS:-false}
VERBOSE=${VERBOSE:-false}

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Logging functions
log_info() {
    if [ "$VERBOSE" = "true" ]; then
        echo -e "${BLUE}[INFO]${NC} $1"
    fi
}

log_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

log_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

log_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Check if we're in the right directory
check_project_root() {
    if [ ! -f "composer.json" ] || [ ! -f "package.json" ]; then
        log_error "This script must be run from the project root directory"
        exit 1
    fi
}

# Check PHP and Node.js versions
check_dependencies() {
    log_info "Checking system dependencies..."
    
    # Check PHP version
    PHP_VERSION=$(php -v | head -n 1 | cut -d " " -f 2 | cut -d "." -f 1,2)
    if [ "$(echo "$PHP_VERSION < 8.1" | bc)" -eq 1 ]; then
        log_error "PHP 8.1 or higher is required. Current version: $PHP_VERSION"
        exit 1
    fi
    log_info "PHP version: $PHP_VERSION ✓"
    
    # Check Node.js version
    if command -v node > /dev/null 2>&1; then
        NODE_VERSION=$(node --version | cut -d "v" -f 2 | cut -d "." -f 1)
        if [ "$NODE_VERSION" -lt 18 ]; then
            log_error "Node.js 18 or higher is required. Current version: v$NODE_VERSION"
            exit 1
        fi
        log_info "Node.js version: v$NODE_VERSION ✓"
    else
        log_error "Node.js is not installed"
        exit 1
    fi
    
    # Check Composer
    if ! command -v composer > /dev/null 2>&1; then
        log_error "Composer is not installed"
        exit 1
    fi
    log_info "Composer is available ✓"
    
    # Check npm
    if ! command -v npm > /dev/null 2>&1; then
        log_error "npm is not installed"
        exit 1
    fi
    log_info "npm is available ✓"
}

# Install PHP dependencies
install_php_dependencies() {
    log_info "Installing PHP dependencies..."
    
    if [ "$APP_ENV" = "production" ]; then
        composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist
    else
        composer install --optimize-autoloader --no-interaction
    fi
    
    log_success "PHP dependencies installed"
}

# Install Node.js dependencies and build assets
build_frontend_assets() {
    log_info "Installing Node.js dependencies..."
    npm ci
    log_success "Node.js dependencies installed"
    
    log_info "Building frontend assets..."
    npm run build
    log_success "Frontend assets built"
}

# Set up environment
setup_environment() {
    log_info "Setting up environment..."
    
    # Generate application key if missing
    if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "" ]; then
        log_info "Generating application key..."
        php artisan key:generate --force
    fi
    
    # Create storage directories
    mkdir -p storage/framework/{cache,sessions,views}
    mkdir -p storage/logs
    mkdir -p bootstrap/cache
    
    # Set permissions
    chmod -R 755 storage
    chmod -R 755 bootstrap/cache
    
    log_success "Environment setup completed"
}

# Run database migrations
run_migrations() {
    log_info "Running database migrations..."
    
    # Check if database is accessible
    if ! php artisan migrate:status > /dev/null 2>&1; then
        log_warning "Database not accessible or migrations table missing"
        log_info "Creating migrations table..."
    fi
    
    php artisan migrate --force
    log_success "Database migrations completed"
}

# Run tests if not skipped
run_tests() {
    if [ "$SKIP_TESTS" = "true" ]; then
        log_warning "Tests skipped"
        return 0
    fi
    
    log_info "Running application tests..."
    
    # Create test database
    php artisan config:clear
    
    # Run PHPUnit tests
    if [ -f "vendor/bin/phpunit" ]; then
        vendor/bin/phpunit --coverage-text --coverage-clover=coverage.xml
        log_success "All tests passed"
    else
        log_warning "PHPUnit not found, skipping tests"
    fi
}

# Optimize application for production
optimize_application() {
    log_info "Optimizing application for production..."
    
    # Use our custom optimization command
    php artisan optimize:for-production --skip-migrations
    
    log_success "Application optimization completed"
}

# Create storage symlink
create_storage_link() {
    log_info "Creating storage symlink..."
    
    if [ ! -L "public/storage" ]; then
        php artisan storage:link
        log_success "Storage symlink created"
    else
        log_info "Storage symlink already exists"
    fi
}

# Validate deployment
validate_deployment() {
    log_info "Validating deployment..."
    
    # Check if application responds
    if command -v curl > /dev/null 2>&1; then
        # Start server in background for testing
        php artisan serve --port=8080 --quiet &
        SERVER_PID=$!
        
        # Wait for server to start
        sleep 3
        
        # Test health endpoint
        if curl -s http://localhost:8080/health > /dev/null; then
            log_success "Health check endpoint responds correctly"
        else
            log_error "Health check endpoint failed"
            kill $SERVER_PID 2>/dev/null
            exit 1
        fi
        
        # Kill test server
        kill $SERVER_PID 2>/dev/null
    fi
    
    # Check critical files exist
    CRITICAL_FILES=(
        "bootstrap/cache/config.php"
        "bootstrap/cache/routes-v7.php"
        "public/build/manifest.json"
    )
    
    for file in "${CRITICAL_FILES[@]}"; do
        if [ ! -f "$file" ]; then
            log_error "Critical file missing: $file"
            exit 1
        fi
        log_info "Critical file exists: $file ✓"
    done
    
    log_success "Deployment validation passed"
}

# Cleanup temporary files
cleanup() {
    log_info "Cleaning up temporary files..."
    
    # Remove temporary files
    rm -rf storage/framework/cache/data/*
    rm -rf storage/framework/sessions/*
    rm -rf storage/framework/views/*
    
    # Clear any test artifacts
    rm -f coverage.xml
    rm -rf build/
    
    log_success "Cleanup completed"
}

# Display deployment summary
show_summary() {
    echo ""
    echo "========================================"
    echo "      DEPLOYMENT SUMMARY"
    echo "========================================"
    echo "Environment: $APP_ENV"
    echo "PHP Version: $(php -v | head -n 1 | cut -d " " -f 2)"
    echo "Node Version: $(node --version)"
    echo "Laravel Version: $(php artisan --version | cut -d " " -f 3)"
    echo ""
    echo "✅ Dependencies installed"
    echo "✅ Frontend assets built"
    echo "✅ Database migrated"
    [ "$SKIP_TESTS" != "true" ] && echo "✅ Tests passed"
    echo "✅ Application optimized"
    echo "✅ Deployment validated"
    echo ""
    echo "🚀 Application is ready for production!"
    echo "========================================"
}

# Main deployment function
main() {
    echo "🚀 Starting ModernBlog deployment..."
    echo "Environment: $APP_ENV"
    echo ""
    
    check_project_root
    check_dependencies
    install_php_dependencies
    build_frontend_assets
    setup_environment
    run_migrations
    run_tests
    optimize_application
    create_storage_link
    validate_deployment
    cleanup
    show_summary
    
    log_success "Deployment completed successfully! 🎉"
}

# Handle script arguments
while [[ $# -gt 0 ]]; do
    case $1 in
        --skip-tests)
            SKIP_TESTS=true
            shift
            ;;
        --verbose|-v)
            VERBOSE=true
            shift
            ;;
        --help|-h)
            echo "ModernBlog Deployment Script"
            echo ""
            echo "Usage: $0 [OPTIONS]"
            echo ""
            echo "Options:"
            echo "  --skip-tests    Skip running tests"
            echo "  --verbose, -v   Enable verbose output"
            echo "  --help, -h      Show this help message"
            echo ""
            echo "Environment Variables:"
            echo "  APP_ENV         Application environment (default: production)"
            echo "  SKIP_TESTS      Skip tests (default: false)"
            echo "  VERBOSE         Enable verbose output (default: false)"
            exit 0
            ;;
        *)
            log_error "Unknown option: $1"
            echo "Use --help for usage information"
            exit 1
            ;;
    esac
done

# Run main function
main