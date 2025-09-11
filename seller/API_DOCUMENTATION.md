# Seller Backend API Documentation

This document outlines the seller-side backend APIs for the Core1 E-commerce platform.

## Base URL
```
http://localhost/Core1_ecommerce/seller/api
```

## Authentication
All protected endpoints require seller authentication. Login first to establish a session.

## Response Format
All endpoints return JSON responses in the following format:

### Success Response
```json
{
    "success": true,
    "message": "Success message",
    "data": {
        // Response data
    }
}
```

### Error Response
```json
{
    "success": false,
    "message": "Error message",
    "data": {
        // Optional error details
    }
}
```

## Authentication Endpoints

### POST /auth/login
Login a seller account.

**Request Body:**
```json
{
    "email": "seller@example.com",
    "password": "password123"
}
```

**Response:**
```json
{
    "success": true,
    "message": "Login successful",
    "data": {
        "id": 1,
        "user_id": 5,
        "email": "seller@example.com",
        "name": "John Doe",
        "store_name": "John's Store",
        "store_slug": "johns-store"
    }
}
```

### POST /auth/register
Register a new seller account.

**Request Body:**
```json
{
    "email": "newseller@example.com",
    "password": "password123",
    "first_name": "Jane",
    "last_name": "Smith",
    "phone": "+1234567890",
    "store_name": "Jane's Store",
    "store_description": "Quality products for everyone",
    "business_type": "individual",
    "tax_id": "123456789"
}
```

### GET /auth/me
Get current seller information.

**Response:**
```json
{
    "success": true,
    "message": "Seller information retrieved",
    "data": {
        "id": 1,
        "store_name": "John's Store",
        "store_slug": "johns-store",
        "email": "seller@example.com",
        "first_name": "John",
        "last_name": "Doe"
        // ... other seller details
    }
}
```

### POST /auth/logout
Logout the current seller.

## Product Management

### GET /products/
Get seller's products with optional filters.

**Query Parameters:**
- `category_id` (optional): Filter by category
- `status` (optional): Filter by product status (draft, published, archived)
- `search` (optional): Search in product name/description
- `limit` (optional): Number of products per page (default: 20)
- `offset` (optional): Pagination offset (default: 0)

**Response:**
```json
{
    "success": true,
    "message": "Products retrieved successfully",
    "data": {
        "products": [
            {
                "id": 1,
                "name": "Product Name",
                "price": 29.99,
                "stock_quantity": 100,
                "status": "published",
                "category_name": "Electronics",
                "primary_image": "path/to/image.jpg"
                // ... other product details
            }
        ],
        "total": 150,
        "limit": 20,
        "offset": 0
    }
}
```

### POST /products/
Create a new product.

**Request Body:**
```json
{
    "name": "New Product",
    "description": "Product description",
    "short_description": "Short description",
    "category_id": 1,
    "price": 29.99,
    "compare_price": 39.99,
    "sku": "PROD001",
    "stock_quantity": 100,
    "weight": 0.5,
    "status": "draft",
    "images": [
        {
            "url": "path/to/image1.jpg",
            "alt_text": "Product image"
        }
    ],
    "variants": [
        {
            "name": "Size-Color",
            "sku": "PROD001-L-RED",
            "price": 31.99,
            "stock_quantity": 20,
            "attributes": {
                "size": "L",
                "color": "Red"
            }
        }
    ]
}
```

### GET /products/detail?id={product_id}
Get detailed information about a specific product.

### PUT /products/detail?id={product_id}
Update a product.

### DELETE /products/detail?id={product_id}
Delete a product.

### GET /products/categories
Get all available product categories.

## Order Management

### GET /orders/
Get seller's orders with optional filters.

**Query Parameters:**
- `status` (optional): Filter by order status
- `payment_status` (optional): Filter by payment status
- `search` (optional): Search in order number or customer name
- `date_from` (optional): Filter orders from date (YYYY-MM-DD)
- `date_to` (optional): Filter orders to date (YYYY-MM-DD)
- `limit` (optional): Number of orders per page
- `offset` (optional): Pagination offset

### GET /orders/detail?id={order_id}
Get detailed information about a specific order.

### PUT /orders/detail?id={order_id}
Update order status or tracking information.

**Request Body for Status Update:**
```json
{
    "status": "processing",
    "notes": "Order is being prepared"
}
```

**Request Body for Tracking Update:**
```json
{
    "courier_company": "FedEx",
    "tracking_number": "1234567890",
    "estimated_delivery_date": "2024-01-15"
}
```

### GET /orders/stats
Get order statistics for the seller.

## Store Management

### GET /store/profile
Get store profile information.

### PUT /store/profile
Update store profile.

**Request Body:**
```json
{
    "store_name": "Updated Store Name",
    "store_description": "Updated description",
    "business_type": "business",
    "first_name": "Updated First Name",
    "last_name": "Updated Last Name",
    "phone": "+1234567890"
}
```

### GET /store/dashboard
Get dashboard summary data including:
- Product counts
- Order statistics
- Revenue data
- Recent orders
- Top selling products

## Analytics

### GET /analytics/?period={days}
Get store analytics for specified period.

**Query Parameters:**
- `period`: Number of days (7, 30, 90, 365)

**Response includes:**
- Sales over time
- Product performance
- Customer insights

## Error Codes

- `400` - Bad Request (validation errors)
- `401` - Unauthorized (authentication required)
- `403` - Forbidden (access denied)
- `404` - Not Found
- `405` - Method Not Allowed
- `422` - Validation Failed

## Status Codes

### Order Status
- `pending` - Order received
- `processing` - Order being prepared
- `shipped` - Order shipped
- `delivered` - Order delivered
- `cancelled` - Order cancelled

### Product Status
- `draft` - Not published
- `published` - Live on store
- `archived` - Archived product

### Payment Status
- `pending` - Payment pending
- `paid` - Payment completed
- `failed` - Payment failed
- `refunded` - Payment refunded