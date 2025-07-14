<template>
    <div class="modal-overlay" @click.self="$emit('close')">
      <div class="modal-container" @click.stop>
        <h2 class="modal-title">
          {{ customer ? 'Edit Customer' : 'Create Customer' }}
        </h2>
  
        <form @submit.prevent="saveCustomer" class="customer-form">
          <!-- Add error display section -->
          <div v-if="errors" class="error-messages">
            <p v-for="(errorArray, field) in errors" 
              :key="field" 
              class="error-message">
              {{ errorArray[0] }}
            </p>
          </div>
          <!-- General Information -->
          <div class="form-section">
            <h3 class="section-title">General</h3>
            
            <div class="form-grid">
              <div class="form-group">
                <label class="form-label">Name *</label>
                <input 
                  v-model="form.name"
                  type="text" 
                  required
                  class="form-input"
                >
              </div>
              
              <div class="form-group">
                <label class="form-label">Reference *</label>
                <input 
                  v-model="form.reference"
                  type="text" 
                  required
                  class="form-input"
                >
              </div>
              
              <div class="form-group">
                <label class="form-label">Category *</label>
                <select v-model="form.customer_category_id" required class="form-select">
                  <option value="">Select Category</option>
                  <option v-for="category in categories" :key="category.id" :value="category.id">
                    {{ category.name }}
                  </option>
                </select>
              </div>
              
              <div class="form-group">
                <label class="form-label">Start Date *</label>
                <input 
                  v-model="form.start_date"
                  type="date" 
                  required
                  class="form-input"
                >
              </div>
            </div>
            
            <div class="form-group">
              <label class="form-label">Description</label>
              <textarea 
                v-model="form.description"
                rows="3"
                class="form-textarea"
              ></textarea>
            </div>
          </div>
  
          <!-- Contacts Section -->
          <div class="form-section" v-if="customer">
            <div class="section-header">
              <h3 class="section-title">Contacts</h3>
              <button 
                type="button"
                @click="openContactModal"
                class="btn btn-success btn-sm"
              >
                Create Contact
              </button>
            </div>
            
            <div class="contacts-table-container">
              <table class="contacts-table">
                <thead>
                  <tr>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="contact in contacts" :key="contact.id">
                    <td>{{ contact.first_name }}</td>
                    <td>{{ contact.last_name }}</td>
                    <td>
                      <button 
                        @click="(e) => editContact(contact, e)" 
                        class="btn-link btn-edit"
                      >
                        Edit
                      </button>
                      <button 
                        @click="(e) => deleteContact(contact, e)" 
                        class="btn-link btn-delete"
                      >
                        Delete
                      </button>
                    </td>
                  </tr>
                  <tr v-if="contacts.length === 0">
                    <td colspan="3" class="no-contacts">No contacts found</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
  
          <!-- Action Buttons -->
          <div class="form-actions">
            <button 
              type="button"
              @click="$emit('close')"
              class="btn btn-secondary"
            >
              Back
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
  
        <!-- Contact Modal -->
        <ContactModal
          v-if="showContactModal"
          :contact="selectedContact"
          :customer-id="customer?.id"
          @close="closeContactModal"
          @save="handleContactSave"
          
        />
  
        <!-- Delete Contact Confirmation -->
        <DeleteConfirmation
          v-if="showContactDeleteModal"
          :item="contactToDelete"
          item-type="contact"
          @confirm="confirmContactDelete"
          @cancel="cancelContactDelete"
          
        />
      </div>
    </div>
  </template>
  
  <script>
  import { customerService } from '@/services/customerService'
  import { contactService } from '@/services/contactService'
  import ContactModal from './ContactModal.vue'
  import DeleteConfirmation from './DeleteConfirmation.vue'
  
  export default {
    name: 'CustomerModal',
    components: {
      ContactModal,
      DeleteConfirmation
    },
    props: {
      customer: Object,
      categories: Array
    },
    data() {
      return {
        form: {
          name: '',
          reference: '',
          customer_category_id: '',
          start_date: '',
          description: ''
        },
        contacts: [],
        showContactModal: false,
        showContactDeleteModal: false,
        selectedContact: null,
        contactToDelete: null,
        saving: false,
        errors: null
      }
    },
    mounted() {
      if (this.customer) {
        this.form = { ...this.customer }
        this.form.start_date = this.customer.start_date?.split('T')[0]
        this.contacts = this.customer.contacts || []
      }
    },
    methods: {
      async saveCustomer() {
        this.saving = true
        this.errors = null //clear previous errors
        try {
          if (this.customer) {
            await customerService.update(this.customer.id, this.form)
          } else {
            await customerService.create(this.form)
          }
          this.$emit('save')
        } catch (error) {
          if (error.response?.status === 422) {
            this.errors = error.response.data.errors || {}
          } else {
            console.error('Error saving customer:', error)
          }
        } finally {
          this.saving = false
        }
      },
  
      openContactModal() {
        this.selectedContact = null
        this.showContactModal = true
      },
  
      editContact(contact, event) {
        if (event) {
          event.preventDefault();
          event.stopPropagation();
        }        
        this.selectedContact = { ...contact };
        this.showContactModal = true;
      },
  
       
      closeContactModal() {
        this.showContactModal = false;
        setTimeout(() => {
          this.selectedContact = null;
        }, 100);
      },
      async handleContactSave() {
        this.closeContactModal()
        if (this.customer) {
          const response = await customerService.getById(this.customer.id)
          this.contacts = response.data.data.contacts
        }
      },
  
      deleteContact(contact, event) {
        if (event) {
          event.preventDefault();
          event.stopPropagation();
        }
        this.contactToDelete = { ...contact };
        this.showContactDeleteModal = true
      },
  
      async confirmContactDelete() {
        try {
          await contactService.delete(this.contactToDelete.id)
          this.contacts = this.contacts.filter(c => c.id !== this.contactToDelete.id)
          this.showContactDeleteModal = false
          this.contactToDelete = null
        } catch (error) {
          console.error('Error deleting contact:', error)
        }
      },
  
      cancelContactDelete() {
        this.showContactDeleteModal = false;
        setTimeout(() => {
          this.contactToDelete = null;
        }, 100);
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
    z-index: 1000;
  }
  
  .modal-container {
    background: white;
    border-radius: 8px;
    padding: 30px;
    width: 90%;
    max-width: 800px;
    max-height: 90vh;
    overflow-y: auto;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
    z-index: 1001;
  }

  /* Add styles for nested modals */
  .modal-container :deep(.modal-overlay) {
    z-index: 1002;
  }

  .modal-container :deep(.modal-container) {
    z-index: 1003;
  }
  
  .modal-title {
    font-size: 1.5rem;
    font-weight: bold;
    color: #333;
    margin-bottom: 30px;
  }
  
  .customer-form {
    display: flex;
    flex-direction: column;
    gap: 30px;
  }
  
  .form-section {
    border-bottom: 1px solid #eee;
    padding-bottom: 25px;
  }
  
  .form-section:last-child {
    border-bottom: none;
    padding-bottom: 0;
  }
  
  .section-title {
    font-size: 1.2rem;
    font-weight: 600;
    color: #495057;
    margin-bottom: 20px;
  }
  
  .section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
  }
  
  .form-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 20px;
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
  
  .form-input,
  .form-select,
  .form-textarea {
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
    transition: border-color 0.3s ease;
  }
  
  .form-input:focus,
  .form-select:focus,
  .form-textarea:focus {
    outline: none;
    border-color: #007bff;
    box-shadow: 0 0 0 2px rgba(0,123,255,0.25);
  }
  
  .form-textarea {
    resize: vertical;
    min-height: 80px;
  }
  
  .contacts-table-container {
    border: 1px solid #ddd;
    border-radius: 4px;
    overflow: hidden;
  }
  
  .contacts-table {
    width: 100%;
    border-collapse: collapse;
  }
  
  .contacts-table th {
    background-color: #f8f9fa;
    padding: 12px;
    text-align: left;
    font-weight: 600;
    color: #495057;
    font-size: 12px;
    text-transform: uppercase;
  }
  
  .contacts-table td {
    padding: 12px;
    border-bottom: 1px solid #eee;
    color: #495057;
  }
  
  .contacts-table tr:last-child td {
    border-bottom: none;
  }
  
  .no-contacts {
    text-align: center;
    color: #6c757d;
    font-style: italic;
    padding: 20px;
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
  
  .btn-success {
    background-color: #28a745;
    color: white;
  }
  
  .btn-success:hover {
    background-color: #1e7e34;
  }
  
  .btn-sm {
    padding: 6px 12px;
    font-size: 12px;
  }
  
  .btn-link {
    background: none;
    border: none;
    cursor: pointer;
    font-size: 14px;
    font-weight: 500;
    text-decoration: none;
    margin-right: 15px;
    padding: 5px 0;
  }
  
  .btn-edit {
    color: #007bff;
  }
  
  .btn-edit:hover {
    color: #0056b3;
    text-decoration: underline;
  }
  
  .btn-delete {
    color: #dc3545;
  }
  
  .btn-delete:hover {
    color: #c82333;
    text-decoration: underline;
  }
  
  /* Move error styles outside media query */
  .error-messages {
    background-color: #fee2e2;
    border: 1px solid #ef4444;
    border-radius: 4px;
    padding: 8px;
    margin-bottom: 16px;
  }

  .error-message {
    color: #dc2626;
    font-size: 14px;
    margin: 4px 0;
  }

  @media (max-width: 768px) {
    .modal-container {
      width: 95%;
      padding: 20px;
    }
    
    .form-grid {
      grid-template-columns: 1fr;
    }
    
    .section-header {
      flex-direction: column;
      align-items: flex-start;
      gap: 10px;
    }
    
    .form-actions {
      flex-direction: column;
    }
  }
</style>
