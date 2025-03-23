<?php
$username = $_SESSION['username'];
// Assuming the form is processed and the facture is created
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $_POST['products'] = [];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factures Page</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const quantityInputs = document.querySelectorAll('input[name^="products"][name$="[quantity]"]');

        quantityInputs.forEach(input => {
            input.addEventListener("input", () => {
                const card = input.closest('.card');
                if (input.value > 0) {
                    card.classList.add('border-selected');
                } else {
                    card.classList.remove('border-selected');
                }
            });

            if (input.value > 0) {
                const card = input.closest('.card');
                card.classList.add('border-selected');
            }
        });
    });
</script>

<style>
    .border-selected {
        border: 3px solid #007bff;
        box-shadow: 0 0 10px rgba(0, 123, 255, 0.5);
        transition: border 0.3s ease, box-shadow 0.3s ease;
    }

    .border-selected:hover {
        transform: scale(1.01);
    }

    .btn-primary {
        font-size: 1rem;
        font-weight: bold;
        transition: transform 0.3s ease, background-color 0.3s ease;
    }

    .btn-primary:hover {
        transform: scale(1.05);
        background-color: #0056b3;
    }

    .facture-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }
</style>

<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand" href="/stock_managment/index.php">
                <i class="bi bi-box-seam"></i> Stock Management
            </a>

            <div class="d-flex">
                <a href="/stock_managment/index.php" class="btn btn-secondary btn-sm me-2" title="Home">
                    <i class="bi bi-house"></i> Home
                </a>
                <a href="/stock_managment/index.php?page=users&action=logout" class="btn btn-danger btn-sm" title="Logout">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="alert alert-primary text-center" role="alert">
            <h4>Welcome <?php echo htmlspecialchars($username); ?> to the Facture Management System</h4>
        </div>
    </div>

    <?php if (!empty($error)): ?>
        <div class="container mt-4">
            <div class="alert alert-danger text-center" role="alert">
                <strong>Error:</strong> <?php echo htmlspecialchars($error); ?>
            </div>
        </div>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
        <div class="container mt-4">
            <div class="alert alert-success text-center" role="alert">
                <strong>Success:</strong> <?php echo htmlspecialchars($success); ?>
            </div>
        </div>
    <?php endif; ?>

    <div class="container mt-4">
        <div class="card">
            <div class="card-body">
                <div class="facture-header">
                    <button type="submit" form="facture-form" name="submit" class="btn btn-primary">
                        <i class="bi bi-file-earmark-plus"></i> Create Facture
                    </button>
                    <button type="button" class="btn btn-secondary" onclick="location.href='/stock_managment/index.php?page=factures&action=show_factures';">
                        <i class="bi bi-card-list"></i> Show All Factures
                    </button>

                </div>

                <form id="facture-form" method="POST" action="/stock_managment/index.php?page=factures">
                    <div class="mb-4">
                        <label for="client_id" class="form-label">Select Client</label>
                        <select class="form-select" id="client_id" name="client_id" required>
                            <option value="" disabled selected>Select a client</option>
                            <?php
                            foreach ($clients as $client) {
                                echo '<option value="' . htmlspecialchars($client['id']) . '">' . htmlspecialchars($client['name']) . '</option>';
                            }
                            ?>
                        </select>
                    </div>

                    <p>Select Products</p>
                    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
                        <?php foreach ($products as $product): ?>
                            <div class="col">
                                <div class="card h-100 <?php echo !empty($_POST['products'][$product['id']]['quantity']) && $_POST['products'][$product['id']]['quantity'] > 0 ? 'border-selected' : ''; ?>">
                                    <img src="/stock_managment/<?php echo htmlspecialchars($product['productImagePath']); ?>" class="card-img-top" alt="Product Image" style="height: 200px; object-fit: cover;">
                                    <div class="card-body d-flex flex-column">
                                        <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                                        <p class="card-text text-warning">
                                            <i class="bi bi-cash-stack"></i> $<?php echo number_format($product['price'], 2); ?>
                                        </p>
                                        <p class="card-text text-success">
                                            <i class="bi bi-archive"></i> <?php echo htmlspecialchars($product['stock']); ?> in stock
                                        </p>
                                        <div class="mt-auto">
                                            <input type="hidden" name="products[<?php echo $product['id']; ?>][id]" value="<?php echo $product['id']; ?>">
                                            <input type="number" class="form-control" name="products[<?php echo $product['id']; ?>][quantity]" placeholder="Quantity" min="0" value="<?php echo !empty($_POST['products'][$product['id']]['quantity']) ? htmlspecialchars($_POST['products'][$product['id']]['quantity']) : '0'; ?>">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>