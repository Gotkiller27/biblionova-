<template>
  <div class="user-profile">
    <Card>
      <template #title>
        <div class="flex justify-between items-center">
          <h2>Mon Profil</h2>
          <Button 
            label="Modifier" 
            icon="pi pi-pencil" 
            @click="showEditDialog = true"
          />
        </div>
      </template>

      <template #content>
        <div class="grid">
          <!-- Avatar Section -->
          <div class="col-12 md:col-4">
            <div class="avatar-section">
              <Avatar 
                :image="profile.avatar" 
                :label="profile.full_name.substring(0, 2).toUpperCase()"
                shape="circle" 
                size="xlarge"
                class="mb-3"
              />
              <div class="avatar-actions">
                <Button 
                  label="Changer l'avatar" 
                  icon="pi pi-upload" 
                  size="small"
                  outlined
                  @click="showAvatarDialog = true"
                />
                <Button 
                  label="Supprimer" 
                  icon="pi pi-trash" 
                  size="small"
                  outlined
                  severity="danger"
                  @click="confirmDeleteAvatar"
                  v-if="profile.avatar"
                />
              </div>
            </div>
          </div>

          <!-- Profile Info -->
          <div class="col-12 md:col-8">
            <div class="profile-info">
              <div class="info-group">
                <label>Nom complet</label>
                <div class="info-value">{{ profile.full_name }}</div>
              </div>
              <div class="info-group">
                <label>Email</label>
                <div class="info-value">{{ profile.email }}</div>
              </div>
              <div class="info-group">
                <label>Téléphone</label>
                <div class="info-value">{{ profile.phone || 'Non renseigné' }}</div>
              </div>
              <div class="info-group">
                <label>Rôle</label>
                <Tag 
                  :value="getRoleLabel(profile.roles[0]?.name)"
                  :severity="getRoleSeverity(profile.roles[0]?.name)"
                />
              </div>
              <div class="info-group">
                <label>Statut</label>
                <Tag 
                  :value="getStatusLabel(profile.status)"
                  :severity="getStatusSeverity(profile.status)"
                />
              </div>
              <div class="info-group">
                <label>Dernière connexion</label>
                <div class="info-value">{{ formatDate(profile.last_login_at) }}</div>
              </div>
              <div class="info-group">
                <label>Membre depuis</label>
                <div class="info-value">{{ formatDate(profile.created_at) }}</div>
              </div>
            </div>
          </div>
        </div>

        <!-- Statistics -->
        <div class="statistics-section mt-4">
          <h3>Statistiques</h3>
          <div class="statistics-grid">
            <div class="stat-card">
              <div class="stat-value">{{ statistics?.deposit_requests_count || 0 }}</div>
              <div class="stat-label">Demandes de dépôt</div>
            </div>
            <div class="stat-card">
              <div class="stat-value">{{ statistics?.uploaded_references_count || 0 }}</div>
              <div class="stat-label">Références uploadées</div>
            </div>
            <div class="stat-card">
              <div class="stat-value">{{ statistics?.reviews_count || 0 }}</div>
              <div class="stat-label">Avis effectués</div>
            </div>
          </div>
        </div>

        <!-- Change Password Section -->
        <div class="password-section mt-4">
          <h3>Changer le mot de passe</h3>
          <Button 
            label="Changer le mot de passe" 
            icon="pi pi-key" 
            outlined
            @click="showPasswordDialog = true"
          />
        </div>
      </template>
    </Card>

    <!-- Edit Profile Dialog -->
    <Dialog 
      v-model:visible="showEditDialog" 
      header="Modifier mon profil" 
      :style="{ width: '50vw' }"
      :modal="true"
    >
      <form @submit.prevent="updateProfile">
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
        </div>

        <div class="flex justify-content-between mt-4">
          <Button 
            label="Annuler" 
            severity="secondary" 
            outlined 
            @click="showEditDialog = false" 
          />
          <Button 
            label="Enregistrer" 
            type="submit" 
            :loading="loading" 
          />
        </div>
      </form>
    </Dialog>

    <!-- Upload Avatar Dialog -->
    <Dialog 
      v-model:visible="showAvatarDialog" 
      header="Changer l'avatar" 
      :style="{ width: '30vw' }"
      :modal="true"
    >
      <form @submit.prevent="uploadAvatar">
        <div class="avatar-upload">
          <FileUpload 
            mode="basic" 
            accept="image/*" 
            :maxFileSize="5242880"
            @select="onFileSelect"
            :auto="false"
            chooseLabel="Choisir une image"
          />
          <small class="text-color-secondary">Formats acceptés: JPG, JPEG, PNG, WEBP (max 5Mo)</small>
        </div>

        <div class="flex justify-content-between mt-4">
          <Button 
            label="Annuler" 
            severity="secondary" 
            outlined 
            @click="showAvatarDialog = false" 
          />
          <Button 
            label="Télécharger" 
            type="submit" 
            :loading="loading" 
            :disabled="!selectedFile"
          />
        </div>
      </form>
    </Dialog>

    <!-- Change Password Dialog -->
    <Dialog 
      v-model:visible="showPasswordDialog" 
      header="Changer le mot de passe" 
      :style="{ width: '40vw' }"
      :modal="true"
    >
      <form @submit.prevent="changePassword">
        <div class="grid">
          <div class="col-12">
            <label for="current_password">Mot de passe actuel *</label>
            <Password 
              id="current_password" 
              v-model="passwordForm.current_password" 
              class="w-full"
              :class="{ 'p-invalid': passwordErrors.current_password }"
              toggleMask
              feedback="false"
            />
            <small class="p-error" v-if="passwordErrors.current_password">{{ passwordErrors.current_password }}</small>
          </div>

          <div class="col-12">
            <label for="new_password">Nouveau mot de passe *</label>
            <Password 
              id="new_password" 
              v-model="passwordForm.new_password" 
              class="w-full"
              :class="{ 'p-invalid': passwordErrors.new_password }"
              toggleMask
              :strongRegex="passwordStrongRegex"
              promptLabel="Entrez un mot de passe"
              weakLabel="Trop simple"
              mediumLabel="Moyenne complexité"
              strongLabel="Mot de passe complexe"
            />
            <small class="p-error" v-if="passwordErrors.new_password">{{ passwordErrors.new_password }}</small>
          </div>

          <div class="col-12">
            <label for="new_password_confirmation">Confirmer le nouveau mot de passe *</label>
            <Password 
              id="new_password_confirmation" 
              v-model="passwordForm.new_password_confirmation" 
              class="w-full"
              :class="{ 'p-invalid': passwordErrors.new_password_confirmation }"
              toggleMask
              feedback="false"
            />
            <small class="p-error" v-if="passwordErrors.new_password_confirmation">{{ passwordErrors.new_password_confirmation }}</small>
          </div>
        </div>

        <div class="flex justify-content-between mt-4">
          <Button 
            label="Annuler" 
            severity="secondary" 
            outlined 
            @click="showPasswordDialog = false" 
          />
          <Button 
            label="Changer" 
            type="submit" 
            :loading="loading" 
          />
        </div>
      </form>
    </Dialog>

    <ConfirmDialog />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from '@/plugins/axios'
