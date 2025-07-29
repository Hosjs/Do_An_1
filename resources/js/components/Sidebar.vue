<template>
  <aside class="sidebar" :class="{ collapsed: isCollapsed }">
    <button class="toggle-btn" @click="toggleSidebar">☰</button>
    <nav v-if="!isCollapsed" class="menu">
      <router-link to="/admin/dashboard">🏠 Trang chủ</router-link>
      <router-link to="/admin/users">👤 Quản lý người dùng</router-link>
      <router-link to="/admin/generate-test">👤 Quản lý đề thi</router-link>
      <router-link to="/admin/tests">👤 Quản lý bài thi</router-link>
    </nav>
  </aside>
</template>

<script setup>
import { ref } from 'vue'
import '@/assets/css/sidebar.css'

const isCollapsed = ref(false)
const toggleSidebar = () => {
  isCollapsed.value = !isCollapsed.value
}
</script>
