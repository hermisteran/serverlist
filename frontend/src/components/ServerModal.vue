<template>
  <div class="modal-backdrop" v-if="show">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title mb-3">
            {{ server && server.id ? "Editar Servidor" : "Nuevo Servidor" }}
          </h5>
          <button type="button" class="btn-close" @click="close"></button>
        </div>
        <form @submit.prevent="submit">
          <div class="modal-body">
            <ImageUploadPreview v-model="form.image" :image-url="form.image" />
            <div class="mb-3">
              <label class="form-label">Host <span class="required">*</span></label>
              <input
                v-model="form.host"
                type="text"
                class="form-control"
                maxlength="100"
                required
                placeholder="Nombre del servidor"
              />
              <div v-if="errors.host" class="text-danger small">
                {{ errors.host[0] }}
              </div>
            </div>
            <div class="mb-3">
              <label class="form-label">IP <span class="required">*</span></label>
              <input
                v-model="form.ip"
                type="text"
                class="form-control"
                maxlength="45"
                required
                pattern="^((25[0-5]|2[0-4]\d|[01]?\d\d?)\.){3}(25[0-5]|2[0-4]\d|[01]?\d\d?)$"
                placeholder="Dirección IP (IPv4) Ejem: 11.11.11.0"
              />
              <div v-if="errors.ip" class="text-danger small">
                {{ errors.ip[0] }}
              </div>
            </div>
            <div class="mb-3">
              <label class="form-label">Descripción</label>
              <textarea
                v-model="form.description"
                class="form-control"
                maxlength="200"
                placeholder="Detalles del servidor"
              ></textarea>
              <div v-if="errors.description" class="text-danger small">
                {{ errors.description[0] }}
              </div>
            </div>
          </div>
          <div class="modal-footer gap-2">
            <button type="button" class="btn btn-secondary btn-sm mr-3" @click="close">
              Cancelar
            </button>
            <button type="submit" class="btn btn-primary btn-sm" :disabled="porocessing">
              {{ porocessing ? "Procesando..." : "Guardar" }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, computed } from "vue";
import ImageUploadPreview from "./ImageUploadPreview.vue";
import { useServerStore } from "../stores/serverStore";

const props = defineProps({
  show: Boolean,
  server: Object,
});
const emit = defineEmits(["close", "save", "update"]);

const form = ref({
  image: null,
  host: "",
  ip: "",
  description: "",
});

const store = useServerStore();
const errors = ref({});
const porocessing = computed(() => store.porocessing);

watch(
  () => props.show,
  (val) => {
    if (val && props.server) {
      errors.value = {};
      form.value = {
        image: props.server.image !== null ? props.server.image_url : null,
        host: props.server.host || "",
        ip: props.server.ip || "",
        description: props.server.description || "",
      };
    } else if (val) {
      form.value = { image: null, host: "", ip: "", description: "" };
    }
  }
);

function close() {
  emit("close");
}

async function submit() {
  errors.value = {};
  try {
    if (props.server && props.server.id) {
      await store.updateServer({ ...form.value, id: props.server.id });
    } else {
      await store.createServer({ ...form.value });
    }
    emit("close");
  } catch (e) {
    errors.value = e.response?.data?.errors || {};
  }
}
</script>

<style scoped>
.modal-backdrop {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.3);
  z-index: 1050;
  display: flex;
  align-items: center;
  justify-content: center;
}
.modal-content {
  background: #fff;
  padding: 1.5rem;
  border-radius: 8px;
  min-width: 350px;
  max-width: 100%;
  box-shadow: 0 2px 16px rgba(0, 0, 0, 0.15);
}
</style>
