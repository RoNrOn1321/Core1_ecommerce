-- Sample data for ShopZone E-commerce Platform
-- Run this after creating the database structure

-- Create admin users
INSERT INTO admin_users (username, email, password_hash, first_name, last_name, role, is_active) VALUES
('admin', 'admin@shopzone.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Super', 'Admin', 'super_admin', 1),
('manager', 'manager@shopzone.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Store', 'Manager', 'admin', 1),
('support', 'support@shopzone.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Support', 'Agent', 'support_agent', 1);

-- Password for all admin accounts is: password

-- Create sample customers
INSERT INTO users (email, password_hash, phone, first_name, last_name, email_verified, phone_verified) VALUES
('john.doe@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+639123456789', 'John', 'Doe', 1, 1),
('jane.smith@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+639123456790', 'Jane', 'Smith', 1, 1),
('mike.wilson@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+639123456791', 'Mike', 'Wilson', 1, 0),
('sarah.brown@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+639123456792', 'Sarah', 'Brown', 1, 1),
('david.lee@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+639123456793', 'David', 'Lee', 1, 1);

-- Create sample sellers (some pending, some approved)
INSERT INTO sellers (user_id, store_name, store_slug, store_description, business_type, status) VALUES
(1, 'TechGear Pro', 'techgear-pro', 'Premium electronics and gadgets for tech enthusiasts', 'business', 'approved'),
(2, 'Fashion Hub', 'fashion-hub', 'Trendy clothing and accessories for all ages', 'business', 'approved'),
(3, 'Home Essentials', 'home-essentials', 'Quality home goods and furniture', 'individual', 'pending'),
(4, 'Sports Arena', 'sports-arena', 'Sports equipment and athletic wear', 'business', 'pending'),
(5, 'Book Corner', 'book-corner', 'Books, magazines, and educational materials', 'individual', 'approved');

-- Create product categories
INSERT INTO categories (name, slug, description, parent_id, is_active) VALUES
('Electronics', 'electronics', 'Electronic devices and gadgets', NULL, 1),
('Fashion', 'fashion', 'Clothing and accessories', NULL, 1),
('Home & Garden', 'home-garden', 'Home improvement and garden supplies', NULL, 1),
('Sports', 'sports', 'Sports equipment and fitness gear', NULL, 1),
('Books', 'books', 'Books and educational materials', NULL, 1),
('Smartphones', 'smartphones', 'Mobile phones and accessories', 1, 1),
('Laptops', 'laptops', 'Laptops and computer accessories', 1, 1),
('Men\'s Clothing', 'mens-clothing', 'Clothing for men', 2, 1),
('Women\'s Clothing', 'womens-clothing', 'Clothing for women', 2, 1),
('Furniture', 'furniture', 'Home furniture', 3, 1);

-- Create sample products
INSERT INTO products (seller_id, category_id, name, slug, description, short_description, sku, price, compare_price, stock_quantity, status, featured) VALUES
(1, 6, 'iPhone 15 Pro', 'iphone-15-pro', 'Latest iPhone with advanced camera system and A17 Pro chip', 'Premium smartphone with cutting-edge technology', 'IP15PRO128', 52999.00, 55999.00, 25, 'published', 1),
(1, 7, 'MacBook Air M2', 'macbook-air-m2', 'Thin and light laptop with M2 chip and all-day battery life', 'Ultra-portable laptop for professionals', 'MBA13M2256', 65999.00, 69999.00, 15, 'published', 1),
(1, 1, 'AirPods Pro 2nd Gen', 'airpods-pro-2', 'Active noise cancellation with spatial audio', 'Premium wireless earbuds', 'APP2ND256', 12999.00, 14999.00, 50, 'published', 0),
(2, 8, 'Premium Cotton T-Shirt', 'premium-cotton-tshirt', 'High-quality cotton t-shirt with modern fit', 'Comfortable everyday wear', 'TCT001M', 899.00, 1199.00, 100, 'published', 0),
(2, 9, 'Summer Dress Collection', 'summer-dress', 'Elegant summer dress perfect for any occasion', 'Stylish and comfortable dress', 'SCD001S', 1899.00, 2299.00, 30, 'published', 1),
(5, 5, 'The Psychology of Money', 'psychology-of-money', 'Bestselling book about financial psychology and decision making', 'Must-read finance book', 'BK001POM', 599.00, 699.00, 75, 'published', 0),
(3, 10, 'Modern Office Chair', 'modern-office-chair', 'Ergonomic office chair with lumbar support', 'Comfortable work-from-home chair', 'MOC001BLK', 4999.00, 5999.00, 20, 'draft', 0),
(4, 4, 'Professional Basketball', 'pro-basketball', 'Official size basketball for serious players', 'High-quality sports equipment', 'BB001PRO', 1299.00, 1599.00, 40, 'draft', 0);

