<template>
    <div class="modal-overlay" @click.self="$emit('cancel')">
      <div class="modal-container" @click.stop>
        <!-- Modal Header -->
        <div class="modal-header">
          <h2 class="modal-title">Confirm Delete</h2>
          <button @click="$emit('cancel')" class="close-btn">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <line x1="18" y1="6" x2="6" y2="18"></line>
              <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
          </button>
        </div>
  
        <!-- Warning Icon -->
        <div class="warning-icon">
          <div class="icon-container">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"></path>
              <line x1="12" y1="9" x2="12" y2="13"></line>
              <line x1="12" y1="17" x2="12.01" y2="17"></line>
            </svg>
          </div>
        </div>
  
        <!-- Confirmation Message -->
        <div class="confirmation-message">
          <p class="main-message">
            Are you sure you want to delete this {{ itemType }}?
          </p>
          <p class="item-name">
            <span class="item-highlight">{{ itemName }}</span>
          </p>
          <p class="warning-text" v-if="additionalWarning">
            {{ additionalWarning }}
          </p>
        </div>
  
        <!-- Action Buttons -->
        <div class="action-buttons">
          <button @click="$emit('cancel')" class="btn btn-cancel">
            Cancel
          </button>
          <button @click="$emit('confirm')" class="btn btn-delete">
            Delete
          </button>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  export default {
    name: 'DeleteConfirmation',
    props: {
      item: {
        type: Object,
        required: true
      },
      itemType: {
        type: String,
        default: 'item'
      }
    },
    computed: {
      itemName() {
        if (this.item.name) {
          return this.item.name
        } else if (this.item.first_name && this.item.last_name) {
          return `${this.item.first_name} ${this.item.last_name}`
        } else if (this.item.reference) {
          return this.item.reference
        }
        return 'this item'
      },
      
      additionalWarning() {
        if (this.itemType === 'customer') {
          return 'All associated contacts will also be deleted.'
        }
        return ''
      }
    },
    emits: ['confirm', 'cancel'] // Ensure emits are properly declared
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
    max-width: 450px;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
  }
  
  .modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
  }
  
  .modal-title {
    font-size: 1.3rem;
    font-weight: bold;
    color: #333;
    margin: 0;
  }
  
  .close-btn {
    background: none;
    border: none;
    cursor: pointer;
    color: #6c757d;
    padding: 5px;
    border-radius: 4px;
    transition: color 0.3s ease;
  }
  
  .close-btn:hover {
    color: #495057;
  }
  
  .warning-icon {
    display: flex;
    justify-content: center;
    margin-bottom: 20px;
  }
  
  .icon-container {
    width: 60px;
    height: 60px;
    background-color: #fef2f2;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #dc3545;
  }
  
  .confirmation-message {
    text-align: center;
    margin-bottom: 30px;
  }
  
  .main-message {
    font-size: 1.1rem;
    font-weight: 500;
    color: #333;
    margin-bottom: 15px;
  }
  
  .item-name {
    margin-bottom: 15px;
  }
  
  .item-highlight {
    background-color: #f8f9fa;
    padding: 4px 8px;
    border-radius: 4px;
    font-weight: 500;
    color: #495057;
  }
  
  .warning-text {
    font-size: 14px;
    color: #6c757d;
    margin: 0;
  }
  
  .action-buttons {
    display: flex;
    justify-content: flex-end;
    gap: 15px;
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
  
  .btn-cancel {
    background-color: #f8f9fa;
    color: #495057;
    border: 1px solid #dee2e6;
  }
  
  .btn-cancel:hover {
    background-color: #e9ecef;
  }
  
  .btn-delete {
    background-color: #dc3545;
    color: white;
  }
  
  .btn-delete:hover {
    background-color: #c82333;
  }
</style>
  