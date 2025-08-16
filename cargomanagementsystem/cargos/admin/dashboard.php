<?php
session_start();

// Check if user is logged in and is admin
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["role"] !== "admin") {
    header("location: ../index.php");
    exit;
}

require_once "../config/database.php";

// Initialize variables
$total_customers = $total_shipments = $total_vehicles = $total_locations = 0;
$recent_shipments = array();
$error_message = '';

try {
    // Get total customers
    $sql = "SELECT COUNT(*) as total FROM customers";
    if ($result = mysqli_query($conn, $sql)) {
        if ($row = mysqli_fetch_assoc($result)) {
            $total_customers = $row['total'];
        }
        mysqli_free_result($result);
    } else {
        throw new Exception("Error fetching customers count: " . mysqli_error($conn));
    }

    // Get total shipments
    $sql = "SELECT COUNT(*) as total FROM shipments";
    if ($result = mysqli_query($conn, $sql)) {
        if ($row = mysqli_fetch_assoc($result)) {
            $total_shipments = $row['total'];
        }
        mysqli_free_result($result);
    } else {
        throw new Exception("Error fetching shipments count: " . mysqli_error($conn));
    }

    // Get total vehicles
    $sql = "SELECT COUNT(*) as total FROM vehicles";
    if ($result = mysqli_query($conn, $sql)) {
        if ($row = mysqli_fetch_assoc($result)) {
            $total_vehicles = $row['total'];
        }
        mysqli_free_result($result);
    } else {
        throw new Exception("Error fetching vehicles count: " . mysqli_error($conn));
    }

    // Get total locations
    $sql = "SELECT COUNT(*) as total FROM locations";
    if ($result = mysqli_query($conn, $sql)) {
        if ($row = mysqli_fetch_assoc($result)) {
            $total_locations = $row['total'];
        }
        mysqli_free_result($result);
    } else {
        throw new Exception("Error fetching locations count: " . mysqli_error($conn));
    }

    // Get recent shipments with additional information
    $sql = "SELECT 
                s.id,
                s.tracking_number,
                s.status,
                s.created_at,
                c.full_name as customer_name,
                l1.name as pickup_location,
                l2.name as delivery_location,
                v.vehicle_number
            FROM shipments s 
            LEFT JOIN customers c ON s.customer_id = c.id 
            LEFT JOIN locations l1 ON s.pickup_location_id = l1.id 
            LEFT JOIN locations l2 ON s.delivery_location_id = l2.id 
            LEFT JOIN vehicles v ON s.vehicle_id = v.id 
            ORDER BY s.created_at DESC 
            LIMIT 5";

    if ($result = mysqli_query($conn, $sql)) {
        while ($row = mysqli_fetch_assoc($result)) {
            $recent_shipments[] = $row;
        }
        mysqli_free_result($result);
    } else {
        throw new Exception("Error fetching recent shipments: " . mysqli_error($conn));
    }
} catch (Exception $e) {
    $error_message = $e->getMessage();
    // Log the error for admin review
    error_log("Dashboard Error: " . $error_message);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Cargo Management System</title>
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-bg: #212529;
            --hover-bg: #0d6efd;
            --transition-speed: 0.3s;
        }

        body {
            background-color: #f8f9fa;
        }

        .sidebar {
            min-height: 100vh;
            background-color: var(--primary-bg);
            color: #fff;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
            transition: all var(--transition-speed);
        }

        .sidebar .nav-link {
            color: #fff;
            padding: 12px 20px;
            border-radius: 5px;
            margin: 2px 10px;
            transition: all var(--transition-speed);
        }

        .sidebar .nav-link:hover {
            background-color: var(--hover-bg);
            transform: translateX(5px);
        }

        .sidebar .nav-link.active {
            background-color: var(--hover-bg);
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        .main-content {
            padding: 25px;
        }

        .stat-card {
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: all var(--transition-speed);
            overflow: hidden;
            border: none;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
        }

        .stat-card .card-body {
            padding: 1.5rem;
            position: relative;
            z-index: 1;
        }

        .stat-card .card-title {
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 1rem;
        }

        .stat-card .card-text {
            font-size: 2rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .stat-card i.fa-2x {
            opacity: 0.15;
            transform: scale(1.5);
            transition: all var(--transition-speed);
        }

        .stat-card:hover i.fa-2x {
            transform: scale(1.8) rotate(10deg);
        }

        .table {
            vertical-align: middle;
        }

        .table thead th {
            background-color: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 0.5px;
        }

        .badge {
            padding: 0.5em 0.8em;
            font-weight: 500;
        }

        .btn-group-sm>.btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
            border-radius: 0.2rem;
            margin: 0 1px;
        }

        .alert {
            border-radius: 10px;
            border: none;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .dropdown-menu {
            border-radius: 10px;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.15);
            border: none;
            padding: 0.5rem;
        }

        .dropdown-item {
            padding: 0.5rem 1rem;
            border-radius: 5px;
            transition: all var(--transition-speed);
        }

        .dropdown-item:hover {
            background-color: #f8f9fa;
            transform: translateX(5px);
        }

        .card {
            border-radius: 15px;
            border: none;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.08);
        }

        .card-header {
            background-color: #fff;
            border-bottom: 1px solid rgba(0, 0, 0, 0.08);
            padding: 1rem 1.5rem;
        }

        .table-responsive {
            border-radius: 10px;
        }

        @media (max-width: 768px) {
            .sidebar {
                position: fixed;
                z-index: 1000;
                width: 100%;
                height: auto;
                min-height: auto;
            }

            .main-content {
                margin-top: 60px;
            }
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 px-0 sidebar">
                <div class="p-3">
                    <h4 class="text-center">Cargo Management</h4>
                    <hr>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link active" href="dashboard.php">
                                <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="customers.php">
                                <i class="fas fa-users me-2"></i> Customers
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="shipments.php">
                                <i class="fas fa-shipping-fast me-2"></i> Shipments
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="vehicles.php">
                                <i class="fas fa-truck me-2"></i> Vehicles
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="locations.php">
                                <i class="fas fa-map-marker-alt me-2"></i> Locations
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="reports.php">
                                <i class="fas fa-chart-bar me-2"></i> Reports
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../logout.php">
                                <i class="fas fa-sign-out-alt me-2"></i> Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 main-content">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Dashboard</h2>
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <i class="fas fa-user-circle me-2"></i>
                            <span>Welcome, <?php echo htmlspecialchars($_SESSION["username"] ?? 'Admin'); ?></span>
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-light dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown">
                                <i class="fas fa-cog"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="profile.php"><i class="fas fa-user-edit me-2"></i>Edit Profile</a></li>
                                <li><a class="dropdown-item" href="settings.php"><i class="fas fa-cogs me-2"></i>Settings</a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item text-danger" href="../logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <?php if (!empty($error_message)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <?php echo htmlspecialchars($error_message); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card stat-card bg-primary text-white">
                            <div class="card-body">
                                <h5 class="card-title">Total Customers</h5>
                                <h2 class="card-text"><?php echo number_format($total_customers); ?></h2>
                                <i class="fas fa-users fa-2x position-absolute top-50 end-0 me-3 opacity-50"></i>
                                <div class="mt-2">
                                    <a href="customers.php" class="text-white text-decoration-none small">View Details <i class="fas fa-arrow-right ms-1"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card bg-success text-white">
                            <div class="card-body">
                                <h5 class="card-title">Total Shipments</h5>
                                <h2 class="card-text"><?php echo number_format($total_shipments); ?></h2>
                                <i class="fas fa-shipping-fast fa-2x position-absolute top-50 end-0 me-3 opacity-50"></i>
                                <div class="mt-2">
                                    <a href="shipments.php" class="text-white text-decoration-none small">View Details <i class="fas fa-arrow-right ms-1"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card bg-warning text-white">
                            <div class="card-body">
                                <h5 class="card-title">Total Vehicles</h5>
                                <h2 class="card-text"><?php echo number_format($total_vehicles); ?></h2>
                                <i class="fas fa-truck fa-2x position-absolute top-50 end-0 me-3 opacity-50"></i>
                                <div class="mt-2">
                                    <a href="vehicles.php" class="text-white text-decoration-none small">View Details <i class="fas fa-arrow-right ms-1"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card bg-info text-white">
                            <div class="card-body">
                                <h5 class="card-title">Total Locations</h5>
                                <h2 class="card-text"><?php echo number_format($total_locations); ?></h2>
                                <i class="fas fa-map-marker-alt fa-2x position-absolute top-50 end-0 me-3 opacity-50"></i>
                                <div class="mt-2">
                                    <a href="locations.php" class="text-white text-decoration-none small">View Details <i class="fas fa-arrow-right ms-1"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Shipments -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Recent Shipments</h5>
                        <a href="shipments.php" class="btn btn-primary btn-sm">
                            <i class="fas fa-list me-1"></i> View All
                        </a>
                    </div>
                    <div class="card-body">
                        <?php if (empty($recent_shipments)): ?>
                            <div class="text-center text-muted py-4">
                                <i class="fas fa-box fa-3x mb-3"></i>
                                <p>No recent shipments found</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Tracking #</th>
                                            <th>Customer</th>
                                            <th>Pickup</th>
                                            <th>Delivery</th>
                                            <th>Vehicle</th>
                                            <th>Status</th>
                                            <th>Created</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($recent_shipments as $shipment): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($shipment['tracking_number'] ?? 'N/A'); ?></td>
                                                <td><?php echo htmlspecialchars($shipment['customer_name'] ?? 'N/A'); ?></td>
                                                <td><?php echo htmlspecialchars($shipment['pickup_location'] ?? 'N/A'); ?></td>
                                                <td><?php echo htmlspecialchars($shipment['delivery_location'] ?? 'N/A'); ?></td>
                                                <td><?php echo htmlspecialchars($shipment['vehicle_number'] ?? 'N/A'); ?></td>
                                                <td>
                                                    <span class="badge bg-<?php
                                                                            echo ($shipment['status'] ?? 'pending') == 'delivered' ? 'success' : (($shipment['status'] ?? 'pending') == 'in_transit' ? 'primary' : (($shipment['status'] ?? 'pending') == 'cancelled' ? 'danger' : 'warning'));
                                                                            ?>">
                                                        <?php echo ucfirst($shipment['status'] ?? 'pending'); ?>
                                                    </span>
                                                </td>
                                                <td><?php echo isset($shipment['created_at']) ? date('M d, Y', strtotime($shipment['created_at'])) : 'N/A'; ?></td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <a href="tracking.php?id=<?php echo $shipment['id']; ?>" class="btn btn-info" title="Track Shipment">
                                                            <i class="fas fa-map-marker-alt"></i>
                                                        </a>
                                                        <a href="shipments.php?id=<?php echo $shipment['id']; ?>" class="btn btn-primary" title="View Details">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                    </div>
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
        </div>
    </div>

    <!-- Bootstrap 5.3 JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script>
        // Auto-hide alerts after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                setTimeout(function() {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }, 5000);
            });
        });
    </script>
</body>

</html>