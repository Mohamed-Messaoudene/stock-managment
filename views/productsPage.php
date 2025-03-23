<?php
$username = $_SESSION['username'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products Page</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<style>
    .username {
        font-weight: bold;
        color: blue;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Attach the openUpdateProductModal function to all Update buttons dynamically
        document.querySelectorAll('.update-product-btn').forEach(button => {
            button.addEventListener('click', function() {
                const productId = this.getAttribute('data-product-id');
                const productName = this.getAttribute('data-product-name');
                const productPrice = this.getAttribute('data-product-price');
                const productStock = this.getAttribute('data-product-stock');

                // Set the values of the fields in the modal
                document.getElementById('productId').value = productId;
                document.getElementById('productName').value = productName;
                document.getElementById('productPrice').value = productPrice;
                document.getElementById('productStock').value = productStock;

                // Initialize and open the modal
                var myModal = new bootstrap.Modal(document.getElementById('updateProductModal'));
                myModal.show();
            });
        });
    });
</script>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <!-- Brand -->
            <a class="navbar-brand" href="/stock_managment/index.php">
                <i class="bi bi-box-seam"></i> Stock Management
            </a>

            <!-- Action Buttons -->
            <div class="d-flex ">
                <!-- Home Icon Button -->
                <a href="/stock_managment/index.php" class="btn btn-secondary btn-sm me-2" title="Home">
                    <i class="bi bi-house"></i>
                    home
                </a>
                <!-- Logout Icon Button -->
                <a href="/stock_managment/index.php?page=users&action=logout" class="btn btn-danger btn-sm me-2" title="Logout">
                    <i class="bi bi-box-arrow-right"></i>
                    logout
                </a>
            </div>
        </div>
    </nav>
    <div class="container mt-5">
        <div class="alert alert-primary text-center" role="alert">
            <h4>Welcome <?php echo htmlspecialchars($username); ?> to the products page</h4>
        </div>
        <!-- Add Product Form -->
        <div class="card mb-4 mt-5">
            <div class="card-header">
                <h5>Add a New Product</h5>
            </div>
            <div class="card-body">
                <form action="/stock_managment/index.php?page=products" method="POST" enctype="multipart/form-data">
                    <div class="row g-3">
                        <input type="hidden" name="action" value="add">

                        <!-- Product Name -->
                        <div class="col-md-4">
                            <input type="text" name="name" class="form-control" placeholder="Product Name" required>
                        </div>

                        <!-- Price -->
                        <div class="col-md-2">
                            <input type="number" name="price" class="form-control" placeholder="Price" step="0.01" required>
                        </div>

                        <!-- Stock -->
                        <div class="col-md-2">
                            <input type="number" name="quantity" class="form-control" placeholder="quantity" required>
                        </div>

                        <!-- Product Image (File Upload) -->
                        <div class="col-md-4">
                            <input type="file" name="product_image" class="form-control" accept="image/*" required>
                        </div>

                        <!-- Submit Button -->
                        <div class="col-12">
                            <button type="submit" name="submit" class="btn btn-success">Add Product</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Display Messages -->
        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php elseif (!empty($error)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <!-- Display Products -->
        <div class="container mt-5">
            <div class="row">
                <?php foreach ($products as $product): ?>
                    <div class="col-md-3 mb-4">
                        <div class="card shadow-lg h-100">
                            <img src="/stock_managment/<?php echo htmlspecialchars($product['productImagePath']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($product['name']); ?>" style="object-fit: cover; height: 200px;">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title" style="color: #333; margin-bottom: 15px;"><?php echo htmlspecialchars($product['name']); ?></h5>
                                <p class="card-text text-warning">
                                    <i class="bi bi-cash-stack"></i> $<?php echo number_format($product['price'], 2); ?>
                                </p>
                                <p class="card-text text-success">
                                    <i class="bi bi-archive"></i> <?php echo htmlspecialchars($product['stock']); ?> in stock
                                </p>
                                <div class="d-flex justify-content-between mt-auto">
                                    <button type="button" class="btn btn-primary btn-sm update-product-btn"
                                        data-product-id="<?php echo $product['id']; ?>"
                                        data-product-name="<?php echo htmlspecialchars($product['name']); ?>"
                                        data-product-price="<?php echo $product['price']; ?>"
                                        data-product-stock="<?php echo $product['stock']; ?>">
                                        <i class="bi bi-pencil-square"></i> Update
                                    </button>
                                    <form action="/stock_managment/index.php?page=products" method="POST" class="d-inline">
                                        <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
                                        <input type="hidden" name="action" value="delete">
                                        <button type="submit" class="btn btn-danger" name="submit">
                                            <i class="bi bi-trash"></i> Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>


        <!-- Modal for Updating Product -->
        <div class="modal fade" id="updateProductModal" tabindex="-1" aria-labelledby="updateProductModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="updateProductModalLabel">Update Product</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="updateProductForm" action="/stock_managment/index.php?page=products" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="action" value="update">
                            <input type="hidden" id="productId" name="id">

                            <!-- Product Name -->
                            <div class="mb-3">
                                <label for="productName" class="form-label">Product Name</label>
                                <input type="text" name="name" id="productName" class="form-control" placeholder="Product Name" required>
                            </div>

                            <!-- Price -->
                            <div class="mb-3">
                                <label for="productPrice" class="form-label">Price</label>
                                <input type="number" name="price" id="productPrice" class="form-control" placeholder="Price" step="0.01" required>
                            </div>

                            <!-- Stock -->
                            <div class="mb-3">
                                <label for="productStock" class="form-label">Stock</label>
                                <input type="number" name="quantity" id="productStock" class="form-control" placeholder="quantity" required>
                            </div>

                            <!-- Product Image (File Upload) -->
                            <div class="mb-3">
                                <label for="productImage" class="form-label">Product Image</label>
                                <input type="file" name="product_image" id="productImage" class="form-control" accept="image/*">
                            </div>

                            <!-- Submit Button -->
                            <div class="mb-3">
                                <button type="submit" name="submit" class="btn btn-success">Update Product</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>