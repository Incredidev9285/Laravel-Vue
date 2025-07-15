# Customer Management System

A modern customer management system built with Laravel 11 backend API and Vue.js 3 frontend, featuring comprehensive CRUD operations, advanced validation, and a responsive user interface.

## 🚀 Features

- **Customer Management**: Create, read, update, and delete customers with comprehensive validation
- **Contact Management**: Manage customer contacts with nested CRUD operations
- **Category System**: Organize customers by pre-defined categories (Gold, Silver, Bronze)
- **Advanced Search**: Plain text search across customer names, references, and categories
- **Real-time Validation**: Client-side and server-side validation with inline error display
- **Responsive Design**: Mobile-friendly interface with custom CSS styling
- **Modal-based UI**: Intuitive modal dialogs for create/edit operations
- **Data Security**: HTML tag prevention and input sanitization

## 🛠️ Tech Stack

### Backend
- **Laravel 11** - PHP framework
- **MySQL** - Database
- **Laravel Sanctum** - API authentication
- **Eloquent ORM** - Database relationships

### Frontend
- **Vue.js 3** - JavaScript framework
- **Axios** - HTTP client
- **Custom CSS** - Styling (no external frameworks)
- **Vite** - Build tool

## 📋 Prerequisites

- PHP 8.2 or higher
- Composer
- Node.js 18+ and npm
- MySQL 8.0+
- Git

## 🔧 Installation

### Backend Setup (Laravel)

1. **Clone the repository**
   ```bash
   git clone 
   cd customer-management-system
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Environment configuration**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Database configuration**
   
   Update your `.env` file:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=laravel
   DB_USERNAME=root
   DB_PASSWORD=
   ```

5. **Install API routes**
   ```bash
   php artisan install:api
   ```

6. **Run migrations and seeders**
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

7. **Start Laravel server**
   ```bash
   php artisan serve
   ```

### Frontend Setup (Vue.js)

1. **Navigate to frontend directory**
   ```bash
   cd vue-frontend
   ```

2. **Install dependencies**
   ```bash
   npm install
   ```

3. **Start development server**
   ```bash
   npm run dev
   ```

## 🗄️ Database Schema

### Customer Categories
- `id` - Primary key
- `name` - Category name (Gold, Silver, Bronze)
- `timestamps`

### Customers
- `id` - Primary key
- `name` - Customer name (2-100 chars, letters/spaces/hyphens/apostrophes/periods only)
- `reference` - Unique reference code (3-20 chars, uppercase letters/numbers/hyphens)
- `category_id` - Foreign key to customer_categories
- `start_date` - Customer start date
- `description` - Optional description (max 500 chars)
- `timestamps`

### Contacts
- `id` - Primary key
- `first_name` - Contact first name
- `last_name` - Contact last name
- `customer_id` - Foreign key to customers (cascade delete)
- `timestamps`

## 📡 API Endpoints

### Customers
- `GET /api/customers` - List all customers (with search)
- `POST /api/customers` - Create new customer
- `GET /api/customers/{id}` - Get specific customer
- `PUT /api/customers/{id}` - Update customer
- `DELETE /api/customers/{id}` - Delete customer

### Contacts
- `GET /api/contacts` - List all contacts
- `POST /api/contacts` - Create new contact
- `GET /api/contacts/{id}` - Get specific contact
- `PUT /api/contacts/{id}` - Update contact
- `DELETE /api/contacts/{id}` - Delete contact

### Categories
- `GET /api/customer-categories` - List all categories

## 🎯 Usage

### Creating a Customer
1. Click the "Create" button on the customer list
2. Fill in the required fields:
   - **Name**: Customer name (letters, spaces, hyphens, apostrophes, periods only)
   - **Reference**: Unique identifier (uppercase letters, numbers, hyphens)
   - **Category**: Select from dropdown
   - **Start Date**: Customer start date
   - **Description**: Optional description
