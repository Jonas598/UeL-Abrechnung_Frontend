<script setup lang="ts">
import { ref, watch, onMounted } from 'vue'
import axios from 'axios'
import type { VForm } from 'vuetify/components'

// --- Typen ---
type Department = {
  id: number
  name: string
}

// --- 1. State & Refs ---

const form = ref<VForm | null>(null)
const isLoading = ref(false)

// Persönliche Daten
const email = ref('')
const firstName = ref('')
const lastName = ref('')

// Rollen-Status
const isOffice = ref(false)
const isDepartmentHead = ref(false)
const isTrainer = ref(false)

// Ausgewählte Abteilungen (Speichert die IDs)
const selectedHeadDepartments = ref<number[]>([])
const selectedTrainerDepartments = ref<number[]>([])

// Verfügbare Abteilungen (werden vom Backend geladen)
const departments = ref<Department[]>([])

// --- 2. API Calls ---

async function fetchDepartments() {
  try {
    // Holt die Liste: [{id: 1, name: 'Fußball'}, ...]
    const response = await axios.get('http://127.0.0.1:8000/api/abteilungen')
    departments.value = response.data
  } catch (error) {
    console.error('Konnte Abteilungen nicht laden. Ist das Backend gestartet?', error)
  }
}

onMounted(() => {
  fetchDepartments()
})

// --- 3. Validierungs-Regeln ---

const emailRules = [
  (v: string) => !!v?.trim() || 'E-Mail ist erforderlich',
  (v: string) => /.+@.+\..+/.test(v) || 'E-Mail muss gültig sein',
]

const requiredRules = [
  (v: string) => !!v?.trim() || 'Dieses Feld ist erforderlich',
]

// --- 4. Watcher (Aufräumen) ---
// Wenn die Checkbox deaktiviert wird, leeren wir die Auswahl.

watch(isDepartmentHead, (isActive) => {
  if (!isActive) selectedHeadDepartments.value = []
})

watch(isTrainer, (isActive) => {
  if (!isActive) selectedTrainerDepartments.value = []
})

// --- 5. Submit Logik ---

async function onSubmit() {
  // Validierung prüfen
  const { valid } = await form.value?.validate() || { valid: false }
  if (!valid) return

  isLoading.value = true

  // Payload bauen
  const payload = {
    email: email.value,
    vorname: firstName.value, // Frontend: firstName -> Backend: vorname
    name: lastName.value,     // Frontend: lastName  -> Backend: name

    // ÄNDERUNG: Direktes Mapping auf die User-Tabellenspalte
    isGeschaeftsstelle: isOffice.value,

    roles: {
      // isOffice hier entfernt, da es ein User-Attribut ist
      departmentHead: isDepartmentHead.value ? selectedHeadDepartments.value : [],
      trainer: isTrainer.value ? selectedTrainerDepartments.value : [],
    }
  }

  // Request senden
  try {
    const response = await axios.post('http://127.0.0.1:8000/api/create-user', payload)

    console.log('Erfolg:', response.data)
    alert(`Benutzer ${firstName.value} erfolgreich angelegt!`)

    // Formular zurücksetzen
    form.value?.reset()
    // Manuelles Reset der Booleans
    isOffice.value = false
    isDepartmentHead.value = false
    isTrainer.value = false
    // Arrays leeren
    selectedHeadDepartments.value = []
    selectedTrainerDepartments.value = []

  } catch (error: any) {
    console.error('API Error:', error)
    // Verbesserte Fehleranzeige: Zeigt Details, falls vom Backend validation errors kommen
    let errorMsg = error.response?.data?.message || 'Serverfehler beim Anlegen.'
    if (error.response?.data?.errors) {
      errorMsg += '\n' + JSON.stringify(error.response.data.errors, null, 2)
    }
    alert(errorMsg)
  } finally {
    isLoading.value = false
  }
}
</script>