import { useAuthStore } from '@/stores/auth'
import { useConfirm } from 'primevue/useconfirm'
import { useToast } from 'primevue/usetoast'

const authStore = useAuthStore()
const confirm = useConfirm()
const toast = useToast()

const showEditDialog = ref(false)
const showAvatarDialog = ref(false)
const showPasswordDialog = ref(false)
const loading = ref(false)

const profile = ref({
  full_name: '',
  email: '',
  phone: '',
  avatar: '',
  status: '',
  last_login_at: null,
  created_at: null,
  roles: [],
})

const statistics = ref({})

const form = ref({
  first_name: '',
  last_name: '',
  email: '',
  phone: '',
})

const errors = ref({})

const passwordForm = ref({
  current_password: '',
  new_password: '',
  new_password_confirmation: '',
})

const passwordErrors = ref({})

const selectedFile = ref(null)

const passwordStrongRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#\$%\^&\*])(?=.{8,})/

const loadProfile = async () => {
  try {
    const response = await axios.get('/api/v1/profile')
    profile.value = response.data.data.user
    statistics.value = response.data.data.statistics
  } catch (error) {
    toast.add({
      severity: 'error',
      summary: 'Erreur',
      detail: 'Erreur lors du chargement du profil',
      life: 3000
    })
  }
}

const updateProfile = async () => {
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

  if (Object.keys(errors.value).length > 0) {
    return
  }

  loading.value = true

  try {
    await axios.put('/api/v1/profile', form.value)
    showEditDialog.value = false
    await loadProfile()
    await authStore.me()
    toast.add({
      severity: 'success',
      summary: 'Succès',
      detail: 'Profil mis à jour avec succès',
      life: 3000
    })
  } catch (error) {
    if (error.response?.data?.errors) {
      errors.value = error.response.data.errors
    } else {
      toast.add({
        severity: 'error',
        summary: 'Erreur',
        detail: 'Erreur lors de la mise à jour du profil',
        life: 3000
      })
    }
  } finally {
    loading.value = false
  }
}

const onFileSelect = (event) => {
  selectedFile.value = event.files[0]
}

const uploadAvatar = async () => {
  if (!selectedFile.value) {
    return
  }

  loading.value = true

  try {
    const formData = new FormData()
    formData.append('avatar', selectedFile.value)

    await axios.post('/api/v1/profile/avatar', formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    })

    showAvatarDialog.value = false
    selectedFile.value = null
    await loadProfile()
    await authStore.me()
    toast.add({
      severity: 'success',
      summary: 'Succès',
      detail: 'Avatar mis à jour avec succès',
      life: 3000
    })
  } catch (error) {
    toast.add({
      severity: 'error',
      summary: 'Erreur',
      detail: error.response?.data?.message || 'Erreur lors du téléchargement de l\'avatar',
      life: 3000
    })
  } finally {
    loading.value = false
  }
}