-- Create product images
INSERT INTO product_images (product_id, image_url, alt_text, sort_order, is_primary) VALUES
(1, 'images/iphone-15-pro-1.jpg', 'iPhone 15 Pro front view', 0, 1),
(1, 'images/iphone-15-pro-2.jpg', 'iPhone 15 Pro back view', 1, 0),
(2, 'images/macbook-air-1.jpg', 'MacBook Air open view', 0, 1),
(2, 'images/macbook-air-2.jpg', 'MacBook Air closed view', 1, 0),
(3, 'images/airpods-pro-1.jpg', 'AirPods Pro with case', 0, 1),
(4, 'images/tshirt-1.jpg', 'Cotton T-Shirt front', 0, 1),
(5, 'images/dress-1.jpg', 'Summer dress model', 0, 1),
(6, 'images/book-1.jpg', 'Psychology of Money cover', 0, 1);

-- Create sample customer addresses
INSERT INTO user_addresses (user_id, type, label, first_name, last_name, address_line_1, city, state, postal_code, is_default) VALUES
(1, 'home', 'Home Address', 'John', 'Doe', '123 Rizal Street', 'Makati', 'Metro Manila', '1200', 1),
(2, 'home', 'Home Address', 'Jane', 'Smith', '456 Bonifacio Avenue', 'Quezon City', 'Metro Manila', '1100', 1),
(2, 'office', 'Office Address', 'Jane', 'Smith', '789 BGC Central', 'Taguig', 'Metro Manila', '1630', 0),
(3, 'home', 'Home Address', 'Mike', 'Wilson', '321 EDSA Extension', 'Pasay', 'Metro Manila', '1300', 1),
(4, 'home', 'Home Address', 'Sarah', 'Brown', '654 Ortigas Center', 'Pasig', 'Metro Manila', '1605', 1);

-- Create sample orders
INSERT INTO orders (order_number, user_id, status, subtotal, tax_amount, shipping_cost, total_amount, shipping_first_name, shipping_last_name, shipping_address_1, shipping_city, shipping_state, shipping_postal_code, payment_method, payment_status) VALUES
('ORD-2024-001', 1, 'delivered', 52999.00, 6359.88, 0.00, 59358.88, 'John', 'Doe', '123 Rizal Street', 'Makati', 'Metro Manila', '1200', 'card', 'paid'),
('ORD-2024-002', 2, 'shipped', 1899.00, 227.88, 150.00, 2276.88, 'Jane', 'Smith', '456 Bonifacio Avenue', 'Quezon City', 'Metro Manila', '1100', 'gcash', 'paid'),
('ORD-2024-003', 1, 'processing', 12999.00, 1559.88, 0.00, 14558.88, 'John', 'Doe', '123 Rizal Street', 'Makati', 'Metro Manila', '1200', 'cod', 'pending'),
('ORD-2024-004', 3, 'pending', 899.00, 107.88, 150.00, 1156.88, 'Mike', 'Wilson', '321 EDSA Extension', 'Pasay', 'Metro Manila', '1300', 'card', 'paid'),
('ORD-2024-005', 4, 'cancelled', 65999.00, 7919.88, 0.00, 73918.88, 'Sarah', 'Brown', '654 Ortigas Center', 'Pasig', 'Metro Manila', '1605', 'card', 'refunded');

-- Create order items
INSERT INTO order_items (order_id, product_id, seller_id, product_name, product_sku, quantity, unit_price, total_price) VALUES
(1, 1, 1, 'iPhone 15 Pro', 'IP15PRO128', 1, 52999.00, 52999.00),
(2, 5, 2, 'Summer Dress Collection', 'SCD001S', 1, 1899.00, 1899.00),
(3, 3, 1, 'AirPods Pro 2nd Gen', 'APP2ND256', 1, 12999.00, 12999.00),
(4, 4, 2, 'Premium Cotton T-Shirt', 'TCT001M', 1, 899.00, 899.00),
(5, 2, 1, 'MacBook Air M2', 'MBA13M2256', 1, 65999.00, 65999.00);

-- Create order status history
INSERT INTO order_status_history (order_id, status, notes) VALUES
(1, 'pending', 'Order placed successfully'),
(1, 'processing', 'Payment confirmed, preparing for shipment'),
(1, 'shipped', 'Order shipped via LBC Express'),
(1, 'delivered', 'Order delivered successfully'),
(2, 'pending', 'Order placed successfully'),
(2, 'processing', 'Payment confirmed via GCash'),
(2, 'shipped', 'Order shipped via J&T Express'),
(3, 'pending', 'Order placed successfully'),
(3, 'processing', 'COD order being prepared'),
(4, 'pending', 'Order placed successfully'),
(5, 'pending', 'Order placed successfully'),
(5, 'cancelled', 'Customer requested cancellation');

