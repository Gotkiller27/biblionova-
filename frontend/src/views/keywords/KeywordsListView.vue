<template>
  <div class="keywords-list-view">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold">Gestion des Mots-clés</h1>
      <div class="flex gap-2">
        <Button 
          label="Importer" 
          icon="pi pi-upload" 
          severity="secondary" 
          @click="showImportDialog = true"
          v-if="canCreateKeyword"
        />
        <Button 
          label="Exporter" 
          icon="pi pi-download" 
          severity="secondary" 
          @click="handleExport"
          v-if="canCreateKeyword"
        />
        <Button 
          label="Nouveau mot-clé" 
          icon="pi pi-plus" 
          @click="showCreateDialog = true"
          v-if="canCreateKeyword"
        />
      </div>
    </div>

    <!-- Tag Cloud Section -->
    <Card class="mb-6">
      <template #title>Nuage de mots-clés</template>
      <template #content>
        <div v-if="tagCloud.length > 0" class="tag-cloud">
          <router-link
            v-for="keyword in tagCloud"
            :key="keyword.id"
            :to="`/admin/keywords/${keyword.id}`"
            class="tag-cloud-item"
            :style="{ fontSize: `${keyword.weight}px` }"
          >
            {{ keyword.name }}
            <span class="tag-count">({{ keyword.references_count }})</span>
          </router-link>
        </div>
        <p v-else class="text-color-secondary">Aucun mot-clé disponible</p>
      </template>
    </Card>

    <!-- Filters -->
    <Card class="mb-6">
      <template #content>
        <div class="grid">
          <div class="col-12 md:col-4">
            <InputText 
              v-model="filters.search" 
              placeholder="Rechercher..." 
              class="w-full"
              @input="handleSearch"
            />
          </div>
          <div class="col-12 md:col-4">
            <Dropdown 
              v-model="filters.only_trashed" 
              :options="trashedOptions" 
              optionLabel="label" 
              optionValue="value"
              placeholder="État" 
              class="w-full"
              @change="handleFilterChange"
            />
          </div>
          <div class="col-12 md:col-4">
            <Button 
              label="Réinitialiser" 
              severity="secondary" 
              @click="resetFilters"
              class="w-full"
            />
          </div>
        </div>
      </template>
    </Card>

    <!-- Keywords Table -->
    <Card>
      <template #content>
        <DataTable 
          :value="keywords" 
          :loading="loading"
          :paginator="true"
          :rows="pagination.per_page"
          :totalRecords="pagination.total"
          :lazy="true"
          @page="onPageChange"
          :rowsPerPageOptions="[10, 15, 25, 50]"
          @rowsPerPageChange="onPerPageChange"
          :sortField="sortBy"
          :sortOrder="sortOrder === 'asc' ? 1 : -1"
          @sort="onSort"
          stripedRows
        >
          <Column field="name" header="Nom" sortable>
            <template #body="{ data }">
              <router-link :to="`/admin/keywords/${data.id}`" class="keyword-link">
                {{ data.name }}
              </router-link>
            </template>
          </Column>
          <Column field="slug" header="Slug" sortable />
          <Column field="usage_count" header="Utilisations" sortable />
          <Column field="popularity_score" header="Popularité" sortable />
          <Column field="references_count" header="Références" sortable />
          <Column field="created_at" header="Créé le" sortable>
            <template #body="{ data }">
              {{ formatDate(data.created_at) }}
            </template>
          </Column>
          <Column header="Actions" :exportable="false">
            <template #body="{ data }">
              <div class="flex gap-2">
                <Button 
                  icon="pi pi-eye" 
                  size="small" 
                  severity="secondary" 
                  @click="viewKeyword(data)"
                />
                <Button 
                  icon="pi pi-pencil" 
                  size="small" 
                  severity="secondary" 
                  @click="editKeyword(data)"
                  v-if="canEditKeyword"
                />
                <Button 
                  icon="pi pi-trash" 
                  size="small" 
                  severity="danger" 
                  @click="deleteKeyword(data)"
                  v-if="canDeleteKeyword"
                />
                <Button 
                  v-if="data.deleted_at"
                  icon="pi pi-undo" 
                  size="small" 
                  severity="success" 
                  @click="restoreKeyword(data)"
                />
              </div>
            </template>
          </Column>
        </DataTable>
      </template>
    </Card>

    <!-- Create Dialog -->
    <Dialog 
      v-model:visible="showCreateDialog" 
      header="Nouveau mot-clé" 
      :style="{ width: '500px' }"
      :modal="true"
    >
      <KeywordCreateForm @created="handleCreated" @cancel="showCreateDialog = false" />
    </Dialog>

    <!-- Edit Dialog -->
    <Dialog 
      v-model:visible="showEditDialog" 
      header="Modifier le mot-clé" 
      :style="{ width: '500px' }"
      :modal="true"
    >
      <KeywordEditForm 
        v-if="currentKeyword" 
        :keyword="currentKeyword" 
        @updated="handleUpdated" 
        @cancel="showEditDialog = false" 
      />
    </Dialog>

    <!-- View Dialog -->
    <Dialog 
      v-model:visible="showViewDialog" 
      header="Détails du mot-clé" 
      :style="{ width: '700px' }"
      :modal="true"
    >
      <KeywordDetailsView 
        v-if="currentKeyword" 
        :keyword="currentKeyword" 
        :statistics="currentKeywordStatistics"
        @close="showViewDialog = false"
        @edit="editKeywordFromView"
      />
    </Dialog>

    <!-- Import Dialog -->
    <Dialog 
      v-model:visible="showImportDialog" 
      header="Importer des mots-clés" 
      :style="{ width: '600px' }"
      :modal="true"
    >
      <div class="import-form">
        <div class="field">
          <label>Format JSON</label>
          <Textarea 
            v-model="importData" 
            rows="10" 
            placeholder='[{"name": "mot-clé 1", "description": "description"}, ...]'
            class="w-full"
          />
        </div>
        <div class="flex justify-end gap-2 mt-4">
          <Button 
            label="Annuler" 
            severity="secondary" 
            @click="showImportDialog = false" 
          />
          <Button 
            label="Importer" 
            @click="handleImport" 
            :loading="loading"
          />
        </div>
      </div>
    </Dialog>

    <Toast />
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useKeywordsStore } from '@/stores/keywords'
import { useAuthStore } from '@/stores/auth'
import { useToast } from 'primevue/usetoast'
import KeywordCreateForm from './KeywordCreateForm.vue'
import KeywordEditForm from './KeywordEditForm.vue'
import KeywordDetailsView from './KeywordDetailsView.vue'

