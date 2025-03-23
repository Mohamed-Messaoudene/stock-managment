<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$username = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factures</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .facture-card {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .facture-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 12px rgba(0, 0, 0, 0.2);
        }

        .client-info {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }

        .client-info i {
            font-size: 1.5rem;
            margin-right: 10px;
            color: #6c757d;
        }

        .total-price {
            font-weight: bold;
            color: #28a745;
        }

        .form-group {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .form-group select {
            flex: 1;
        }

        .btn-wrapper {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        /* Optional: Add a little margin between form elements */
        .form-group select,
        .btn-wrapper button {
            min-width: 150px;
        }
    </style>

</head>

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
                <a href="/stock_managment/index.php?page=factures" class="btn btn-secondary btn-sm me-2" title="Go Back">
                    <i class="bi bi-arrow-left"></i> Go Back
                </a>

                <a href="/stock_managment/index.php?page=users&action=logout" class="btn btn-danger btn-sm" title="Logout">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </a>
            </div>
        </div>
    </nav>

    <!-- Welcome Message -->
    <div class="container mt-4">
        <div class="alert alert-primary text-center" role="alert">
            <h4>Welcome <?php echo htmlspecialchars($username); ?> to the Facture page</h4>
        </div>
        <hr>
        <!-- Success and Error Messages -->
        <?php if ($success): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle"></i> <?= $success ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php elseif ($error): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-x-circle"></i> <?= $error ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        <!-- clients selection -->
        <h2 class="mt-4 mb-3">Select Client and View Factures</h2>
        <form action="/stock_managment/index.php" method="GET" class="mb-4"> <!-- Added margin bottom to form -->
            <div class="form-group d-flex align-items-center mb-4"> <!-- Added margin bottom to form group -->
                <label for="clientSelect" class="form-label me-2">Select Client</label>
                <select class="form-control me-3" id="clientSelect" name="client_id" required>
                    <option value="">-- Select Client --</option>
                    <?php
                    // Loop through the clients and create an option for each one
                    foreach ($clients as $client) {
                        $selected = (isset($_GET['client_id']) && $_GET['client_id'] == $client['id']) ? 'selected' : '';
                        echo "<option value='" . $client['id'] . "' $selected>" . htmlspecialchars($client['name']) . "</option>";
                    }
                    ?>
                </select>
                <input type="hidden" name="page" value="factures">
                <input type="hidden" name="action" value="show_factures">
                <button type="submit" class="btn btn-primary">Show Factures</button>
            </div>
        </form>



        <!-- Factures Display -->
        <div class="row">
            <?php
            if (empty($factures)) {
                echo '<div class="col-12"><div class="alert alert-warning" role="alert"><i class="bi bi-info-circle"></i> No factures found for this client.</div></div>';
            } else {
                foreach ($factures as $facture):
                    $clientInfo = $facture['client'];
                    $totalPrice = 0;
            ?>
                    <div class="col-12 mb-4">
                        <div class="card facture-card">
                            <div class="card-header bg-primary text-white">
                                <h5 class="card-title mb-0">
                                    <i class="bi bi-calendar-event"></i> Facture Date: <?= date('F j, Y', strtotime($facture['created_at'])) ?>
                                </h5>
                            </div>
                            <div class="card-body">
                                <!-- Client Info -->
                                <div class="client-info">
                                    <i class="bi bi-person-circle"></i>
                                    <span><strong>Name:</strong> <?= $clientInfo['name'] ?></span>
                                </div>
                                <div class="client-info">
                                    <i class="bi bi-telephone"></i>
                                    <span><strong>Phone:</strong> <?= $clientInfo['phone'] ?></span>
                                </div>
                                <div class="client-info">
                                    <i class="bi bi-envelope"></i>
                                    <span><strong>Email:</strong> <?= $clientInfo['email'] ?></span>
                                </div>

                                <hr>
                                <!-- Products -->
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th scope="col"><i class="bi bi-box"></i> Product Name</th>
                                                <th scope="col"><i class="bi bi-list-ol"></i> Quantity</th>
                                                <th scope="col"><i class="bi bi-cash-stack"></i> Price per Unit</th>
                                                <th scope="col"><i class="bi bi-currency-dollar"></i> Total Price</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($facture['items'] as $item): ?>
                                                <?php
                                                $productTotal = $item['total_price'];
                                                $totalPrice += $productTotal;
                                                ?>
                                                <tr>
                                                    <td><?= $item['product_name'] ?></td>
                                                    <td><?= $item['quantity'] ?></td>
                                                    <td>$<?= number_format($item['product_price'], 2) ?></td>
                                                    <td>$<?= number_format($productTotal, 2) ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>

                                <hr>
                                <!-- Total Price of the Facture -->
                                <div class="d-flex justify-content-between">
                                    <strong><i class="bi bi-wallet2"></i> Total Price:</strong>
                                    <span class="h5 total-price">$<?= number_format($totalPrice, 2) ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
            <?php endforeach;
            } ?>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>