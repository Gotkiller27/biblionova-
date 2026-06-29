<template>
  <div class="authors-list">
    <Card>
      <template #title>
        <div class="flex justify-between items-center">
          <h2>Gestion des Auteurs</h2>
          <Button 
            label="Nouvel Auteur" 
            icon="pi pi-plus" 
            @click="showCreateDialog = true"
            v-if="canCreateAuthors"
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
                v-model="filters.nationality" 
                :options="nationalityOptions" 
                optionLabel="label" 
                optionValue="value"
                placeholder="Nationalité" 
                class="w-full"
                showClear
                @change="applyFilters"
              />
            </div>
            <div class="col-12 md:col-2">
              <Dropdown 
                v-model="filters.status" 
                :options="statusOptions" 
                optionLabel="label" 
                optionValue="value"
                placeholder="Statut" 
                class="w-full"
                showClear
                @change="applyFilters"
              />
            </div>
            <div class="col-12 md:col-3">
              <div class="flex gap-2">
                <Button 
                  icon="pi pi-refresh" 
                  @click="loadAuthors"
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
          :value="authors" 
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
          <Column field="photo" header="Photo" style="min-width: 80px">
            <template #body="{ data }">
              <Avatar 
                :image="data.photo_url" 
                :label="data.full_name.substring(0, 2).toUpperCase()"
                shape="circle" 
                size="large"
              />
            </template>
          </Column>
          
          <Column field="full_name" header="Nom complet" sortable>
            <template #body="{ data }">
              <div class="font-semibold">{{ data.full_name }}</div>
            </template>
          </Column>
          
          <Column field="nationality" header="Nationalité" sortable />
          
          <Column field="birth_date" header="Date de naissance" sortable>
            <template #body="{ data }">
              {{ formatDate(data.birth_date) }}
            </template>
          </Column>
          
          <Column field="death_date" header="Date de décès" sortable>
            <template #body="{ data }">
              {{ data.death_date ? formatDate(data.death_date) : '-' }}
            </template>
          </Column>
          
          <Column field="age" header="Âge" sortable>
            <template #body="{ data }">
              {{ data.age ? data.age + ' ans' : '-' }}
            </template>
          </Column>
          
          <Column field="references_count" header="Références" sortable>
            <template #body="{ data }">
              <Tag :value="data.references_count" severity="info" />
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
                  @click="viewAuthor(data)"
                  v-tooltip.top="Voir"
                />
                <Button 
                  icon="pi pi-pencil" 
                  size="small" 
                  outlined 
                  @click="editAuthor(data)"
                  v-if="canEditAuthor"
                  v-tooltip.top="Modifier"
                />
                <Button 
                  icon="pi pi-trash" 
                  size="small" 
                  outlined 
                  severity="danger"
                  @click="confirmDelete(data)"
                  v-if="canDeleteAuthor(data)"
                  v-tooltip.top="Supprimer"
                />
                <Button 
                  icon="pi pi-undo" 
                  size="small" 
                  outlined 
                  severity="success"
                  @click="restoreAuthor(data)"
                  v-if="data.deleted_at"
                  v-tooltip.top="Restaurer"
                />
              </div>
            </template>
          </Column>
        </DataTable>
      </template>
    </Card>

    <!-- Create Author Dialog -->
    <Dialog 
      v-model:visible="showCreateDialog" 
      header="Créer un Auteur" 
      :style="{ width: '60vw' }"
      :modal="true"
    >
      <AuthorCreateForm @created="onAuthorCreated" @cancel="showCreateDialog = false" />
    </Dialog>

    <!-- Edit Author Dialog -->
    <Dialog 
      v-model:visible="showEditDialog" 
      header="Modifier l'Auteur" 
      :style="{ width: '60vw' }"
      :modal="true"
    >
      <AuthorEditForm 
        v-if="selectedAuthor" 
        :author="selectedAuthor" 
        @updated="onAuthorUpdated" 
        @cancel="showEditDialog = false" 
      />
    </Dialog>

    <!-- View Author Dialog -->
    <Dialog 
      v-model:visible="showViewDialog" 
      header="Détails de l'Auteur" 
      :style="{ width: '70vw' }"
      :modal="true"
    >
      <AuthorDetailsView 
        v-if="selectedAuthor" 
        :author="selectedAuthor" 
        :statistics="statistics"
        @close="showViewDialog = false" 
      />
    </Dialog>

    <ConfirmDialog />
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useAuthorsStore } from '@/stores/authors'
import { useAuthStore } from '@/stores/auth'
import { useConfirm } from 'primevue/useconfirm'
import { useToast } from 'primevue/usetoast'
import AuthorCreateForm from './AuthorCreateForm.vue'
import AuthorEditForm from './AuthorEditForm.vue'
import AuthorDetailsView from './AuthorDetailsView.vue'

