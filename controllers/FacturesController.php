<?php
require_once(__DIR__ . '/../models/Clients.php');
require_once(__DIR__ . '/../models/Products.php');
require_once(__DIR__ . '/../models/Factures.php');
require_once(__DIR__ . '/../models/Factures_items.php');
require_once(__DIR__ . '/../config/db.php');

class FacturesController
{
    private $clientsModel;
    private $productsModel;
    private $facturesModel;
    private $factureItemsModel;
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
        $this->clientsModel = new Client($db);
        $this->productsModel = new Product($db);
        $this->facturesModel = new Facture($db);
        $this->factureItemsModel = new FactureItem($db);
    }

    public function handleRequest()
    {
        $error = '';
        $success = '';
        $clients = $this->clientsModel->getAllClients();
        $products = $this->productsModel->getAllProducts();
        $factures = [];

        // Handle the POST request (creating a facture)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $result = $this->createFacture();
            if ($result['success']) {
                $success = $result['message'];
                // After creating the facture, show the factures page
                $client_id = $_POST['client_id'] ?? null;
                $factures = $this->getFactures($client_id);
                $this->showFacturesPage($clients, $products, $factures, $error, $success);
            } else {
                $error = $result['message'];
                $this->showAddFacturesPage($clients, $products, $error, $success);
            }
            return; // Exit after POST request handling
        }
        // Handle the GET request to show factures
        if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['action']) && $_GET['action'] == 'show_factures') {
            // Get the client ID from the GET parameter (if available)
            $client_id = $_GET['client_id'] ?? null;
            $factures = $this->getFactures($client_id);
            $this->showFacturesPage($clients, $products, $factures, $error, $success);
            return; // Exit after GET request handling for factures
        }
        // Default case: Show the AddFacturesPage
        $this->showAddFacturesPage($clients, $products, $error, $success);
    }

    // Handle facture creation
    private function createFacture()
    {
        $client_id = $_POST['client_id'] ?? '';
        $products = $_POST['products'] ?? [];

        if (empty($client_id)) {
            return [
                'success' => false,
                'message' => 'Client ID is required.',
            ];
        }

        // Check if at least one product has a quantity > 0
        $hasSelectedProduct = false;
        foreach ($products as $product) {
            if (!empty($product['quantity']) && $product['quantity'] > 0) {
                $hasSelectedProduct = true;
                break;
            }
        }

        if (!$hasSelectedProduct) {
            return [
                'success' => false,
                'message' => 'At least one product must be selected with a quantity greater than 0.',
            ];
        }

        try {
            $this->db->beginTransaction();

            // Insert new facture
            $this->facturesModel->createFacture($client_id);
            $facture_id = $this->db->lastInsertId();

            // Insert facture items and update stock
            foreach ($products as $product) {
                if (!empty($product['quantity']) && $product['quantity'] > 0) {
                    $product_id = $product['id'];
                    $quantity = $product['quantity'];

                    // Fetch product price
                    $priceStmt = $this->db->prepare("SELECT price, stock FROM products WHERE id = :id");
                    $priceStmt->execute(['id' => $product_id]);
                    $productData = $priceStmt->fetch(PDO::FETCH_ASSOC);

                    if ($productData === false) {
                        throw new Exception("Invalid product ID: $product_id");
                    }

                    $price = $productData['price'];
                    $stock = $productData['stock'];

                    if ($stock < $quantity) {
                        throw new Exception("Not enough stock for product ID: $product_id. Available stock: $stock.");
                    }

                    $total_price = $price * $quantity;

                    // Use FactureItem model to create facture item
                    $this->factureItemsModel->createFactureItem($facture_id, $product_id, $quantity, $total_price);

                    // Update product stock by decreasing the quantity sold
                    $new_stock = $stock - $quantity;
                    $updateStockStmt = $this->db->prepare("UPDATE products SET stock = :stock WHERE id = :id");
                    $updateStockStmt->execute(['stock' => $new_stock, 'id' => $product_id]);
                }
            }

            $this->db->commit();

            return [
                'success' => true,
                'message' => 'Facture created successfully.',
            ];
        } catch (Exception $e) {
            $this->db->rollBack();

            return [
                'success' => false,
                'message' => 'Failed to create facture: ' . $e->getMessage(),
            ];
        }
    }

    // Fetch factures from the database, filtered by client_id if provided
    private function getFactures($client_id = null)
    {
        if ($client_id) {
            // Fetch factures for the specific client
            $stmt = $this->db->prepare("SELECT * FROM factures WHERE client_id = :client_id");
            $stmt->execute(['client_id' => $client_id]);
        } else {
            // Fetch all factures if no client_id is specified
            $stmt = $this->db->prepare("SELECT * FROM factures");
            $stmt->execute();
        }
    
        // Fetch factures data
        $factures = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        // Fetch factures items for each facture
        foreach ($factures as &$facture) {
            $facture_id = $facture['id'];
    
            // Join facture_items with products to get product details
            $itemStmt = $this->db->prepare("
                SELECT 
                    fi.*,
                    p.name AS product_name,
                    p.price AS product_price
                FROM facture_items fi
                JOIN products p ON fi.product_id = p.id
                WHERE fi.facture_id = :facture_id
            ");
            $itemStmt->execute(['facture_id' => $facture_id]);
            $facture['items'] = $itemStmt->fetchAll(PDO::FETCH_ASSOC);
    
            // Get client info for this facture
            $clientStmt = $this->db->prepare("SELECT * FROM clients WHERE id = :client_id");
            $clientStmt->execute(['client_id' => $facture['client_id']]);
            $facture['client'] = $clientStmt->fetch(PDO::FETCH_ASSOC);
        }
    
        return $factures;
    }

    // Render the add facture page view
    private function showAddFacturesPage($clients, $products, $error, $success)
    {
        require_once __DIR__.'/../views/AddFacturesPage.php';
    }
    // Render the facture page view
    private function showFacturesPage($clients, $products, $factures, $error, $success)
    {
        require_once __DIR__.'/../views/FacturesPage.php';
    }
}
