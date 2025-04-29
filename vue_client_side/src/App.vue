<template>
  <div id="app" class="dashboard-container">
    <!-- Only show Navbar and Footer if not on login page -->
    <Navbar v-if="showLayout" @logout="logout" />

    <router-view />

    <Footer v-if="showLayout" />
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import Navbar from '@/components/layouts/NavBar.vue'
import Footer from '@/components/layouts/FooterBar.vue'

const route = useRoute()
const router = useRouter()

// Hide Navbar/Footer on login page
const showLayout = computed(() => route.path !== '/')

function logout() {
  localStorage.removeItem('isConnected')
  localStorage.removeItem('token')
  router.push('/')
}
</script>

<style>
/* Your styles */
</style>
