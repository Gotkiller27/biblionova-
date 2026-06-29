<template>
  <form @submit.prevent="handleSubmit">
    <div class="grid">
      <div class="col-12">
        <label for="name">Nom *</label>
        <InputText 
          id="name" 
          v-model="form.name" 
          class="w-full"
          :class="{ 'p-invalid': errors.name }"
        />
        <small class="p-error" v-if="errors.name">{{ errors.name }}</small>
      </div>

      <div class="col-12">
        <label for="slug">Slug</label>
        <InputText 
          id="slug" 
          v-model="form.slug" 
          class="w-full"
          placeholder="Laisser vide pour génération automatique"
          :class="{ 'p-invalid': errors.slug }"
        />
        <small class="p-error" v-if="errors.slug">{{ errors.slug }}</small>
        <small class="text-color-secondary">Laisser vide pour génération automatique à partir du nom</small>
      </div>

      <div class="col-12">
        <label for="description">Description</label>
        <Textarea 
          id="description" 
          v-model="form.description" 
          class="w-full"
          rows="4"
          :class="{ 'p-invalid': errors.description }"
        />
        <small class="p-error" v-if="errors.description">{{ errors.description }}</small>
      </div>

      <div class="col-12">
        <label for="parent_id">Catégorie parente</label>
        <TreeSelect 
          id="parent_id" 
          v-model="form.parent_id" 
          :options="parentTreeOptions"
          placeholder="Sélectionner une catégorie parente (optionnel)"
          class="w-full"
          :class="{ 'p-invalid': errors.parent_id }"
        />
        <small class="p-error" v-if="errors.parent_id">{{ errors.parent_id }}</small>
        <small class="text-color-secondary">Laisser vide pour créer une catégorie racine</small>
      </div>

      <div class="col-12">
        <label for="status">Statut</label>
        <Dropdown 
          id="status" 
          v-model="form.status" 
          :options="statuses" 
          optionLabel="label" 
          optionValue="value"
          placeholder="Sélectionner un statut"
          class="w-full"
          :class="{ 'p-invalid': errors.status }"
        />
        <small class="p-error" v-if="errors.status">{{ errors.status }}</small>
      </div>
    </div>

    <div class="flex justify-content-between mt-4">
      <Button 
        label="Annuler" 
        severity="secondary" 
        outlined 
        @click="$emit('cancel')" 
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
import { ref, onMounted } from 'vue'
import { useCategoriesStore } from '@/stores/categories'
import { useToast } from 'primevue/usetoast'

const props = defineProps({
  category: {
    type: Object,
    required: true
  }
})

const emit = defineEmits(['updated', 'cancel'])

const categoriesStore = useCategoriesStore()
const toast = useToast()

const loading = ref(false)
const errors = ref({})
const parentTreeOptions = ref([])

const form = ref({
  name: '',
  slug: '',
  description: '',
  parent_id: null,
  status: 'active',
})

const statuses = [
  { label: 'Actif', value: 'active' },
  { label: 'Inactif', value: 'inactive' },
]

const validateForm = () => {
  errors.value = {}

  if (!form.value.name) {
    errors.value.name = 'Le nom est requis'
  }

  if (!form.value.status) {
    errors.value.status = 'Le statut est requis'
  }

  return Object.keys(errors.value).length === 0
}

const handleSubmit = async () => {
  if (!validateForm()) {
    return
  }

  loading.value = true

  try {
    const updateData = {
      name: form.value.name,
      slug: form.value.slug || null,
      description: form.value.description,
      parent_id: form.value.parent_id,
      status: form.value.status,
    }

    await categoriesStore.updateCategory(props.category.id, updateData)

    emit('updated')
  } catch (error) {
    if (error.response?.data?.errors) {
      errors.value = error.response.data.errors
    } else {
      toast.add({
        severity: 'error',
        summary: 'Erreur',
        detail: error.message || 'Erreur lors de la modification de la catégorie',
        life: 3000
      })
    }
  } finally {
    loading.value = false
  }
}

const loadParentOptions = async () => {
  try {
    const tree = await categoriesStore.fetchCategoryTree()
    parentTreeOptions.value = formatTreeForSelect(tree, props.category.id)
  } catch (error) {
    console.error('Error loading parent options:', error)
  }
}

const formatTreeForSelect = (nodes, excludeId = null) => {
  return nodes
    .filter(node => node.id !== excludeId)
    .map(node => ({
      key: node.id,
      label: node.name,
      children: node.children?.length ? formatTreeForSelect(node.children, excludeId) : undefined,
    }))
}

onMounted(() => {
  form.value = {
    name: props.category.name,
    slug: props.category.slug,
    description: props.category.description || '',
    parent_id: props.category.parent_id,
    status: props.category.status,
  }
  loadParentOptions()
})
</script>

<style scoped>
label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: 600;
}

.grid {
  display: grid;
  grid-template-columns: repeat(12, 1fr);
  gap: 1rem;
}

.col-12 {
  grid-column: span 12;
}

.mt-4 {
  margin-top: 1rem;
}

.p-error {
  color: #ef4444;
  font-size: 0.875rem;
  margin-top: 0.25rem;
}

.text-color-secondary {
  color: #6c757d;
  font-size: 0.875rem;
}
</style>
