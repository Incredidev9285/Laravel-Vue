<template>
    <div class="modal-overlay" @click.self="$emit('close')" @mousedown="handleOverlayMouseDown" @click="handleOverlayClick">
      <div class="modal-container" @click.stop>
        <h2 class="modal-title">
          {{ customer ? 'Edit Customer' : 'Create Customer' }}
        </h2>
  
        <form @submit.prevent="saveCustomer" class="customer-form">
          
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
                  :class="{ 'input-error': nameErrors.length > 0 }"
                  @blur="validateName"
                  @input="clearNameErrors"
                  placeholder="e.g. John Smith or ABC Corporation"
                >
                <div v-if="nameErrors.length > 0" class="field-errors">
                  <small v-for="error in nameErrors" :key="error" class="error-text">
                    {{ error }}
                  </small>
                </div>
                <small class="field-hint">
                  Letters, spaces, hyphens, apostrophes, and periods only. 2-100 characters.
                </small>
              </div>
              
              <div class="form-group">
                <label class="form-label">Reference *</label>
                
                <input 
                  v-model="form.reference"
                  type="text" 
                  required
                  class="form-input"
                  :class="{ 'input-error': referenceErrors.length > 0 }"
                  @blur="validateReference"
                  @input="clearReferenceErrors"
                  placeholder="e.g. CUST-001 or ABC123"
                  style="text-transform: uppercase;"
                >
                <div v-if="referenceErrors.length > 0" class="field-errors">
                  <small v-for="(error, index) in referenceErrors" :key="index" class="error-text">
                    {{ error }}
                  </small>
                </div>
                <small class="field-hint">
                  Uppercase letters, numbers, and hyphens only. 3-20 characters. Must be unique.
                </small>
              </div>
              
              <div class="form-group">
                <label class="form-label">Category *</label>
                <select v-model="form.customer_category_id" required class="form-select" :class="{ 'input-error': categoryErrors.length > 0 }"
                  @change="clearCategoryErrors">
                  <option value="">Select Category</option>
                  <option v-for="category in categories" :key="category.id" :value="category.id">
                    {{ category.name }}
                  </option>
                </select>
                <div v-if="categoryErrors.length > 0" class="field-errors">
                  <small v-for="(error, index) in categoryErrors" :key="index" class="error-text">
                    {{ error }}
                  </small>
                </div>
              </div>
              
              <div class="form-group">
                <label class="form-label">Start Date *</label>
                <input 
                  v-model="form.start_date"
                  type="date" 
                  required
                  class="form-input"
                  :class="{ 'input-error': startDateErrors.length > 0 }"
                  
                  @change="clearStartDateErrors"
                >
                <div v-if="startDateErrors.length > 0" class="field-errors">
                  <small v-for="(error, index) in startDateErrors" :key="index" class="error-text">
                    {{ error }}
                  </small>
                </div>
              </div>
            </div>
            
            <div class="form-group">
              <label class="form-label">Description</label>
                            
              <textarea 
                v-model="form.description"
                rows="3"
                class="form-textarea"
                :class="{ 'input-error': descriptionErrors.length > 0 }"
                maxlength="500"
                placeholder="Optional description (max 500 characters)"
                @input="clearDescriptionErrors"
              ></textarea>
              <div v-if="descriptionErrors.length > 0" class="field-errors">
                <small v-for="(error, index) in descriptionErrors" :key="index" class="error-text">
                  {{ error }}
                </small>
              </div>
              <small class="field-hint">
                {{ form.description ? form.description.length : 0 }}/500 characters
              </small>
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
        customerDataChanged: false,
        textSelectionActive: false,
        nameErrors: [],
        referenceErrors: [],
        categoryErrors: [],
        startDateErrors: [],
        descriptionErrors: []
      }
    },
    computed: {
      isFormValid() {
        return this.form.name && 
              this.form.reference && 
              this.form.category_id && 
              this.form.start_date &&
              this.nameErrors.length === 0 &&
              this.referenceErrors.length === 0 &&
              this.categoryErrors.length === 0 &&
              this.startDateErrors.length === 0 &&
              this.descriptionErrors.length === 0
      }
    },

    beforeUnmount() {
      // Clean up event listener
      document.removeEventListener('selectionchange', this.handleTextSelection)
    },

    mounted() {
      if (this.customer) {
        this.form = { ...this.customer }
        this.form.start_date = this.customer.start_date?.split('T')[0]
        this.contacts = this.customer.contacts || []
      }

      // Listen for selection changes
      document.addEventListener('selectionchange', this.handleTextSelection)
    },
    methods: {
      handleOverlayMouseDown(event) {
        // Only track mousedown if it's directly on the overlay (not on modal content)
        if (event.target === event.currentTarget) {
          this.mouseDownOnOverlay = true
          this.textSelectionActive = false
        } else {
          this.mouseDownOnOverlay = false
        }
      },

      handleOverlayClick(event) {
        // Only close modal if:
        // 1. Click is directly on overlay (not bubbled from content)
        // 2. Mouse was pressed down on overlay (not dragged from content)
        // 3. No text selection is active
        if (event.target === event.currentTarget && 
            this.mouseDownOnOverlay && 
            !this.textSelectionActive) {
          this.$emit('close')
        }
        
        // Reset tracking
        this.mouseDownOnOverlay = false
      },

      // Track text selection activity
      handleTextSelection() {
        const selection = window.getSelection()
        this.textSelectionActive = selection.toString().length > 0
      },
      clearAllErrors() {
        this.nameErrors = []
        this.referenceErrors = []
        this.categoryErrors = []
        this.startDateErrors = []
        this.descriptionErrors = []
      },
      validateDescription() {
        this.descriptionErrors = []
        if (this.form.description && this.form.description.length > 500) {
          this.descriptionErrors.push('Description cannot exceed 500 characters')
        }
      },
      validateName() {
        this.nameErrors = []
        const name = this.form.name?.trim()
        
        if (!name) {
          this.nameErrors.push('Customer name is required')
          return
        }
        
        if (name.length < 2) {
          this.nameErrors.push('Name must be at least 2 characters')
        }
        
        if (name.length > 100) {
          this.nameErrors.push('Name cannot exceed 100 characters')
        }
        
        if (!/^[A-Za-z\s\-'.]+$/u.test(name)) {
          this.nameErrors.push('Name can only contain letters, spaces, hyphens, apostrophes, and periods')
        }
        
        if (/^\s|\s$/.test(name)) {
          this.nameErrors.push('Name cannot start or end with spaces')
        }
        
        if (/\s{2,}/.test(name)) {
          this.nameErrors.push('Name cannot contain consecutive spaces')
        }
        
        if (/[-'.]{2,}/.test(name)) {
          this.nameErrors.push('Name cannot contain consecutive special characters')
        }
      },

      validateReference() {
        this.referenceErrors = []
        const reference = this.form.reference?.trim().toUpperCase()
        
        if (!reference) {
          this.referenceErrors.push('Customer reference is required')
          return
        }
        
        if (reference.length < 3) {
          this.referenceErrors.push('Reference must be at least 3 characters')
        }
        
        if (reference.length > 20) {
          this.referenceErrors.push('Reference cannot exceed 20 characters')
        }
        
        if (!/^[A-Z0-9-]+$/.test(reference)) {
          this.referenceErrors.push('Reference can only contain uppercase letters, numbers, and hyphens')
        }
        
        // Update form with uppercase version
        this.form.reference = reference.toUpperCase()
      },

      clearNameErrors() {
        this.nameErrors = []
      },

      clearReferenceErrors() {
        this.referenceErrors = []
      },

      clearCategoryErrors() { this.categoryErrors = [] },
      clearStartDateErrors() { this.startDateErrors = [] },
      clearDescriptionErrors() { this.descriptionErrors = [] },

      async saveCustomer() {
        // Clear all previous errors
        this.clearAllErrors()
        // Validate before submission
        this.validateName()
        this.validateReference()
        this.validateDescription()
        this.saving = true
        console.log('Saving customer:', this.form);
        
        try {
          if (this.customer) {
            await customerService.update(this.customer.id, this.form)
          } else {
            await customerService.create(this.form)
          }
          this.$emit('save')
          this.$emit('close')
        } catch (error) {
          if (error.response?.status === 422) {
            const errors = error.response.data.errors
          
            // Map backend errors to component error arrays
            this.nameErrors = errors.name || []
            this.referenceErrors = errors.reference || []
            this.categoryErrors = errors.category_id || []
            this.startDateErrors = errors.start_date || []
            this.descriptionErrors = errors.description || []
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

          // Mark that customer data has changed
          this.customerDataChanged = true
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

          // Mark that customer data has changed
          this.customerDataChanged = true
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
  .form-input.error,
  .form-select.error,
  .form-textarea.error {
    border-color: #dc3545;
    box-shadow: 0 0 0 2px rgba(220, 53, 69, 0.25);
  }

  .field-errors {
    margin-top: 5px;
  }

  .input-error {
    border-color: #dc3545 !important;
    box-shadow: 0 0 0 2px rgba(220, 53, 69, 0.25) !important;
  }

  .error-text {
    color: #dc3545;
    font-size: 12px;
    display: block;
    margin-bottom: 2px;
  }

  .field-hint {
    color: #6c757d;
    font-size: 12px;
    margin-top: 3px;
    display: block;
  }

  .btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
  }
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
  /* .error-messages {
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
  } */

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
