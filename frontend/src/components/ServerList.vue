<template>
  <div class="sin-centrar">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h5>
        Total servidores: <span class="badge bg-primary">{{ count }}</span>
      </h5>
      <button class="btn btn-success btn-sm" @click="openCreateModal">
        <i class="bi bi-plus-circle"></i> Nuevo
      </button>

      <transition name="fade">
        <div
          v-if="store.message"
          class="alert alert-success w-100 text-center py-2 mb-0 position-absolute top-0 start-0"
        >
          {{ store.message }}
        </div>
      </transition>
    </div>

    <div v-if="loading" class="text-center py-4">
      <div class="spinner-border text-primary"></div>
    </div>

    <draggable
      v-else
      v-model="store.servers"
      @end="onDragEnd"
      item-key="id"
      class="row g-3"
    >
      <template #item="{ element }">
        <div class="col-12 elemento_draggable">
          <div
            class="server-list-item d-flex align-items-center py-2 px-3 border rounded mb-2 bg-white flex-nowrap"
          >
            <div
              class="server-img flex-shrink-0 d-flex align-items-center justify-content-center me-3"
              style="width: 64px; height: 64px"
            >
              <img
                :src="element.image !== null ? element.image_url : placeholder"
                alt="Imagen del servidor"
                class="img-fluid rounded"
                style="width: 56px; height: 56px; object-fit: cover"
              />
            </div>
            <div class="server-info flex-grow-1">
              <div class="fw-bold">{{ element.host }}</div>
              <div class="text-muted small"><strong>IP:</strong> {{ element.ip }}</div>
              <div class="small">{{ element.description }}</div>
            </div>
            <div class="server-actions d-flex flex-column align-items-end ms-3">
              <button
                class="btn btn-warning btn-sm mb-2"
                @click="openCreateModal(element)"
              >
                <i class="bi bi-pencil-square"></i>
              </button>
              <button class="btn btn-danger btn-sm" @click="deleteServer(element.id)">
                <i class="bi bi-trash"></i>
              </button>
            </div>
          </div>
        </div>
      </template>
    </draggable>

    <ServerModal
      :show="showModal"
      :server="selectedServer"
      @close="showModal = false"
      @save="createServer"
      @update="updateServer"
    />
  </div>
</template>

<script setup>
import { ref, computed } from "vue";
import draggable from "vuedraggable";
import ServerModal from "./ServerModal.vue";
import { useServerStore } from "../stores/serverStore";

const store = useServerStore();
const servers = computed(() => store.servers);
const loading = computed(() => store.loading);
const count = computed(() => store.count);

const placeholder = "/img/placeholder-img.png";

// Controla la visibilidad del modal
const showModal = ref(false);
const selectedServer = ref(null);

const deleteServer = async (id) => {
  if (confirm("¿Se procederá a eliminar este servidor?")) {
    await store.deleteServer(id);
  }
};

const onDragEnd = async () => {
  servers.value.forEach((server, idx) => {
    server.order = idx;
  });
  await store.updateOrder(servers.value);
};

const openCreateModal = (server = null) => {
  selectedServer.value = server;
  showModal.value = true;
};

const createServer = async (data) => {
  await store.createServer(data);
};

const updateServer = async (data) => {
  await store.updateServer(data);
};
</script>
