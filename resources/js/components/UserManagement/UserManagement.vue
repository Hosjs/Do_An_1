<template>
  <div class="user-manager">
    <h2>Quản lý người dùng</h2>

    <!-- Nút bật/tắt form -->
    <button @click="toggleForm">{{ showForm ? 'Đóng' : 'Thêm người dùng' }}</button>

    <!-- Bảng người dùng -->
    <table class="user-table">
      <thead>
        <tr>
          <th>Họ tên</th>
          <th>Email</th>
          <th>Tài khoản</th>
          <th>Mật khẩu</th>
          <th>Hành động</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="user in users" :key="user.id">
          <td>{{ user.name }}</td>
          <td>{{ user.email }}</td>
          <td>{{ user.username }}</td>
          <td>••••••</td>
          <td>
            <button @click="editUser(user)">✏️</button>
            <button @click="deleteUser(user.id)">🗑️</button>
          </td>
        </tr>
      </tbody>
    </table>

    <!-- Form thêm/sửa -->
    <div v-if="showForm">
      <h3>{{ isEditing ? 'Chỉnh sửa' : 'Thêm mới' }} người dùng</h3>
      <form class="user-form" @submit.prevent="saveUser">
        <input v-model="form.name" placeholder="Họ tên" required />
        <input v-model="form.email" type="email" placeholder="Email" required />
        <input v-model="form.username" placeholder="Tài khoản" required />
        <input
          v-model="form.password"
          type="password"
          placeholder="Mật khẩu"
          :required="!isEditing"
        />
        <div class="form-buttons">
          <button type="submit">{{ isEditing ? 'Cập nhật' : 'Thêm' }}</button>
          <button @click.prevent="resetForm">Hủy</button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import axios from 'axios'
import { useAuthStore } from '@/store/auth'

export default {
  data() {
    return {
      users: [],
      form: {
        id: null,
        name: '',
        email: '',
        username: '',
        password: '',
      },
      isEditing: false,
      showForm: false,
    }
  },
  methods: {
    async fetchUsers() {
      try {
        const auth = useAuthStore()
        const res = await axios.get('/api/users', {
          headers: {
            Authorization: `Bearer ${auth.token}`,
          },
        })
        this.users = res.data
      } catch (error) {
        console.error('Lỗi khi tải danh sách người dùng:', error)
      }
    },

    async saveUser() {
      const auth = useAuthStore()
      try {
        if (this.isEditing) {
          await axios.put(`/api/users/${this.form.id}`, this.form, {
            headers: {
              Authorization: `Bearer ${auth.token}`,
            },
          })
        } else {
          await axios.post('/api/users', this.form, {
            headers: {
              Authorization: `Bearer ${auth.token}`,
            },
          })
        }
        this.resetForm()
        this.fetchUsers()
      } catch (error) {
        console.error('Lỗi khi lưu người dùng:', error)
      }
    },

    editUser(user) {
      this.form = { ...user, password: '' }
      this.isEditing = true
      this.showForm = true
    },

    async deleteUser(id) {
      const auth = useAuthStore()
      if (confirm('Bạn chắc chắn muốn xóa?')) {
        try {
          await axios.delete(`/api/users/${id}`, {
            headers: {
              Authorization: `Bearer ${auth.token}`,
            },
          })
          this.fetchUsers()
        } catch (error) {
          console.error('Lỗi khi xóa người dùng:', error)
        }
      }
    },

    resetForm() {
      this.form = {
        id: null,
        name: '',
        email: '',
        username: '',
        password: '',
      }
      this.isEditing = false
      this.showForm = false
    },

    toggleForm() {
      this.showForm = !this.showForm
      if (!this.showForm) this.resetForm()
    },
  },
  mounted() {
    this.fetchUsers()
  },
}
</script>

<style scoped>
.user-manager {
  max-width: 800px;
  margin: 40px auto;
  font-family: Arial, sans-serif;
}
.user-table {
  width: 100%;
  border-collapse: collapse;
  margin-bottom: 20px;
}
.user-table th,
.user-table td {
  padding: 12px;
  border: 1px solid #ccc;
  text-align: left;
}
.user-table th {
  background-color: #f4f4f4;
}
.user-form input {
  display: block;
  margin: 8px 0;
  padding: 8px;
  width: 100%;
  box-sizing: border-box;
}
.form-buttons {
  margin-top: 10px;
}
button {
  padding: 6px 12px;
  margin-right: 8px;
  cursor: pointer;
  background-color: #007bff;
  color: white;
  border: none;
  border-radius: 4px;
}
button:hover {
  background-color: #0056b3;
}
</style>
