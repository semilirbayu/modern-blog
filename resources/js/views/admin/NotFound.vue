<template>
  <div class="p-8 text-center">
    <h1 class="text-2xl font-semibold">Page Not Found</h1>
    <p class="text-gray-600 mt-2">The requested page could not be found.</p>
    <router-link :to="{ name: 'admin.dashboard' }" class="text-blue-600 hover:underline mt-4 inline-block">Go to Dashboard</router-link>
  </div>
</template>