-- Create sample promo codes
INSERT INTO promo_codes (code, description, type, value, minimum_order_amount, usage_limit, starts_at, expires_at) VALUES
('WELCOME10', 'Welcome discount for new customers', 'percentage', 10.00, 1000.00, 100, NOW(), DATE_ADD(NOW(), INTERVAL 30 DAY)),
('FREESHIP', 'Free shipping on orders over 2000', 'free_shipping', 0.00, 2000.00, NULL, NOW(), DATE_ADD(NOW(), INTERVAL 60 DAY)),
('SAVE500', 'Save 500 pesos on orders over 5000', 'fixed_amount', 500.00, 5000.00, 50, NOW(), DATE_ADD(NOW(), INTERVAL 15 DAY));

-- Create sample product reviews
INSERT INTO product_reviews (product_id, user_id, order_id, rating, title, review_text, is_verified_purchase, is_approved) VALUES
(1, 1, 1, 5, 'Amazing phone!', 'The iPhone 15 Pro exceeded my expectations. Camera quality is outstanding and battery life is great.', 1, 1),
(5, 2, 2, 4, 'Beautiful dress', 'Love the design and fabric quality. Fits perfectly and very comfortable to wear.', 1, 1),
(4, 4, 4, 5, 'Great quality t-shirt', 'Soft cotton material and the fit is perfect. Will definitely order more colors.', 1, 1);

-- Create FAQ categories
INSERT INTO faq_categories (name, slug, description, is_active) VALUES
('Ordering', 'ordering', 'Questions about placing orders', 1),
('Shipping', 'shipping', 'Shipping and delivery information', 1),
('Returns', 'returns', 'Return and refund policies', 1),
('Account', 'account', 'Account management questions', 1);

-- Create sample FAQs
INSERT INTO faqs (category_id, question, answer, is_featured, is_active) VALUES
(1, 'How do I place an order?', 'Simply browse our products, add items to your cart, and proceed to checkout. You can pay using various methods including credit card, GCash, PayMaya, or Cash on Delivery.', 1, 1),
(2, 'How long does shipping take?', 'Standard shipping takes 2-5 business days within Metro Manila and 5-7 business days for provincial areas. Express shipping is available for faster delivery.', 1, 1),
(3, 'What is your return policy?', 'We accept returns within 7 days of delivery for unused items in original packaging. Some restrictions apply for certain product categories.', 1, 1),
(4, 'How do I track my order?', 'You can track your order status in your account dashboard or use the tracking number provided in your confirmation email.', 1, 1),
(2, 'Do you offer free shipping?', 'Yes! We offer free shipping on orders over ₱2,000 within Metro Manila. Use promo code FREESHIP at checkout.', 0, 1);

-- Update some settings
UPDATE settings SET setting_value = 'ShopZone E-commerce' WHERE setting_key = 'site_name';
UPDATE settings SET setting_value = '+63-917-123-4567' WHERE setting_key = 'support_phone';
UPDATE settings SET setting_value = 'support@shopzone.com' WHERE setting_key = 'support_email';

-- Create sample notifications
INSERT INTO notifications (user_id, type, title, message, data) VALUES
(1, 'order_update', 'Order Delivered', 'Your order ORD-2024-001 has been delivered successfully!', '{"order_id": 1, "order_number": "ORD-2024-001"}'),
(2, 'order_update', 'Order Shipped', 'Your order ORD-2024-002 is on the way!', '{"order_id": 2, "order_number": "ORD-2024-002"}'),
(3, 'promotion', 'Welcome Offer', 'Get 10% off your first order with code WELCOME10!', '{"promo_code": "WELCOME10"}');

-- Add some activity logs
INSERT INTO activity_logs (user_type, user_id, action, resource_type, resource_id, description, ip_address) VALUES
('customer', 1, 'order_placed', 'order', 1, 'Customer placed order ORD-2024-001', '192.168.1.100'),
('customer', 2, 'order_placed', 'order', 2, 'Customer placed order ORD-2024-002', '192.168.1.101'),
('admin', 1, 'order_updated', 'order', 1, 'Order status updated to delivered', '192.168.1.200'),
('customer', 1, 'product_reviewed', 'product', 1, 'Customer reviewed iPhone 15 Pro', '192.168.1.100'),
('admin', 1, 'seller_approved', 'seller', 1, 'Seller TechGear Pro approved', '192.168.1.200');

-- Note: All user passwords are 'password' (hashed with bcrypt)
-- You can login to admin with:
-- Email: admin@shopzone.com, Password: password
-- Email: manager@shopzone.com, Password: password  
-- Email: support@shopzone.com, Password: password