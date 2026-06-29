<template>
  <div class="publisher-details">
    <div class="grid">
      <!-- Publisher Logo and Basic Info -->
      <div class="col-12 md:col-4">
        <Card>
          <template #content>
            <div class="text-center">
              <Avatar 
                :image="publisher.logo_url" 
                :label="publisher.name.substring(0, 2).toUpperCase()"
                shape="circle" 
                size="xlarge"
                class="mb-3"
              />
              <h3>{{ publisher.name }}</h3>
              <p class="text-color-secondary">{{ publisher.full_address || 'Adresse non spécifiée' }}</p>
            </div>

            <Divider />

            <div class="publisher-info">
              <div class="info-item">
                <strong>Pays:</strong>
                <span>{{ publisher.country || 'Non spécifié' }}</span>
              </div>
              <div class="info-item">
                <strong>Ville:</strong>
                <span>{{ publisher.city || 'Non spécifiée' }}</span>
              </div>
              <div class="info-item">
                <strong>Email:</strong>
                <span>{{ publisher.email || 'Non spécifié' }}</span>
              </div>
              <div class="info-item">
                <strong>Téléphone:</strong>
                <span>{{ publisher.phone || 'Non spécifié' }}</span>
              </div>
            </div>

            <Divider />

            <div class="flex gap-2">
              <Button 
                label="Modifier" 
                icon="pi pi-pencil" 
                @click="$emit('edit', publisher)"
                v-if="canEditPublisher"
                class="w-full"
              />
              <a 
                v-if="publisher.website" 
                :href="publisher.website" 
                target="_blank" 
                class="w-full"
              >
                <Button 
                  label="Site web" 
                  icon="pi pi-external-link" 
                  class="w-full"
                />
              </a>
            </div>
          </template>
        </Card>
      </div>

      <!-- Description and Statistics -->
      <div class="col-12 md:col-8">
        <Card class="mb-4">
          <template #title>Description</template>
          <template #content>
            <p v-if="publisher.description">{{ publisher.description }}</p>
            <p v-else class="text-color-secondary">Aucune description disponible</p>
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
            </div>
          </template>
        </Card>

        <Card>
          <template #title>Informations système</template>
          <template #content>
            <div class="system-info">
              <div class="info-item">
                <strong>Créé le:</strong>
                <span>{{ formatDateTime(publisher.created_at) }}</span>
              </div>
              <div class="info-item">
                <strong>Modifié le:</strong>
                <span>{{ formatDateTime(publisher.updated_at) }}</span>
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
import { computed } from 'vue'
import { useAuthStore } from '@/stores/auth'

const props = defineProps({
  publisher: {
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

const canEditPublisher = computed(() => {
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
.publisher-details {
  padding: 1rem;
}

.publisher-info {
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

.system-info {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}
</style>
