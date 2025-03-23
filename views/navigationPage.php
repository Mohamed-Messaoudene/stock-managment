<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Mock user data (Replace with actual user session data)
$username = $_SESSION["username"];
$userRole = $_SESSION["role"];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Navigation Page</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        .card img {
            height: 250px;
            object-fit: contain;
        }

        .navbar-brand {
            font-weight: bold;
        }

        .username {
            font-weight: bold;
            color: blue;
        }

        .card-row {
            display: flex;
            flex-wrap: nowrap;
            /* Prevent wrapping */
            gap: 1rem;
            /* Add spacing between cards */
        }

        .card {
            flex: 1;
            /* Ensure cards are equally sized */
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <!-- Brand -->
            <a class="navbar-brand" href="/stock_managment/index.php">
                <i class="bi bi-box-seam"></i> Stock Management
            </a>

            <div class="d-flex ">
                <!-- Logout Icon Button -->
                <a href="/stock_managment/index.php?page=users&action=logout" class="btn btn-danger btn-sm me-2" title="Logout">
                    <i class="bi bi-box-arrow-right"></i>
                    logout
                </a>
            </div>
        </div>
    </nav>

    <!-- Welcome Section -->
    <div class="container mt-5 text-center">
        <div class="alert alert-primary text-center" role="alert">
            <h4>Welcome <?php echo htmlspecialchars($username); ?> to the navigation page</h4>
        </div>
        <!-- Cards Section -->
        <div class="card-row">
            <?php if ($userRole === 'admin'): ?>
                <!-- Users Page Card -->
                <div class="card shadow">
                    <img src="/stock_managment/assets/users.jpeg" class="card-img-top" alt="Users">
                    <div class="card-body text-center">
                        <a href="/stock_managment/index.php?page=users&action=usersPage" class="btn btn-outline-info">Go to Users Page</a>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Products Page Card -->
            <div class="card shadow">
                <img src="/stock_managment/assets/imgs.jpeg" class="card-img-top" alt="Products">
                <div class="card-body text-center">
                    <a href="/stock_managment/index.php?page=products" class="btn btn-outline-primary">Go to Products Page</a>
                </div>
            </div>

            <!-- Factures Page Card -->
            <div class="card shadow">
                <img src="/stock_managment/assets/fact.png" class="card-img-top" alt="Factures">
                <div class="card-body text-center">
                    <a href="/stock_managment/index.php?page=factures" class="btn btn-outline-success">Go to Factures Page</a>
                </div>
            </div>

            <!-- Clients Page Card -->
            <div class="card shadow">
                <img src="/stock_managment/assets/clients.jpeg" class="card-img-top" alt="Clients">
                <div class="card-body text-center">
                    <a href="/stock_managment/index.php?page=clients" class="btn btn-outline-warning">Go to Clients Page</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS (optional for interactive features) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>