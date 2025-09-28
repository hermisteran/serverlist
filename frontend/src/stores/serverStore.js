import { defineStore } from 'pinia'
import api from '../services/api'

export const useServerStore = defineStore('server', {
  state: () => ({
    servers: [],
    loading: false,
    message: '',
    porocessing: false,
  }),
  getters: {
    count: (state) => state.servers.length,
  },
  actions: {
    async fetchServers() {
      this.loading = true
      try {
        const res = await api.get('/servers')
        this.servers = res.data.data
      } finally {
        this.loading = false
      }
    },
    async deleteServer(id) {
      await api.delete(`/servers/${id}`)
      this.message = 'Servidor eliminado correctamente.'
      await this.fetchServers()
      setTimeout(() => {
        this.message = ''
      }, 2500)
    },
    async createServer(data) {
      this.porocessing = true
      try {
        const formData = new FormData()
        if (data.image) formData.append('image', data.image)
        formData.append('host', data.host)
        formData.append('ip', data.ip)
        formData.append('description', data.description || '')
        await api.post('/servers', formData)
        this.message = 'Servidor creado correctamente.'
        await this.fetchServers()
        setTimeout(() => {
          this.message = ''
        }, 2500)
      } catch (e) {
        throw e
      } finally {
        this.porocessing = false
      }
    },
    async updateServer(data) {
      this.porocessing = true
      try {
        const formData = new FormData()

        if (data.image && typeof data.image !== 'string') {
          formData.append('image', data.image)
        }
        formData.append('host', data.host)
        formData.append('ip', data.ip)
        formData.append('description', data.description || '')
        formData.append('_method', 'PUT')
        await api.post(`/servers/${data.id}`, formData)
        this.message = 'Servidor actualizado correctamente.'
        await this.fetchServers()
        setTimeout(() => {
          this.message = ''
        }, 2500)
      } catch (e) {
        throw e
      } finally {
        this.porocessing = false
      }
    },
    async updateOrder(newOrder) {
      try {
        // Crea el payload con id y nuevo order
        const payload = newOrder.map((s, idx) => ({
          id: s.id,
          order: idx,
        }))
        await api.patch('/update-order-server', { servers: payload })
        this.message = 'Listado reordenado correctamente.'
        this.servers = newOrder
        setTimeout(() => {
          this.message = ''
        }, 2500)
      } catch (e) {
        throw e
      }
    },
  },
})
