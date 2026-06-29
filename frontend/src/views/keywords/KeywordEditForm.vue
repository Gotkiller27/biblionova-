<template>
  <form @submit.prevent="handleSubmit" class="keyword-edit-form">
    <div class="grid">
      <div class="col-12">
        <div class="field">
          <label for="name">Nom *</label>
          <AutoComplete 
            id="name" 
            v-model="form.name" 
            :suggestions="suggestions"
            @complete="searchSuggestions"
            :class="{ 'p-invalid': errors.name }"
            class="w-full"
            placeholder="Commencez à taper..."
            field="name"
          />
          <small class="p-error" v-if="errors.name">{{ errors.name }}</small>
        </div>
      </div>

      <div class="col-12">
        <div class="field">
          <label for="slug">Slug</label>
          <InputText 
            id="slug" 
            v-model="form.slug" 
            class="w-full"
            placeholder="url-friendly-name"
          />
          <small class="text-color-secondary">Laisser vide pour génération automatique</small>
        </div>
      </div>

      <div class="col-12">
        <div class="field">
          <label for="description">Description</label>
          <Textarea 
            id="description" 
            v-model="form.description" 
            rows="4" 
            class="w-full"
            placeholder="Description du mot-clé..."
          />
        </div>
      </div>

      <div class="col-12 md:col-6">
        <div class="field">
          <label for="usage_count">Nombre d'utilisations</label>
          <InputNumber 
            id="usage_count" 
            v-model="form.usage_count" 
            :min="0"
            class="w-full"
          />
        </div>
      </div>

      <div class="col-12 md:col-6">
        <div class="field">
          <label for="popularity_score">Score de popularité</label>
          <InputNumber 
            id="popularity_score" 
            v-model="form.popularity_score" 
            :min="0"
            class="w-full"
          />
        </div>
      </div>
    </div>

    <div class="flex justify-end gap-2 mt-4">
      <Button 
        label="Annuler" 
        severity="secondary" 
        @click="$emit('cancel')" 
        :loading="loading"
      />
      <Button 
        label="Enregistrer" 
        type="submit" 
        :loading="loading"
      />
    </div>
  </form>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { useKeywordsStore } from '@/stores/keywords'
import { useToast } from 'primevue/usetoast'

const props = defineProps({
  keyword: {
    type: Object,
    required: true
  }
})

const emit = defineEmits(['updated', 'cancel'])

const keywordsStore = useKeywordsStore()
const toast = useToast()

const loading = ref(false)
const suggestions = ref([])

const form = reactive({
  name: '',
  slug: '',
  description: '',
  usage_count: 0,
  popularity_score: 0,
})

const errors = reactive({})

onMounted(() => {
  form.name = props.keyword.name
  form.slug = props.keyword.slug || ''
  form.description = props.keyword.description || ''
  form.usage_count = props.keyword.usage_count || 0
  form.popularity_score = props.keyword.popularity_score || 0
})

const searchSuggestions = async (event) => {
  const query = event.query
  if (query.length < 2) {
    suggestions.value = []
    return
  }

  try {
    const results = await keywordsStore.fetchSuggestions(query, 10)
    suggestions.value = results
  } catch (error) {
    console.error('Error fetching suggestions:', error)
    suggestions.value = []
  }
}

const validateForm = () => {
  const newErrors = {}

  if (!form.name) {
    newErrors.name = 'Le nom est requis'
  }

  Object.assign(errors, newErrors)
  return Object.keys(newErrors).length === 0
}

const handleSubmit = async () => {
  if (!validateForm()) {
    return
  }

  loading.value = true

  try {
    const data = {
      name: form.name,
      slug: form.slug || null,
      description: form.description || null,
      usage_count: form.usage_count,
      popularity_score: form.popularity_score,
    }

    await keywordsStore.updateKeyword(props.keyword.id, data)
    
    toast.add({
      severity: 'success',
      summary: 'Succès',
      detail: 'Mot-clé mis à jour avec succès',
      life: 3000
    })

    emit('updated')
  } catch (error) {
    if (error.response?.data?.errors) {
      Object.assign(errors, error.response.data.errors)
    } else {
      toast.add({
        severity: 'error',
        summary: 'Erreur',
        detail: error.response?.data?.message || 'Erreur lors de la mise à jour du mot-clé',
        life: 3000
      })
    }
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.keyword-edit-form {
  padding: 1rem;
}

.field {
  margin-bottom: 1rem;
}

.field label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: 500;
}
</style>
