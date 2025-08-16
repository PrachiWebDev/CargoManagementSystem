<?php
session_start();
require_once "../config/database.php";
if (!isset($_SESSION["loggedin"]) || $_SESSION["role"] != "customer") {
    header("location: ../login.php");
    exit;
}
$customer_id = $_SESSION["customer_id"];
$success_message = "";
$error_message = "";
$sql = "SELECT * FROM customers c LEFT JOIN users u on c.user_id=u.id WHERE c.id = ?";
if ($stmt = mysqli_prepare($conn, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $customer_id);
    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        $customer = mysqli_fetch_assoc($result);
    }
    mysqli_stmt_close($stmt);
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["update_profile"])) {
        $name = trim($_POST["name"]);
        $email = trim($_POST["email"]);
        $phone = trim($_POST["phone"]);
        $address = trim($_POST["address"]);
        if (empty($name) || empty($email) || empty($phone) || empty($address)) {
            $error_message = "Please fill all required fields.";
        } else {
            $sql = "UPDATE customers SET name = ?, email = ?, phone = ?, address = ? WHERE id = ?";
            if ($stmt = mysqli_prepare($conn, $sql)) {
                mysqli_stmt_bind_param($stmt, "ssssi", $name, $email, $phone, $address, $customer_id);
                if (mysqli_stmt_execute($stmt)) {
                    $success_message = "Profile updated successfully!";
                    $customer["name"] = $name;
                    $customer["email"] = $email;
                    $customer["phone"] = $phone;
                    $customer["address"] = $address;
                } else {
                    $error_message = "Something went wrong. Please try again later.";
                }
                mysqli_stmt_close($stmt);
            }
        }
    } elseif (isset($_POST["change_password"])) {
        $current_password = trim($_POST["current_password"]);
        $new_password = trim($_POST["new_password"]);
        $confirm_password = trim($_POST["confirm_password"]);
        if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
            $error_message = "Please fill all password fields.";
        } elseif ($new_password != $confirm_password) {
            $error_message = "New password and confirmation password do not match.";
        } else {
            $sql = "SELECT password FROM customers WHERE id = ?";
            if ($stmt = mysqli_prepare($conn, $sql)) {
                mysqli_stmt_bind_param($stmt, "i", $customer_id);
                if (mysqli_stmt_execute($stmt)) {
                    $result = mysqli_stmt_get_result($stmt);
                    $row = mysqli_fetch_assoc($result);
                    if (password_verify($current_password, $row["password"])) {
                        $sql = "UPDATE customers SET password = ? WHERE id = ?";
                        if ($stmt = mysqli_prepare($conn, $sql)) {
                            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                            mysqli_stmt_bind_param($stmt, "si", $hashed_password, $customer_id);
                            if (mysqli_stmt_execute($stmt)) {
                                $success_message = "Password changed successfully!";
                            } else {
                                $error_message = "Something went wrong. Please try again later.";
                            }
                        }
                    } else {
                        $error_message = "Current password is incorrect.";
                    }
                }
                mysqli_stmt_close($stmt);
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Cargo Management System</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <a class="navbar-brand" href="#">Cargo Management</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="dashboard.php">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="shipments.php">My Shipments</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="new_shipment.php">New Shipment</a>
                </li>
                <li class="nav-item active">
                    <a class="nav-link" href="profile.php">Profile</a>
                </li>
            </ul>
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="../logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </nav>
    <div class="container mt-4">
        <h2>My Profile</h2>
        <?php if (!empty($success_message)): ?>
            <div class="alert alert-success">
                <?php echo $success_message; ?>
            </div>
        <?php endif; ?>
        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>
        <div class="row">
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header">
                        <h4>Profile Information</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                            <div class="form-group">
                                <label for="name">Full Name</label>
                                <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($customer["full_name"]); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="email">Email Address</label>
                                <label for="email" class="form-control"><?php echo htmlspecialchars($customer["email"]); ?></label>
                                </div>
                            <div class="form-group">
                                <label for="phone">Phone Number</label>
                                <input type="tel" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($customer["phone"]); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="address">Address</label>
                                <textarea class="form-control" id="address" name="address" rows="3" required><?php echo htmlspecialchars($customer["address"]); ?></textarea>
                            </div>
                            <button type="submit" name="update_profile" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Profile
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h4>Change Password</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                            <div class="form-group">
                                <label for="current_password">Current Password</label>
                                <input type="password" class="form-control" id="current_password" name="current_password" required>
                            </div>
                            <div class="form-group">
                                <label for="new_password">New Password</label>
                                <input type="password" class="form-control" id="new_password" name="new_password" required>
                            </div>
                            <div class="form-group">
                                <label for="confirm_password">Confirm New Password</label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                            </div>
                            <button type="submit" name="change_password" class="btn btn-primary">
                                <i class="fas fa-key"></i> Change Password
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>