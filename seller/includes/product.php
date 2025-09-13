<?php
require_once __DIR__ . '/../config/database.php';

class ProductManager {
    private $pdo;
    
    public function __construct($database) {
        $this->pdo = $database;
    }
    
    public function getProducts($sellerId, $filters = []) {
        try {
            $where = "WHERE p.seller_id = ?";
            $params = [$sellerId];
            
            if (!empty($filters['category_id'])) {
                $where .= " AND p.category_id = ?";
                $params[] = $filters['category_id'];
            }
            
            if (!empty($filters['status'])) {
                $where .= " AND p.status = ?";
                $params[] = $filters['status'];
            }
            
            if (!empty($filters['search'])) {
                $where .= " AND (p.name LIKE ? OR p.description LIKE ?)";
                $searchTerm = '%' . $filters['search'] . '%';
                $params[] = $searchTerm;
                $params[] = $searchTerm;
            }
            
            $limit = isset($filters['limit']) ? (int)$filters['limit'] : 20;
            $offset = isset($filters['offset']) ? (int)$filters['offset'] : 0;
            
            $sql = "
                SELECT p.*, c.name as category_name,
                       (SELECT image_url FROM product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) as primary_image
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                $where
                ORDER BY p.created_at DESC
                LIMIT $limit OFFSET $offset
            ";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            $products = $stmt->fetchAll();
            
            // Get total count
            $countSql = "SELECT COUNT(*) as total FROM products p $where";
            $countStmt = $this->pdo->prepare($countSql);
            $countStmt->execute($params);
            $total = $countStmt->fetch()['total'];
            
            return [
                'success' => true,
                'products' => $products,
                'total' => $total,
                'limit' => $limit,
                'offset' => $offset
            ];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }
    
    public function getProduct($sellerId, $productId) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT p.*, c.name as category_name
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE p.id = ? AND p.seller_id = ?
            ");
            $stmt->execute([$productId, $sellerId]);
            $product = $stmt->fetch();
            
            if (!$product) {
                return ['success' => false, 'message' => 'Product not found'];
            }
            
            // Get images
            $stmt = $this->pdo->prepare("
                SELECT * FROM product_images 
                WHERE product_id = ? 
                ORDER BY is_primary DESC, sort_order
            ");
            $stmt->execute([$productId]);
            $product['images'] = $stmt->fetchAll();
            
            // Set primary image
            $product['primary_image'] = null;
            if (!empty($product['images'])) {
                foreach ($product['images'] as $image) {
                    if ($image['is_primary']) {
                        $product['primary_image'] = $image['image_url'];
                        break;
                    }
                }
                // If no primary image found, use the first image
                if (!$product['primary_image'] && !empty($product['images'])) {
                    $product['primary_image'] = $product['images'][0]['image_url'];
                }
            }
            
            // Get variants
            $stmt = $this->pdo->prepare("
                SELECT * FROM product_variants 
                WHERE product_id = ? 
                ORDER BY id
            ");
            $stmt->execute([$productId]);
            $product['variants'] = $stmt->fetchAll();
            
            return ['success' => true, 'product' => $product];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }
    
    public function createProduct($sellerId, $productData) {
        try {
            $this->pdo->beginTransaction();
            
            $slug = $this->generateSlug($productData['name']);
            
            $stmt = $this->pdo->prepare("
                INSERT INTO products (
                    seller_id, category_id, name, slug, description, short_description, 
                    sku, price, compare_price, cost_price, weight, dimensions,
                    stock_quantity, low_stock_threshold, manage_stock, stock_status,
                    visibility, status, featured, meta_title, meta_description
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $sellerId,
                $productData['category_id'] ?? null,
                $productData['name'],
                $slug,
                $productData['description'] ?? '',
                $productData['short_description'] ?? '',
                $productData['sku'] ?? '',
                $productData['price'],
                $productData['compare_price'] ?? null,
                $productData['cost_price'] ?? null,
                $productData['weight'] ?? null,
                $productData['dimensions'] ?? '',
                $productData['stock_quantity'] ?? 0,
                $productData['low_stock_threshold'] ?? 5,
                isset($productData['manage_stock']) ? (bool)$productData['manage_stock'] : true,
                $productData['stock_status'] ?? 'in_stock',
                $productData['visibility'] ?? 'visible',
                $productData['status'] ?? 'draft',
                isset($productData['featured']) ? (bool)$productData['featured'] : false,
                $productData['meta_title'] ?? '',
                $productData['meta_description'] ?? ''
            ]);
            
            $productId = $this->pdo->lastInsertId();
            
            // Add images if provided
            if (!empty($productData['images'])) {
                $this->addProductImages($productId, $productData['images']);
            }
            
            // Add variants if provided
            if (!empty($productData['variants'])) {
                $this->addProductVariants($productId, $productData['variants']);
            }
            
            $this->pdo->commit();
            
            return ['success' => true, 'product_id' => $productId, 'message' => 'Product created successfully'];
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }
    
    public function updateProduct($sellerId, $productId, $productData) {
        try {
            // Check if product belongs to seller
            $stmt = $this->pdo->prepare("SELECT id FROM products WHERE id = ? AND seller_id = ?");
            $stmt->execute([$productId, $sellerId]);
            if (!$stmt->fetch()) {
                return ['success' => false, 'message' => 'Product not found or access denied'];
            }
            
            $this->pdo->beginTransaction();
            
            $updateFields = [];
            $params = [];
            
            $allowedFields = [
                'category_id', 'name', 'description', 'short_description', 'sku',
                'price', 'compare_price', 'cost_price', 'weight', 'dimensions',
                'stock_quantity', 'low_stock_threshold', 'manage_stock', 'stock_status',
                'visibility', 'status', 'featured', 'meta_title', 'meta_description'
            ];
            
            foreach ($allowedFields as $field) {
                if (array_key_exists($field, $productData)) {
                    $updateFields[] = "$field = ?";
                    $params[] = $productData[$field];
                }
            }
            
            if (!empty($updateFields)) {
                $params[] = $productId;
                $sql = "UPDATE products SET " . implode(', ', $updateFields) . " WHERE id = ?";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute($params);
            }
            
            $this->pdo->commit();
            
            return ['success' => true, 'message' => 'Product updated successfully'];
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }
    
    public function deleteProduct($sellerId, $productId) {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM products WHERE id = ? AND seller_id = ?");
            $stmt->execute([$productId, $sellerId]);
            
            if ($stmt->rowCount() > 0) {
                return ['success' => true, 'message' => 'Product deleted successfully'];
            } else {
                return ['success' => false, 'message' => 'Product not found or access denied'];
            }
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }
    
    public function getCategories() {
        try {
            $stmt = $this->pdo->prepare("
                SELECT * FROM categories 
                WHERE is_active = 1 
                ORDER BY parent_id, sort_order, name
            ");
            $stmt->execute();
            return ['success' => true, 'categories' => $stmt->fetchAll()];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }
    
    private function generateSlug($name) {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
        
        $originalSlug = $slug;
        $counter = 1;
        
        while ($this->slugExists($slug)) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }
        
        return $slug;
    }
    
    private function slugExists($slug) {
        $stmt = $this->pdo->prepare("SELECT id FROM products WHERE slug = ?");
        $stmt->execute([$slug]);
        return $stmt->fetch() !== false;
    }
    
    private function addProductImages($productId, $images) {
        foreach ($images as $index => $image) {
            $stmt = $this->pdo->prepare("
                INSERT INTO product_images (product_id, image_url, alt_text, sort_order, is_primary)
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $productId,
                $image['url'],
                $image['alt_text'] ?? '',
                $index,
                $index === 0 ? 1 : 0
            ]);
        }
    }
    
    private function addProductVariants($productId, $variants) {
        foreach ($variants as $variant) {
            $stmt = $this->pdo->prepare("
                INSERT INTO product_variants (product_id, variant_name, sku, price, stock_quantity, attributes)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $productId,
                $variant['name'] ?? '',
                $variant['sku'] ?? '',
                $variant['price'] ?? null,
                $variant['stock_quantity'] ?? 0,
                json_encode($variant['attributes'] ?? [])
            ]);
        }
    }
}
?>