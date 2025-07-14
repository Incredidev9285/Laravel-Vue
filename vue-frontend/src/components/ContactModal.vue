<template>
    <div class="modal-overlay">
      <div class="modal-container">
        <h2 class="modal-title">
          {{ contact ? 'Edit Contact' : 'Create Contact' }}
        </h2>
  
        <form @submit.prevent="saveContact" class="contact-form">
          <div class="form-group">
            <label class="form-label">First Name *</label>
            <input 
              v-model="form.first_name"
              type="text" 
              required
              class="form-input"
            >
          </div>
  
          <div class="form-group">
            <label class="form-label">Last Name *</label>
            <input 
              v-model="form.last_name"
              type="text" 
              required
              class="form-input"
            >
          </div>
  
          <div class="form-actions">
            <button 
              type="button"
              @click="$emit('close')"
              class="btn btn-secondary"
            >
              Cancel
            </button>
            <button 
              type="submit"
              :disabled="saving"
              class="btn btn-primary"
            >
              {{ saving ? 'Saving...' : 'Save' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </template>
  
  <script>
  import { contactService } from '@/services/contactService'
  
  export default {
    name: 'ContactModal',
    props: {
      contact: Object,
      customerId: [String, Number]
    },
    data() {
      return {
        form: {
          first_name: '',
          last_name: '',
          customer_id: this.customerId
        },
        saving: false
      }
    },
    mounted() {
      if (this.contact) {
        this.form = { ...this.contact }
      }
    },
    methods: {
      async saveContact() {
        this.saving = true
        try {
          if (this.contact) {
            await contactService.update(this.contact.id, this.form)
          } else {
            await contactService.create(this.form)
          }
          this.$emit('save')
        } catch (error) {
          console.error('Error saving contact:', error)
        } finally {
          this.saving = false
        }
      }
    }
  }
  </script>
  
  <style scoped>
  .modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1100;
  }
  
  .modal-container {
    background: white;
    border-radius: 8px;
    padding: 30px;
    width: 90%;
    max-width: 500px;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
  }
  
  .modal-title {
    font-size: 1.3rem;
    font-weight: bold;
    color: #333;
    margin-bottom: 25px;
  }
  
  .contact-form {
    display: flex;
    flex-direction: column;
    gap: 20px;
  }
  
  .form-group {
    display: flex;
    flex-direction: column;
  }
  
  .form-label {
    font-weight: 500;
    color: #495057;
    margin-bottom: 5px;
    font-size: 14px;
  }
  
  .form-input {
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
    transition: border-color 0.3s ease;
  }
  
  .form-input:focus {
    outline: none;
    border-color: #007bff;
    box-shadow: 0 0 0 2px rgba(0,123,255,0.25);
  }
  
  .form-actions {
    display: flex;
    justify-content: flex-end;
    gap: 15px;
    padding-top: 20px;
    border-top: 1px solid #eee;
  }
  
  .btn {
    padding: 10px 20px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 14px;
    font-weight: 500;
    transition: all 0.3s ease;
  }
  
  .btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
  }
  
  .btn-primary {
    background-color: #007bff;
    color: white;
  }
  
  .btn-primary:hover:not(:disabled) {
    background-color: #0056b3;
  }
  
  .btn-secondary {
    background-color: #6c757d;
    color: white;
  }
  
  .btn-secondary:hover {
    background-color: #545b62;
  }
</style>
  