export const publicRoutes = [
    {
        path: '/',
        name: 'public.layout',
        component: () => import('../views/public/Layout.vue'),
        children: [
            {
                path: '',
                name: 'public.home',
                component: () => import('../views/public/Home.vue'),
                meta: {
                    title: 'ModernBlog'
                }
            },
            {
                path: 'post/:slug',
                name: 'public.post',
                component: () => import('../views/public/Post.vue'),
                meta: {
                    title: 'Post'
                },
                props: true
            },
            {
                path: 'category/:slug',
                name: 'public.category',
                component: () => import('../views/public/Category.vue'),
                meta: {
                    title: 'Category'
                },
                props: true
            },
            {
                path: 'about',
                name: 'public.about',
                component: () => import('../views/public/About.vue'),
                meta: {
                    title: 'About'
                }
            },
            {
                path: 'contact',
                name: 'public.contact',
                component: () => import('../views/public/Contact.vue'),
                meta: {
                    title: 'Contact'
                }
            }
        ]
    },
    {
        path: '/:pathMatch(.*)*',
        name: 'public.not-found',
        component: () => import('../views/public/NotFound.vue'),
        meta: {
            title: '404 - Page Not Found'
        }
    }
]