const confirmDeleteAvatar = () => {
  confirm.require({
    message: 'Êtes-vous sûr de vouloir supprimer votre avatar?',
    header: 'Confirmation de suppression',
    icon: 'pi pi-exclamation-triangle',
    accept: deleteAvatar,
    reject: () => {}
  })
}

const deleteAvatar = async () => {
  try {
    await axios.delete('/api/v1/profile/avatar')
    await loadProfile()
    await authStore.me()
    toast.add({
      severity: 'success',
      summary: 'Succès',
      detail: 'Avatar supprimé avec succès',
      life: 3000
    })
  } catch (error) {
    toast.add({
      severity: 'error',
      summary: 'Erreur',
      detail: 'Erreur lors de la suppression de l\'avatar',
      life: 3000
    })
  }
}

const changePassword = async () => {
  passwordErrors.value = {}

  if (!passwordForm.value.current_password) {
    passwordErrors.value.current_password = 'Le mot de passe actuel est requis'
  }

  if (!passwordForm.value.new_password) {
    passwordErrors.value.new_password = 'Le nouveau mot de passe est requis'
  } else if (passwordForm.value.new_password.length < 8) {
    passwordErrors.value.new_password = 'Le mot de passe doit contenir au moins 8 caractères'
  }

  if (passwordForm.value.new_password !== passwordForm.value.new_password_confirmation) {
    passwordErrors.value.new_password_confirmation = 'Les mots de passe ne correspondent pas'
  }

  if (Object.keys(passwordErrors.value).length > 0) {
    return
  }

  loading.value = true

  try {
    await axios.post('/api/v1/profile/change-password', passwordForm.value)
    showPasswordDialog.value = false
    passwordForm.value = {
      current_password: '',
      new_password: '',
      new_password_confirmation: '',
    }
    toast.add({
      severity: 'success',
      summary: 'Succès',
      detail: 'Mot de passe changé avec succès',
      life: 3000
    })
  } catch (error) {
    if (error.response?.data?.message) {
      passwordErrors.value.current_password = error.response.data.message
    } else {
      toast.add({
        severity: 'error',
        summary: 'Erreur',
        detail: 'Erreur lors du changement de mot de passe',
        life: 3000
      })
    }
  } finally {
    loading.value = false
  }
}

const getRoleLabel = (role) => {
  const roleMap = {
    admin: 'Admin',
    responsable_rh: 'Resp. RH',
    responsable_validation: 'Resp. Validation',
    bibliothecaire: 'Bibliothécaire',
    utilisateur: 'Utilisateur'
  }
  return roleMap[role] || role
}

const getRoleSeverity = (role) => {
  const severityMap = {
    admin: 'danger',
    responsable_rh: 'warning',
    responsable_validation: 'info',
    bibliothecaire: 'success',
    utilisateur: 'secondary'
  }
  return severityMap[role] || 'secondary'
}

const getStatusLabel = (status) => {
  const statusMap = {
    active: 'Actif',
    inactive: 'Inactif',
    suspended: 'Suspendu'
  }
  return statusMap[status] || status
}

const getStatusSeverity = (status) => {
  const severityMap = {
    active: 'success',
    inactive: 'secondary',
    suspended: 'danger'
  }
  return severityMap[status] || 'secondary'
}

const formatDate = (date) => {
  if (!date) return 'Jamais'
  return new Date(date).toLocaleDateString('fr-FR', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

onMounted(() => {
  loadProfile()
})
</script>

<style scoped>
.user-profile {
  padding: 1rem;
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
  .col-12.md\:col-4 {
    grid-column: span 4;
  }
  .col-12.md\:col-8 {
    grid-column: span 8;
  }
  .col-12.md\:col-6 {
    grid-column: span 6;
  }
}

.avatar-section {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 1rem;
}

.avatar-actions {
  display: flex;
  gap: 0.5rem;
}

.profile-info {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 1rem;
}

.info-group {
  padding: 0.5rem;
  background: #f8f9fa;
  border-radius: 8px;
}

.info-group label {
  display: block;
  font-size: 0.875rem;
  color: #666;
  margin-bottom: 0.25rem;
}

.info-value {
  font-weight: 600;
  color: #333;
}

.statistics-section h3,
.password-section h3 {
  margin-bottom: 1rem;
  color: #333;
}

.statistics-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 1rem;
}

.stat-card {
  text-align: center;
  padding: 1.5rem;
  background: #f8f9fa;
  border-radius: 8px;
}

.stat-value {
  font-size: 2rem;
  font-weight: bold;
  color: #4472C4;
}

.stat-label {
  font-size: 0.875rem;
  color: #666;
  margin-top: 0.5rem;
}

.avatar-upload {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.mt-4 {
  margin-top: 1rem;
}

.mb-3 {
  margin-bottom: 1rem;
}

.text-color-secondary {
  color: #6c757d;
  font-size: 0.875rem;
}
</style>
