<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const username = ref('')
const password = ref('')

function submit() {
  // placeholder: call API and store token
  localStorage.setItem('auth_token', 'mock-token')
  router.push({ name: 'Dashboard' })
}
</script>

<template>
  <div class="page">
    <h2>Login</h2>
    <form @submit.prevent="submit">
      <input v-model="username" placeholder="Username" />
      <input v-model="password" type="password" placeholder="Password" />
      <button type="submit">Login</button>
    </form>
  </div>
</template>