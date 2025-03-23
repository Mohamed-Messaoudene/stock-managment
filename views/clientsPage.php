<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$username = $_SESSION["username"];

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clients Page</title>
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
    function populateUpdateModal(id, name, email, phone) {
        document.getElementById('clientId').value = id;
        document.getElementById('clientName').value = name;
        document.getElementById('clientEmail').value = email;
        document.getElementById('clientPhone').value = phone;
    }
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

    <!-- Main content -->
    <div class="container mt-5">
        <div class="alert alert-primary text-center" role="alert">
            <h4>Welcome <?php echo htmlspecialchars($username); ?> to the clients page</h4>
        </div>
        <!-- Add Client Form -->
        <div class="card mb-4 mt-5">
            <div class="card-header">
                <h5>Add a New Client</h5>
            </div>
            <div class="card-body">
                <form action="/stock_managment/index.php?page=clients" method="POST">
                    <div class="row g-3">
                        <input type="hidden" name="action" value="add">

                        <div class="col-md-4">
                            <input type="text" name="name" class="form-control" placeholder="Client Name" required>
                        </div>
                        <div class="col-md-4">
                            <input type="email" name="email" class="form-control" placeholder="Client Email" required>
                        </div>
                        <div class="col-md-4">
                            <input type="text" name="phone" class="form-control" placeholder="Client Phone">
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-success" name="action" value="add">Add Client</button>
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

        <!-- Clients Table -->
        <div class="card">
            <div class="card-header">
                <h5>Client List</h5>
            </div>
            <div class="card-body">
                <table class="table table-striped table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($clients as $client): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($client['id']); ?></td>
                                <td><?php echo htmlspecialchars($client['name']); ?></td>
                                <td><?php echo htmlspecialchars($client['email']); ?></td>
                                <td><?php echo htmlspecialchars($client['phone']); ?></td>
                                <td><?php echo htmlspecialchars($client['created_at']); ?></td>
                                <td>
                                    <!-- Update and Delete buttons -->
                                    <!-- Update Button in Table -->
                                    <form action="" method="POST" class="d-inline">
                                        <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#updateModal"
                                            onclick="populateUpdateModal(
                                               '<?php echo htmlspecialchars($client['id']); ?>', 
                                               '<?php echo htmlspecialchars($client['name']); ?>', 
                                               '<?php echo htmlspecialchars($client['email']); ?>', 
                                               '<?php echo htmlspecialchars($client['phone']); ?>'
                                        )">
                                            <i class="bi bi-pencil-square"></i> Update
                                        </button>
                                    </form>
                                    <form action="/stock_managment/index.php?page=clients" method="POST" class="d-inline">
                                        <input type="hidden" name="id" value="<?php echo $client['id']; ?>">
                                        <input type="hidden" name="action" value="delete">
                                        <button type="submit" class="btn btn-danger" name="action" value="delete">
                                            <i class="bi bi-trash"></i> Delete
                                        </button>
                                    </form>

                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- Update Modal -->
    <div class="modal fade" id="updateModal" tabindex="-1" aria-labelledby="updateModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateModalLabel">Update Client</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">s
                    <form id="updateForm" action="/stock_managment/index.php?page=clients" method="POST">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="id" id="clientId">
                        <div class="mb-3">
                            <label for="clientName" class="form-label">Name</label>
                            <input type="text" class="form-control" id="clientName" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="clientEmail" class="form-label">Email</label>
                            <input type="email" class="form-control" id="clientEmail" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="clientPhone" class="form-label">Phone</label>
                            <input type="text" class="form-control" id="clientPhone" name="phone">
                        </div>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>