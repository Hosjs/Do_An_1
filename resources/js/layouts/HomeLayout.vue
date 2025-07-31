<template>
  <div class="layout">
    <div class="content-wrapper">
      <Header />
      <main class="main-view">
        <router-view />
      </main>
    </div>
  </div>
</template>

<script setup>
import Header from '@/components/Header.vue'
</script>

<style scoped>
.layout {
  display: flex;
  height: 100vh;
}
.content-wrapper {
  flex: 1;
  display: flex;
  flex-direction: column;
}
.main-view {
  flex: 1;
  padding: 20px;
  overflow-y: auto;
}
</style>
