<?php
session_start();

// Check if user is logged in and is admin
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["role"] !== "admin"){
    header("location: ../index.php");
    exit;
}

require_once "../config/database.php";

// Get shipment statistics
$shipment_stats = array();
$sql = "SELECT 
            COUNT(*) as total_shipments,
            SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_shipments,
            SUM(CASE WHEN status = 'in_transit' THEN 1 ELSE 0 END) as in_transit_shipments,
            SUM(CASE WHEN status = 'delivered' THEN 1 ELSE 0 END) as delivered_shipments,
            SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled_shipments
        FROM shipments";
$result = mysqli_query($conn, $sql);
if($row = mysqli_fetch_assoc($result)){
    $shipment_stats = $row;
}

// Get monthly shipment data
$monthly_shipments = array();
$sql = "SELECT 
            DATE_FORMAT(created_at, '%Y-%m') as month,
            COUNT(*) as total
        FROM shipments
        GROUP BY DATE_FORMAT(created_at, '%Y-%m')
        ORDER BY month DESC
        LIMIT 12";
$result = mysqli_query($conn, $sql);
while($row = mysqli_fetch_assoc($result)){
    $monthly_shipments[] = $row;
}

// Get vehicle utilization
$vehicle_utilization = array();
$sql = "SELECT 
            v.vehicle_number,
            COUNT(s.id) as total_shipments,
            SUM(CASE WHEN s.status = 'in_transit' THEN 1 ELSE 0 END) as active_shipments
        FROM vehicles v
        LEFT JOIN shipments s ON v.id = s.vehicle_id
        GROUP BY v.id
        ORDER BY total_shipments DESC";
$result = mysqli_query($conn, $sql);
while($row = mysqli_fetch_assoc($result)){
    $vehicle_utilization[] = $row;
}

// Get location statistics
$location_stats = array();
$sql = "SELECT 
            l.name,
            COUNT(s.id) as total_shipments,
            SUM(CASE WHEN s.status = 'delivered' THEN 1 ELSE 0 END) as delivered_shipments
        FROM locations l
        LEFT JOIN shipments s ON l.id = s.delivery_location_id
        GROUP BY l.id
        ORDER BY total_shipments DESC";
$result = mysqli_query($conn, $sql);
while($row = mysqli_fetch_assoc($result)){
    $location_stats[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - Cargo Management System</title>
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
        .chart-container {
            position: relative;
            margin: auto;
            height: 300px;
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
                            <a class="nav-link active" href="reports.php">
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
                    <h2>Reports & Analytics</h2>
                    <div>
                        <button class="btn btn-primary" onclick="window.print()">
                            <i class="fas fa-print"></i> Print Report
                        </button>
                    </div>
                </div>

                <!-- Shipment Statistics -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <h5 class="card-title">Total Shipments</h5>
                                <h2 class="card-text"><?php echo $shipment_stats['total_shipments']; ?></h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body">
                                <h5 class="card-title">Pending Shipments</h5>
                                <h2 class="card-text"><?php echo $shipment_stats['pending_shipments']; ?></h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <h5 class="card-title">In Transit</h5>
                                <h2 class="card-text"><?php echo $shipment_stats['in_transit_shipments']; ?></h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <h5 class="card-title">Delivered</h5>
                                <h2 class="card-text"><?php echo $shipment_stats['delivered_shipments']; ?></h2>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts -->
                <div class="row">
                    <!-- Monthly Shipments Chart -->
                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Monthly Shipments</h5>
                            </div>
                            <div class="card-body">
                                <div class="chart-container">
                                    <canvas id="monthlyShipmentsChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Shipment Status Chart -->
                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Shipment Status Distribution</h5>
                            </div>
                            <div class="card-body">
                                <div class="chart-container">
                                    <canvas id="shipmentStatusChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Vehicle Utilization Chart -->
                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Vehicle Utilization</h5>
                            </div>
                            <div class="card-body">
                                <div class="chart-container">
                                    <canvas id="vehicleUtilizationChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Location Performance Chart -->
                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Location Performance</h5>
                            </div>
                            <div class="card-body">
                                <div class="chart-container">
                                    <canvas id="locationPerformanceChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5.3 JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Initialize Charts -->
    <script>
        // Monthly Shipments Chart
        const monthlyShipmentsCtx = document.getElementById('monthlyShipmentsChart').getContext('2d');
        new Chart(monthlyShipmentsCtx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode(array_column(array_reverse($monthly_shipments), 'month')); ?>,
                datasets: [{
                    label: 'Total Shipments',
                    data: <?php echo json_encode(array_column(array_reverse($monthly_shipments), 'total')); ?>,
                    borderColor: '#0d6efd',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });

        // Shipment Status Chart
        const shipmentStatusCtx = document.getElementById('shipmentStatusChart').getContext('2d');
        new Chart(shipmentStatusCtx, {
            type: 'doughnut',
            data: {
                labels: ['Pending', 'In Transit', 'Delivered', 'Cancelled'],
                datasets: [{
                    data: [
                        <?php echo $shipment_stats['pending_shipments']; ?>,
                        <?php echo $shipment_stats['in_transit_shipments']; ?>,
                        <?php echo $shipment_stats['delivered_shipments']; ?>,
                        <?php echo $shipment_stats['cancelled_shipments']; ?>
                    ],
                    backgroundColor: ['#ffc107', '#0dcaf0', '#198754', '#dc3545']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });

        // Vehicle Utilization Chart
        const vehicleUtilizationCtx = document.getElementById('vehicleUtilizationChart').getContext('2d');
        new Chart(vehicleUtilizationCtx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode(array_column($vehicle_utilization, 'vehicle_number')); ?>,
                datasets: [{
                    label: 'Total Shipments',
                    data: <?php echo json_encode(array_column($vehicle_utilization, 'total_shipments')); ?>,
                    backgroundColor: '#0d6efd'
                }, {
                    label: 'Active Shipments',
                    data: <?php echo json_encode(array_column($vehicle_utilization, 'active_shipments')); ?>,
                    backgroundColor: '#198754'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Location Performance Chart
        const locationPerformanceCtx = document.getElementById('locationPerformanceChart').getContext('2d');
        new Chart(locationPerformanceCtx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode(array_column($location_stats, 'name')); ?>,
                datasets: [{
                    label: 'Total Shipments',
                    data: <?php echo json_encode(array_column($location_stats, 'total_shipments')); ?>,
                    backgroundColor: '#0d6efd'
                }, {
                    label: 'Delivered Shipments',
                    data: <?php echo json_encode(array_column($location_stats, 'delivered_shipments')); ?>,
                    backgroundColor: '#198754'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>
</html> 