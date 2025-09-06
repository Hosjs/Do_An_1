<template>
  <div class="login-page">
    <div class="login-box">
      <h1>Đăng nhập</h1>
      <p class="sub">Bạn chưa có tài khoản? <router-link to="/register">Đăng ký ngay</router-link></p>

      <div class="social-login">
        <button class="btn fb">Facebook</button>
        <button class="btn gg">Google</button>
      </div>

      <div class="divider">hoặc</div>

      <form @submit.prevent="submitLogin">
        <input v-model="email" type="email" placeholder="Email đã đăng ký" required />
        <input v-model="password" type="password" placeholder="Mật khẩu" required />
        <button type="submit" class="btn-submit">Đăng nhập</button>
      </form>

      <router-link class="forgot" to="/forgot-password">Quên mật khẩu?</router-link>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../store/auth'

const email = ref('')
const password = ref('')
const auth = useAuthStore()
const router = useRouter()

const submitLogin = async () => {
  try {
    await auth.login(email.value, password.value)
    const role = auth.role.toLowerCase()

    if (role === 'admin') router.push('/admin')
    else if (role === 'student') router.push('/student')
    else if (role === 'teacher') router.push('/teacher')
    else router.push('/')
  } catch {
    alert('Sai tài khoản hoặc mật khẩu')
  }
}
</script>

<style scoped src="@/assets/css/login.css" />
