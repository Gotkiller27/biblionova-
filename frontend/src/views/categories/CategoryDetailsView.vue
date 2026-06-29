<template>
  <div class=" category-details">
    <div class="grid">
      <!-- Category Info Card -->
      <div class="col-12 md:col-6">
        <Card>
          <template #title>
            <div class="flex items-center gap-2">
              <i :class="category.is_root ? 'pi pi-folder' : 'pi pi-file'" class="text-2xl"></i>
              <h3>{{ category.name }}</h3>
            </div>
          </template>
          <template #content>
            <div class="category-info">
              <div class="info-item">
                <strong>Slug:</strong> {{ category.slug }}
              </div>
              <div class="info-item">
                <strong>Chemin complet:</strong> {{ category.full_path }}
              </div>
              <div class="info-item">
                <strong>Description:</strong> {{ category.description || 'N/A' }}
              </div>
              <div class="info-item">
                <strong>Statut:</strong>
                <Tag 
                  :value="getStatusLabel(category.status)"
                  :severity="getStatusSeverity(category.status)"
                />
              </div>
              <div class="info-item">
                <strong>Profondeur:</strong> {{ category.depth }}
              </div>
              <div class="info-item">
                <strong>Type:</strong>
                <Tag 
                  :value="category.is_root ? 'Racine' : 'Enfant'"
                  :severity="category.is_root ? 'info' : 'secondary'"
                />
              </div>
              <div class="info-item">
                <strong>Créé le:</strong> {{ formatDate(category.created_at) }}
              </div>
              <div class="info-item">
                <strong>Mis à jour le:</strong> {{ formatDate(category.updated_at) }}
              </div>
            </div>
          </template>
        </Card>
      </div>

      <!-- Statistics Card -->
      <div class="col-12 md:col-6">
        <Card>
          <template #title>Statistiques</template>
          <template #content>
            <div class="statistics">
              <div class="stat-item">
                <div class="stat-value">{{ statistics?.direct_references_count || 0 }}</div>
                <div class="stat-label">Références directes</div>
              </div>
              <div class="stat-item">
                <div class="stat-value">{{ statistics?.total_references_count || 0 }}</div>
                <div class="stat-label">Références totales</div>
              </div>
              <div class="stat-item">
                <div class="stat-value">{{ statistics?.direct_children_count || 0 }}</div>
                <div class="stat-label">Enfants directs</div>
              </div>
              <div class="stat-item">
                <div class="stat-value">{{ statistics?.total_children_count || 0 }}</div>
                <div class="stat-label">Enfants totaux</div>
              </div>
            </div>
          </template>
        </Card>
      </div>
    </div>

    <!-- Hierarchy Card -->
    <div class="col-12 mt-4">
      <Card>
        <template #title>Hiérarchie</template>
        <template #content>
          <div class="hierarchy-section">
            <h4>Catégorie parente</h4>
            <div v-if="category.parent" class="parent-info">
              <Tag 
                :value="category.parent.name"
                severity="info"
                class="mr-2"
              />
              <span class="text-color-secondary">{{ category.parent.full_path }}</span>
            </div>
            <div v-else class="text-color-secondary">Aucune catégorie parente (racine)</div>

            <h4 class="mt-4">Sous-catégories</h4>
            <div v-if="category.children && category.children.length > 0" class="children-list">
              <Tag 
                v-for="child in category.children" 
                :key="child.id"
                :value="child.name"
                severity="secondary"
                class="mr-2 mb-2"
              />
            </div>
            <div v-else class="text-color-secondary">Aucune sous-catégorie</div>
          </div>
        </template>
      </Card>
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
        @click="editCategory"
        v-if="canEditCategory"
      />
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useAuthStore } from '@/stores/auth'

const props = defineProps({
  category: {
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

const canEditCategory = computed(() => {
  return authStore.hasRole(['admin', 'bibliothecaire'])
})

const editCategory = () => {
  emit('edit', props.category)
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
</script>

<style scoped>
.category-details {
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
  .col-12.md\:col-6 {
    grid-column: span 6;
  }
}

.category-info {
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
  grid-template-columns: repeat(2, 1fr);
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

.hierarchy-section h4 {
  margin-bottom: 0.5rem;
  color: #333;
}

.parent-info,
.children-list {
  padding: 0.5rem 0;
}

.mt-4 {
  margin-top: 1rem;
}

.mr-2 {
  margin-right: 0.5rem;
}

.mb-2 {
  margin-bottom: 0.5rem;
}

.text-color-secondary {
  color: #6c757d;
}
</style>
