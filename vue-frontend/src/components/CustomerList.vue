<template>
    <div class="container">
      <!-- Header -->
      <div class="header">
        <h1 class="title">Customers</h1>
        <button @click="openCreateModal" class="btn btn-primary">
          Create
        </button>
      </div>
  
      <!-- Search Section -->
      <div class="search-section">
        <div class="search-controls">
          <input 
            v-model="searchText"
            @input="handleSearch"
            type="text" 
            placeholder="Search customers..."
            class="search-input"
          >
          <select v-model="categoryFilter" class="select-input">
            <option value="">All Categories</option>
            <option v-for="category in categories" :key="category.id" :value="category.id">
              {{ category.name }}
            </option>
          </select>
          <button @click="clearFilters" class="btn btn-secondary">
            Clear
          </button>
          <button @click="applyFilters" class="btn btn-success">
            Apply
          </button>
        </div>
      </div>
  
      <!-- Customer Table -->
      <div class="table-container">
        <table class="customer-table">
          <thead>
            <tr>
              <th>Name</th>
              <th>Reference</th>
              <th>Category</th>
              <th>No of Contacts</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="customer in customers" :key="customer.id" class="table-row">
              <td class="table-cell">{{ customer.name }}</td>
              <td class="table-cell">{{ customer.reference }}</td>
              <td class="table-cell">{{ customer.category?.name }}</td>
              <td class="table-cell">{{ customer.contacts?.length || 0 }}</td>
              <td class="table-cell">
                <button @click="editCustomer(customer)" class="btn-link btn-edit">
                  Edit
                </button>
                <button @click="deleteCustomer(customer)" class="btn-link btn-delete">
                  Delete
                </button>
              </td>
            </tr>
            <tr v-if="customers.length === 0">
              <td colspan="5" class="no-data">No customers found</td>
            </tr>
          </tbody>
        </table>
      </div>
  
      <!-- Customer Modal -->
      <CustomerModal 
        v-if="showModal"
        :customer="selectedCustomer"
        :categories="categories"
        @close="closeModal"
        @save="handleSave"
      />
  
      <!-- Delete Confirmation Modal -->
      <DeleteConfirmation
        v-if="showDeleteModal"
        :item="customerToDelete"
        item-type="customer"
        @confirm="confirmDelete"
        @cancel="cancelDelete"
      />
    </div>
  </template>
  
  <script>
  import { customerService } from '@/services/customerService'
  import { categoryService } from '@/services/categoryService'
  import CustomerModal from './CustomerModal.vue'
  import DeleteConfirmation from './DeleteConfirmation.vue'
  
  export default {
    name: 'CustomerList',
    components: {
      CustomerModal,
      DeleteConfirmation
    },
    data() {
      return {
        customers: [],
        categories: [],
        searchText: '',
        categoryFilter: '',
        showModal: false,
        showDeleteModal: false,
        selectedCustomer: null,
        customerToDelete: null,
        loading: false
      }
    },
    async mounted() {
      await this.loadCustomers()
      await this.loadCategories()
    },
    methods: {
      async loadCustomers() {
        this.loading = true
        try {
          const response = await customerService.getAll()
          this.customers = response.data.data
        } catch (error) {
          console.error('Error loading customers:', error)
        } finally {
          this.loading = false
        }
      },
      
      async loadCategories() {
        try {
          const response = await categoryService.getAll()
          this.categories = response.data.data
        } catch (error) {
          console.error('Error loading categories:', error)
        }
      },
  
      handleSearch() {
        this.applyFilters()
      },
  
      async applyFilters() {
        try {
          const params = {}
          if (this.searchText) params.search = this.searchText
          if (this.categoryFilter) params.category_id = this.categoryFilter
          
          console.log('Sending filter parameters:', params)
          
          const response = await customerService.getAll(params)
          this.customers = response.data.data
        } catch (error) {
          console.error('Error applying filters:', error)
        }
      },
  
      clearFilters() {
        this.searchText = ''
        this.categoryFilter = ''
        this.loadCustomers()
      },
  
      openCreateModal() {
        this.selectedCustomer = null
        this.showModal = true
      },
  
      editCustomer(customer) {
        this.selectedCustomer = customer
        this.showModal = true
      },
  
      async closeModal() {
        this.showModal = false
        this.selectedCustomer = null

        // Always refresh the list when modal closes
        await this.loadCustomers()
      },
  
      async handleSave() {
        this.closeModal()
        await this.loadCustomers()
      },
  
      deleteCustomer(customer) {
        this.customerToDelete = customer
        this.showDeleteModal = true
      },
  
      async confirmDelete() {
        try {
          console.log('Deleting customer:', this.customerToDelete.id)
          await customerService.delete(this.customerToDelete.id)
          this.showDeleteModal = false
          this.customerToDelete = null
          await this.loadCustomers()
        } catch (error) {
          console.error('Error deleting customer:', error)
        }
      },
  
      cancelDelete() {
        this.showDeleteModal = false
        this.customerToDelete = null
      }
    }
  }
  </script>
  
  <style scoped>
  .container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
    background-color: #f5f5f5;
    min-height: 100vh;
  }
  
  .header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
  }
  
  .title {
    font-size: 2rem;
    font-weight: bold;
    color: #333;
    margin: 0;
  }
  
  .search-section {
    background: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-bottom: 20px;
  }
  
  .search-controls {
    display: flex;
    gap: 15px;
    align-items: center;
    flex-wrap: wrap;
  }
  
  .search-input {
    flex: 1;
    min-width: 200px;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
  }
  
  .search-input:focus {
    outline: none;
    border-color: #007bff;
    box-shadow: 0 0 0 2px rgba(0,123,255,0.25);
  }
  
  .select-input {
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
    background: white;
    min-width: 150px;
  }
  
  .select-input:focus {
    outline: none;
    border-color: #007bff;
    box-shadow: 0 0 0 2px rgba(0,123,255,0.25);
  }
  
  .btn {
    padding: 10px 20px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 14px;
    font-weight: 500;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-block;
  }
  
  .btn-primary {
    background-color: #007bff;
    color: white;
  }
  
  .btn-primary:hover {
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
  
  .table-container {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    overflow: hidden;
  }
  
  .customer-table {
    width: 100%;
    border-collapse: collapse;
  }
  
  .customer-table th {
    background-color: #f8f9fa;
    padding: 15px;
    text-align: left;
    font-weight: 600;
    color: #495057;
    border-bottom: 2px solid #dee2e6;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }
  
  .table-row {
    transition: background-color 0.2s ease;
  }
  
  .table-row:hover {
    background-color: #f8f9fa;
  }
  
  .table-cell {
    padding: 15px;
    border-bottom: 1px solid #dee2e6;
    color: #495057;
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
  
  .no-data {
    text-align: center;
    padding: 40px;
    color: #6c757d;
    font-style: italic;
  }
  
  @media (max-width: 768px) {
    .search-controls {
      flex-direction: column;
      align-items: stretch;
    }
    
    .search-input {
      min-width: auto;
    }
    
    .customer-table {
      font-size: 14px;
    }
    
    .table-cell {
      padding: 10px;
    }
  }
  </style>
  