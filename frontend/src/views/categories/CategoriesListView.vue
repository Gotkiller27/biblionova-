<template>
  <div class="categories-list">
    <Card>
      <template #title>
        <div class="flex justify-between items-center">
          <h2>Gestion des Catégories</h2>
          <div class="flex gap-2">
            <Button 
              label="Vue Arbre" 
              icon="pi pi-sitemap" 
              :outlined="viewMode !== 'tree'"
              @click="viewMode = 'tree'"
              v-if="canCreateCategories"
            />
            <Button 
              label="Vue Liste" 
              icon="pi pi-list" 
              :outlined="viewMode !== 'list'"
              @click="viewMode = 'list'"
              v-if="canCreateCategories"
            />
            <Button 
              label="Nouvelle Catégorie" 
              icon="pi pi-plus" 
              @click="showCreateDialog = true"
              v-if="canCreateCategories"
            />
          </div>
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
              <Dropdown 
                v-model="filters.parent_id" 
                :options="parentOptions" 
                optionLabel="label" 
                optionValue="value"
                placeholder="Parent" 
                class="w-full"
                showClear
                @change="applyFilters"
              />
            </div>
            <div class="col-12 md:col-3">
              <div class="flex gap-2">
                <Button 
                  icon="pi pi-refresh" 
                  @click="loadCategories"
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

        <!-- Tree View -->
        <div v-if="viewMode === 'tree'" class="tree-view">
          <Tree 
            :value="categoryTree" 
            :expandedKeys="expandedKeys"
            @node-expand="onNodeExpand"
            @node-collapse="onNodeCollapse"
            selectionMode="single"
            v-model:selectionKeys="selectedNode"
            class="w-full"
          >
            <template #default="{ node }">
              <div class="flex items-center gap-2">
                <i :class="node.children?.length ? 'pi pi-folder' : 'pi pi-file'" class="text-lg"></i>
                <span class="font-semibold">{{ node.name }}</span>
                <Tag 
                  :value="getStatusLabel(node.status)"
                  :severity="getStatusSeverity(node.status)"
                  size="small"
                />
                <span class="text-color-secondary text-sm">{{ node.total_references_count }} références</span>
                <div class="flex gap-1 ml-auto">
                  <Button 
                    icon="pi pi-eye" 
                    size="small" 
                    outlined 
                    @click="viewCategory(node)"
                    v-tooltip.top="Voir"
                  />
                  <Button 
                    icon="pi pi-pencil" 
                    size="small" 
                    outlined 
                    @click="editCategory(node)"
                    v-if="canEditCategory"
                    v-tooltip.top="Modifier"
                  />
                  <Button 
                    icon="pi pi-trash" 
                    size="small" 
                    outlined 
                    severity="danger"
                    @click="confirmDelete(node)"
                    v-if="canDeleteCategory(node)"
                    v-tooltip.top="Supprimer"
                  />
                </div>
              </div>
            </template>
          </Tree>
        </div>

        <!-- DataTable View -->
        <DataTable 
          v-else
          :value="categories" 
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
          <Column field="name" header="Nom" sortable>
            <template #body="{ data }">
              <div class="flex items-center gap-2">
                <i :class="data.is_root ? 'pi pi-folder' : 'pi pi-file'" class="text-lg"></i>
                <div>
                  <div class="font-semibold">{{ data.name }}</div>
                  <div class="text-color-secondary text-sm">{{ data.full_path }}</div>
                </div>
              </div>
            </template>
          </Column>
          
          <Column field="slug" header="Slug" sortable />
          
          <Column field="status" header="Statut" sortable>
            <template #body="{ data }">
              <Tag 
                :value="getStatusLabel(data.status)"
                :severity="getStatusSeverity(data.status)"
              />
            </template>
          </Column>
          
          <Column field="depth" header="Profondeur" sortable>
            <template #body="{ data }">
              {{ data.depth }}
            </template>
          </Column>
          
          <Column field="total_references_count" header="Références" sortable>
            <template #body="{ data }">
              {{ data.total_references_count }}
            </template>
          </Column>
          
          <Column field="children_count" header="Enfants" sortable>
            <template #body="{ data }">
              {{ data.children_count }}
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
                  @click="viewCategory(data)"
                  v-tooltip.top="Voir"
                />
                <Button 
                  icon="pi pi-pencil" 
                  size="small" 
                  outlined 
                  @click="editCategory(data)"
                  v-if="canEditCategory"
                  v-tooltip.top="Modifier"
                />
                <Button 
                  icon="pi pi-trash" 
                  size="small" 
                  outlined 
                  severity="danger"
                  @click="confirmDelete(data)"
                  v-if="canDeleteCategory(data)"
                  v-tooltip.top="Supprimer"
                />
                <Button 
                  icon="pi pi-undo" 
                  size="small" 
                  outlined 
                  severity="success"
                  @click="restoreCategory(data)"
                  v-if="data.deleted_at"
                  v-tooltip.top="Restaurer"
                />
              </div>
            </template>
          </Column>
        </DataTable>
      </template>
    </Card>

    <!-- Create Category Dialog -->
    <Dialog 
      v-model:visible="showCreateDialog" 
      header="Créer une Catégorie" 
      :style="{ width: '50vw' }"
      :modal="true"
    >
      <CategoryCreateForm @created="onCategoryCreated" @cancel="showCreateDialog = false" />
    </Dialog>

    <!-- Edit Category Dialog -->
    <Dialog 
      v-model:visible="showEditDialog" 
      header="Modifier la Catégorie" 
      :style="{ width: '50vw' }"
      :modal="true"
    >
      <CategoryEditForm 
        v-if="selectedCategory" 
        :category="selectedCategory" 
        @updated="onCategoryUpdated" 
        @cancel="showEditDialog = false" 
      />
    </Dialog>

    <!-- View Category Dialog -->
    <Dialog 
      v-model:visible="showViewDialog" 
      header="Détails de la Catégorie" 
      :style="{ width: '60vw' }"
      :modal="true"
    >
      <CategoryDetailsView 
        v-if="selectedCategory" 
        :category="selectedCategory" 
        @close="showViewDialog = false" 
      />
    </Dialog>

    <ConfirmDialog />
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useCategoriesStore } from '@/stores/categories'
import { useAuthStore } from '@/stores/auth'
import { useConfirm } from 'primevue/useconfirm'
import { useToast } from 'primevue/usetoast'
import CategoryCreateForm from './CategoryCreateForm.vue'
import CategoryEditForm from './CategoryEditForm.vue'
import CategoryDetailsView from './CategoryDetailsView.vue'

