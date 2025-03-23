<?php

require_once './config/db.php';
require_once './models/Users.php';
require_once './models/products.php';
require_once './models/Clients.php';
require_once './models/Factures.php';
require_once './models/Factures_items.php';


require_once './controllers/AuthController.php';
require_once './controllers/clientController.php';
require_once './controllers/facturesController.php';
require_once './controllers/productsController.php';
session_start();

// Create a database connection
$database = new Database();
$db = $database->getConnection();

// Check if the data has already been initialized by checking a file
if (!file_exists('data_initialized.txt')) {
    // Initialize product data
    $productModel = new Product($db);
    $productModel->initializeData();

    // Initialize client data
    $clientModel = new Client($db);
    $clientModel->initializeData();

    // Initialize user data
    $userModel = new User($db);
    $userModel->initializeData();

    // Initialize factures data
    $factureModel = new Facture($db);
    $factureModel->initializeData();

    // Initialize user data
    $factureItemModel = new FactureItem($db);
    $factureItemModel->initializeData();

    // Create a file to mark that the data has been initialized
    file_put_contents('data_initialized.txt', 'initialized');
}

$authController = new AuthController($db);
$clientsController = new ClientsController($db);
$productsController = new ProductsController($db);
$facturesController = new FacturesController($db);


// Determine page
$page = $_GET['page'] ?? ($_POST['page'] ?? 'login');

// Authentication check
if ($authController->isLoggedIn()) {
    // Handle logged-in pages
    switch ($page) {
        case 'clients':
            $clientsController->handleRequest();
            break;
        case 'factures':
            $facturesController->handleRequest();
            break;
        case 'products':
            $productsController->handleRequest();
            break;
        case 'users':
            $authController->handleRequest();
            break;
        default:
            // Default page when logged in
            require_once './views/navigationPage.php';
            break;
    }
} else {
    // Handle login page
        $authController->handleRequest();
        exit;
    
}
