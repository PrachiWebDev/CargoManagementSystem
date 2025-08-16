<?php
session_start();
require_once "../config/database.php";

// Check if the user is logged in and is a customer
if (!isset($_SESSION["loggedin"]) || $_SESSION["role"] != "customer") {
    header("location: ../login.php");
    exit;
}

$customer_id = $_SESSION["customer_id"];
$error_message = "";
$success_message = "";

// Get all shipments for the customer
$sql = "SELECT s.*, 
        pl.name as pickup_location, pl.city as pickup_city,
        dl.name as delivery_location, dl.city as delivery_city
        FROM shipments s
        LEFT JOIN locations pl ON s.pickup_location_id = pl.id
        LEFT JOIN locations dl ON s.delivery_location_id = dl.id
        WHERE s.customer_id = ?
        ORDER BY s.created_at DESC";

$shipments = [];
if ($stmt = mysqli_prepare($conn, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $customer_id);
    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        while ($row = mysqli_fetch_assoc($result)) {
            $shipments[] = $row;
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
    <title>My Shipments - Cargo Management System</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.22/css/dataTables.bootstrap4.min.css">
    <style>
        .shipment-container {
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .tracking-modal .timeline {
            position: relative;
            padding: 20px 0;
        }

        .tracking-modal .timeline-item {
            padding: 10px 0;
            position: relative;
            border-left: 2px solid #dee2e6;
            margin-left: 20px;
            padding-left: 20px;
        }

        .tracking-modal .timeline-item::before {
            content: '';
            position: absolute;
            left: -6px;
            top: 15px;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #0d6efd;
        }

        .tracking-modal .timeline-date {
            color: #6c757d;
            font-size: 0.9em;
        }

        .status-badge {
            font-size: 0.9em;
            padding: 5px 10px;
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
        <div class="shipment-container">
            <div class="mb-4">
                <h2>My Shipments</h2>
            </div>

            <?php if (!empty($error_message)): ?>
                <div class="alert alert-danger"><?php echo $error_message; ?></div>
            <?php endif; ?>

            <?php if (!empty($success_message)): ?>
                <div class="alert alert-success"><?php echo $success_message; ?></div>
            <?php endif; ?>

            <?php if (empty($shipments)): ?>
                <div class="alert alert-info">
                    <p>No shipments found.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table id="shipmentsTable" class="table table-striped">
                        <thead>
                            <tr>
                                <th>Tracking Number</th>
                                <th>From</th>
                                <th>To</th>
                                <th>Status</th>
                                <th>Created Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($shipments as $shipment): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($shipment['tracking_number']); ?></td>
                                    <td><?php echo htmlspecialchars($shipment['pickup_location'] . ' - ' . $shipment['pickup_city']); ?></td>
                                    <td><?php echo htmlspecialchars($shipment['delivery_location'] . ' - ' . $shipment['delivery_city']); ?></td>
                                    <td>
                                        <span class="badge status-badge badge-<?php
                                                                                echo $shipment['status'] == 'delivered' ? 'success' : ($shipment['status'] == 'in_transit' ? 'primary' : ($shipment['status'] == 'cancelled' ? 'danger' : 'warning'));
                                                                                ?>">
                                            <?php echo ucfirst($shipment['status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('d M Y', strtotime($shipment['created_at'])); ?></td>
                                    <td>
                                        <button type="button" class="btn btn-info btn-sm track-btn"
                                            data-shipment-id="<?php echo $shipment['id']; ?>"
                                            data-tracking-number="<?php echo htmlspecialchars($shipment['tracking_number']); ?>">
                                            <i class="fas fa-search"></i> Track
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Tracking Modal -->
    <div class="modal fade tracking-modal" id="trackingModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tracking Information</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="trackingContent">
                        <div class="text-center">
                            <div class="spinner-border text-primary" role="status">
                                <span class="sr-only">Loading...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.22/js/dataTables.bootstrap4.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize DataTable
            $('#shipmentsTable').DataTable({
                "order": [
                    [4, "desc"]
                ], // Sort by created date by default
                "pageLength": 10,
                "language": {
                    "lengthMenu": "Show _MENU_ shipments per page",
                    "zeroRecords": "No shipments found",
                    "info": "Showing page _PAGE_ of _PAGES_",
                    "infoEmpty": "No shipments available",
                    "infoFiltered": "(filtered from _MAX_ total shipments)"
                }
            });

            // Handle tracking button click
            $('.track-btn').click(function() {
                const shipmentId = $(this).data('shipment-id');
                const trackingNumber = $(this).data('tracking-number');

                $('#trackingModal').modal('show');

                // Load tracking information
                $.ajax({
                    url: 'get_tracking.php',
                    method: 'POST',
                    data: {
                        shipment_id: shipmentId
                    },
                    success: function(response) {
                        $('#trackingContent').html(response);
                    },
                    error: function() {
                        $('#trackingContent').html('<div class="alert alert-danger">Error loading tracking information</div>');
                    }
                });
            });
        });
    </script>
</body>

</html>