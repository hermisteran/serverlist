<template>
  <div class="mb-3">
    <label class="form-label">Imagen</label>
    <input type="file" class="form-control" accept="image/png, image/jpeg" @change="onFileChange" />
    <div v-if="preview" class="mt-2">
      <img
        :src="preview"
        alt="Preview"
        class="img-thumbnail"
        style="max-width: 150px; max-height: 150px"
      />
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue'

const modelValue = defineModel()
// Recibe la URL de la imagen actual
const props = defineProps({
  imageUrl: String, 
})

const preview = ref(null)

watch(modelValue, (val) => {
  if (!val) {
    // Si no hay imagen nueva, muestra la imagen previa si existe
    preview.value = props.imageUrl || null
  }
})

// Inicializa el preview con la imagen previa al montar
if (props.imageUrl) {
  preview.value = props.imageUrl
}

function onFileChange(e) {
  const file = e.target.files[0]
  if (file) {
    modelValue.value = file
    const reader = new FileReader()
    reader.onload = (ev) => {
      preview.value = ev.target.result
    }
    reader.readAsDataURL(file)
  } else {
    modelValue.value = null
    preview.value = props.imageUrl || null
  }
}
</script>
