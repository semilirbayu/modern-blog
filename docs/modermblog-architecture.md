# ModernBlog Platform - Full Stack Architecture Document

## Introduction

This document outlines the complete fullstack architecture for **ModernBlog Platform**, including backend systems, frontend implementation, and their integration. It serves as the single source of truth for AI-driven development, ensuring consistency across the entire technology stack.

This unified approach combines backend and frontend architecture concerns, streamlining the development process for the modern API-first blogging platform where these concerns are increasingly intertwined.

### Starter Template Assessment

**Project Type**: Greenfield development - building from scratch
**Architecture Pattern**: API-First with separate Laravel backend and Vue.js frontend applications
**No existing templates**: Custom implementation following Laravel and Vue.js best practices

---

## System Architecture Overview

### High-Level Architecture

```
┌─────────────────┐    HTTP/API    ┌──────────────────────┐
│                 │   Requests     │                      │
│   Vue.js        │◄──────────────►│   Laravel Backend    │
│   Frontend      │                │   (REST API)         │
│                 │                │                      │
├─────────────────┤                ├──────────────────────┤
│ • Admin Dashboard│                │ • Authentication     │
│ • Public Blog   │                │ • Posts API          │
│ • SPA + SSG     │                │ • Categories API     │
└─────────────────┘                │ • Media API          │
                                   │                      │
                                   │                      │
                                   ├──────────────────────┤
                                   │                      │
                                   │   MySQL Database     │
                                   │                      │
                                   └──────────────────────┘
```

### Architecture Principles

1. **API-First Design**: Complete separation between backend and frontend
2. **Single Responsibility**: Each layer has clear, focused responsibilities  
3. **Stateless Backend**: Laravel API maintains no session state
4. **Token-Based Authentication**: JWT tokens via Laravel Sanctum
5. **Progressive Enhancement**: Core functionality works, enhanced with JavaScript

---

## Technology Stack

| Category | Technology | Version | Purpose | Rationale |
|----------|------------|---------|---------|-----------|
| Backend Framework | Laravel | 10+ | REST API & Business Logic | Mature PHP framework with excellent API capabilities |
| Frontend Framework | Vue.js | 3.x | User Interface | Reactive framework with great developer experience |
| Database | MySQL | 8.0+ | Data Persistence | Reliable relational database for structured blog data |
| Authentication | Laravel Sanctum | Latest | API Token Management | Simple, secure API authentication for SPAs |
| HTTP Client | Axios | Latest | API Communication | Promise-based HTTP client for Vue.js |
| Build Tool | Vite | Latest | Asset Compilation | Fast development server and optimized builds |
| CSS Framework | Tailwind CSS | 3.x | Styling | Utility-first CSS for rapid UI development |
| PHP Package Manager | Composer | 2.x | Dependency Management | Standard PHP dependency manager |
| JS Package Manager | npm | 9.x | Frontend Dependencies | Standard Node.js package manager |
| Development Environment | Laravel Sail | Latest | Docker Development | Consistent development environment |

---

## Backend Architecture (Laravel)

### API Design Pattern

**RESTful API Architecture**
- Resource-based URLs (`/api/posts`, `/api/categories`)
- HTTP methods map to CRUD operations
- JSON request/response format
- Consistent error handling and status codes

### Core API Endpoints

#### Authentication Endpoints
```
POST   /api/login           # Admin login
POST   /api/logout          # Admin logout  
GET    /api/user            # Get authenticated user
```

#### Posts Management
```
GET    /api/posts           # List all posts (with filters)
POST   /api/posts           # Create new post
GET    /api/posts/{id}      # Get specific post
PUT    /api/posts/{id}      # Update post
DELETE /api/posts/{id}      # Delete post
```

#### Categories Management  
```
GET    /api/categories      # List all categories
POST   /api/categories     # Create category
PUT    /api/categories/{id} # Update category
DELETE /api/categories/{id} # Delete category
```

#### Public Endpoints (No Auth Required)
```
GET    /api/public/posts           # Public post listing
GET    /api/public/posts/{slug}    # Get post by slug
GET    /api/public/categories      # Public categories
GET    /api/public/categories/{slug}/posts # Posts by category
```

### Laravel Project Structure

