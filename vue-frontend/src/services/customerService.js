import api from './api'

export const customerService = {
    getAll(params = {}) {
        return api.get('/customers', { params })
    },

    getById(id) {
        return api.get(`/customers/${id}`)
    },

    create(customerData) {
        return api.post('/customers', customerData)
    },

    update(id, customerData) {
        return api.put(`/customers/${id}`, customerData)
    },

    delete(id) {
        return api.delete(`/customers/${id}`)
    }
}