3. Click "Save" to create the customer

### Managing Contacts
1. Edit an existing customer
2. In the "Contacts" section, click "Create Contact"
3. Enter first name and last name
4. Save the contact
5. Edit or delete contacts as needed

### Searching Customers
1. Use the search input to find customers by:
   - Customer name
   - Reference code
   - Category name
2. Use category filter dropdown for specific categories
3. Click "Apply" to filter results
4. Click "Clear" to reset filters

## ✅ Validation Rules

### Customer Name
- Required field
- 2-100 characters
- Letters, spaces, hyphens, apostrophes, periods only
- No leading/trailing spaces
- No consecutive spaces or special characters
- No HTML tags or entities

### Customer Reference
- Required field
- 3-20 characters
- Uppercase letters, numbers, hyphens only
- Must be unique across all customers
- Auto-converted to uppercase
- No HTML tags

### Description
- Optional field
- Maximum 500 characters
- No HTML tags or scripts
- Character counter displayed

## 🔒 Security Features

- **Input Sanitization**: All inputs are sanitized to prevent XSS attacks
- **HTML Tag Prevention**: Regex validation blocks HTML tags and entities
- **SQL Injection Protection**: Eloquent ORM with parameterized queries
- **CORS Configuration**: Proper cross-origin resource sharing setup
- **Validation**: Multi-layer validation (client-side and server-side)

## 🎨 UI Features

- **Responsive Design**: Works on desktop, tablet, and mobile devices
- **Modal Dialogs**: Intuitive create/edit interfaces
- **Inline Validation**: Real-time error display below form fields
- **Loading States**: Visual feedback during API operations
- **Confirmation Dialogs**: Safe delete operations with confirmation
- **Search and Filter**: Advanced filtering capabilities

## 📁 Project Structure

```
customer-management-system/
├── laravel-api-backend/
│   ├── app/
│   │   ├── Http/Controllers/Api/
│   │   │   ├── CustomerController.php
│   │   │   ├── ContactController.php
│   │   │   └── CustomerCategoryController.php
│   │   └── Models/
│   │       ├── Customer.php
│   │       ├── Contact.php
│   │       └── CustomerCategory.php
│   ├── database/
│   │   ├── migrations/
│   │   └── seeders/
│   └── routes/
│       └── api.php
└── vue-frontend/
    ├── src/
    │   ├── components/
    │   │   ├── CustomerList.vue
    │   │   ├── CustomerModal.vue
    │   │   ├── ContactModal.vue
    │   │   └── DeleteConfirmation.vue
    │   ├── services/
    │   │   ├── api.js
    │   │   ├── customerService.js
    │   │   ├── contactService.js
    │   │   └── categoryService.js
    │   └── App.vue
    └── package.json
```

## 🚀 Development

### Running in Development Mode

1. **Start Laravel backend**:
   ```bash
   cd laravel-api-backend
   php artisan serve
   ```

2. **Start Vue frontend**:
   ```bash
   cd vue-frontend
   npm run serve
   ```

3. **Access the application**:
   - Frontend: `http://localhost:3000` (or Vite dev server URL)
   - Backend API: `http://localhost:8000/api`

### Building for Production

1. **Build frontend assets**:
   ```bash
   cd vue-frontend
   npm run build
   ```

2. **Optimize Laravel**:
   ```bash
   cd laravel-backend
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

## 🧪 Testing

### Manual Testing Checklist

- [ ] Create customer with valid data
- [ ] Create customer with invalid data (test validation)
- [ ] Edit existing customer
- [ ] Delete customer (with confirmation)
- [ ] Search customers by name/reference/category
- [ ] Filter customers by category
- [ ] Create/edit/delete contacts within customer modal
- [ ] Test responsive design on different screen sizes
- [ ] Test modal interactions (close on overlay click, text selection)

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request


**Built with ❤️ using Laravel 11 and Vue.js 3**