```
laravel-backend/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Api/
│   │   │   │   ├── AuthController.php
│   │   │   │   ├── PostController.php
│   │   │   │   ├── CategoryController.php
│   │   │   │   └── PublicController.php
│   │   │   └── Controller.php
│   │   ├── Middleware/
│   │   │   └── Authenticate.php
│   │   └── Requests/
│   │       ├── PostRequest.php
│   │       └── CategoryRequest.php
│   ├── Models/
│   │   ├── User.php
│   │   ├── Post.php
│   │   └── Category.php
│   └── Services/
│       ├── PostService.php
│       └── CategoryService.php
├── database/
│   ├── migrations/
│   │   ├── create_users_table.php
│   │   ├── create_posts_table.php
│   │   └── create_categories_table.php
│   └── seeders/
├── routes/
│   └── api.php
└── config/
    ├── cors.php
    └── sanctum.php
```

### Database Schema

```sql
-- Users table
CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Categories table
CREATE TABLE categories (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Posts table
CREATE TABLE posts (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    content LONGTEXT NOT NULL,
    excerpt TEXT,
    status ENUM('draft', 'published') DEFAULT 'draft',
    category_id BIGINT UNSIGNED,
    user_id BIGINT UNSIGNED NOT NULL,
    published_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_status (status),
    INDEX idx_published_at (published_at),
    INDEX idx_category_id (category_id)
);
```

### Laravel Model Relationships

```php
// Post Model
class Post extends Model {
    public function category() {
        return $this->belongsTo(Category::class);
    }
    
    public function user() {
        return $this->belongsTo(User::class);
    }
}

// Category Model  
class Category extends Model {
    public function posts() {
        return $this->hasMany(Post::class);
    }
}

// User Model
class User extends Model {
    public function posts() {
        return $this->hasMany(Post::class);
    }
}
```

---

## Frontend Architecture (Vue.js)

### Application Architecture

**Dual Frontend Strategy:**
1. **Admin Dashboard**: Single Page Application (SPA) for content management
2. **Public Blog**: Multi-page application for optimal SEO and performance

### Vue.js Project Structure

```
vue-frontend/
├── src/
│   ├── components/           # Reusable components
│   │   ├── common/          # Shared components
│   │   ├── admin/           # Admin-specific components
│   │   └── public/          # Public blog components
│   ├── views/               # Page components
│   │   ├── admin/           # Admin dashboard pages
│   │   │   ├── Dashboard.vue
│   │   │   ├── Posts.vue
│   │   │   ├── PostEditor.vue
│   │   │   └── Categories.vue
│   │   └── public/          # Public blog pages
│   │       ├── Home.vue
│   │       ├── Post.vue
│   │       └── Category.vue
│   ├── router/              # Vue Router configuration
│   │   └── index.js
│   ├── stores/              # Pinia state management
│   │   ├── auth.js
│   │   ├── posts.js
│   │   └── categories.js
│   ├── services/            # API service layer
│   │   ├── api.js
│   │   ├── authService.js
│   │   ├── postsService.js
│   │   └── categoriesService.js
│   ├── composables/         # Vue 3 composables
│   │   ├── useAuth.js
│   │   └── useApi.js
│   ├── utils/               # Utility functions
│   └── assets/              # Static assets
├── public/                  # Public assets
└── dist/                    # Built files
```

### Component Architecture

#### Base Component Template
```vue
<template>
  <div class="component-name">
    <!-- Component content -->
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useStore } from '@/stores/store'

// Props and emits
const props = defineProps({
  // props definition
})

const emit = defineEmits(['event-name'])

// Reactive state
const state = ref(null)

// Computed properties
const computedValue = computed(() => {
  // computed logic
})

// Lifecycle hooks
onMounted(() => {
  // component mounted logic
})
</script>

<style scoped>
.component-name {
  /* component styles */
}
</style>
```

### State Management (Pinia)

#### Auth Store Example
```javascript
// stores/auth.js
import { defineStore } from 'pinia'
import { authService } from '@/services/authService'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    token: localStorage.getItem('auth-token'),
    isAuthenticated: false
  }),
  
  getters: {
    isLoggedIn: (state) => !!state.token
  },
  
  actions: {
    async login(credentials) {
      try {
        const response = await authService.login(credentials)
        this.token = response.data.token
        this.user = response.data.user
        this.isAuthenticated = true
        localStorage.setItem('auth-token', this.token)
        return response
      } catch (error) {
        throw error
      }
    },
    
    logout() {
      this.user = null
      this.token = null
      this.isAuthenticated = false
      localStorage.removeItem('auth-token')
    }
  }
})
```

