<template>
  <div class="keyword-details">
    <div class="grid">
      <!-- Basic Info -->
      <div class="col-12 md:col-6">
        <Card>
          <template #title>Informations générales</template>
          <template #content>
            <div class="keyword-info">
              <div class="info-item">
                <strong>Nom:</strong>
                <span>{{ keyword.name }}</span>
              </div>
              <div class="info-item">
                <strong>Slug:</strong>
                <span>{{ keyword.slug || 'Non défini' }}</span>
              </div>
              <div class="info-item">
                <strong>Description:</strong>
                <span>{{ keyword.description || 'Aucune description' }}</span>
              </div>
            </div>
          </template>
        </Card>
      </div>

      <!-- Statistics -->
      <div class="col-12 md:col-6">
        <Card>
          <template #title>Statistiques</template>
          <template #content>
            <div v-if="statistics" class="statistics-grid">
              <div class="stat-card">
                <div class="stat-value">{{ statistics.usage_count }}</div>
                <div class="stat-label">Utilisations</div>
              </div>
              <div class="stat-card">
                <div class="stat-value">{{ statistics.popularity_score }}</div>
                <div class="stat-label">Popularité</div>
              </div>
              <div class="stat-card">
                <div class="stat-value">{{ statistics.references_count }}</div>
                <div class="stat-label">Références</div>
              </div>
            </div>
          </template>
        </Card>
      </div>

      <!-- System Info -->
      <div class="col-12">
        <Card>
          <template #title>Informations système</template>
          <template #content>
            <div class="system-info">
              <div class="info-item">
                <strong>Créé le:</strong>
                <span>{{ formatDateTime(keyword.created_at) }}</span>
              </div>
              <div class="info-item">
                <strong>Modifié le:</strong>
                <span>{{ formatDateTime(keyword.updated_at) }}</span>
              </div>
            </div>
          </template>
        </Card>
      </div>
    </div>

    <div class="flex justify-end gap-2 mt-4">
      <Button 
        label="Modifier" 
        icon="pi pi-pencil" 
        @click="$emit('edit', keyword)"
        v-if="canEditKeyword"
      />
      <Button 
        label="Fermer" 
        severity="secondary" 
        @click="$emit('close')" 
      />
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useAuthStore } from '@/stores/auth'

const props = defineProps({
  keyword: {
    type: Object,
    required: true
  },
  statistics: {
    type: Object,
    default: null
  }
})

const emit = defineEmits(['close', 'edit'])

const authStore = useAuthStore()

const canEditKeyword = computed(() => {
  return authStore.hasRole(['admin', 'bibliothecaire'])
})

const formatDateTime = (date) => {
  if (!date) return 'N/A'
  return new Date(date).toLocaleString('fr-FR', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}
</script>

<style scoped>
.keyword-details {
  padding: 1rem;
}

.keyword-info {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.info-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0.5rem 0;
  border-bottom: 1px solid #eee;
}

.info-item:last-child {
  border-bottom: none;
}

.info-item strong {
  color: #666;
  min-width: 120px;
}

.info-item span {
  font-weight: 500;
  text-align: right;
}

.statistics-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 1rem;
}

.stat-card {
  background: #f8f9fa;
  padding: 1rem;
  border-radius: 8px;
  text-align: center;
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

.system-info {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}
</style>
