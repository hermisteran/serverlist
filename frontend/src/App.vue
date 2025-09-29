<template>
  <div class="container py-4 mx-auto" style="max-width: 900px">
    <h1 class="mb-4 text-center" style="margin-top: 20px">Gestión de Servidores</h1>

    <ServerList />
  </div>
</template>

<script setup>
import { useServerStore } from "./stores/serverStore";
import ServerList from "./components/ServerList.vue";
import { computed } from "vue";

const store = useServerStore();
store.fetchServers();
</script>
