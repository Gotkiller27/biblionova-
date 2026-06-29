<template>
  <div class="users-list">
    <Card>
      <template #title>
        <div class="flex justify-between items-center">
          <h2>Gestion des Utilisateurs</h2>
          <Button 
            label="Nouvel Utilisateur" 
            icon="pi pi-plus" 
            @click="showCreateDialog = true"
            v-if="canCreateUsers"
          />
        </div>
      </template>

      <template #content>
        <!-- Filters -->
        <div class="filters mb-4 p-4 surface-0 border-round">
          <div class="grid">
            <div class="col-12 md:col-3">
              <InputText 
                v-model="filters.search" 
                placeholder="Rechercher..." 
                class="w-full"
                @input="debouncedSearch"
              />
            </div>
            <div class="col-12 md:col-2">
              <Dropdown 
                v-model="filters.role" 
                :options="roles" 
                optionLabel="label" 
                optionValue="value"
                placeholder="Rôle" 
                class="w-full"
                showClear
                @change="applyFilters"
              />
            </div>
            <div class="col-12 md:col-2">
              <Dropdown 
                v-model="filters.status" 
                :options="statuses" 
                optionLabel="label" 
                optionValue="value"
                placeholder="Statut" 
                class="w-full"
                showClear
                @change="applyFilters"
              />
            </div>
            <div class="col-12 md:col-2">
              <Button 
                label="Exporter" 
                icon="pi pi-download" 
                @click="exportUsers"
                class="w-full"
              />
            </div>
            <div class="col-12 md:col-3">
              <div class="flex gap-2">
                <Button 
                  icon="pi pi-refresh" 
                  @click="loadUsers"
                  :loading="loading"
                />
                <Button 
                  icon="pi pi-filter-slash" 
                  @click="resetFilters"
                  label="Réinitialiser"
                  outlined
                />
              </div>
            </div>
          </div>
        </div>

        <!-- DataTable -->
        <DataTable 
          :value="users" 
          :loading="loading"
          paginator 
          :rows="pagination.per_page"
          :totalRecords="pagination.total"
          :lazy="true"
          @page="onPageChange"
          @sort="onSort"
          sortMode="multiple"
          :rowsPerPageOptions="[10, 15, 25, 50]"
          stripedRows
          responsiveLayout="scroll"
        >
          <Column field="avatar" header="Avatar" style="min-width: 80px">
            <template #body="{ data }">
              <Avatar 
                :image="data.avatar" 
                :label="data.full_name.substring(0, 2).toUpperCase()"
                shape="circle" 
                size="large"
              />
            </template>
          </Column>
          
          <Column field="full_name" header="Nom" sortable>
            <template #body="{ data }">
              <div class="font-semibold">{{ data.full_name }}</div>
            </template>
          </Column>
          
          <Column field="email" header="Email" sortable />
          
          <Column field="phone" header="Téléphone" />
          
          <Column field="roles" header="Rôle" sortable>
            <template #body="{ data }">
              <Tag 
                v-if="data.roles && data.roles.length > 0"
                :value="getRoleLabel(data.roles[0].name)"
                :severity="getRoleSeverity(data.roles[0].name)"
              />
            </template>
          </Column>
          
          <Column field="status" header="Statut" sortable>
            <template #body="{ data }">
              <Tag 
                :value="getStatusLabel(data.status)"
                :severity="getStatusSeverity(data.status)"
              />
            </template>
          </Column>
          
          <Column field="last_login_at" header="Dernière Connexion" sortable>
            <template #body="{ data }">
              {{ formatDate(data.last_login_at) }}
            </template>
          </Column>
          
          <Column field="created_at" header="Créé le" sortable>
            <template #body="{ data }">
              {{ formatDate(data.created_at) }}
            </template>
          </Column>
          
          <Column header="Actions" style="min-width: 200px">
            <template #body="{ data }">
              <div class="flex gap-2">
                <Button 
                  icon="pi pi-eye" 
                  size="small" 
                  outlined 
                  @click="viewUser(data)"
                  v-tooltip.top="Voir"
                />
                <Button 
                  icon="pi pi-pencil" 
                  size="small" 
                  outlined 
                  @click="editUser(data)"
                  v-if="canEditUser(data)"
                  v-tooltip.top="Modifier"
                />
                <Button 
                  icon="pi pi-ban" 
                  size="small" 
                  outlined 
                  severity="warning"
                  @click="updateUserStatus(data, data.status === 'active' ? 'inactive' : 'active')"
                  v-if="canUpdateStatus"
                  v-tooltip.top="Activer/Désactiver"
                />
                <Button 
                  icon="pi pi-trash" 
                  size="small" 
                  outlined 
                  severity="danger"
                  @click="confirmDelete(data)"
                  v-if="canDeleteUser(data)"
                  v-tooltip.top="Supprimer"
                />
                <Button 
                  icon="pi pi-undo" 
                  size="small" 
                  outlined 
                  severity="success"
                  @click="restoreUser(data)"
                  v-if="data.deleted_at"
                  v-tooltip.top="Restaurer"
                />
              </div>
            </template>
          </Column>
        </DataTable>
      </template>
    </Card>

    <!-- Create User Dialog -->
    <Dialog 
      v-model:visible="showCreateDialog" 
      header="Créer un Utilisateur" 
      :style="{ width: '50vw' }"
      :modal="true"
    >
      <UserCreateForm @created="onUserCreated" @cancel="showCreateDialog = false" />
    </Dialog>

    <!-- Edit User Dialog -->
    <Dialog 
      v-model:visible="showEditDialog" 
      header="Modifier l'Utilisateur" 
      :style="{ width: '50vw' }"
      :modal="true"
    >
      <UserEditForm 
        v-if="selectedUser" 
        :user="selectedUser" 
        @updated="onUserUpdated" 
        @cancel="showEditDialog = false" 
      />
    </Dialog>

    <!-- View User Dialog -->
    <Dialog 
      v-model:visible="showViewDialog" 
      header="Détails de l'Utilisateur" 
      :style="{ width: '60vw' }"
      :modal="true"
    >
      <UserDetailsView 
        v-if="selectedUser" 
        :user="selectedUser" 
        @close="showViewDialog = false" 
      />
    </Dialog>

    <!-- Delete Confirmation Dialog -->
    <ConfirmDialog />
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useUsersStore } from '@/stores/users'
import { useAuthStore } from '@/stores/auth'
import { useConfirm } from 'primevue/useconfirm'
import { useToast } from 'primevue/usetoast'
import UserCreateForm from './UserCreateForm.vue'
import UserEditForm from './UserEditForm.vue'
import UserDetailsView from './UserDetailsView.vue'