const authorsStore = useAuthorsStore()
const authStore = useAuthStore()
const confirm = useConfirm()
const toast = useToast()

const showCreateDialog = ref(false)
const showEditDialog = ref(false)
const showViewDialog = ref(false)
const selectedAuthor = ref(null)

const authors = computed(() => authorsStore.authors)
const statistics = computed(() => authorsStore.statistics)
const loading = computed(() => authorsStore.loading)
const pagination = computed(() => authorsStore.pagination)
const filters = computed({
  get: () => authorsStore.filters,
  set: (value) => authorsStore.setFilters(value)
})

const statusOptions = [
  { label: 'Vivant', value: 'alive' },
  { label: 'Décédé', value: 'deceased' },
]

const nationalityOptions = computed(() => {
  return authorsStore.nationalities.map(nat => ({
    label: nat,
    value: nat
  }))
})

const canCreateAuthors = computed(() => {
  return authStore.hasRole(['admin', 'bibliothecaire'])
})

const canEditAuthor = computed(() => {
  return authStore.hasRole(['admin', 'bibliothecaire'])
})

const canDeleteAuthor = (author) => {
  if (!authStore.hasRole(['admin', 'bibliothecaire'])) {
    return false
  }
  // Prevent deletion if author has references
  if (author.references_count > 0) {
    return false
  }
  return true
}

let searchTimeout = null

const debouncedSearch = () => {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    applyFilters()
  }, 500)
}

const applyFilters = () => {
  authorsStore.setFilters(filters.value)
  loadAuthors()
}

const resetFilters = () => {
  authorsStore.resetFilters()
  loadAuthors()
}

const loadAuthors = async () => {
  try {
    await authorsStore.fetchAuthors(pagination.value.current_page)
  } catch (error) {
    toast.add({
      severity: 'error',
      summary: 'Erreur',
      detail: error.message || 'Erreur lors du chargement des auteurs',
      life: 3000
    })
  }
}

const onPageChange = (event) => {
  authorsStore.pagination.current_page = event.page + 1
  authorsStore.pagination.per_page = event.rows
  loadAuthors()
}

const onSort = (event) => {
  const sortField = event.sortField
  const sortOrder = event.sortOrder === 1 ? 'asc' : 'desc'
  authorsStore.setSort(sortField, sortOrder)
  loadAuthors()
}

const viewAuthor = (author) => {
  selectedAuthor.value = author
  showViewDialog.value = true
  authorsStore.fetchAuthor(author.id)
}

const editAuthor = (author) => {
  selectedAuthor.value = author
  showEditDialog.value = true
}

const confirmDelete = (author) => {
  confirm.require({
    message: `Êtes-vous sûr de vouloir supprimer l'auteur ${author.full_name}?`,
    header: 'Confirmation de suppression',
    icon: 'pi pi-exclamation-triangle',
    accept: () => deleteAuthor(author),
    reject: () => {}
  })
}

const deleteAuthor = async (author) => {
  try {
    await authorsStore.deleteAuthor(author.id)
    toast.add({
      severity: 'success',
      summary: 'Succès',
      detail: 'Auteur supprimé avec succès',
      life: 3000
    })
    loadAuthors()
  } catch (error) {
    toast.add({
      severity: 'error',
      summary: 'Erreur',
      detail: error.message || 'Erreur lors de la suppression',
      life: 3000
    })
  }
}

const restoreAuthor = async (author) => {
  try {
    await authorsStore.restoreAuthor(author.id)
    toast.add({
      severity: 'success',
      summary: 'Succès',
      detail: 'Auteur restauré avec succès',
      life: 3000
    })
    loadAuthors()
  } catch (error) {
    toast.add({
      severity: 'error',
      summary: 'Erreur',
      detail: error.message || 'Erreur lors de la restauration',
      life: 3000
    })
  }
}

const onAuthorCreated = () => {
  showCreateDialog.value = false
  loadAuthors()
  toast.add({
    severity: 'success',
    summary: 'Succès',
    detail: 'Auteur créé avec succès',
    life: 3000
  })
}

const onAuthorUpdated = () => {
  showEditDialog.value = false
  loadAuthors()
  toast.add({
    severity: 'success',
    summary: 'Succès',
    detail: 'Auteur mis à jour avec succès',
    life: 3000
  })
}

const formatDate = (date) => {
  if (!date) return 'Jamais'
  return new Date(date).toLocaleDateString('fr-FR', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric'
  })
}

onMounted(() => {
  loadAuthors()
  authorsStore.fetchNationalities()
})
</script>

<style scoped>
.authors-list {
  padding: 1rem;
}

.filters {
  background: #f8f9fa;
}
</style>
