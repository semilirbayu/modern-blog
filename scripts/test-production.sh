#!/bin/bash

# ModernBlog Production Testing Script
# Comprehensive testing script for production environment validation

set -e

# Configuration
BASE_URL=${BASE_URL:-http://localhost:8080}
TEST_EMAIL=${TEST_EMAIL:-test@example.com}
TEST_PASSWORD=${TEST_PASSWORD:-password123}
VERBOSE=${VERBOSE:-false}

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

# Counters
TESTS_PASSED=0
TESTS_FAILED=0
TOTAL_TESTS=0

# Logging functions
log_info() {
    if [ "$VERBOSE" = "true" ]; then
        echo -e "${BLUE}[INFO]${NC} $1"
    fi
}

log_success() {
    echo -e "${GREEN}[PASS]${NC} $1"
    ((TESTS_PASSED++))
    ((TOTAL_TESTS++))
}

log_failure() {
    echo -e "${RED}[FAIL]${NC} $1"
    ((TESTS_FAILED++))
    ((TOTAL_TESTS++))
}

log_warning() {
    echo -e "${YELLOW}[WARN]${NC} $1"
}

log_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Check if required commands are available
check_dependencies() {
    if ! command -v curl > /dev/null 2>&1; then
        log_error "curl is required but not installed"
        exit 1
    fi
    
    if ! command -v jq > /dev/null 2>&1; then
        log_warning "jq is not installed, JSON parsing will be limited"
    fi
    
    if ! command -v php > /dev/null 2>&1; then
        log_error "PHP is required but not installed"
        exit 1
    fi
}

# Start test server if not already running
start_test_server() {
    if ! curl -s "$BASE_URL/health" > /dev/null 2>&1; then
        log_info "Starting test server..."
        php artisan serve --port=8080 --quiet &
        SERVER_PID=$!
        
        # Wait for server to start
        sleep 3
        
        # Check if server started successfully
        if ! curl -s "$BASE_URL/health" > /dev/null 2>&1; then
            log_error "Failed to start test server"
            kill $SERVER_PID 2>/dev/null
            exit 1
        fi
        
        log_info "Test server started on port 8080"
    else
        log_info "Using existing server at $BASE_URL"
        SERVER_PID=""
    fi
}

# Stop test server if we started it
stop_test_server() {
    if [ -n "$SERVER_PID" ]; then
        kill $SERVER_PID 2>/dev/null
        log_info "Test server stopped"
    fi
}

# Test HTTP response
test_http_response() {
    local url=$1
    local expected_status=$2
    local test_name=$3
    local method=${4:-GET}
    local data=${5:-}
    
    local curl_opts="-s -w %{http_code} -o /dev/null"
    
    if [ "$method" = "POST" ] && [ -n "$data" ]; then
        curl_opts="$curl_opts -X POST -H Content-Type:application/json -d $data"
    fi
    
    local status_code=$(curl $curl_opts "$url")
    
    if [ "$status_code" = "$expected_status" ]; then
        log_success "$test_name"
    else
        log_failure "$test_name (expected: $expected_status, got: $status_code)"
    fi
}

# Test JSON response content
test_json_response() {
    local url=$1
    local expected_field=$2
    local expected_value=$3
    local test_name=$4
    local method=${5:-GET}
    local data=${6:-}
    local headers=${7:-}
    
    local curl_opts="-s"
    
    if [ "$method" = "POST" ] && [ -n "$data" ]; then
        curl_opts="$curl_opts -X POST -H Content-Type:application/json -d $data"
    fi
    
    if [ -n "$headers" ]; then
        curl_opts="$curl_opts -H $headers"
    fi
    
    local response=$(curl $curl_opts "$url")
    
    if command -v jq > /dev/null 2>&1; then
        local actual_value=$(echo "$response" | jq -r ".$expected_field")
        
        if [ "$actual_value" = "$expected_value" ]; then
            log_success "$test_name"
        else
            log_failure "$test_name (expected: $expected_value, got: $actual_value)"
        fi
    else
        # Basic grep check if jq is not available
        if echo "$response" | grep -q "$expected_value"; then
            log_success "$test_name"
        else
            log_failure "$test_name (JSON response check failed)"
        fi
    fi
}

# Test basic health checks
test_health_checks() {
    echo "Testing health check endpoints..."
    
    test_http_response "$BASE_URL/health" "200" "Basic health check"
    test_json_response "$BASE_URL/health" "status" "ok" "Health check status"
    test_http_response "$BASE_URL/health/detailed" "200" "Detailed health check"
    test_json_response "$BASE_URL/health/detailed" "status" "healthy" "Detailed health status"
}

# Test API authentication endpoints
test_authentication() {
    echo "Testing authentication endpoints..."
    
    # Test login endpoint exists
    test_http_response "$BASE_URL/api/login" "422" "Login endpoint (missing credentials)"
    
    # Test protected endpoint without auth
    test_http_response "$BASE_URL/api/user" "401" "Protected endpoint without auth"
    
    # Test logout endpoint without auth
    test_http_response "$BASE_URL/api/logout" "401" "Logout endpoint without auth"
}

# Test public API endpoints
test_public_api() {
    echo "Testing public API endpoints..."
    
    test_http_response "$BASE_URL/api/public/posts" "200" "Public posts endpoint"
    test_http_response "$BASE_URL/api/public/categories" "200" "Public categories endpoint"
    
    # Test non-existent post
    test_http_response "$BASE_URL/api/public/posts/non-existent-post" "404" "Non-existent post 404"
    
    # Test non-existent category
    test_http_response "$BASE_URL/api/public/categories/non-existent-category" "404" "Non-existent category 404"
}

# Test Laravel Artisan commands
test_artisan_commands() {
    echo "Testing Laravel Artisan commands..."
    
    # Test basic artisan commands
    if php artisan --version > /dev/null 2>&1; then
        log_success "Artisan version command"
    else
        log_failure "Artisan version command"
    fi
    
    if php artisan route:list > /dev/null 2>&1; then
        log_success "Artisan route:list command"
    else
        log_failure "Artisan route:list command"
    fi
    
    if php artisan config:show app > /dev/null 2>&1; then
        log_success "Artisan config:show command"
    else
        log_failure "Artisan config:show command"
    fi
}

# Test database connectivity
test_database() {
    echo "Testing database connectivity..."
    
    # Test database connection through artisan
    if php artisan migrate:status > /dev/null 2>&1; then
        log_success "Database connection and migrations"
    else
        log_failure "Database connection or migrations"
    fi
    
    # Test if models can be loaded
    if php artisan tinker --execute="App\Models\User::count()" > /dev/null 2>&1; then
        log_success "Model loading and database query"
    else
        log_failure "Model loading or database query"
    fi
}

# Test caching system
test_caching() {
    echo "Testing caching system..."
    
    # Test cache through artisan
    local test_key="test_key_$(date +%s)"
    local test_value="test_value"
    
    if php artisan tinker --execute="Cache::put('$test_key', '$test_value', 60); echo Cache::get('$test_key');" 2>/dev/null | grep -q "$test_value"; then
        log_success "Cache write and read"
        
        # Clean up test cache
        php artisan tinker --execute="Cache::forget('$test_key');" > /dev/null 2>&1
    else
        log_failure "Cache write and read"
    fi
}

# Test file storage
test_storage() {
    echo "Testing file storage..."
    
    # Check if storage directories exist and are writable
    if [ -w "storage/logs" ]; then
        log_success "Storage logs directory writable"
    else
        log_failure "Storage logs directory not writable"
    fi
    
    if [ -w "storage/framework/cache" ]; then
        log_success "Storage cache directory writable"
    else
        log_failure "Storage cache directory not writable"
    fi
    
    if [ -w "storage/app" ]; then
        log_success "Storage app directory writable"
    else
        log_failure "Storage app directory not writable"
    fi
    
    # Test storage link
    if [ -L "public/storage" ]; then
        log_success "Storage symlink exists"
    else
        log_failure "Storage symlink missing"
    fi
}

# Test frontend assets
test_frontend_assets() {
    echo "Testing frontend assets..."
    
    # Check if build manifest exists
    if [ -f "public/build/manifest.json" ]; then
        log_success "Frontend build manifest exists"
    else
        log_failure "Frontend build manifest missing"
    fi
    
    # Test if main pages load
    test_http_response "$BASE_URL/" "200" "Frontend home page"
    test_http_response "$BASE_URL/admin" "200" "Admin frontend page"
}

# Test optimization files
test_optimization() {
    echo "Testing Laravel optimization..."
    
    # Check if optimization files exist
    if [ -f "bootstrap/cache/config.php" ]; then
        log_success "Config cache file exists"
    else
        log_failure "Config cache file missing"
    fi
    
    if [ -f "bootstrap/cache/routes-v7.php" ]; then
        log_success "Route cache file exists"
    else
        log_failure "Route cache file missing"
    fi
    
    # Check if views are cached
    if [ -n "$(ls storage/framework/views/*.php 2>/dev/null)" ]; then
        log_success "View cache files exist"
    else
        log_failure "View cache files missing"
    fi
}

# Test security headers and configurations
test_security() {
    echo "Testing security configurations..."
    
    # Test HTTPS redirect in production (if applicable)
    if [ "$APP_ENV" = "production" ]; then
        # This would need to be tested on the actual production server
        log_info "Security tests require production environment"
    fi
    
    # Test that debug mode is disabled in production
    local debug_mode=$(php artisan tinker --execute="echo config('app.debug') ? 'true' : 'false';" 2>/dev/null | tail -n 1)
    
    if [ "$APP_ENV" = "production" ] && [ "$debug_mode" = "false" ]; then
        log_success "Debug mode disabled in production"
    elif [ "$APP_ENV" != "production" ]; then
        log_info "Debug mode check skipped (not production)"
    else
        log_failure "Debug mode should be disabled in production"
    fi
}

# Run PHPUnit tests if available
test_phpunit() {
    echo "Running PHPUnit tests..."
    
    if [ -f "vendor/bin/phpunit" ]; then
        if vendor/bin/phpunit --testdox 2>/dev/null; then
            log_success "PHPUnit test suite passed"
        else
            log_failure "PHPUnit test suite failed"
        fi
    else
        log_warning "PHPUnit not found, skipping unit tests"
    fi
}

# Display test summary
show_summary() {
    echo ""
    echo "========================================"
    echo "          TEST SUMMARY"
    echo "========================================"
    echo "Total Tests: $TOTAL_TESTS"
    echo -e "Passed: ${GREEN}$TESTS_PASSED${NC}"
    echo -e "Failed: ${RED}$TESTS_FAILED${NC}"
    echo ""
    
    if [ $TESTS_FAILED -eq 0 ]; then
        echo -e "${GREEN}✅ All tests passed! Application is ready for production.${NC}"
        echo "========================================"
        return 0
    else
        echo -e "${RED}❌ Some tests failed. Please review the issues above.${NC}"
        echo "========================================"
        return 1
    fi
}

# Trap to ensure server is stopped on exit
trap stop_test_server EXIT

# Main testing function
main() {
    echo "🧪 Starting ModernBlog production tests..."
    echo "Base URL: $BASE_URL"
    echo "Environment: ${APP_ENV:-testing}"
    echo ""
    
    check_dependencies
    start_test_server
    
    test_health_checks
    test_authentication
    test_public_api
    test_artisan_commands
    test_database
    test_caching
    test_storage
    test_frontend_assets
    test_optimization
    test_security
    test_phpunit
    
    show_summary
}

# Handle script arguments
while [[ $# -gt 0 ]]; do
    case $1 in
        --base-url)
            BASE_URL="$2"
            shift 2
            ;;
        --verbose|-v)
            VERBOSE=true
            shift
            ;;
        --help|-h)
            echo "ModernBlog Production Testing Script"
            echo ""
            echo "Usage: $0 [OPTIONS]"
            echo ""
            echo "Options:"
            echo "  --base-url URL    Base URL to test (default: http://localhost:8080)"
            echo "  --verbose, -v     Enable verbose output"
            echo "  --help, -h        Show this help message"
            echo ""
            echo "Environment Variables:"
            echo "  BASE_URL          Base URL to test"
            echo "  VERBOSE           Enable verbose output"
            echo "  APP_ENV           Application environment"
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