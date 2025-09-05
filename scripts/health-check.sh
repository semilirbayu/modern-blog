#!/bin/bash

# ModernBlog Health Check Script
# Monitoring script for production environment health validation

set -e

# Configuration
BASE_URL=${BASE_URL:-http://localhost:8080}
TIMEOUT=${TIMEOUT:-30}
VERBOSE=${VERBOSE:-false}
ALERT_EMAIL=${ALERT_EMAIL:-}
SLACK_WEBHOOK=${SLACK_WEBHOOK:-}

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

# Health status
HEALTH_STATUS="healthy"
HEALTH_ISSUES=()

# Logging functions
log_info() {
    if [ "$VERBOSE" = "true" ]; then
        echo -e "${BLUE}[INFO]${NC} $1"
    fi
}

log_success() {
    echo -e "${GREEN}[OK]${NC} $1"
}

log_warning() {
    echo -e "${YELLOW}[WARN]${NC} $1"
    HEALTH_ISSUES+=("WARNING: $1")
}

log_error() {
    echo -e "${RED}[ERROR]${NC} $1"
    HEALTH_STATUS="unhealthy"
    HEALTH_ISSUES+=("ERROR: $1")
}

# Check if curl is available
check_dependencies() {
    if ! command -v curl > /dev/null 2>&1; then
        log_error "curl is required but not installed"
        exit 1
    fi
}

# Check basic application health
check_application_health() {
    log_info "Checking application health..."
    
    local response
    local http_code
    
    # Check basic health endpoint
    response=$(curl -s --connect-timeout $TIMEOUT -w "%{http_code}" "$BASE_URL/health" 2>/dev/null || echo "000")
    http_code="${response: -3}"
    
    if [ "$http_code" = "200" ]; then
        log_success "Application is responding"
        
        # Parse response body (remove the last 3 characters which are the HTTP code)
        local body="${response%???}"
        
        # Check if response contains expected status
        if echo "$body" | grep -q '"status":"ok"'; then
            log_success "Application health status is OK"
        else
            log_warning "Application health status is not OK"
        fi
    else
        log_error "Application is not responding (HTTP $http_code)"
    fi
}

# Check detailed system health
check_detailed_health() {
    log_info "Checking detailed system health..."
    
    local response
    local http_code
    
    # Check detailed health endpoint
    response=$(curl -s --connect-timeout $TIMEOUT -w "%{http_code}" "$BASE_URL/health/detailed" 2>/dev/null || echo "000")
    http_code="${response: -3}"
    
    if [ "$http_code" = "200" ]; then
        local body="${response%???}"
        
        # Check overall status
        if echo "$body" | grep -q '"status":"healthy"'; then
            log_success "All system components are healthy"
        else
            log_warning "Some system components may have issues"
        fi
        
        # Check individual components if jq is available
        if command -v jq > /dev/null 2>&1; then
            check_component_status "$body" "database" "Database"
            check_component_status "$body" "cache" "Cache system"
            check_component_status "$body" "storage" "File storage"
            check_component_status "$body" "app" "Application"
        fi
    elif [ "$http_code" = "503" ]; then
        log_error "System is unhealthy (HTTP 503)"
        
        # Try to parse error details
        local body="${response%???}"
        if command -v jq > /dev/null 2>&1; then
            echo "$body" | jq -r '.checks | to_entries[] | select(.value.status != "ok") | "\(.key): \(.value.message)"' 2>/dev/null | while read -r issue; do
                log_error "$issue"
            done
        fi
    else
        log_error "Detailed health check failed (HTTP $http_code)"
    fi
}

# Check individual component status
check_component_status() {
    local response_body=$1
    local component=$2
    local component_name=$3
    
    local status=$(echo "$response_body" | jq -r ".checks.$component.status" 2>/dev/null || echo "unknown")
    
    case "$status" in
        "ok")
            log_success "$component_name is healthy"
            ;;
        "error")
            local message=$(echo "$response_body" | jq -r ".checks.$component.message" 2>/dev/null || echo "Unknown error")
            log_error "$component_name: $message"
            ;;
        "unknown")
            log_warning "$component_name status unknown"
            ;;
        *)
            log_warning "$component_name status: $status"
            ;;
    esac
}

