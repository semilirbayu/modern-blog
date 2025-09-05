# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

ModernBlog is a Laravel + Vue.js blogging platform with API-first architecture. This is currently a greenfield project with comprehensive planning documentation but no implementation yet.

## Architecture

**Technology Stack:**
- **Backend**: Laravel 10+ with PHP 8.1+ (REST API)
- **Frontend**: Vue.js 3 with Composition API
- **Database**: MySQL 8.0+
- **Authentication**: Laravel Sanctum for API tokens
- **Build Tools**: Vite for frontend assets
- **Styling**: Tailwind CSS

**Project Structure:**
```
/
├── docs/                          # Project documentation
│   ├── blogging-project-brief.md  # Original project requirements
│   ├── modermblog-architecture.md # Complete architecture document
│   └── modermblog-prd.md          # Product requirements document
├── laravel-backend/               # Laravel API (to be created)
└── vue-frontend/                  # Vue.js applications (to be created)
```

## Development Commands

**Note**: This project is not yet implemented. Based on the architecture documentation, these will be the expected commands:

**Backend (Laravel):**
```bash
# Initial setup
composer install
php artisan sail:install
./vendor/bin/sail up -d
php artisan migrate --seed

# Development
php artisan serve
php artisan queue:work
php artisan test

# Production optimization
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

**Frontend (Vue.js):**
```bash
# Setup
npm install

# Development
npm run dev

# Production build
npm run build

# Testing
npm run test
```

## Key Implementation Details

**API Structure:**
- Authentication: `POST /api/login`, `POST /api/logout`, `GET /api/user`
- Posts: `GET|POST /api/posts`, `GET|PUT|DELETE /api/posts/{id}`
- Categories: `GET|POST /api/categories`, `PUT|DELETE /api/categories/{id}`
- Public endpoints: `GET /api/public/posts`, `GET /api/public/posts/{slug}`

**Database Schema:**
- users (id, name, email, password, timestamps)
- categories (id, name, slug, description, timestamps)  
- posts (id, title, slug, content, excerpt, status, category_id, user_id, published_at, timestamps)

**Frontend Applications:**
1. Admin Dashboard - Vue.js SPA for content management
2. Public Blog - Multi-page Vue.js application for content consumption

## Implementation Priority

Based on the PRD, implement features in this order:
1. Laravel backend setup with authentication
2. Posts and Categories API endpoints  
3. Vue.js admin dashboard for content management
4. Public blog frontend for content display
5. Advanced features (media upload, rich text editor, etc.)

## Development Notes

- API-first design with complete backend/frontend separation
- Use Laravel Sanctum for stateless API authentication
- Vue.js 3 Composition API with Pinia for state management
- Responsive design for both admin and public interfaces
- Follow Laravel and Vue.js best practices per architecture document