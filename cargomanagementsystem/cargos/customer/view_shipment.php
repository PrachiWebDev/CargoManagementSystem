<?php
session_start();
require_once "../config/database.php";

// Check if the user is logged in and is a customer
if (!isset($_SESSION["loggedin"]) || $_SESSION["role"] != "customer") {
    header("location: ../login.php");
    exit;
}

$customer_id = $_SESSION["customer_id"];
$shipment_id = isset($_GET["id"]) ? $_GET["id"] : 0;

// Get shipment information
$sql = "SELECT s.*, v.name as vehicle_name, v.type as vehicle_type 
        FROM shipments s 
        LEFT JOIN vehicles v ON s.vehicle_id = v.id 
        WHERE s.id = ? AND s.customer_id = ?";
$shipment = null;

if ($stmt = mysqli_prepare($conn, $sql)) {
    mysqli_stmt_bind_param($stmt, "ii", $shipment_id, $customer_id);
    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        $shipment = mysqli_fetch_assoc($result);
    }
    mysqli_stmt_close($stmt);
}

// If shipment not found or doesn't belong to customer, redirect
if (!$shipment) {
    header("location: shipments.php");
    exit;
}

// Get shipment updates/tracking history
$sql = "SELECT * FROM shipment_updates WHERE shipment_id = ? ORDER BY created_at DESC";
$updates = [];
if ($stmt = mysqli_prepare($conn, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $shipment_id);
    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        while ($row = mysqli_fetch_assoc($result)) {
            $updates[] = $row;
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
    <title>View Shipment - Cargo Management System</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        .tracking-timeline {
            position: relative;
            padding-left: 30px;
        }

        .tracking-timeline::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #007bff;
        }

        .timeline-item {
            position: relative;
            padding-bottom: 1.5rem;
        }

        .timeline-item::before {
            content: '';
            position: absolute;
            left: -34px;
            top: 0;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #007bff;
            border: 2px solid #fff;
        }

        .timeline-date {
            color: #6c757d;
            font-size: 0.875rem;
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
                <li class="nav-item">
                    <a class="nav-link" href="dashboard.php">Dashboard</a>
                </li>
                <li class="nav-item active">
                    <a class="nav-link" href="shipments.php">My Shipments</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="new_shipment.php">New Shipment</a>
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
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Shipment Details</h2>
            <a href="shipments.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Shipments
            </a>
        </div>

        <div class="row">
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h4>Shipment Information</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Tracking ID:</strong><br><?php echo htmlspecialchars($shipment["tracking_id"]); ?></p>
                                <p><strong>Status:</strong><br>
                                    <span class="badge badge-<?php
                                                                echo $shipment['status'] == 'delivered' ? 'success' : ($shipment['status'] == 'in_transit' ? 'primary' : 'warning');
                                                                ?>">
                                        <?php echo ucfirst(htmlspecialchars($shipment["status"])); ?>
                                    </span>
                                </p>
                                <p><strong>Origin:</strong><br><?php echo htmlspecialchars($shipment["origin"]); ?></p>
                                <p><strong>Destination:</strong><br><?php echo htmlspecialchars($shipment["destination"]); ?></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Weight:</strong><br><?php echo htmlspecialchars($shipment["weight"]); ?> kg</p>
                                <p><strong>Delivery Type:</strong><br><?php echo ucfirst(htmlspecialchars($shipment["delivery_type"])); ?></p>
                                <p><strong>Created Date:</strong><br><?php echo date('Y-m-d H:i', strtotime($shipment["created_at"])); ?></p>
                                <?php if ($shipment["vehicle_name"]): ?>
                                    <p><strong>Assigned Vehicle:</strong><br><?php echo htmlspecialchars($shipment["vehicle_name"]); ?> (<?php echo htmlspecialchars($shipment["vehicle_type"]); ?>)</p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-12">
                                <p><strong>Package Description:</strong><br><?php echo nl2br(htmlspecialchars($shipment["description"])); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h4>Tracking History</h4>
                    </div>
                    <div class="card-body">
                        <div class="tracking-timeline">
                            <?php if (empty($updates)): ?>
                                <p>No tracking updates available.</p>
                            <?php else: ?>
                                <?php foreach ($updates as $update): ?>
                                    <div class="timeline-item">
                                        <div class="timeline-date">
                                            <?php echo date('Y-m-d H:i', strtotime($update["created_at"])); ?>
                                        </div>
                                        <div class="timeline-content">
                                            <p class="mb-0"><?php echo htmlspecialchars($update["status_message"]); ?></p>
                                            <?php if ($update["location"]): ?>
                                                <small class="text-muted">
                                                    <i class="fas fa-map-marker-alt"></i>
                                                    <?php echo htmlspecialchars($update["location"]); ?>
                                                </small>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
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