const keywordsStore = useKeywordsStore()
const authStore = useAuthStore()
const toast = useToast()

const showCreateDialog = ref(false)
const showEditDialog = ref(false)
const showViewDialog = ref(false)
const showImportDialog = ref(false)
const currentKeyword = ref(null)
const currentKeywordStatistics = ref(null)
const importData = ref('')

const filters = ref({
  search: '',
  only_trashed: false,
})

const trashedOptions = [
  { label: 'Tous', value: false },
  { label: 'Supprimés', value: true },
]

const canCreateKeyword = computed(() => {
  return authStore.hasRole(['admin', 'bibliothecaire'])
})

const canEditKeyword = computed(() => {
  return authStore.hasRole(['admin', 'bibliothecaire'])
})

const canDeleteKeyword = computed(() => {
  return authStore.hasRole(['admin', 'bibliothecaire'])
})

const keywords = computed(() => keywordsStore.keywords)
const loading = computed(() => keywordsStore.loading)
const pagination = computed(() => keywordsStore.pagination)
const sortBy = computed(() => keywordsStore.sortBy)
const sortOrder = computed(() => keywordsStore.sortOrder)
const tagCloud = computed(() => keywordsStore.tagCloud)

onMounted(() => {
  keywordsStore.fetchKeywords()
  keywordsStore.fetchTagCloud()
})

const handleSearch = () => {
  keywordsStore.setFilters(filters.value)
  keywordsStore.fetchKeywords(1)
}

const handleFilterChange = () => {
  keywordsStore.setFilters(filters.value)
  keywordsStore.fetchKeywords(1)
}

const resetFilters = () => {
  filters.value = {
    search: '',
    only_trashed: false,
  }
  keywordsStore.resetFilters()
  keywordsStore.fetchKeywords(1)
}

