<template>
  <form @submit.prevent="handleSubmit">
    <div class="grid">
      <div class="col-12 md:col-6">
        <label for="first_name">Prénom *</label>
        <InputText 
          id="first_name" 
          v-model="form.first_name" 
          class="w-full"
          :class="{ 'p-invalid': errors.first_name }"
        />
        <small class="p-error" v-if="errors.first_name">{{ errors.first_name }}</small>
      </div>

      <div class="col-12 md:col-6">
        <label for="last_name">Nom *</label>
        <InputText 
          id="last_name" 
          v-model="form.last_name" 
          class="w-full"
          :class="{ 'p-invalid': errors.last_name }"
        />
        <small class="p-error" v-if="errors.last_name">{{ errors.last_name }}</small>
      </div>

      <div class="col-12 md:col-6">
        <label for="email">Email *</label>
        <InputText 
          id="email" 
          v-model="form.email" 
          type="email"
          class="w-full"
          :class="{ 'p-invalid': errors.email }"
        />
        <small class="p-error" v-if="errors.email">{{ errors.email }}</small>
      </div>

      <div class="col-12 md:col-6">
        <label for="phone">Téléphone</label>
        <InputText 
          id="phone" 
          v-model="form.phone" 
          class="w-full"
          :class="{ 'p-invalid': errors.phone }"
        />
        <small class="p-error" v-if="errors.phone">{{ errors.phone }}</small>
      </div>

      <div class="col-12 md:col-6">
        <label for="password">Mot de passe</label>
        <Password 
          id="password" 
          v-model="form.password" 
          class="w-full"
          :class="{ 'p-invalid': errors.password }"
          toggleMask
          :strongRegex="passwordStrongRegex"
          promptLabel="Entrez un mot de passe"
          weakLabel="Trop simple"
          mediumLabel="Moyenne complexité"
          strongLabel="Mot de passe complexe"
          placeholder="Laisser vide pour ne pas changer"
        />
        <small class="p-error" v-if="errors.password">{{ errors.password }}</small>
      </div>

      <div class="col-12 md:col-6">
        <label for="password_confirmation">Confirmer le mot de passe</label>
        <Password 
          id="password_confirmation" 
          v-model="form.password_confirmation" 
          class="w-full"
          :class="{ 'p-invalid': errors.password_confirmation }"
          toggleMask
          feedback="false"
          placeholder="Laisser vide pour ne pas changer"
        />
        <small class="p-error" v-if="errors.password_confirmation">{{ errors.password_confirmation }}</small>
      </div>

      <div class="col-12 md:col-6" v-if="canManageRoles">
        <label for="role">Rôle *</label>
        <Dropdown 
          id="role" 
          v-model="form.role" 
          :options="roles" 
          optionLabel="label" 
          optionValue="value"
          placeholder="Sélectionner un rôle"
          class="w-full"
          :class="{ 'p-invalid': errors.role }"
        />
        <small class="p-error" v-if="errors.role">{{ errors.role }}</small>
      </div>

      <div class="col-12 md:col-6" v-if="canManageStatus">
        <label for="status">Statut</label>
        <Dropdown 
          id="status" 
          v-model="form.status" 
          :options="statuses" 
          optionLabel="label" 
          optionValue="value"
          placeholder="Sélectionner un statut"
          class="w-full"
          :class="{ 'p-invalid': errors.status }"
        />
        <small class="p-error" v-if="errors.status">{{ errors.status }}</small>
      </div>
    </div>

    <div class="flex justify-content-between mt-4">
      <Button 
        label="Annuler" 
        severity="secondary" 
        outlined 
        @click="$emit('cancel')" 
      />
      <Button 
        label="Enregistrer" 
        type="submit" 
        :loading="loading" 
      />
    </div>
  </form>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useUsersStore } from '@/stores/users'
import { useAuthStore } from '@/stores/auth'
import { useToast } from 'primevue/usetoast'

const props = defineProps({
  user: {
    type: Object,
    required: true
  }
})

const emit = defineEmits(['updated', 'cancel'])

const usersStore = useUsersStore()
const authStore = useAuthStore()
const toast = useToast()

const loading = ref(false)
const errors = ref({})

const form = ref({
  first_name: '',
  last_name: '',
  email: '',
  phone: '',
  password: '',
  password_confirmation: '',
  role: '',
  status: 'active',
})

const roles = [
  { label: 'Admin', value: 'admin' },
  { label: 'Responsable RH', value: 'responsable_rh' },
  { label: 'Responsable Validation', value: 'responsable_validation' },
  { label: 'Bibliothécaire', value: 'bibliothecaire' },
  { label: 'Utilisateur', value: 'utilisateur' },
]

const statuses = [
  { label: 'Actif', value: 'active' },
  { label: 'Inactif', value: 'inactive' },
  { label: 'Suspendu', value: 'suspended' },
]

const canManageRoles = computed(() => {
  return authStore.hasRole('admin')
})

const canManageStatus = computed(() => {
  return authStore.hasRole('admin')
})

const passwordStrongRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#\$%\^&\*])(?=.{8,})/

const validateForm = () => {
  errors.value = {}

  if (!form.value.first_name) {
    errors.value.first_name = 'Le prénom est requis'
  }

  if (!form.value.last_name) {
    errors.value.last_name = 'Le nom est requis'
  }

  if (!form.value.email) {
    errors.value.email = 'L\'email est requis'
  } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.value.email)) {
    errors.value.email = 'L\'email est invalide'
  }

  if (form.value.password && form.value.password.length < 8) {
    errors.value.password = 'Le mot de passe doit contenir au moins 8 caractères'
  }

  if (form.value.password && form.value.password !== form.value.password_confirmation) {
    errors.value.password_confirmation = 'Les mots de passe ne correspondent pas'
  }

  if (canManageRoles.value && !form.value.role) {
    errors.value.role = 'Le rôle est requis'
  }

  return Object.keys(errors.value).length === 0
}

const handleSubmit = async () => {
  if (!validateForm()) {
    return
  }

  loading.value = true

  try {
    const updateData = {
      first_name: form.value.first_name,
      last_name: form.value.last_name,
      email: form.value.email,
      phone: form.value.phone,
      status: form.value.status,
    }

    if (form.value.password) {
      updateData.password = form.value.password
      updateData.password_confirmation = form.value.password_confirmation
    }

    if (canManageRoles.value) {
      updateData.role = form.value.role
    }

    await usersStore.updateUser(props.user.id, updateData)

    emit('updated')
  } catch (error) {
    if (error.response?.data?.errors) {
      errors.value = error.response.data.errors
    } else {
      toast.add({
        severity: 'error',
        summary: 'Erreur',
        detail: error.message || 'Erreur lors de la modification de l\'utilisateur',
        life: 3000
      })
    }
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  form.value = {
    first_name: props.user.first_name,
    last_name: props.user.last_name,
    email: props.user.email,
    phone: props.user.phone || '',
    password: '',
    password_confirmation: '',
    role: props.user.roles?.[0]?.name || '',
    status: props.user.status,
  }
})
</script>

<style scoped>
label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: 600;
}

.grid {
  display: grid;
  grid-template-columns: repeat(12, 1fr);
  gap: 1rem;
}

.col-12 {
  grid-column: span 12;
}

@media (min-width: 768px) {
  .col-12.md\:col-6 {
    grid-column: span 6;
  }
}

.mt-4 {
  margin-top: 1rem;
}

.p-error {
  color: #ef4444;
  font-size: 0.875rem;
  margin-top: 0.25rem;
}
</style>
