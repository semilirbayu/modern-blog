export const adminRoutes = [
    {
        path: '/login',
        name: 'admin.login',
        component: () => import('../views/admin/Login.vue'),
        meta: {
            requiresGuest: true,
            requiresAuth: false,
            title: 'Admin Login',
            description: 'Sign in to access the admin dashboard',
            layout: 'guest'
        }
    },
    {
        path: '/',
        name: 'admin.layout',
        component: () => import('../views/admin/Layout.vue'),
        meta: {
            requiresAuth: true,
            requiresGuest: false,
            layout: 'admin'
        },
        children: [
            {
                path: '',
                name: 'admin.dashboard',
                component: () => import('../views/admin/Dashboard.vue'),
                meta: {
                    title: 'Dashboard',
                    description: 'Admin dashboard overview',
                    breadcrumb: 'Dashboard',
                    icon: 'home',
                    requiresAuth: true
                }
            },
            {
                path: 'posts',
                name: 'admin.posts',
                component: () => import('../views/admin/PostsSimple.vue'),
                meta: {
                    title: 'Posts',
                    description: 'Manage blog posts',
                    breadcrumb: 'Posts',
                    icon: 'document-text',
                    requiresAuth: true
                }
            },
            {
                path: 'posts/create',
                name: 'admin.posts.create',
                component: () => import('../views/admin/PostEditor.vue'),
                meta: {
                    title: 'Create Post',
                    description: 'Create a new blog post',
                    breadcrumb: 'Create Post',
                    parent: 'admin.posts',
                    icon: 'plus',
                    requiresAuth: true,
                    permissions: ['create-posts']
                }
            },
            {
                path: 'posts/:id/edit',
                name: 'admin.posts.edit',
                component: () => import('../views/admin/PostEditor.vue'),
                meta: {
                    title: 'Edit Post',
                    description: 'Edit blog post',
                    breadcrumb: 'Edit Post',
                    parent: 'admin.posts',
                    icon: 'pencil',
                    requiresAuth: true,
                    permissions: ['edit-posts']
                },
                props: true
            },
            {
                path: 'categories',
                name: 'admin.categories',
                component: () => import('../views/admin/CategoriesSimple.vue'),
                meta: {
                    title: 'Categories',
                    description: 'Manage post categories',
                    breadcrumb: 'Categories',
                    icon: 'tag',
                    requiresAuth: true
                }
            },
            {
                path: 'profile',
                name: 'admin.profile',
                component: () => import('../views/admin/Profile.vue'),
                meta: {
                    title: 'Profile',
                    description: 'Manage your account settings',
                    breadcrumb: 'Profile',
                    icon: 'user',
                    requiresAuth: true
                }
            }
        ]
    },
    {
        path: '/:pathMatch(.*)*',
        name: 'admin.not-found',
        component: () => import('../views/admin/NotFound.vue'),
        meta: {
            title: 'Page Not Found',
            description: 'The requested page could not be found',
            layout: 'admin'
        }
    }
]