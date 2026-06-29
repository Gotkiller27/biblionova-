<template>
  <form @submit.prevent="handleSubmit" class="publisher-create-form">
    <div class="grid">
      <div class="col-12">
        <div class="field">
          <label for="name">Nom *</label>
          <InputText 
            id="name" 
            v-model="form.name" 
            :class="{ 'p-invalid': errors.name }"
            class="w-full"
          />
          <small class="p-error" v-if="errors.name">{{ errors.name }}</small>
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
          />
        </div>
      </div>

      <div class="col-12">
        <div class="field">
          <label for="logo">Logo</label>
          <FileUpload 
            mode="basic" 
            accept="image/*" 
            :maxFileSize="5242880"
            :auto="false"
            chooseLabel="Choisir un logo"
            @select="onLogoSelect"
            @clear="onLogoClear"
            class="w-full"
          />
          <small class="text-color-secondary">Formats acceptés: JPEG, PNG, WebP (max 5MB)</small>
          <small class="p-error" v-if="errors.logo">{{ errors.logo }}</small>
          
          <div v-if="logoPreview" class="mt-3">
            <img :src="logoPreview" alt="Preview" class="publisher-logo-preview" />
          </div>
        </div>
      </div>

      <div class="col-12 md:col-6">
        <div class="field">
          <label for="country">Pays</label>
          <InputText 
            id="country" 
            v-model="form.country" 
            class="w-full"
          />
        </div>
      </div>

      <div class="col-12 md:col-6">
        <div class="field">
          <label for="city">Ville</label>
          <InputText 
            id="city" 
            v-model="form.city" 
            class="w-full"
          />
        </div>
      </div>

      <div class="col-12 md:col-6">
        <div class="field">
          <label for="website">Site web</label>
          <InputText 
            id="website" 
            v-model="form.website" 
            class="w-full"
          />
        </div>
      </div>

      <div class="col-12 md:col-6">
        <div class="field">
          <label for="email">Email</label>
          <InputText 
            id="email" 
            v-model="form.email" 
            class="w-full"
          />
        </div>
      </div>

      <div class="col-12 md:col-6">
        <div class="field">
          <label for="phone">Téléphone</label>
          <InputText 
            id="phone" 
            v-model="form.phone" 
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
import { usePublishersStore } from '@/stores/publishers'
import { useToast } from 'primevue/usetoast'

const emit = defineEmits(['created', 'cancel'])

const publishersStore = usePublishersStore()
const toast = useToast()

const loading = ref(false)
const logoFile = ref(null)
const logoPreview = ref(null)

const form = reactive({
  name: '',
  description: '',
  country: '',
  city: '',
  website: '',
  email: '',
  phone: '',
})

const errors = reactive({})

const onLogoSelect = (event) => {
  const file = event.files[0]
  logoFile.value = file
  
  // Create preview
  const reader = new FileReader()
  reader.onload = (e) => {
    logoPreview.value = e.target.result
  }
  reader.readAsDataURL(file)
}

const onLogoClear = () => {
  logoFile.value = null
  logoPreview.value = null
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
    const formData = new FormData()
    
    formData.append('name', form.name)
    formData.append('description', form.description || '')
    formData.append('country', form.country || '')
    formData.append('city', form.city || '')
    formData.append('website', form.website || '')
    formData.append('email', form.email || '')
    formData.append('phone', form.phone || '')
    
    if (logoFile.value) {
      formData.append('logo', logoFile.value)
    }

    await publishersStore.createPublisher(formData)
    
    toast.add({
      severity: 'success',
      summary: 'Succès',
      detail: 'Éditeur créé avec succès',
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
        detail: error.response?.data?.message || 'Erreur lors de la création de l\'éditeur',
        life: 3000
      })
    }
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.publisher-create-form {
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

.publisher-logo-preview {
  max-width: 200px;
  max-height: 200px;
  border-radius: 8px;
  object-fit: cover;
  border: 1px solid #ddd;
}
</style>
