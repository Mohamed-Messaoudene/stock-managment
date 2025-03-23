<?php

class Product
{
    private $db;

    // Constructor to inject the database connection
    public function __construct($db)
    {
        $this->db = $db;
    }

    // Create the products table if it doesn't exist
    public function createTable()
    {
        $this->db->exec("
            CREATE TABLE IF NOT EXISTS products (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(100) NOT NULL,
                price DECIMAL(10, 2) NOT NULL,
                stock INT NOT NULL,
                productImagePath VARCHAR(255) DEFAULT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");
    }

    public function initializeData()
    {
        $this->createTable();

        $initialData = [
            [
                'name' => 'Wireless Headphones',
                'price' => 99.99,
                'stock' => 150,
                'imagePath' => '/assets/product_images/wirless_headphones.jpeg'
            ],
            [
                'name' => 'Smartphone 5G',
                'price' => 799.99,
                'stock' => 100,
                'imagePath' => '/assets/product_images/smartphone_5G.jpeg'
            ],
            [
                'name' => '4K Ultra HD TV',
                'price' => 499.99,
                'stock' => 80,
                'imagePath' => '/assets/product_images/4k_ultrahd_tv.jpeg'
            ],
            [
                'name' => 'Bluetooth Speaker',
                'price' => 59.99,
                'stock' => 200,
                'imagePath' => '/assets/product_images/bluetooth_speaker.jpeg'
            ],
            [
                'name' => 'Gaming Laptop',
                'price' => 1299.99,
                'stock' => 50,
                'imagePath' => '/assets/product_images/gaming_laptop.jpeg'
            ],
            [
                'name' => 'Smartwatch',
                'price' => 199.99,
                'stock' => 120,
                'imagePath' => '/assets/product_images/smart_watch.jpeg'
            ],
            [
                'name' => 'Noise-Cancelling Earbuds',
                'price' => 149.99,
                'stock' => 180,
                'imagePath' => '/assets/product_images/Noise-Cancelling_Earbuds.jpeg'
            ],
            [
                'name' => 'Portable Charger',
                'price' => 29.99,
                'stock' => 300,
                'imagePath' => '/assets/product_images/portable_charger.jpeg'
            ],
            [
                'name' => 'DSLR Camera',
                'price' => 999.99,
                'stock' => 40,
                'imagePath' => '/assets/product_images/DSLR_camera.jpeg'
            ],
            [
                'name' => 'Gaming Console',
                'price' => 499.99,
                'stock' => 70,
                'imagePath' => '/assets/product_images/game_console.jpeg'
            ],
            [
                'name' => 'Streaming Stick',
                'price' => 49.99,
                'stock' => 250,
                'imagePath' => '/assets/product_images/streaming_stick.jpeg'
            ],
            [
                'name' => 'Robot Vacuum Cleaner',
                'price' => 299.99,
                'stock' => 90,
                'imagePath' => '/assets/product_images/robot_vacuum_cleaner.jpeg'
            ],
            [
                'name' => 'WiFi Router',
                'price' => 89.99,
                'stock' => 120,
                'imagePath' => '/assets/product_images/wifi_router.jpeg'
            ],
            [
                'name' => 'External Hard Drive',
                'price' => 79.99,
                'stock' => 130,
                'imagePath' => '/assets/product_images/external_harddrive.jpeg'
            ],
            [
                'name' => 'Smart Light Bulb',
                'price' => 24.99,
                'stock' => 300,
                'imagePath' => '/assets/product_images/smart_light.jpeg'
            ],
            [
                'name' => 'Home Security Camera',
                'price' => 99.99,
                'stock' => 150,
                'imagePath' => '/assets/product_images/home_security_camera.jpeg'
            ],
            [
                'name' => 'Fitness Tracker',
                'price' => 149.99,
                'stock' => 140,
                'imagePath' => '/assets/product_images/fitness_tracker.jpeg'
            ],
            [
                'name' => 'Electric Toothbrush',
                'price' => 49.99,
                'stock' => 180,
                'imagePath' => '/assets/product_images/electric_toothbrush.jpeg'
            ],
            [
                'name' => 'VR Headset',
                'price' => 299.99,
                'stock' => 50,
                'imagePath' => '/assets/product_images/vr_headset.jpeg'
            ]
        ];
        

        foreach ($initialData as $product) {
            if (!$this->productExists($product['name'])) {
                $this->insertProduct($product['name'], $product['price'], $product['stock'], $product['imagePath']);
            }
        }
    }

    // Check if a product with the given name already exists
    public function productExists($name)
    {
        $query = $this->db->prepare("SELECT COUNT(*) FROM products WHERE name = :name");
        $query->execute(['name' => $name]);
        return $query->fetchColumn() > 0;
    }

    // Insert a new product into the database
    public function insertProduct($name, $price, $stock, $productImagePath)
    {
        $sql = "INSERT INTO products (name, price, stock, productImagePath)
                VALUES (:name, :price, :stock, :productImagePath)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'name' => $name,
            'price' => $price,
            'stock' => $stock,
            'productImagePath' => $productImagePath
        ]);
    }

    // Retrieve all products ordered by newest first
    public function getAllProducts()
    {
        $query = $this->db->query("SELECT * FROM products ORDER BY created_at DESC");
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }


    // Retrieve a product by its ID
    public function getProductById($id)
    {
        $query = $this->db->prepare("SELECT * FROM products WHERE id = :id");
        $query->execute(['id' => $id]);
        return $query->fetch(PDO::FETCH_ASSOC);
    }

    // Update product information
    public function updateProduct($id, $name, $price, $stock, $productImagePath)
    {
        $sql = "UPDATE products SET 
                    name = :name,
                    price = :price,
                    stock = :stock,
                    productImagePath = :productImagePath
                WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'id' => $id,
            'name' => $name,
            'price' => $price,
            'stock' => $stock,
            'productImagePath' => $productImagePath
        ]);
    }

    // Delete a product by its ID
    public function deleteProduct($id)
    {
        $sql = "DELETE FROM products WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
    }
}
