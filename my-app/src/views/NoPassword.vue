<!-- Vue -->
<script setup lang="ts">
import { useRouter} from 'vue-router'

const router = useRouter()

function goBackToLogin() {
  router.push({ name: 'Login' })
}
</script>

<template>
  <div class="auth-page">
    <v-card elevation="6" class="pa-4 auth-card">
      <v-card-title class="pa-0 pb-4">
        <div>
          <h3 class="ma-0">Kein Passwort hinterlegt</h3>
          <div class="caption title-helper">
            Wir haben dir eine E-Mail zum Passwort festlegen zugeschickt.
          </div>
        </div>
      </v-card-title>

      <v-card-actions class="pa-0 mt-4 d-flex justify-center">
        <v-btn
            color="primary"
            class="mx-auto"
            style="min-width:160px"
            @click="goBackToLogin"
        >
          Zurück zum Login
        </v-btn>
      </v-card-actions>
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
  max-width: 460px;
  white-space: normal;
}
</style>
