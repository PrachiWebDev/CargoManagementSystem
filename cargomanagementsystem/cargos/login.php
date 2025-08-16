<?php
session_start();
require_once "config/database.php";

// Check if user is already logged in
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header("location: " . $_SESSION["role"] . "/dashboard.php");
    exit;
}

$login_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);

    // Use prepared statement to prevent SQL injection
    $sql = "SELECT u.*, c.id as customer_id, c.full_name, c.phone, c.address, c.city, c.state 
            FROM users u 
            LEFT JOIN customers c ON u.id = c.user_id 
            WHERE u.username = ?";

    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "s", $username);

        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);

            if (mysqli_num_rows($result) == 1) {
                $row = mysqli_fetch_assoc($result);
                if (password_verify($password, $row['password'])) {
                    // Store data in session variables
                    $_SESSION["loggedin"] = true;
                    $_SESSION["id"] = $row['id']; // This is the user_id
                    $_SESSION["username"] = $row['username'];
                    $_SESSION["role"] = $row['role'];
                    $_SESSION["email"] = $row['email'];

                    // For customers, also store customer-specific information
                    if ($row['role'] === 'customer') {
                        if (!$row['customer_id'] || !$row['full_name']) {
                            $login_err = "Customer account not properly set up. Please contact support.";
                            session_destroy();
                            goto output_page;
                        }
                        $_SESSION["customer_id"] = $row['customer_id'];
                        $_SESSION["customer_name"] = $row['full_name'];
                        $_SESSION["customer_phone"] = $row['phone'];
                        $_SESSION["customer_address"] = $row['address'];
                        $_SESSION["customer_city"] = $row['city'];
                        $_SESSION["customer_state"] = $row['state'];
                    }

                    // Redirect user to appropriate dashboard
                    header("location: " . $row['role'] . "/dashboard.php");
                    exit;
                } else {
                    $login_err = "Invalid username or password.";
                }
            } else {
                $login_err = "Invalid username or password.";
            }
        } else {
            $login_err = "Oops! Something went wrong. Please try again later.";
        }

        mysqli_stmt_close($stmt);
    }
}

output_page:
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Cargo Management System</title>
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <style>
        body {
            background-color: #f8f9fa;
        }

        .login-container {
            max-width: 400px;
            margin: 100px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }

        .login-logo {
            text-align: center;
            margin-bottom: 30px;
        }

        .login-logo h1 {
            color: #0d6efd;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="login-container">
            <div class="login-logo">
                <h1>Cargo Management</h1>
                <p class="text-muted">Sign in to your account</p>
            </div>

            <?php
            if (!empty($login_err)) {
                echo '<div class="alert alert-danger">' . htmlspecialchars($login_err) . '</div>';
                if (strpos($login_err, "not properly set up") !== false) {
                    echo '<br><br><p>To resolve this:</p><ol><li>Try registering a new account with complete information</li><li>Or contact the administrator for assistance</li></ol>';
                }
            }
            ?>

            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary">Sign In</button>
                </div>
                <p class="mt-3 text-center">Don't have an account? <a href="register.php">Sign up now</a></p>
            </form>
        </div>
    </div>

    <!-- Bootstrap 5.3 JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>