const usersStore = useUsersStore()
const authStore = useAuthStore()
const confirm = useConfirm()
const toast = useToast()

const showCreateDialog = ref(false)
const showEditDialog = ref(false)
const showViewDialog = ref(false)
const selectedUser = ref(null)

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

const users = computed(() => usersStore.users)
const loading = computed(() => usersStore.loading)
const pagination = computed(() => usersStore.pagination)
const filters = computed({
  get: () => usersStore.filters,
  set: (value) => usersStore.setFilters(value)
})

const canCreateUsers = computed(() => {
  return authStore.hasRole('admin') || authStore.hasRole('responsable_rh')
})

const canEditUser = (user) => {
  return authStore.hasRole('admin') || 
         (authStore.hasRole('responsable_rh') && !authStore.hasRole('admin', user))
}

const canDeleteUser = (user) => {
  return authStore.hasRole('admin') && user.id !== authStore.user.id
}

const canUpdateStatus = computed(() => {
  return authStore.hasRole('admin')
})

let searchTimeout = null

const debouncedSearch = () => {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    applyFilters()
  }, 500)
}

const applyFilters = () => {
  usersStore.setFilters(filters.value)
  loadUsers()
}

const resetFilters = () => {
  usersStore.resetFilters()
  loadUsers()
}