# Check database connectivity
check_database() {
    log_info "Checking database connectivity..."
    
    # This would typically be done through the API, but we can also check directly
    local response
    local http_code
    
    # Try to access an API endpoint that requires database
    response=$(curl -s --connect-timeout $TIMEOUT -w "%{http_code}" "$BASE_URL/api/public/posts?limit=1" 2>/dev/null || echo "000")
    http_code="${response: -3}"
    
    if [ "$http_code" = "200" ]; then
        log_success "Database is accessible through API"
    else
        log_error "Database may not be accessible (API returned HTTP $http_code)"
    fi
}

# Check API endpoints
check_api_endpoints() {
    log_info "Checking critical API endpoints..."
    
    # List of endpoints to check
    local endpoints=(
        "/api/public/posts:200"
        "/api/public/categories:200"
        "/api/login:422"  # Should fail with validation error, not server error
        "/api/user:401"   # Should fail with authentication error
    )
    
    for endpoint_spec in "${endpoints[@]}"; do
        local endpoint="${endpoint_spec%:*}"
        local expected_code="${endpoint_spec#*:}"
        
        local response
        local http_code
        
        response=$(curl -s --connect-timeout $TIMEOUT -w "%{http_code}" "$BASE_URL$endpoint" 2>/dev/null || echo "000")
        http_code="${response: -3}"
        
        if [ "$http_code" = "$expected_code" ]; then
            log_success "API endpoint $endpoint responds correctly"
        else
            log_error "API endpoint $endpoint failed (expected $expected_code, got $http_code)"
        fi
    done
}

# Check frontend accessibility
check_frontend() {
    log_info "Checking frontend accessibility..."
    
    local endpoints=(
        "/"
        "/admin"
    )
    
    for endpoint in "${endpoints[@]}"; do
        local response
        local http_code
        
        response=$(curl -s --connect-timeout $TIMEOUT -w "%{http_code}" "$BASE_URL$endpoint" 2>/dev/null || echo "000")
        http_code="${response: -3}"
        
        if [ "$http_code" = "200" ]; then
            log_success "Frontend endpoint $endpoint is accessible"
        else
            log_error "Frontend endpoint $endpoint failed (HTTP $http_code)"
        fi
    done
}

