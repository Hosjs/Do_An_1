<template>
  <aside class="sidebar" :class="{ collapsed: isCollapsed }">
    <button class="toggle-btn" @click="toggleSidebar">☰</button>
    <nav v-if="!isCollapsed && useAuthStore().role === 'Admin'" class="menu">
      <router-link to="/admin/dashboard">🏠 Trang chủ</router-link>
      <router-link to="/admin/users">👤 Quản lý người dùng</router-link>
      <router-link to="/admin/generate-test">👤 Quản lý đề thi</router-link>
      <router-link to="/admin/tests">👤 Quản lý bài thi</router-link>
      <router-link to="/admin/statistics">📊 Thống kê</router-link>
      <router-link to="/admin/settings">⚙️ Cài đặt</router-link>
    </nav>
    <nav v-if="!isCollapsed && useAuthStore().role === 'Student'" class="menu">
      <router-link to="/student/tests">🎓 Bài thi</router-link>
      <router-link to="/student/statistics">📊 Thống kê</router-link>
    </nav>
    <nav v-if="!isCollapsed && useAuthStore().role === 'Teacher'" class="menu">
      <router-link to="/teacher/tests">👨‍🏫 Bài thi</router-link>
      <router-link to="/teacher/statistics">📊 Thống kê</router-link>
      <router-link to="/teacher/generate-test">👤 Quản lý đề thi</router-link>
      <router-link to="/teacher/tests">👤 Quản lý bài thi</router-link>
    </nav>
  </aside>
</template>

<script setup>
import { ref } from 'vue'
import '@/assets/css/sidebar.css'
import {useAuthStore} from '@/store/auth'

const isCollapsed = ref(false)
const toggleSidebar = () => {
  isCollapsed.value = !isCollapsed.value
}
</script>
