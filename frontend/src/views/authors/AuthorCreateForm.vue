<template>
  <form @submit.prevent="handleSubmit" class="author-create-form">
    <div class="grid">
      <div class="col-12 md:col-6">
        <div class="field">
          <label for="first_name">Prénom *</label>
          <InputText 
            id="first_name" 
            v-model="form.first_name" 
            :class="{ 'p-invalid': errors.first_name }"
            class="w-full"
          />
          <small class="p-error" v-if="errors.first_name">{{ errors.first_name }}</small>
        </div>
      </div>

      <div class="col-12 md:col-6">
        <div class="field">
          <label for="last_name">Nom *</label>
          <InputText 
            id="last_name" 
            v-model="form.last_name" 
            :class="{ 'p-invalid': errors.last_name }"
            class="w-full"
          />
          <small class="p-error" v-if="errors.last_name">{{ errors.last_name }}</small>
        </div>
      </div>

      <div class="col-12">
        <div class="field">
          <label for="biography">Biographie</label>
          <Textarea 
            id="biography" 
            v-model="form.biography" 
            rows="4" 
            class="w-full"
          />
        </div>
      </div>

      <div class="col-12">
        <div class="field">
          <label for="photo">Photo</label>
          <FileUpload 
            mode="basic" 
            accept="image/*" 
            :maxFileSize="5242880"
            :auto="false"
            chooseLabel="Choisir une photo"
            @select="onPhotoSelect"
            @clear="onPhotoClear"
            class="w-full"
          />
          <small class="text-color-secondary">Formats acceptés: JPEG, PNG, WebP (max 5MB)</small>
          <small class="p-error" v-if="errors.photo">{{ errors.photo }}</small>
          
          <div v-if="photoPreview" class="mt-3">
            <img :src="photoPreview" alt="Preview" class="author-photo-preview" />
          </div>
        </div>
      </div>

      <div class="col-12 md:col-6">
        <div class="field">
          <label for="nationality">Nationalité</label>
          <InputText 
            id="nationality" 
            v-model="form.nationality" 
            class="w-full"
          />
        </div>
      </div>

      <div class="col-12 md:col-6">
        <div class="field">
          <label for="birth_date">Date de naissance</label>
          <Calendar 
            id="birth_date" 
            v-model="form.birth_date" 
            dateFormat="dd/mm/yyyy" 
            showIcon 
            class="w-full"
          />
        </div>
      </div>

      <div class="col-12 md:col-6">
        <div class="field">
          <label for="death_date">Date de décès</label>
          <Calendar 
            id="death_date" 
            v-model="form.death_date" 
            dateFormat="dd/mm/yyyy" 
            showIcon 
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
        label="Créer" 
        type="submit" 
        :loading="loading"
      />
    </div>
  </form>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { useAuthorsStore } from '@/stores/authors'
import { useToast } from 'primevue/usetoast'

const emit = defineEmits(['created', 'cancel'])

const authorsStore = useAuthorsStore()
const toast = useToast()

const loading = ref(false)
const photoFile = ref(null)
const photoPreview = ref(null)

const form = reactive({
  first_name: '',
  last_name: '',
  biography: '',
  nationality: '',
  birth_date: null,
  death_date: null,
})

const errors = reactive({})

const onPhotoSelect = (event) => {
  const file = event.files[0]
  photoFile.value = file
  
  // Create preview
  const reader = new FileReader()
  reader.onload = (e) => {
    photoPreview.value = e.target.result
  }
  reader.readAsDataURL(file)
}

const onPhotoClear = () => {
  photoFile.value = null
  photoPreview.value = null
}

const validateForm = () => {
  const newErrors = {}

  if (!form.first_name) {
    newErrors.first_name = 'Le prénom est requis'
  }

  if (!form.last_name) {
    newErrors.last_name = 'Le nom est requis'
  }

  // Validate death date is after birth date
  if (form.birth_date && form.death_date) {
    const birthDate = new Date(form.birth_date)
    const deathDate = new Date(form.death_date)
    if (deathDate <= birthDate) {
      newErrors.death_date = 'La date de décès doit être après la date de naissance'
    }
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
    const formData = new FormData()
    
    formData.append('first_name', form.first_name)
    formData.append('last_name', form.last_name)
    formData.append('biography', form.biography || '')
    formData.append('nationality', form.nationality || '')
    
    if (form.birth_date) {
      formData.append('birth_date', form.birth_date.toISOString().split('T')[0])
    }
    
    if (form.death_date) {
      formData.append('death_date', form.death_date.toISOString().split('T')[0])
    }
    
    if (photoFile.value) {
      formData.append('photo', photoFile.value)
    }

    await authorsStore.createAuthor(formData)
    
    toast.add({
      severity: 'success',
      summary: 'Succès',
      detail: 'Auteur créé avec succès',
      life: 3000
    })

    emit('created')
  } catch (error) {
    if (error.response?.data?.errors) {
      Object.assign(errors, error.response.data.errors)
    } else {
      toast.add({
        severity: 'error',
        summary: 'Erreur',
        detail: error.response?.data?.message || 'Erreur lors de la création de l\'auteur',
        life: 3000
      })
    }
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.author-create-form {
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

.author-photo-preview {
  max-width: 200px;
  max-height: 200px;
  border-radius: 8px;
  object-fit: cover;
  border: 1px solid #ddd;
}
</style>
