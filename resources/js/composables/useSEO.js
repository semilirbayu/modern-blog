import { onMounted, onUnmounted } from 'vue'

export function useSEO() {
  const updateTitle = (title) => {
    if (typeof document !== 'undefined') {
      document.title = title
    }
  }

  const updateMeta = (name, content) => {
    if (typeof document !== 'undefined') {
      let meta = document.querySelector(`meta[name="${name}"]`)
      
      if (!meta) {
        meta = document.createElement('meta')
        meta.name = name
        document.head.appendChild(meta)
      }
      
      meta.content = content
    }
  }

  const updateProperty = (property, content) => {
    if (typeof document !== 'undefined') {
      let meta = document.querySelector(`meta[property="${property}"]`)
      
      if (!meta) {
        meta = document.createElement('meta')
        meta.setAttribute('property', property)
        document.head.appendChild(meta)
      }
      
      meta.content = content
    }
  }

  const updateOpenGraph = ({ title, description, type = 'website', url, image, siteName = 'ModernBlog' }) => {
    if (title) updateProperty('og:title', title)
    if (description) updateProperty('og:description', description)
    if (type) updateProperty('og:type', type)
    if (url) updateProperty('og:url', url)
    if (image) updateProperty('og:image', image)
    if (siteName) updateProperty('og:site_name', siteName)
  }

  const updateTwitterCard = ({ card = 'summary_large_image', site = '@ModernBlog', title, description, image }) => {
    updateMeta('twitter:card', card)
    if (site) updateMeta('twitter:site', site)
    if (title) updateMeta('twitter:title', title)
    if (description) updateMeta('twitter:description', description)
    if (image) updateMeta('twitter:image', image)
  }

  const updateCanonical = (url) => {
    if (typeof document !== 'undefined') {
      let canonical = document.querySelector('link[rel="canonical"]')
      
      if (!canonical) {
        canonical = document.createElement('link')
        canonical.rel = 'canonical'
        document.head.appendChild(canonical)
      }
      
      canonical.href = url
    }
  }

  const addStructuredData = (data) => {
    if (typeof document !== 'undefined') {
      // Remove existing structured data
      const existing = document.querySelector('script[type="application/ld+json"]')
      if (existing) {
        existing.remove()
      }

      // Add new structured data
      const script = document.createElement('script')
      script.type = 'application/ld+json'
      script.textContent = JSON.stringify(data)
      document.head.appendChild(script)
    }
  }

  const createBlogPostSchema = ({ title, description, author, datePublished, dateModified, image, url, category }) => {
    return {
      '@context': 'https://schema.org',
      '@type': 'BlogPosting',
      headline: title,
      description: description,
      author: {
        '@type': 'Person',
        name: author
      },
      datePublished: datePublished,
      dateModified: dateModified || datePublished,
      image: image,
      url: url,
      publisher: {
        '@type': 'Organization',
        name: 'ModernBlog',
        logo: {
          '@type': 'ImageObject',
          url: '/images/logo.png'
        }
      },
      mainEntityOfPage: {
        '@type': 'WebPage',
        '@id': url
      },
      ...(category && {
        articleSection: category
      })
    }
  }

  const createWebsiteSchema = () => {
    return {
      '@context': 'https://schema.org',
      '@type': 'WebSite',
      name: 'ModernBlog',
      description: 'A modern blogging platform built with Laravel and Vue.js',
      url: window.location.origin,
      potentialAction: {
        '@type': 'SearchAction',
        target: {
          '@type': 'EntryPoint',
          urlTemplate: `${window.location.origin}/search?q={search_term_string}`
        },
        'query-input': 'required name=search_term_string'
      },
      publisher: {
        '@type': 'Organization',
        name: 'ModernBlog',
        logo: {
          '@type': 'ImageObject',
          url: '/images/logo.png'
        }
      }
    }
  }

  const createBreadcrumbSchema = (breadcrumbs) => {
    if (!breadcrumbs || breadcrumbs.length === 0) return null

    return {
      '@context': 'https://schema.org',
      '@type': 'BreadcrumbList',
      itemListElement: breadcrumbs.map((crumb, index) => ({
        '@type': 'ListItem',
        position: index + 1,
        name: crumb.name,
        item: window.location.origin + crumb.path
      }))
    }
  }

  const setPageSEO = ({
    title,
    description,
    keywords = [],
    image,
    type = 'website',
    author,
    datePublished,
    dateModified,
    category,
    breadcrumbs = []
  }) => {
    const url = window.location.href
    const fullTitle = title.includes('ModernBlog') ? title : `${title} - ModernBlog`

    // Basic meta tags
    updateTitle(fullTitle)
    updateMeta('description', description)
    updateCanonical(url)

    // Keywords
    if (keywords.length > 0) {
      updateMeta('keywords', keywords.join(', '))
    }

    // Robots
    updateMeta('robots', 'index, follow')

    // Open Graph
    updateOpenGraph({
      title: fullTitle,
      description,
      type,
      url,
      image: image || '/images/default-og.jpg'
    })

    // Twitter Card
    updateTwitterCard({
      title: fullTitle,
      description,
      image: image || '/images/default-og.jpg'
    })

    // Structured Data
    let schemas = []

    // Website schema (for homepage)
    if (type === 'website') {
      schemas.push(createWebsiteSchema())
    }

    // Blog post schema
    if (type === 'article' && author && datePublished) {
      schemas.push(createBlogPostSchema({
        title,
        description,
        author,
        datePublished,
        dateModified,
        image,
        url,
        category
      }))
    }

    // Breadcrumb schema
    if (breadcrumbs.length > 0) {
      const breadcrumbSchema = createBreadcrumbSchema(breadcrumbs)
      if (breadcrumbSchema) {
        schemas.push(breadcrumbSchema)
      }
    }

    // Add combined structured data
    if (schemas.length > 0) {
      addStructuredData(schemas.length === 1 ? schemas[0] : schemas)
    }
  }

  const resetSEO = () => {
    updateTitle('ModernBlog - A Modern Blogging Platform')
    updateMeta('description', 'A modern blogging platform built with Laravel and Vue.js. Discover amazing content and share your thoughts with the world.')
    
    // Remove dynamic meta tags
    const dynamicMetas = document.querySelectorAll('meta[property^="og:"], meta[name^="twitter:"]')
    dynamicMetas.forEach(meta => meta.remove())
    
    // Remove structured data
    const structuredData = document.querySelector('script[type="application/ld+json"]')
    if (structuredData) {
      structuredData.remove()
    }
  }

  return {
    updateTitle,
    updateMeta,
    updateProperty,
    updateOpenGraph,
    updateTwitterCard,
    updateCanonical,
    addStructuredData,
    createBlogPostSchema,
    createWebsiteSchema,
    createBreadcrumbSchema,
    setPageSEO,
    resetSEO
  }
}