import { defineStore } from 'pinia'
import { ref } from 'vue'

export const useUserStore = defineStore('user', () => {
    const token = ref<string | null>(localStorage.getItem('auth_token'))
    function setToken(t: string | null) {
        token.value = t
        if (t) localStorage.setItem('auth_token', t)
        else localStorage.removeItem('auth_token')
    }
    return { token, setToken }
})