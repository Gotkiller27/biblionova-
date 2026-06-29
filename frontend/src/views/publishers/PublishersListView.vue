<template>
  <div class="publishers-list">
    <Card>
      <template #title>
        <div class="flex justify-between items-center">
          <h2>Gestion des Éditeurs</h2>
          <Button 
            label="Nouvel Éditeur" 
            icon="pi pi-plus" 
            @click="showCreateDialog = true"
            v-if="canCreatePublishers"
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
                v-model="filters.country" 
                :options="countryOptions" 
                optionLabel="label" 
                optionValue="value"
                placeholder="Pays" 
                class="w-full"
                showClear
                @change="applyFilters"
              />
            </div>
            <div class="col-12 md:col-2">
              <Dropdown 
                v-model="filters.city" 
                :options="cityOptions" 
                optionLabel="label" 
                optionValue="value"
                placeholder="Ville" 
                class="w-full"
                showClear
                @change="applyFilters"
              />
            </div>
            <div class="col-12 md:col-3">
              <div class="flex gap-2">
                <Button 
                  icon="pi pi-refresh" 
                  @click="loadPublishers"
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
          :value="publishers" 
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
          <Column field="logo" header="Logo" style="min-width: 80px">
            <template #body="{ data }">
              <Avatar 
                :image="data.logo_url" 
                :label="data.name.substring(0, 2).toUpperCase()"
                shape="circle" 
                size="large"
              />
            </template>
          </Column>
          
          <Column field="name" header="Nom" sortable>
            <template #body="{ data }">
              <div class="font-semibold">{{ data.name }}</div>
            </template>
          </Column>
          
          <Column field="country" header="Pays" sortable />
          
          <Column field="city" header="Ville" sortable />
          
          <Column field="full_address" header="Adresse" />
          
          <Column field="website" header="Site web">
            <template #body="{ data }">
              <a v-if="data.website" :href="data.website" target="_blank" class="text-primary-600 hover:underline">
                <i class="pi pi-external-link mr-1"></i>
                Site web
              </a>
              <span v-else>-</span>
            </template>
          </Column>
          
          <Column field="email" header="Email" />
          
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
                  @click="viewPublisher(data)"
                  v-tooltip.top="Voir"
                />
                <Button 
                  icon="pi pi-pencil" 
                  size="small" 
                  outlined 
                  @click="editPublisher(data)"
                  v-if="canEditPublisher"
                  v-tooltip.top="Modifier"
                />
                <Button 
                  icon="pi pi-trash" 
                  size="small" 
                  outlined 
                  severity="danger"
                  @click="confirmDelete(data)"
                  v-if="canDeletePublisher(data)"
                  v-tooltip.top="Supprimer"
                />
                <Button 
                  icon="pi pi-undo" 
                  size="small" 
                  outlined 
                  severity="success"
                  @click="restorePublisher(data)"
                  v-if="data.deleted_at"
                  v-tooltip.top="Restaurer"
                />
              </div>
            </template>
          </Column>
        </DataTable>
      </template>
    </Card>

    <!-- Create Publisher Dialog -->
    <Dialog 
      v-model:visible="showCreateDialog" 
      header="Créer un Éditeur" 
      :style="{ width: '60vw' }"
      :modal="true"
    >
      <PublisherCreateForm @created="onPublisherCreated" @cancel="showCreateDialog = false" />
    </Dialog>

    <!-- Edit Publisher Dialog -->
    <Dialog 
      v-model:visible="showEditDialog" 
      header="Modifier l'Éditeur" 
      :style="{ width: '60vw' }"
      :modal="true"
    >
      <PublisherEditForm 
        v-if="selectedPublisher" 
        :publisher="selectedPublisher" 
        @updated="onPublisherUpdated" 
        @cancel="showEditDialog = false" 
      />
    </Dialog>

    <!-- View Publisher Dialog -->
    <Dialog 
      v-model:visible="showViewDialog" 
      header="Détails de l'Éditeur" 
      :style="{ width: '70vw' }"
      :modal="true"
    >
      <PublisherDetailsView 
        v-if="selectedPublisher" 
        :publisher="selectedPublisher" 
        :statistics="statistics"
        @close="showViewDialog = false" 
      />
    </Dialog>

    <ConfirmDialog />
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { usePublishersStore } from '@/stores/publishers'
import { useAuthStore } from '@/stores/auth'
import { useConfirm } from 'primevue/useconfirm'
import { useToast } from 'primevue/usetoast'
import PublisherCreateForm from './PublisherCreateForm.vue'
import PublisherEditForm from './PublisherEditForm.vue'
import PublisherDetailsView from './PublisherDetailsView.vue'

