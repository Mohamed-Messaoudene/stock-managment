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
    <title>Users Page</title>
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
    function populateUpdateModal(id, username, email, role) {
        document.getElementById('userId').value = id;
        document.getElementById('username').value = username;
        document.getElementById('email').value = email;
        document.getElementById('role').value = role;
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
            <div class="d-flex">
                <!-- Home Icon Button -->
                <a href="/stock_managment/index.php" class="btn btn-secondary btn-sm me-2" title="Home">
                    <i class="bi bi-house"></i> Home
                </a>
                <!-- Logout Icon Button -->
                <a href="/stock_managment/index.php?page=users&action=logout" class="btn btn-danger btn-sm me-2" title="Logout">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </a>
            </div>
        </div>
    </nav>

    <!-- Main content -->
    <div class="container mt-5">
        <div class="alert alert-primary text-center" role="alert">
            <h4>Welcome <?php echo htmlspecialchars($username); ?> to the users page</h4>
        </div>
        <!-- Add User Form -->
        <div class="card mb-4 mt-5">
            <div class="card-header">
                <h5>Add a New User (or Admin)</h5>
            </div>
            <div class="card-body">
                <form action="/stock_managment/index.php?page=users" method="POST">
                    <div class="row g-3">
                        <input type="hidden" name="action" value="add">

                        <div class="col-md-3">
                            <input type="text" name="name" class="form-control" placeholder="Username" required>
                        </div>
                        <div class="col-md-3">
                            <input type="email" name="email" class="form-control" placeholder="Email" required>
                        </div>
                        <div class="col-md-3">
                            <input type="password" name="password" class="form-control" placeholder="Password" required>
                        </div>
                        <div class="col-md-3">
                            <select name="role" class="form-control" required>
                                <option value="">Select Role</option>
                                <option value="admin">Admin</option>
                                <option value="user">User</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-success" name="submit">Add User</button>
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

        <!-- Users Table -->
        <div class="card">
            <div class="card-header">
                <h5>User List</h5>
            </div>
            <div class="card-body">
                <table class="table table-striped table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($user['id']); ?></td>
                                <td><?php echo htmlspecialchars($user['username']); ?></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td><?php echo htmlspecialchars($user['role']); ?></td>
                                <td><?php echo htmlspecialchars($user['created_at']); ?></td>
                                <td>
                                    <!-- Update Button in Table -->
                                    <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#updateModal"
                                        onclick="populateUpdateModal(
                                           '<?php echo htmlspecialchars($user['id']); ?>', 
                                           '<?php echo htmlspecialchars($user['username']); ?>', 
                                           '<?php echo htmlspecialchars($user['email']); ?>', 
                                           '<?php echo htmlspecialchars($user['role']); ?>'
                                    )">
                                        <i class="bi bi-pencil-square"></i> Update
                                    </button>
                                    <!-- Delete Button -->
                                    <form action="/stock_managment/index.php?page=users" method="POST" class="d-inline">
                                        <input type="hidden" name="email" value="<?php echo $user['email']; ?>">
                                        <input type="hidden" name="action" value="delete">
                                        <button type="submit" name="submit" class="btn btn-danger">
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
                    <h5 class="modal-title" id="updateModalLabel">Update User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="updateForm" action="/stock_managment/index.php?page=users" method="POST">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="id" id="userId">
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="role" class="form-label">Role</label>
                            <select class="form-control" id="role" name="role" required>
                                <option value="admin">Admin</option>
                                <option value="user">User</option>
                            </select>
                        </div>
                        <button type="submit" name="submit" class="btn btn-primary">Save Changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>