const loadUsers = async () => {
  try {
    await usersStore.fetchUsers(pagination.value.current_page)
  } catch (error) {
    toast.add({
      severity: 'error',
      summary: 'Erreur',
      detail: error.message || 'Erreur lors du chargement des utilisateurs',
      life: 3000
    })
  }
}

const onPageChange = (event) => {
  usersStore.pagination.current_page = event.page + 1
  usersStore.pagination.per_page = event.rows
  loadUsers()
}

const onSort = (event) => {
  const sortField = event.sortField
  const sortOrder = event.sortOrder === 1 ? 'asc' : 'desc'
  usersStore.setSort(sortField, sortOrder)
  loadUsers()
}

const viewUser = (user) => {
  selectedUser.value = user
  showViewDialog.value = true
}

const editUser = (user) => {
  selectedUser.value = user
  showEditDialog.value = true
}

const confirmDelete = (user) => {
  confirm.require({
    message: `Êtes-vous sûr de vouloir supprimer l'utilisateur ${user.full_name}?`,
    header: 'Confirmation de suppression',
    icon: 'pi pi-exclamation-triangle',
    accept: () => deleteUser(user),
    reject: () => {}
  })
}

const deleteUser = async (user) => {
  try {
    await usersStore.deleteUser(user.id)
    toast.add({
      severity: 'success',
      summary: 'Succès',
      detail: 'Utilisateur supprimé avec succès',
      life: 3000
    })
    loadUsers()
  } catch (error) {
    toast.add({
      severity: 'error',
      summary: 'Erreur',
      detail: error.message || 'Erreur lors de la suppression',
      life: 3000
    })
  }
}

const restoreUser = async (user) => {
  try {
    await usersStore.restoreUser(user.id)
    toast.add({
      severity: 'success',
      summary: 'Succès',
      detail: 'Utilisateur restauré avec succès',
      life: 3000
    })
    loadUsers()
  } catch (error) {
    toast.add({
      severity: 'error',
      summary: 'Erreur',
      detail: error.message || 'Erreur lors de la restauration',
      life: 3000
    })
  }
}

const updateUserStatus = async (user, status) => {
  try {
    await usersStore.updateStatus(user.id, status)
    toast.add({
      severity: 'success',
      summary: 'Succès',
      detail: 'Statut mis à jour avec succès',
      life: 3000
    })
    loadUsers()
  } catch (error) {
    toast.add({
      severity: 'error',
      summary: 'Erreur',
      detail: error.message || 'Erreur lors de la mise à jour du statut',
      life: 3000
    })
  }
}

const exportUsers = async () => {
  try {
    const response = await usersStore.exportUsers('xlsx')
    
    const url = window.URL.createObjectURL(new Blob([response.data]))
    const link = document.createElement('a')
    link.href = url
    link.setAttribute('download', `users_${new Date().toISOString().split('T')[0]}.xlsx`)
    document.body.appendChild(link)
    link.click()
    link.remove()
    
    toast.add({
      severity: 'success',
      summary: 'Succès',
      detail: 'Export réussi',
      life: 3000
    })
  } catch (error) {
    toast.add({
      severity: 'error',
      summary: 'Erreur',
      detail: error.message || 'Erreur lors de l\'export',
      life: 3000
    })
  }
}

const onUserCreated = () => {
  showCreateDialog.value = false
  loadUsers()
  toast.add({
    severity: 'success',
    summary: 'Succès',
    detail: 'Utilisateur créé avec succès',
    life: 3000
  })
}

const onUserUpdated = () => {
  showEditDialog.value = false
  loadUsers()
  toast.add({
    severity: 'success',
    summary: 'Succès',
    detail: 'Utilisateur mis à jour avec succès',
    life: 3000
  })
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
  loadUsers()
})
</script>

<style scoped>
.users-list {
  padding: 1rem;
}

.filters {
  background: #f8f9fa;
}
</style>