const onPageChange = (event) => {
  keywordsStore.fetchKeywords(event.page + 1)
}

const onPerPageChange = (event) => {
  keywordsStore.setPerPage(event.value)
  keywordsStore.fetchKeywords(1)
}

const onSort = (event) => {
  keywordsStore.setSort(event.sortField, event.sortOrder === 1 ? 'asc' : 'desc')
  keywordsStore.fetchKeywords(1)
}

const viewKeyword = async (keyword) => {
  currentKeyword.value = keyword
  try {
    const data = await keywordsStore.fetchKeywordStatistics(keyword.id)
    currentKeywordStatistics.value = data
    showViewDialog.value = true
  } catch (error) {
    toast.add({
      severity: 'error',
      summary: 'Erreur',
      detail: 'Erreur lors du chargement des statistiques',
      life: 3000
    })
  }
}

const editKeyword = (keyword) => {
  currentKeyword.value = keyword
  showEditDialog.value = true
}

const editKeywordFromView = (keyword) => {
  showViewDialog.value = false
  editKeyword(keyword)
}

const deleteKeyword = async (keyword) => {
  if (confirm(`Êtes-vous sûr de vouloir supprimer le mot-clé "${keyword.name}" ?`)) {
    try {
      await keywordsStore.deleteKeyword(keyword.id)
      toast.add({
        severity: 'success',
        summary: 'Succès',
        detail: 'Mot-clé supprimé avec succès',
        life: 3000
      })
    } catch (error) {
      toast.add({
        severity: 'error',
        summary: 'Erreur',
        detail: error.response?.data?.message || 'Erreur lors de la suppression',
        life: 3000
      })
    }
  }
}

const restoreKeyword = async (keyword) => {
  try {
    await keywordsStore.restoreKeyword(keyword.id)
    toast.add({
      severity: 'success',
      summary: 'Succès',
      detail: 'Mot-clé restauré avec succès',
      life: 3000
    })
  } catch (error) {
    toast.add({
      severity: 'error',
      summary: 'Erreur',
      detail: error.response?.data?.message || 'Erreur lors de la restauration',
      life: 3000
    })
  }
}

const handleCreated = () => {
  showCreateDialog.value = false
  toast.add({
    severity: 'success',
    summary: 'Succès',
    detail: 'Mot-clé créé avec succès',
    life: 3000
  })
}

const handleUpdated = () => {
  showEditDialog.value = false
  toast.add({
    severity: 'success',
    summary: 'Succès',
    detail: 'Mot-clé mis à jour avec succès',
    life: 3000
  })
}

const handleExport = async () => {
  try {
    await keywordsStore.exportKeywords()
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
      detail: error.response?.data?.message || 'Erreur lors de l\'export',
      life: 3000
    })
  }
}

const handleImport = async () => {
  try {
    const keywords = JSON.parse(importData.value)
    const result = await keywordsStore.importKeywords(keywords)
    showImportDialog.value = false
    importData.value = ''
    toast.add({
      severity: 'success',
      summary: 'Succès',
      detail: `${result.imported} mots-clés importés, ${result.skipped} ignorés`,
      life: 3000
    })
  } catch (error) {
    toast.add({
      severity: 'error',
      summary: 'Erreur',
      detail: error.response?.data?.message || 'Erreur lors de l\'import',
      life: 3000
    })
  }
}

const formatDate = (date) => {
  if (!date) return 'N/A'
  return new Date(date).toLocaleDateString('fr-FR')
}
</script>

<style scoped>
.keywords-list-view {
  padding: 1rem;
}

.tag-cloud {
  display: flex;
  flex-wrap: wrap;
  gap: 1rem;
  padding: 1rem;
}

.tag-cloud-item {
  text-decoration: none;
  color: #4472C4;
  transition: all 0.2s;
  cursor: pointer;
}

.tag-cloud-item:hover {
  color: #2a5298;
  text-decoration: underline;
}

.tag-count {
  font-size: 0.8em;
  color: #666;
}

.keyword-link {
  color: #4472C4;
  text-decoration: none;
  font-weight: 500;
}

.keyword-link:hover {
  text-decoration: underline;
}

.import-form .field {
  margin-bottom: 1rem;
}

.import-form label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: 500;
}
</style>
