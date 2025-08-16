<?php
session_start();
require_once "../config/database.php";

// Check if the user is logged in and is a customer
if (!isset($_SESSION["loggedin"]) || $_SESSION["role"] != "customer") {
    header("location: ../login.php");
    exit;
}

// Get customer information from session
$customer_id = isset($_SESSION["customer_id"]) ? $_SESSION["customer_id"] : 0;
$customer_name = isset($_SESSION["customer_name"]) ? $_SESSION["customer_name"] : "Customer";
$customer_phone = isset($_SESSION["customer_phone"]) ? $_SESSION["customer_phone"] : "";
$customer_address = isset($_SESSION["customer_address"]) ? $_SESSION["customer_address"] : "";
$customer_city = isset($_SESSION["customer_city"]) ? $_SESSION["customer_city"] : "";
$customer_state = isset($_SESSION["customer_state"]) ? $_SESSION["customer_state"] : "";

// If customer_id is not set or is 0, redirect to login
if (!$customer_id) {
    session_destroy();
    header("location: ../login.php");
    exit;
}

// Get recent shipments
$sql = "SELECT * FROM shipments WHERE customer_id = ? ORDER BY created_at DESC LIMIT 5";
$recent_shipments = [];
if ($stmt = mysqli_prepare($conn, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $customer_id);
    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        while ($row = mysqli_fetch_assoc($result)) {
            $recent_shipments[] = $row;
        }
    }
    mysqli_stmt_close($stmt);
}

// Get shipment counts by status
$shipment_stats = [
    'total' => 0,
    'pending' => 0,
    'in_transit' => 0,
    'delivered' => 0
];

$sql = "SELECT status, COUNT(*) as count FROM shipments WHERE customer_id = ? GROUP BY status";
if ($stmt = mysqli_prepare($conn, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $customer_id);
    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        while ($row = mysqli_fetch_assoc($result)) {
            $shipment_stats[$row['status']] = $row['count'];
            $shipment_stats['total'] += $row['count'];
        }
    }
    mysqli_stmt_close($stmt);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Dashboard - Cargo Management System</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        .dashboard-stats {
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
            background-color: #f8f9fa;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .nav-link {
            color: #333;
        }

        .nav-link:hover {
            color: #007bff;
        }

        .profile-info {
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .profile-info h4 {
            color: #0d6efd;
            margin-bottom: 15px;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <a class="navbar-brand" href="#">Cargo Management</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item active">
                    <a class="nav-link" href="dashboard.php">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="shipments.php">My Shipments</a>
                </li>
                <li class="nav-item">
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
        <div class="profile-info">
            <h4><i class="fas fa-user"></i> Customer Profile</h4>
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Name:</strong> <?php echo htmlspecialchars($customer_name); ?></p>
                    <p><strong>Phone:</strong> <?php echo htmlspecialchars($customer_phone); ?></p>
                </div>
                <div class="col-md-6">
                    <p><strong>Address:</strong> <?php echo htmlspecialchars($customer_address); ?></p>
                    <p><strong>Location:</strong> <?php echo htmlspecialchars($customer_city . ", " . $customer_state); ?></p>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-3">
                <div class="dashboard-stats">
                    <h4><i class="fas fa-box"></i> Total Shipments</h4>
                    <h3><?php echo $shipment_stats['total']; ?></h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="dashboard-stats">
                    <h4><i class="fas fa-clock"></i> Pending</h4>
                    <h3><?php echo $shipment_stats['pending']; ?></h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="dashboard-stats">
                    <h4><i class="fas fa-truck"></i> In Transit</h4>
                    <h3><?php echo $shipment_stats['in_transit']; ?></h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="dashboard-stats">
                    <h4><i class="fas fa-check-circle"></i> Delivered</h4>
                    <h3><?php echo $shipment_stats['delivered']; ?></h3>
                </div>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <h4>Recent Shipments</h4>
            </div>
            <div class="card-body">
                <?php if (empty($recent_shipments)): ?>
                    <div class="alert alert-info">
                        <p>No shipments found. <a href="new_shipment.php" class="alert-link">Create your first shipment</a></p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Tracking ID</th>
                                    <th>From</th>
                                    <th>To</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_shipments as $shipment): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($shipment['tracking_id']); ?></td>
                                        <td><?php echo htmlspecialchars($shipment['origin']); ?></td>
                                        <td><?php echo htmlspecialchars($shipment['destination']); ?></td>
                                        <td>
                                            <span class="badge badge-<?php
                                                                        echo $shipment['status'] == 'delivered' ? 'success' : ($shipment['status'] == 'in_transit' ? 'primary' : 'warning');
                                                                        ?>">
                                                <?php echo ucfirst(htmlspecialchars($shipment['status'])); ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('Y-m-d', strtotime($shipment['created_at'])); ?></td>
                                        <td>
                                            <a href="view_shipment.php?id=<?php echo $shipment['id']; ?>" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>