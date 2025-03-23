<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Login</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f8f9fa;
        }
        .container {
            max-width: 600px;
            background: #ffffff;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .btn {
            width: 100%;
        }
        .error {
            color: #d9534f;
            background-color: #f8d7da;
            padding: 10px;
            border: 1px solid #f5c6cb;
            border-radius: 4px;
            margin-bottom: 15px;
            text-align: center;
        }
    </style>
</head>
<body>

    <div class="container">
        
        <h2 class="text-center mb-4">User Login</h2>

        <!-- Check if there is an error message to display -->
        <?php if (isset($error_message)): ?>
            <div class="error" id="error-message">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <form action="/stock_managment/index.php?action=login" method="POST">
            <input type="hidden" name="action" value="login"> 

            <div class="mb-3">
                <label for="email" class="form-label">Email:</label>
                <input type="email" id="email" name="email" class="form-control" required placeholder="Enter your email">
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password:</label>
                <input type="password" id="password" name="password" class="form-control" required placeholder="Enter your password">
            </div>

            <button type="submit" name="submit" class="btn btn-primary">Login</button>
        </form>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

    <!-- JavaScript to Remove Error on Focus -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const inputs = document.querySelectorAll("input");
            const errorMessage = document.getElementById("error-message");

            inputs.forEach(input => {
                input.addEventListener("focus", function () {
                    if (errorMessage) {
                        errorMessage.style.display = "none"; // Hide error message
                    }
                });
            });
        });
    </script>
</body>
</html>
