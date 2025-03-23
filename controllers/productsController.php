<?php
require_once(__DIR__ . '/../models/Products.php');

class ProductsController {
    private $productModel;

    public function __construct($db) {
        $this->productModel = new Product($db);
    }

    public function handleRequest() {
        $error = $success = '';

        // Handle POST requests
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';

            if ($action === 'add') {
                $name = $_POST['name'] ?? '';
                $price = $_POST['price'] ?? '';
                $quantity = $_POST['quantity'] ?? '';

                // Handle image upload
                [$error, $productImagePath] = $this->handleImageUpload($_FILES['product_image'] ?? null);

                // Insert product into the database if no errors
                if ($name && $price && $quantity && !$error) {
                    $this->productModel->insertProduct($name, $price, $quantity, $productImagePath);
                    $success = "Product added successfully!";
                } else {
                    $error = $error ?: "All fields are required to add a product.";
                }
            } elseif ($action === 'update') {
                $id = $_POST['id'] ?? '';
                $name = $_POST['name'] ?? '';
                $price = $_POST['price'] ?? '';
                $quantity = $_POST['quantity'] ?? '';
                $productImagePath = '';

                if ($id && $name && $price && $quantity) {
                    [$error, $newImagePath] = $this->handleImageUpload($_FILES['product_image'] ?? null);

                    // Use new image if uploaded, otherwise retain the existing image
                    if ($newImagePath) {
                        $productImagePath = $newImagePath;
                    } else {
                        $currentProduct = $this->productModel->getProductById($id);
                        $productImagePath = $currentProduct['productImagePath'] ?? '';
                    }

                    if (!$error) {
                        $this->productModel->updateProduct($id, $name, $price, $quantity, $productImagePath);
                        $success = "Product updated successfully!";
                    }
                } else {
                    $error = "All fields are required to update a product.";
                }
            } elseif ($action === 'delete') {
                $id = $_POST['id'] ?? '';

                if ($id) {
                    // Get the product details to retrieve the image path
                    $product = $this->productModel->getProductById($id);
                    if ($product && $product['productImagePath']) {
                        $imagePath = '../' . $product['productImagePath'];

                        // Delete the image from the server
                        if (file_exists($imagePath)) {
                            unlink($imagePath); // Delete the image file
                        }
                    }

                    // Now delete the product from the database
                    $this->productModel->deleteProduct($id);
                    $success = "Product deleted successfully!";
                } else {
                    $error = "Invalid product ID.";
                }
            }
        }

        // Fetch all products
        $products = $this->productModel->getAllProducts();

        // Render the view
        $this->renderView($error, $success, $products);
    }

    private function handleImageUpload($file) {
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        $error = null;
        $imagePath = '';

        if ($file && $file['error'] == 0) {
            $imageName = $file['name'];
            $imageTmpName = $file['tmp_name'];
            $imageExt = pathinfo($imageName, PATHINFO_EXTENSION);

            if (!in_array(strtolower($imageExt), $allowedExtensions)) {
                $error = "Invalid image type. Only JPG, JPEG, PNG, and GIF are allowed.";
            } else {
                $newImageName = uniqid('', true) . '.' . $imageExt;
                $imageDestination = __DIR__.'/../assets/product_images/' . $newImageName;

                if (move_uploaded_file($imageTmpName, $imageDestination)) {
                    $imagePath = '/assets/product_images/' . $newImageName;
                } else {
                    $error = "Failed to upload the image.";
                }
            }
        }

        return [$error, $imagePath];
    }

    private function renderView($error, $success, $products) {
        include __DIR__ . '/../views/productsPage.php';

    }
}
?>