const publishersStore = usePublishersStore()
const authStore = useAuthStore()
const confirm = useConfirm()
const toast = useToast()

const showCreateDialog = ref(false)
const showEditDialog = ref(false)
const showViewDialog = ref(false)
const selectedPublisher = ref(null)

const publishers = computed(() => publishersStore.publishers)
const statistics = computed(() => publishersStore.statistics)
const loading = computed(() => publishersStore.loading)
const pagination = computed(() => publishersStore.pagination)
const filters = computed({
  get: () => publishersStore.filters,
  set: (value) => publishersStore.setFilters(value)
})

const countryOptions = computed(() => {
  return publishersStore.countries.map(country => ({
    label: country,
    value: country
  }))
})

const cityOptions = computed(() => {
  return publishersStore.cities.map(city => ({
    label: city,
    value: city
  }))
})

const canCreatePublishers = computed(() => {
  return authStore.hasRole(['admin', 'bibliothecaire'])
})

const canEditPublisher = computed(() => {
  return authStore.hasRole(['admin', 'bibliothecaire'])
})

const canDeletePublisher = (publisher) => {
  if (!authStore.hasRole(['admin', 'bibliothecaire'])) {
    return false
  }
  // Prevent deletion if publisher has references
  if (publisher.references_count > 0) {
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
  publishersStore.setFilters(filters.value)
  loadPublishers()
}

const resetFilters = () => {
  publishersStore.resetFilters()
  loadPublishers()
}

const loadPublishers = async () => {
  try {
    await publishersStore.fetchPublishers(pagination.value.current_page)
  } catch (error) {
    toast.add({
      severity: 'error',
      summary: 'Erreur',
      detail: error.message || 'Erreur lors du chargement des éditeurs',
      life: 3000
    })
  }
}

const onPageChange = (event) => {
  publishersStore.pagination.current_page = event.page + 1
  publishersStore.pagination.per_page = event.rows
  loadPublishers()
}

const onSort = (event) => {
  const sortField = event.sortField
  const sortOrder = event.sortOrder === 1 ? 'asc' : 'desc'
  publishersStore.setSort(sortField, sortOrder)
  loadPublishers()
}

const viewPublisher = (publisher) => {
  selectedPublisher.value = publisher
  showViewDialog.value = true
  publishersStore.fetchPublisher(publisher.id)
}

const editPublisher = (publisher) => {
  selectedPublisher.value = publisher
  showEditDialog.value = true
}

const confirmDelete = (publisher) => {
  confirm.require({
    message: `Êtes-vous sûr de vouloir supprimer l'éditeur ${publisher.name}?`,
    header: 'Confirmation de suppression',
    icon: 'pi pi-exclamation-triangle',
    accept: () => deletePublisher(publisher),
    reject: () => {}
  })
}

const deletePublisher = async (publisher) => {
  try {
    await publishersStore.deletePublisher(publisher.id)
    toast.add({
      severity: 'success',
      summary: 'Succès',
      detail: 'Éditeur supprimé avec succès',
      life: 3000
    })
    loadPublishers()
  } catch (error) {
    toast.add({
      severity: 'error',
      summary: 'Erreur',
      detail: error.message || 'Erreur lors de la suppression',
      life: 3000
    })
  }
}

const restorePublisher = async (publisher) => {
  try {
    await publishersStore.restorePublisher(publisher.id)
    toast.add({
      severity: 'success',
      summary: 'Succès',
      detail: 'Éditeur restauré avec succès',
      life: 3000
    })
    loadPublishers()
  } catch (error) {
    toast.add({
      severity: 'error',
      summary: 'Erreur',
      detail: error.message || 'Erreur lors de la restauration',
      life: 3000
    })
  }
}

const onPublisherCreated = () => {
  showCreateDialog.value = false
  loadPublishers()
  toast.add({
    severity: 'success',
    summary: 'Succès',
    detail: 'Éditeur créé avec succès',
    life: 3000
  })
}

const onPublisherUpdated = () => {
  showEditDialog.value = false
  loadPublishers()
  toast.add({
    severity: 'success',
    summary: 'Succès',
    detail: 'Éditeur mis à jour avec succès',
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
  loadPublishers()
  publishersStore.fetchCountries()
  publishersStore.fetchCities()
})
</script>

<style scoped>
.publishers-list {
  padding: 1rem;
}

.filters {
  background: #f8f9fa;
}
</style>
