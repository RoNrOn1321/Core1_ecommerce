# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Development Environment

This is a Core1 E-commerce platform built on PHP/MySQL running on XAMPP:

- **Database**: MySQL (core1_ecommerce) via `C:\xampp\mysql\bin\mysql -u root`
- **Web Server**: Apache via XAMPP
- **Base URL**: `http://localhost/Core1_ecommerce/`

### Database Access Commands

```bash
# Connect to MySQL
"C:\xampp\mysql\bin\mysql" -u root

# Access the database
"C:\xampp\mysql\bin\mysql" -u root -e "USE core1_ecommerce; [SQL_QUERY]"

# Common database queries
"C:\xampp\mysql\bin\mysql" -u root -e "USE core1_ecommerce; SHOW TABLES;"
"C:\xampp\mysql\bin\mysql" -u root -e "USE core1_ecommerce; DESCRIBE products;"
```

## Architecture Overview

### Multi-Portal System

The platform consists of three main portals:

1. **Admin Portal** (`/admin/`) - Platform administration
2. **Seller Portal** (`/seller/`) - Vendor management system with comprehensive API
3. **Customer Portal** (`/customer/`) - Customer-facing e-commerce interface (in development)

### Seller Portal Architecture

The seller portal is the most developed component with:

- **API Structure**: RESTful endpoints in `/seller/api/` with modular organization:
  - `/auth/` - Authentication endpoints
  - `/products/` - Product management
  - `/orders/` - Order management
  - `/store/` - Store management
  - `/analytics/` - Analytics endpoints

- **Database Connection**: Centralized in `/seller/config/database.php`
- **JavaScript SDK**: Complete client library at `/seller/js/seller-api.js`
- **Documentation**: Comprehensive API docs in `/seller/API_DOCUMENTATION.md`

### Database Schema

Core tables include:
- `products`, `product_images`, `product_variants`, `product_reviews`
- `orders`, `order_items`, `order_status_history`
- `sellers`, `users`, `user_addresses`
- `categories`, `cart_items`, `wishlist_items`
- `support_tickets`, `support_ticket_messages`
- `chat_sessions`, `chat_messages`
- Customer features tables (see `/database/customer_features.sql`)

### Key Configuration Files

- Database config: `/seller/config/database.php` (MySQL root user, no password)
- Database schema: `/database/customer_features.sql`
- Customer features plan: `/Customer features plan.txt`

### Planned Features

Customer portal development is planned with:
- PayMongo payment integration (GCash, cards, e-wallets)
- Leaflet maps with LocationIQ API for address management
- Live chat and support ticket system
- Order tracking with real-time updates
- Mobile-responsive design

### API Standards

- Session-based authentication
- JSON response format with consistent structure
- Prepared statements for SQL security
- CORS headers for cross-origin requests
- Error handling without sensitive data exposure

### Security Practices

- Password hashing with PHP's `password_hash()`
- SQL injection prevention via prepared statements
- Input validation and sanitization
- Secure session management

### File Organization

```
├── admin/          # Admin dashboard
├── seller/         # Seller portal (main backend)
│   ├── api/       # RESTful API endpoints
│   ├── config/    # Configuration files
│   ├── includes/  # Core classes (referenced but not found)
│   ├── js/        # JavaScript SDK
│   └── examples/  # Usage examples
├── customer/       # Customer portal (in development)
├── database/       # Database schemas and plans
└── uploads/        # File uploads storage
```

When working on this codebase:
1. Always test database queries with the MySQL command first
2. Follow the existing API patterns in the seller portal
3. Use prepared statements for all database operations
4. Reference the seller portal's structure for consistency
5. Check existing documentation before creating new endpoints