### API Service Layer

```javascript
// services/api.js
import axios from 'axios'
import { useAuthStore } from '@/stores/auth'

const api = axios.create({
  baseURL: 'http://localhost:8000/api',
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  }
})

// Request interceptor for auth token
api.interceptors.request.use((config) => {
  const authStore = useAuthStore()
  if (authStore.token) {
    config.headers.Authorization = `Bearer ${authStore.token}`
  }
  return config
})

// Response interceptor for error handling
api.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401) {
      const authStore = useAuthStore()
      authStore.logout()
    }
    return Promise.reject(error)
  }
)

export default api
```

---

## Security Architecture

### Authentication & Authorization

1. **Laravel Sanctum**: Token-based authentication for API access
2. **Password Hashing**: Laravel's built-in bcrypt hashing
3. **CSRF Protection**: Disabled for API routes, token-based security instead
4. **CORS Configuration**: Proper cross-origin resource sharing setup

### Security Measures

- **Input Validation**: Laravel Form Requests validate all input
- **SQL Injection Prevention**: Eloquent ORM with parameterized queries
- **XSS Protection**: Vue.js automatic output escaping
- **Authorization**: Laravel Policies for fine-grained access control
- **Rate Limiting**: Laravel's built-in API rate limiting
- **HTTPS**: SSL/TLS encryption for production

---

## Development Workflow

### Local Development Setup

1. **Backend Setup**:
   ```bash
   composer install
   php artisan sail:install
   ./vendor/bin/sail up -d
   php artisan migrate --seed
   ```

2. **Frontend Setup**:
   ```bash
   npm install
   npm run dev
   ```

3. **Development URLs**:
   - Laravel API: `http://localhost:8000`
   - Vue.js Frontend: `http://localhost:5173`

### Build & Deployment

1. **Frontend Build**:
   ```bash
   npm run build
   ```

2. **Laravel Optimization**:
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

### Testing Strategy

- **Backend**: PHPUnit tests for API endpoints and business logic
- **Frontend**: Vitest for unit tests, Cypress for E2E testing
- **API Testing**: Postman/Insomnia collections for API validation

---

## Performance Considerations

### Backend Optimization
- **Database Indexing**: Strategic indexes on frequently queried columns
- **Query Optimization**: Eager loading relationships to prevent N+1 queries
- **Caching**: Redis for session/cache storage
- **API Pagination**: Efficient pagination for large datasets

### Frontend Optimization
- **Code Splitting**: Vue Router lazy loading for page components
- **Asset Optimization**: Vite's built-in asset optimization
- **Image Optimization**: WebP format with fallbacks
- **Bundle Analysis**: Bundle size monitoring and optimization

---

## Deployment Architecture

### Production Stack
- **Web Server**: Nginx as reverse proxy
- **Application Server**: PHP-FPM for Laravel
- **Database**: MySQL 8.0+ with proper configuration
- **Process Manager**: Supervisor for queue workers
- **SSL**: Let's Encrypt certificates
- **CDN**: CloudFlare for static asset delivery

### Deployment Pipeline
1. **Version Control**: Git-based deployment workflow
2. **CI/CD**: GitHub Actions for automated testing and deployment
3. **Zero Downtime**: Blue-green deployment strategy
4. **Monitoring**: Application and infrastructure monitoring
5. **Backup Strategy**: Automated database and file backups

---

## Scalability Considerations

### Horizontal Scaling Options
- **Load Balancer**: Nginx load balancing for multiple app servers
- **Database Replication**: MySQL read replicas for scaling reads
- **Queue Workers**: Distributed queue processing
- **CDN Integration**: Global content delivery network
- **Microservices**: Future migration path if needed

### Monitoring & Observability
- **Application Monitoring**: Laravel Telescope for development
- **Error Tracking**: Sentry for production error monitoring  
- **Performance Monitoring**: New Relic or similar APM tools
- **Log Management**: Centralized logging with ELK stack

This architecture provides a solid foundation for the ModernBlog Platform while maintaining flexibility for future enhancements and scaling requirements.