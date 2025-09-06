<template>
  <div class="login-page">
    <div class="login-box">
      <h1>Đăng ký</h1>
      <p class="sub">Bạn đã có tài khoản? <router-link to="/login">Đăng nhập</router-link></p>

      <div class="social-login">
        <button class="btn fb">Facebook</button>
        <button class="btn gg">Google</button>
      </div>

      <div class="divider">hoặc</div>

      <form @submit.prevent="submitRegister">
        <input v-model="name" type="text" placeholder="Họ và tên" required />
        <input v-model="email" type="email" placeholder="Email" required />
        <input v-model="password" type="password" placeholder="Mật khẩu" required />
        <input v-model="password_confirmation" type="password" placeholder="Nhập lại mật khẩu" required />
        <select v-model="role" required>
          <option disabled value="">Chọn vai trò</option>
          <option value="Student">Học sinh</option>
          <option value="Teacher">Giáo viên</option>
        </select>
        <button type="submit" class="btn-submit">Đăng ký</button>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'

const name = ref('')
const email = ref('')
const password = ref('')
const password_confirmation = ref('')
const role = ref('')
const router = useRouter()

const submitRegister = async () => {
  try {
    await axios.post('/api/register', {
      name: name.value,
      email: email.value,
      password: password.value,
      password_confirmation: password_confirmation.value,
      role: role.value
    })
    alert('Đăng ký thành công! Vui lòng đăng nhập.')
    router.push('/login')
  } catch (e) {
    alert('Đăng ký thất bại. Vui lòng kiểm tra lại thông tin.')
  }
}
</script>

<style scoped src="@/assets/css/login.css" />