<template>
  <div class="author-details">
    <div class="grid">
      <!-- Author Photo and Basic Info -->
      <div class="col-12 md:col-4">
        <Card>
          <template #content>
            <div class="text-center">
              <Avatar 
                :image="author.photo_url" 
                :label="author.full_name.substring(0, 2).toUpperCase()"
                shape="circle" 
                size="xlarge"
                class="mb-3"
              />
              <h3>{{ author.full_name }}</h3>
              <p class="text-color-secondary">{{ author.nationality || 'Nationalité non spécifiée' }}</p>
              
              <div class="mt-4">
                <Tag 
                  :value="author.is_deceased ? 'Décédé' : 'Vivant'" 
                  :severity="author.is_deceased ? 'danger' : 'success'"
                />
              </div>
            </div>

            <Divider />

            <div class="author-info">
              <div class="info-item">
                <strong>Date de naissance:</strong>
                <span>{{ formatDate(author.birth_date) || 'Non spécifiée' }}</span>
              </div>
              <div class="info-item">
                <strong>Date de décès:</strong>
                <span>{{ formatDate(author.death_date) || 'N/A' }}</span>
              </div>
              <div class="info-item">
                <strong>Âge:</strong>
                <span>{{ author.age ? author.age + ' ans' : 'N/A' }}</span>
              </div>
            </div>

            <Divider />

            <div class="flex gap-2">
              <Button 
                label="Modifier" 
                icon="pi pi-pencil" 
                @click="$emit('edit', author)"
                v-if="canEditAuthor"
                class="w-full"
              />
            </div>
          </template>
        </Card>
      </div>

      <!-- Biography and Statistics -->
      <div class="col-12 md:col-8">
        <Card class="mb-4">
          <template #title>Biographie</template>
          <template #content>
            <p v-if="author.biography">{{ author.biography }}</p>
            <p v-else class="text-color-secondary">Aucune biographie disponible</p>
          </template>
        </Card>

        <Card class="mb-4">
          <template #title>Statistiques</template>
          <template #content>
            <div v-if="statistics" class="statistics-grid">
              <div class="stat-card">
                <div class="stat-value">{{ statistics.references_count }}</div>
                <div class="stat-label">Références</div>
              </div>
              <div class="stat-card">
                <div class="stat-value">{{ statistics.co_authors_count }}</div>
                <div class="stat-label">Co-auteurs</div>
              </div>
              <div class="stat-card">
                <div class="stat-value">{{ statistics.age ? statistics.age + ' ans' : 'N/A' }}</div>
                <div class="stat-label">Âge</div>
              </div>
            </div>
          </template>
        </Card>

        <Card class="mb-4">
          <template #title>Co-auteurs</template>
          <template #content>
            <div v-if="coAuthors.length > 0" class="co-authors-list">
              <div 
                v-for="coAuthor in coAuthors" 
                :key="coAuthor.id" 
                class="co-author-item"
              >
                <Avatar 
                  :image="coAuthor.photo_url" 
                  :label="coAuthor.full_name.substring(0, 2).toUpperCase()"
                  shape="circle" 
                  size="small"
                />
                <span>{{ coAuthor.full_name }}</span>
                <Tag :value="coAuthor.references_count" severity="info" />
              </div>
            </div>
            <p v-else class="text-color-secondary">Aucun co-auteur associé</p>
          </template>
        </Card>

        <Card>
          <template #title>Informations système</template>
          <template #content>
            <div class="system-info">
              <div class="info-item">
                <strong>Créé le:</strong>
                <span>{{ formatDateTime(author.created_at) }}</span>
              </div>
              <div class="info-item">
                <strong>Modifié le:</strong>
                <span>{{ formatDateTime(author.updated_at) }}</span>
              </div>
            </div>
          </template>
        </Card>
      </div>
    </div>

    <div class="flex justify-end gap-2 mt-4">
      <Button 
        label="Fermer" 
        severity="secondary" 
        @click="$emit('close')" 
      />
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useAuthorsStore } from '@/stores/authors'
import { useAuthStore } from '@/stores/auth'

const props = defineProps({
  author: {
    type: Object,
    required: true
  },
  statistics: {
    type: Object,
    default: null
  }
})

const emit = defineEmits(['close', 'edit'])

const authorsStore = useAuthorsStore()
const authStore = useAuthStore()

const coAuthors = ref([])

const canEditAuthor = computed(() => {
  return authStore.hasRole(['admin', 'bibliothecaire'])
})

const formatDate = (date) => {
  if (!date) return null
  return new Date(date).toLocaleDateString('fr-FR', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric'
  })
}

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

onMounted(async () => {
  if (props.author.id) {
    try {
      await authorsStore.fetchCoAuthors(props.author.id)
      coAuthors.value = authorsStore.coAuthors
    } catch (error) {
      console.error('Error fetching co-authors:', error)
    }
  }
})
</script>

<style scoped>
.author-details {
  padding: 1rem;
}

.author-info {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.info-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.info-item strong {
  color: #666;
}

.info-item span {
  font-weight: 500;
}

.statistics-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
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

.co-authors-list {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.co-author-item {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.75rem;
  background: #f8f9fa;
  border-radius: 8px;
}

.system-info {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}
</style>
