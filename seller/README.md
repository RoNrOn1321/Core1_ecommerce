# Core1 E-commerce Seller Backend

A comprehensive PHP backend API system for seller portal functionality in the Core1 E-commerce platform.

## Features

### 🔐 Authentication System
- Seller registration and approval workflow
- Session-based authentication
- Password hashing and security
- User profile management

### 📦 Product Management
- Complete CRUD operations for products
- Product variants and images support
- Category management
- Inventory tracking
- Bulk operations

### 📋 Order Management
- Real-time order tracking
- Status updates and history
- Shipping and tracking information
- Order analytics and reporting

### 🏪 Store Management
- Store profile customization
- Business information management
- Store analytics dashboard
- Performance metrics

### 📊 Analytics & Reporting
- Sales analytics over time
- Product performance metrics
- Customer insights
- Revenue tracking

## Directory Structure

```
seller/
├── api/                    # API endpoints
│   ├── auth/              # Authentication endpoints
│   ├── products/          # Product management
│   ├── orders/            # Order management
│   ├── store/             # Store management
│   └── analytics/         # Analytics endpoints
├── config/                # Configuration files
│   └── database.php       # Database connection
├── includes/              # Core classes and utilities
│   ├── auth.php           # Authentication class
│   ├── product.php        # Product management class
│   ├── order.php          # Order management class
│   ├── store.php          # Store management class
│   └── response.php       # API response utilities
├── js/                    # JavaScript SDK
│   └── seller-api.js      # Complete JavaScript API client
├── examples/              # Usage examples
│   ├── javascript/        # Frontend examples
│   └── postman_collection.json # Postman collection
└── docs/                  # Documentation
    └── API_DOCUMENTATION.md
```

## Quick Start

### 1. Database Setup
Ensure your MySQL database is set up with the Core1 e-commerce schema. Update the database configuration in:
```php
// seller/config/database.php
$host = 'localhost';
$dbname = 'core1_ecommerce';
$username = 'root';
$password = '';
```

### 2. Basic API Usage

#### Authentication
```javascript
const api = new SellerAPI('http://localhost/Core1_ecommerce/seller/api');

// Login
await api.login('seller@example.com', 'password123');

// Get current seller info
const seller = await api.getCurrentSeller();
```

#### Product Management
```javascript
// Get products
const products = await api.getProducts({
    limit: 10,
    status: 'published',
    category_id: 1
});

// Create product
const newProduct = await api.createProduct({
    name: 'Sample Product',
    price: 29.99,
    description: 'Product description',
    stock_quantity: 100
});

// Update product
await api.updateProduct(productId, {
    price: 35.99,
    stock_quantity: 150
});
```

#### Order Management
```javascript
// Get orders
const orders = await api.getOrders({
    status: 'pending',
    limit: 20
});

// Update order status
await api.updateOrderStatus(orderId, 'processing', 'Order is being prepared');

// Update tracking info
await api.updateOrderTracking(orderId, {
    courier_company: 'FedEx',
    tracking_number: '123456789',
    estimated_delivery_date: '2024-01-15'
});
```

### 3. Frontend Integration

Include the JavaScript SDK in your HTML:
```html
<script src="path/to/seller/js/seller-api.js"></script>
<script>
    const api = new SellerAPI();
    
    // Your application logic here
</script>
```

## API Endpoints

### Authentication
- `POST /api/auth/login` - Seller login
- `POST /api/auth/register` - Register new seller
- `GET /api/auth/me` - Get current seller info
- `POST /api/auth/logout` - Logout

### Products
- `GET /api/products/` - List products with filters
- `POST /api/products/` - Create new product
- `GET /api/products/detail?id={id}` - Get product details
- `PUT /api/products/detail?id={id}` - Update product
- `DELETE /api/products/detail?id={id}` - Delete product
- `GET /api/products/categories` - Get categories

### Orders
- `GET /api/orders/` - List orders with filters
- `GET /api/orders/detail?id={id}` - Get order details
- `PUT /api/orders/detail?id={id}` - Update order status/tracking
- `GET /api/orders/stats` - Get order statistics

### Store
- `GET /api/store/profile` - Get store profile
- `PUT /api/store/profile` - Update store profile
- `GET /api/store/dashboard` - Get dashboard data

### Analytics
- `GET /api/analytics/?period={days}` - Get analytics data

## Examples

### 1. Basic Usage Example
Open `examples/javascript/basic-usage.html` to see a complete working example of the API in action.

### 2. Product Management
See `examples/javascript/product-management.html` for a full product management interface.

### 3. Order Management  
Check `examples/javascript/order-management.html` for order processing workflows.

### 4. Postman Collection
Import `examples/postman_collection.json` into Postman for API testing.

## JavaScript SDK Features

The included JavaScript SDK (`js/seller-api.js`) provides:

- **Complete API Coverage**: All endpoints wrapped in easy-to-use methods
- **Error Handling**: Proper error handling and reporting
- **Session Management**: Automatic session handling
- **Utility Functions**: Currency formatting, date formatting, status helpers
- **Modern JavaScript**: Promise-based API with async/await support
- **Framework Agnostic**: Works with any frontend framework or vanilla JS

## Security Features

- Password hashing using PHP's `password_hash()`
- SQL injection prevention with prepared statements
- Session-based authentication
- Input validation and sanitization
- CORS headers for cross-origin requests
- Proper error handling without sensitive data exposure

## Development

### Adding New Endpoints
1. Create PHP file in appropriate `api/` subdirectory
2. Include necessary classes from `includes/`
3. Implement proper authentication checks
4. Add validation and error handling
5. Update JavaScript SDK with new methods
6. Document in API_DOCUMENTATION.md

### Database Changes
When adding new features that require database changes:
1. Update the main database schema
2. Update relevant classes in `includes/`
3. Test thoroughly with existing data

## Contributing

1. Follow PSR-4 autoloading standards
2. Use prepared statements for all database queries
3. Implement proper error handling
4. Add JSDoc comments for JavaScript functions
5. Update documentation for new features

## Requirements

- PHP 7.4+
- MySQL 5.7+
- Web server (Apache/Nginx)
- Modern browser for JavaScript examples

## License

This project is part of the Core1 E-commerce platform.