<template>
  <div class="auth-page">
    <v-card elevation="6" class="pa-4 auth-card">

      <!-- Ladebalken -->
      <v-progress-linear
          v-if="isLoading"
          indeterminate
          color="primary"
          absolute
          top
      ></v-progress-linear>

      <v-card-title class="pa-0 pb-4">
        <div>
          <h3 class="ma-0">Benutzererstellung</h3>
          <div class="caption">Bitte Daten des Benutzers eintragen:</div>
        </div>
      </v-card-title>

      <v-card-text class="pa-0">
        <v-form ref="form" @submit.prevent="onSubmit">

          <!-- Personendaten -->
          <v-text-field
              v-model="email"
              label="E-Mail"
              type="email"
              :rules="emailRules"
              density="comfortable"
              autocomplete="email"
              placeholder="E-Mail"
              required
              class="mb-4"
          />

          <v-text-field
              v-model="firstName"
              label="Vorname"
              type="text"
              :rules="requiredRules"
              density="comfortable"
              placeholder="Vorname"
              required
              class="mb-4"
          />

          <v-text-field
              v-model="lastName"
              label="Nachname"
              type="text"
              :rules="requiredRules"
              density="comfortable"
              placeholder="Nachname"
              required
              class="mb-4"
          />

          <!-- 1. Geschäftsstelle -->
          <div class="role-group" :class="{ 'role-active': isOffice }">
            <v-checkbox
                v-model="isOffice"
                label="Geschäftsstelle"
                color="primary"
                true-icon="mdi-checkbox-marked"
                false-icon="mdi-checkbox-blank-outline"
                base-color="grey-darken-1"
                hide-details
                density="compact"
            ></v-checkbox>
          </div>

          <!-- 2. Abteilungsleitung -->
          <div class="role-group mt-2" :class="{ 'role-active': isDepartmentHead }">
            <v-checkbox
                v-model="isDepartmentHead"
                label="Abteilungsleitung"
                color="primary"
                true-icon="mdi-checkbox-marked"
                false-icon="mdi-checkbox-blank-outline"
                base-color="grey-darken-1"
                hide-details
                density="compact"
            ></v-checkbox>

            <v-expand-transition>
              <div v-if="isDepartmentHead" class="pl-8 pt-2">
                <v-select
                    v-model="selectedHeadDepartments"
                    :items="departments"
                    item-title="name"
                    item-value="id"
                    label="Abteilungen wählen"
                    multiple
                    chips
                    closable-chips
                    variant="outlined"
                    density="compact"
                    placeholder="Bitte wählen..."
                    :rules="[v => v.length > 0 || 'Bitte mindestens eine Abteilung wählen']"
                    no-data-text="Keine Abteilungen geladen"
                ></v-select>
              </div>
            </v-expand-transition>
          </div>

          <!-- 3. Übungsleiter -->
          <div class="role-group mt-2" :class="{ 'role-active': isTrainer }">
            <v-checkbox
                v-model="isTrainer"
                label="Übungsleiter"
                color="primary"
                true-icon="mdi-checkbox-marked"
                false-icon="mdi-checkbox-blank-outline"
                base-color="grey-darken-1"
                hide-details
                density="compact"
            ></v-checkbox>

            <v-expand-transition>
              <div v-if="isTrainer" class="pl-8 pt-2">
                <v-select
                    v-model="selectedTrainerDepartments"
                    :items="departments"
                    item-title="name"
                    item-value="id"
                    label="Abteilungen wählen"
                    multiple
                    chips
                    closable-chips
                    variant="outlined"
                    density="compact"
                    placeholder="Bitte wählen..."
                    :rules="[v => v.length > 0 || 'Bitte mindestens eine Abteilung wählen']"
                    no-data-text="Keine Abteilungen geladen"
                ></v-select>
              </div>
            </v-expand-transition>
          </div>

          <!-- Submit Button -->
          <div class="d-flex justify-center mt-6">
            <v-btn
                color="primary"
                class="mx-auto submit-btn"
                style="min-width:160px"
                type="submit"
                :loading="isLoading"
                :disabled="isLoading"
            >
              Benutzer erstellen
            </v-btn>
          </div>

        </v-form>
      </v-card-text>
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

.submit-btn {
  font-weight: 600;
}

.role-group {
  border-radius: 8px;
  padding: 4px; /* Etwas Platz für den Hintergrund */
  transition: background-color 0.2s ease;
}

.role-active {
  background-color: rgba(25, 118, 210, 0.08); /* Sehr helles Blau als Hintergrund */
}

/* Label fett machen, wenn aktiv */
.role-active :deep(.v-label) {
  font-weight: 600;
  color: #1565C0; /* Etwas dunkleres Blau für den Text */
  opacity: 1;
}
</style>