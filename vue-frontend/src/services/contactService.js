import api from './api'

export const contactService = {
    getAll(params = {}) {
        return api.get('/contacts', { params })
    },

    create(contactData) {
        return api.post('/contacts', contactData)
    },

    update(id, contactData) {
        return api.put(`/contacts/${id}`, contactData)
    },

    delete(id) {
        return api.delete(`/contacts/${id}`)
    }
}