# Check SSL certificate (if HTTPS)
check_ssl_certificate() {
    if [[ "$BASE_URL" =~ ^https:// ]]; then
        log_info "Checking SSL certificate..."
        
        local domain=$(echo "$BASE_URL" | sed 's|https\?://||' | sed 's|/.*||')
        local cert_info
        
        cert_info=$(echo | openssl s_client -servername "$domain" -connect "$domain:443" 2>/dev/null | openssl x509 -noout -dates 2>/dev/null || echo "")
        
        if [ -n "$cert_info" ]; then
            local not_after=$(echo "$cert_info" | grep "notAfter" | sed 's/notAfter=//')
            local expiry_timestamp=$(date -d "$not_after" +%s 2>/dev/null || echo "0")
            local current_timestamp=$(date +%s)
            local days_until_expiry=$(( (expiry_timestamp - current_timestamp) / 86400 ))
            
            if [ $days_until_expiry -gt 30 ]; then
                log_success "SSL certificate is valid ($days_until_expiry days until expiry)"
            elif [ $days_until_expiry -gt 0 ]; then
                log_warning "SSL certificate expires in $days_until_expiry days"
            else
                log_error "SSL certificate has expired"
            fi
        else
            log_error "Could not retrieve SSL certificate information"
        fi
    else
        log_info "Skipping SSL check (not HTTPS)"
    fi
}

# Check disk space (if running locally)
check_disk_space() {
    if [ -f "storage/logs/laravel.log" ]; then
        log_info "Checking disk space..."
        
        local disk_usage
        disk_usage=$(df -h . | awk 'NR==2 {print $5}' | sed 's/%//')
        
        if [ "$disk_usage" -lt 80 ]; then
            log_success "Disk usage is acceptable ($disk_usage%)"
        elif [ "$disk_usage" -lt 90 ]; then
            log_warning "Disk usage is high ($disk_usage%)"
        else
            log_error "Disk usage is critical ($disk_usage%)"
        fi
    fi
}

# Send alert via email
send_email_alert() {
    if [ -n "$ALERT_EMAIL" ] && command -v mail > /dev/null 2>&1; then
        local subject="ModernBlog Health Check Alert"
        local body="Health check failed at $(date)\n\nIssues found:\n"
        
        for issue in "${HEALTH_ISSUES[@]}"; do
            body="$body\n- $issue"
        done
        
        echo -e "$body" | mail -s "$subject" "$ALERT_EMAIL"
        log_info "Alert email sent to $ALERT_EMAIL"
    fi
}

# Send alert via Slack
send_slack_alert() {
    if [ -n "$SLACK_WEBHOOK" ]; then
        local payload='{"text":"🚨 ModernBlog Health Check Alert","attachments":[{"color":"danger","fields":[{"title":"Status","value":"'$HEALTH_STATUS'","short":true},{"title":"Time","value":"'$(date)'","short":true}],"text":"Issues found:\n'
        
        for issue in "${HEALTH_ISSUES[@]}"; do
            payload="$payload• $issue\n"
        done
        
        payload="$payload\"}]}"
        
        if curl -s -X POST -H 'Content-type: application/json' --data "$payload" "$SLACK_WEBHOOK" > /dev/null; then
            log_info "Alert sent to Slack"
        else
            log_error "Failed to send Slack alert"
        fi
    fi
}

# Display health summary
show_summary() {
    echo ""
    echo "========================================"
    echo "        HEALTH CHECK SUMMARY"
    echo "========================================"
    echo "URL: $BASE_URL"
    echo "Time: $(date)"
    echo -e "Status: $([ "$HEALTH_STATUS" = "healthy" ] && echo -e "${GREEN}HEALTHY${NC}" || echo -e "${RED}UNHEALTHY${NC}")"
    echo ""
    
    if [ ${#HEALTH_ISSUES[@]} -gt 0 ]; then
        echo "Issues found:"
        for issue in "${HEALTH_ISSUES[@]}"; do
            echo "  - $issue"
        done
        echo ""
    fi
    
    echo "========================================"
    
    # Send alerts if unhealthy
    if [ "$HEALTH_STATUS" != "healthy" ]; then
        send_email_alert
        send_slack_alert
        return 1
    fi
    
    return 0
}

# Main health check function
main() {
    echo "🏥 Starting ModernBlog health check..."
    echo "Target: $BASE_URL"
    echo "Timeout: ${TIMEOUT}s"
    echo ""
    
    check_dependencies
    check_application_health
    check_detailed_health
    check_database
    check_api_endpoints
    check_frontend
    check_ssl_certificate
    check_disk_space
    
    show_summary
}

# Handle script arguments
while [[ $# -gt 0 ]]; do
    case $1 in
        --url)
            BASE_URL="$2"
            shift 2
            ;;
        --timeout)
            TIMEOUT="$2"
            shift 2
            ;;
        --alert-email)
            ALERT_EMAIL="$2"
            shift 2
            ;;
        --slack-webhook)
            SLACK_WEBHOOK="$2"
            shift 2
            ;;
        --verbose|-v)
            VERBOSE=true
            shift
            ;;
        --help|-h)
            echo "ModernBlog Health Check Script"
            echo ""
            echo "Usage: $0 [OPTIONS]"
            echo ""
            echo "Options:"
            echo "  --url URL            Base URL to check (default: http://localhost:8080)"
            echo "  --timeout SECONDS    Request timeout (default: 30)"
            echo "  --alert-email EMAIL  Email address for alerts"
            echo "  --slack-webhook URL  Slack webhook URL for alerts"
            echo "  --verbose, -v        Enable verbose output"
            echo "  --help, -h           Show this help message"
            echo ""
            echo "Environment Variables:"
            echo "  BASE_URL         Base URL to check"
            echo "  TIMEOUT          Request timeout in seconds"
            echo "  ALERT_EMAIL      Email address for alerts"
            echo "  SLACK_WEBHOOK    Slack webhook URL for alerts"
            echo "  VERBOSE          Enable verbose output"
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