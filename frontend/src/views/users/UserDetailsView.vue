<template>
  <div class="user-details">
    <div class="grid">
      <!-- User Info Card -->
      <div class="col-12 md:col-4">
        <Card>
          <template #title>
            <div class="flex flex-column align-items-center">
              <Avatar 
                :image="user.avatar" 
                :label="user.full_name.substring(0, 2).toUpperCase()"
                shape="circle" 
                size="xlarge"
                class="mb-3"
              />
              <h3>{{ user.full_name }}</h3>
              <Tag 
                :value="getRoleLabel(user.roles[0]?.name)"
                :severity="getRoleSeverity(user.roles[0]?.name)"
              />
            </div>
          </template>
          <template #content>
            <div class="user-info">
              <div class="info-item">
                <strong>Email:</strong> {{ user.email }}
              </div>
              <div class="info-item">
                <strong>Téléphone:</strong> {{ user.phone || 'N/A' }}
              </div>
              <div class="info-item">
                <strong>Statut:</strong>
                <Tag 
                  :value="getStatusLabel(user.status)"
                  :severity="getStatusSeverity(user.status)"
                />
              </div>
              <div class="info-item">
                <strong>Email vérifié:</strong>
                <Tag 
                  :value="user.email_verified_at ? 'Oui' : 'Non'"
                  :severity="user.email_verified_at ? 'success' : 'warning'"
                />
              </div>
              <div class="info-item">
                <strong>Dernière connexion:</strong>
                {{ formatDate(user.last_login_at) }}
              </div>
              <div class="info-item">
                <strong>Créé le:</strong>
                {{ formatDate(user.created_at) }}
              </div>
              <div class="info-item">
                <strong>Mis à jour le:</strong>
                {{ formatDate(user.updated_at) }}
              </div>
            </div>
          </template>
        </Card>
      </div>

      <!-- Statistics Card -->
      <div class="col-12 md:col-4">
        <Card>
          <template #title>Statistiques</template>
          <template #content>
            <div class="statistics">
              <div class="stat-item">
                <div class="stat-value">{{ statistics?.deposit_requests_count || 0 }}</div>
                <div class="stat-label">Demandes de dépôt</div>
              </div>
              <div class="stat-item">
                <div class="stat-value">{{ statistics?.uploaded_references_count || 0 }}</div>
                <div class="stat-label">Références uploadées</div>
              </div>
              <div class="stat-item">
                <div class="stat-value">{{ statistics?.reviews_count || 0 }}</div>
                <div class="stat-label">Avis effectués</div>
              </div>
            </div>
          </template>
        </Card>
      </div>

      <!-- Roles & Permissions Card -->
      <div class="col-12 md:col-4">
        <Card>
          <template #title>Rôles & Permissions</template>
          <template #content>
            <div class="roles-permissions">
              <div class="section">
                <h4>Rôles</h4>
                <div class="tags">
                  <Tag 
                    v-for="role in user.roles" 
                    :key="role.id"
                    :value="role.name"
                    :severity="getRoleSeverity(role.name)"
                    class="mr-2 mb-2"
                  />
                </div>
              </div>
              <div class="section">
                <h4>Permissions</h4>
                <div class="tags">
                  <Tag 
                    v-for="permission in user.permissions" 
                    :key="permission.id"
                    :value="permission.name"
                    severity="info"
                    class="mr-2 mb-2"
                  />
                </div>
              </div>
            </div>
          </template>
        </Card>
      </div>
    </div>

    <div class="flex justify-content-between mt-4">
      <Button 
        label="Fermer" 
        severity="secondary" 
        outlined 
        @click="$emit('close')" 
      />
      <Button 
        label="Modifier" 
        icon="pi pi-pencil"
        @click="editUser"
        v-if="canEditUser"
      />
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useAuthStore } from '@/stores/auth'

const props = defineProps({
  user: {
    type: Object,
    required: true
  },
  statistics: {
    type: Object,
    default: () => ({})
  }
})

const emit = defineEmits(['close', 'edit'])

const authStore = useAuthStore()

const canEditUser = computed(() => {
  return authStore.hasRole('admin') || 
         (authStore.hasRole('responsable_rh') && !authStore.hasRole('admin', props.user))
})

const editUser = () => {
  emit('edit', props.user)
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
</script>

<style scoped>
.user-details {
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
}

.user-info {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.info-item {
  padding: 0.5rem 0;
  border-bottom: 1px solid #e0e0e0;
}

.info-item:last-child {
  border-bottom: none;
}

.statistics {
  display: grid;
  grid-template-columns: repeat(1, 1fr);
  gap: 1rem;
}

.stat-item {
  text-align: center;
  padding: 1rem;
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

.roles-permissions {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.section h4 {
  margin-bottom: 0.5rem;
  color: #333;
}

.tags {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
}

.mt-4 {
  margin-top: 1rem;
}

.mb-3 {
  margin-bottom: 1rem;
}

.mr-2 {
  margin-right: 0.5rem;
}

.mb-2 {
  margin-bottom: 0.5rem;
}
</style>