const categoriesStore = useCategoriesStore()
const authStore = useAuthStore()
const confirm = useConfirm()
const toast = useToast()

const viewMode = ref('tree')
const showCreateDialog = ref(false)
const showEditDialog = ref(false)
const showViewDialog = ref(false)
const selectedCategory = ref(null)
const selectedNode = ref({})
const expandedKeys = ref({})

const categories = computed(() => categoriesStore.categories)
const categoryTree = computed(() => categoriesStore.categoryTree)
const loading = computed(() => categoriesStore.loading)
const pagination = computed(() => categoriesStore.pagination)
const filters = computed({
  get: () => categoriesStore.filters,
  set: (value) => categoriesStore.setFilters(value)
})

const statuses = [
  { label: 'Actif', value: 'active' },
  { label: 'Inactif', value: 'inactive' },
]

const parentOptions = computed(() => [
  { label: 'Racine', value: 'null' },
  ...categoriesStore.flatList.map(cat => ({
    label: cat.full_path,
    value: cat.id,
  }))
])

const canCreateCategories = computed(() => {
  return authStore.hasRole(['admin', 'bibliothecaire'])
})

const canEditCategory = computed(() => {
  return authStore.hasRole(['admin', 'bibliothecaire'])
})

const canDeleteCategory = (category) => {
  if (!authStore.hasRole(['admin', 'bibliothecaire'])) {
    return false
  }
  // Prevent deletion if category has references or children
  if (category.total_references_count > 0 || category.children_count > 0) {
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
  categoriesStore.setFilters(filters.value)
  loadCategories()
}

const resetFilters = () => {
  categoriesStore.resetFilters()
  loadCategories()
}

const loadCategories = async () => {
  try {
    if (viewMode.value === 'tree') {
      await categoriesStore.fetchCategoryTree()
    } else {
      await categoriesStore.fetchCategories(pagination.value.current_page)
    }
  } catch (error) {
    toast.add({
      severity: 'error',
      summary: 'Erreur',
      detail: error.message || 'Erreur lors du chargement des catégories',
      life: 3000
    })
  }
}

const onPageChange = (event) => {
  categoriesStore.pagination.current_page = event.page + 1
  categoriesStore.pagination.per_page = event.rows
  loadCategories()
}

const onSort = (event) => {
  const sortField = event.sortField
  const sortOrder = event.sortOrder === 1 ? 'asc' : 'desc'
  categoriesStore.setSort(sortField, sortOrder)
  loadCategories()
}

const onNodeExpand = (node) => {
  expandedKeys.value[node.key] = true
}

const onNodeCollapse = (node) => {
  delete expandedKeys.value[node.key]
}

const viewCategory = (category) => {
  selectedCategory.value = category
  showViewDialog.value = true
}

const editCategory = (category) => {
  selectedCategory.value = category
  showEditDialog.value = true
}

const confirmDelete = (category) => {
  confirm.require({
    message: `Êtes-vous sûr de vouloir supprimer la catégorie ${category.name}?`,
    header: 'Confirmation de suppression',
    icon: 'pi pi-exclamation-triangle',
    accept: () => deleteCategory(category),
    reject: () => {}
  })
}

const deleteCategory = async (category) => {
  try {
    await categoriesStore.deleteCategory(category.id)
    toast.add({
      severity: 'success',
      summary: 'Succès',
      detail: 'Catégorie supprimée avec succès',
      life: 3000
    })
    loadCategories()
  } catch (error) {
    toast.add({
      severity: 'error',
      summary: 'Erreur',
      detail: error.message || 'Erreur lors de la suppression',
      life: 3000
    })
  }
}

const restoreCategory = async (category) => {
  try {
    await categoriesStore.restoreCategory(category.id)
    toast.add({
      severity: 'success',
      summary: 'Succès',
      detail: 'Catégorie restaurée avec succès',
      life: 3000
    })
    loadCategories()
  } catch (error) {
    toast.add({
      severity: 'error',
      summary: 'Erreur',
      detail: error.message || 'Erreur lors de la restauration',
      life: 3000
    })
  }
}

const onCategoryCreated = () => {
  showCreateDialog.value = false
  loadCategories()
  toast.add({
    severity: 'success',
    summary: 'Succès',
    detail: 'Catégorie créée avec succès',
    life: 3000
  })
}

const onCategoryUpdated = () => {
  showEditDialog.value = false
  loadCategories()
  toast.add({
    severity: 'success',
    summary: 'Succès',
    detail: 'Catégorie mise à jour avec succès',
    life: 3000
  })
}

const getStatusLabel = (status) => {
  const statusMap = {
    active: 'Actif',
    inactive: 'Inactif'
  }
  return statusMap[status] || status
}

const getStatusSeverity = (status) => {
  const severityMap = {
    active: 'success',
    inactive: 'secondary'
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
  loadCategories()
  categoriesStore.fetchFlatList()
})
</script>

<style scoped>
.categories-list {
  padding: 1rem;
}

.filters {
  background: #f8f9fa;
}

.tree-view {
  padding: 1rem;
}
</style>
