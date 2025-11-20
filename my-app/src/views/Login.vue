<!-- File: `my-app/src/views/Login.vue` (script section only) -->
<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const email = ref('')
const form = ref<any>(null)

const emailRules = [
  (v: string) => !!v?.trim() || 'Email ist erforderlich',
  (v: string) =>
      /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(v?.trim()) ||
      'Gib eine gültige E-Mail ein'
]

async function onSubmit() {
  const result = await Promise.resolve(form.value?.validate?.())
  const isValid = typeof result === 'boolean' ? result : !!result?.valid
  if (!isValid) return

  const trimmed = email.value.trim().toLowerCase()

  // Testmail für Hinweisseite, dass kein PW festgelegt wurde
  if (trimmed === 'no@pw.com') {
    router.push({ name: 'NoPassword' })
    return
  }

  router.push({ name: 'EnterPassword', query: { email: trimmed } })
}


</script>



<template>
  <div class="auth-page">
    <!-- Vue -->
    <v-card elevation="6" class="pa-4 auth-card">
      <v-form ref="form" @submit.prevent="onSubmit">
        <v-card-title class="pa-0 pb-4">
          <div>
            <h3 class="ma-0">Anmeldung</h3>
            <div class="caption">Bitte trage deine E-Mail ein:</div>
          </div>
        </v-card-title>

        <v-card-text class="pa-0">
          <v-text-field
              v-model="email"
              label="Email"
              type="email"
              :rules="emailRules"
              prepend-inner-icon="mdi-email"
              density="comfortable"
              autocomplete="email"
              placeholder="E-Mail"
              required
          />
        </v-card-text>

        <v-card-actions class="pa-0 mt-4 d-flex justify-center">
          <v-btn
              color="primary"
              class="mx-auto"
              style="min-width:160px"
              type="submit"
          >
            Weiter
          </v-btn>
        </v-card-actions>
      </v-form>
    </v-card>
  </div>
</template>

<style scoped>
.auth-page {
  max-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 24px;
  box-sizing: border-box;
  overflow: hidden;
}

.auth-card {
  width: 100%;
  max-width: 420px;
  border-radius: 12px;
}
</style>
