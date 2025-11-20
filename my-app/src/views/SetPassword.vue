<!-- Vue -->
<script setup lang="ts">
import { ref } from 'vue'

const form = ref<any>(null)
const password = ref('')
const confirmPassword = ref('')
const success = ref(false)

const passwordRules = [
  (v: string) => !!v?.trim() || 'Bitte ein Passwort eintragen',
  (v: string) => (v?.trim().length >= 6) || 'Mindestens 6 Zeichen',
]

const confirmRules = [
  (v: string) => !!v?.trim() || 'Bitte Passwort bestätigen',
  (v: string) => v === password.value || 'Passwörter stimmen nicht überein',
]

async function onSubmit() {
  success.value = false
  const result = await Promise.resolve(form.value?.validate?.())
  const isValid = typeof result === 'boolean' ? result : !!result?.valid
  if (!isValid) return
  success.value = true
}
</script>

<template>
  <div class="auth-page">
    <v-card elevation="6" class="pa-4 auth-card">
      <v-form ref="form" @submit.prevent="onSubmit">
        <v-card-title class="pa-0 pb-4">
          <div>
            <h3 class="ma-0">Passwort festlegen</h3>
            <div class="caption title-helper">
              Bitte neues Passwort eingeben und bestätigen.
            </div>
          </div>
        </v-card-title>

        <v-card-text class="pa-0">
          <v-text-field
              v-model="password"
              label="Neues Passwort"
              type="password"
              :rules="passwordRules"
              prepend-inner-icon="mdi-lock"
              density="comfortable"
              placeholder="Passwort"
              required
              class="mb-4"
          />
          <v-text-field
              v-model="confirmPassword"
              label="Passwort bestätigen"
              type="password"
              :rules="confirmRules"
              prepend-inner-icon="mdi-lock-check"
              density="comfortable"
              placeholder="Passwort wiederholen"
              required
          />

          <v-alert
              v-if="success"
              type="success"
              variant="tonal"
              class="mt-4"
          >
            Passwort erfolgreich festgelegt.
          </v-alert>
        </v-card-text>

        <v-card-actions class="pa-0 mt-4 d-flex justify-center">
          <v-btn
              color="primary"
              class="mx-auto"
              style="min-width:160px"
              type="submit"
              :disabled="success"
          >
            Passwort speichern
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

.title-helper {
  max-width: 260px; /* bei Bedarf anpassen */
  white-space: normal;
}
</style>
