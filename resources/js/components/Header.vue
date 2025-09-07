<template>
  <header class="header">
    <div class="logo-menu">
      <img src="@/assets/images/logo.png" alt="Logo" class="logo" />
    </div>

    <div class="login-section">
      <div v-if="isAuthenticated" class="user-info">
        <i class="fas fa-user"></i>
        <span>{{ auth.roleLabel }}: {{ auth.user?.name }}</span>
        <button class="logout-btn" @click="handleLogout">Đăng xuất</button>
      </div>
      <router-link v-else class="login-link" to="/login">
        <i class="fas fa-user"></i> Thí sinh <span>Đăng nhập</span>
      </router-link>
    </div>
  </header>
</template>

<script setup>
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/store/auth'
import { storeToRefs } from 'pinia'

const router = useRouter()
const auth = useAuthStore()
const { isAuthenticated } = storeToRefs(auth)

const handleLogout = async () => {
  await auth.logout()
  router.push('/')
}
</script>

<style scoped src="@/assets/css/header.css" />
