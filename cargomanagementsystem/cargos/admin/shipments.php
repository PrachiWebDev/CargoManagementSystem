<?php
session_start();

// Check if user is logged in and is admin
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["role"] !== "admin") {
    header("location: ../index.php");
    exit;
}

require_once "../config/database.php";

// Process delete operation
if (isset($_GET["delete"]) && !empty($_GET["delete"])) {
    $sql = "DELETE FROM shipments WHERE id = ?";
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $_GET["delete"]);
        if (mysqli_stmt_execute($stmt)) {
            header("location: shipments.php");
            exit();
        }
    }
}

// Get all shipments
$shipments = array();
$sql = "SELECT s.*, c.full_name as customer_name, l1.name as pickup_location, l2.name as delivery_location, v.vehicle_number 
        FROM shipments s 
        LEFT JOIN customers c ON s.customer_id = c.id 
        LEFT JOIN locations l1 ON s.pickup_location_id = l1.id 
        LEFT JOIN locations l2 ON s.delivery_location_id = l2.id 
        LEFT JOIN vehicles v ON s.vehicle_id = v.id 
        ORDER BY s.created_at DESC";
$result = mysqli_query($conn, $sql);
while ($row = mysqli_fetch_assoc($result)) {
    $shipments[] = $row;
}

// Get all customers for dropdown
$customers = array();
$sql = "SELECT id, full_name FROM customers";
$result = mysqli_query($conn, $sql);
while ($row = mysqli_fetch_assoc($result)) {
    $customers[] = $row;
}

// Get all locations for dropdown
$locations = array();
$sql = "SELECT id, name FROM locations";
$result = mysqli_query($conn, $sql);
while ($row = mysqli_fetch_assoc($result)) {
    $locations[] = $row;
}

// Get all vehicles for dropdown
$vehicles = array();
$sql = "SELECT id, vehicle_number FROM vehicles WHERE status = 'available'";
$result = mysqli_query($conn, $sql);
while ($row = mysqli_fetch_assoc($result)) {
    $vehicles[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shipment Management - Cargo Management System</title>
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <style>
        .sidebar {
            min-height: 100vh;
            background-color: #212529;
            color: #fff;
        }

        .sidebar .nav-link {
            color: #fff;
            padding: 10px 20px;
        }

        .sidebar .nav-link:hover {
            background-color: #0d6efd;
        }

        .sidebar .nav-link.active {
            background-color: #0d6efd;
        }

        .main-content {
            padding: 20px;
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
                            <a class="nav-link" href="dashboard.php">
                                <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="customers.php">
                                <i class="fas fa-users me-2"></i> Customers
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="shipments.php">
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
                    <h2>Shipment Management</h2>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addShipmentModal">
                        <i class="fas fa-plus"></i> Add New Shipment
                    </button>
                </div>

                <!-- Shipments Table -->
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="shipmentsTable" class="table table-striped">
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
                                    <?php foreach ($shipments as $shipment): ?>
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
                                                <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#editShipmentModal<?php echo $shipment['id']; ?>">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <a href="shipments.php?delete=<?php echo $shipment['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this shipment?')">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                                <a href="tracking.php?id=<?php echo $shipment['id']; ?>" class="btn btn-sm btn-success">
                                                    <i class="fas fa-map-marker-alt"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Shipment Modal -->
    <div class="modal fade" id="addShipmentModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Shipment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="add_shipment.php" method="post">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="customer_id" class="form-label">Customer</label>
                                    <select class="form-select" id="customer_id" name="customer_id" required>
                                        <option value="">Select Customer</option>
                                        <?php foreach ($customers as $customer): ?>
                                            <option value="<?php echo $customer['id']; ?>"><?php echo htmlspecialchars($customer['full_name']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="vehicle_id" class="form-label">Vehicle</label>
                                    <select class="form-select" id="vehicle_id" name="vehicle_id" required>
                                        <option value="">Select Vehicle</option>
                                        <?php foreach ($vehicles as $vehicle): ?>
                                            <option value="<?php echo $vehicle['id']; ?>"><?php echo htmlspecialchars($vehicle['vehicle_number']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="pickup_location_id" class="form-label">Pickup Location</label>
                                    <select class="form-select" id="pickup_location_id" name="pickup_location_id" required>
                                        <option value="">Select Location</option>
                                        <?php foreach ($locations as $location): ?>
                                            <option value="<?php echo $location['id']; ?>"><?php echo htmlspecialchars($location['name']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="delivery_location_id" class="form-label">Delivery Location</label>
                                    <select class="form-select" id="delivery_location_id" name="delivery_location_id" required>
                                        <option value="">Select Location</option>
                                        <?php foreach ($locations as $location): ?>
                                            <option value="<?php echo $location['id']; ?>"><?php echo htmlspecialchars($location['name']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="weight" class="form-label">Weight (kg)</label>
                                    <input type="number" class="form-control" id="weight" name="weight" step="0.01" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="dimensions" class="form-label">Dimensions</label>
                                    <input type="text" class="form-control" id="dimensions" name="dimensions" placeholder="L x W x H">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="status" class="form-label">Status</label>
                                    <select class="form-select" id="status" name="status" required>
                                        <option value="pending">Pending</option>
                                        <option value="in_transit">In Transit</option>
                                        <option value="delivered">Delivered</option>
                                        <option value="cancelled">Cancelled</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Add Shipment</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5.3 JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <!-- Initialize DataTables -->
    <script>
        $(document).ready(function() {
            $('#shipmentsTable').DataTable();
        });
    </script>
</body>

</html>