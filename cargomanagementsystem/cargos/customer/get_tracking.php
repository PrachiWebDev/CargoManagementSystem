<?php
session_start();
require_once "../config/database.php";

// Check if the user is logged in and is a customer
if (!isset($_SESSION["loggedin"]) || $_SESSION["role"] != "customer") {
    die("Unauthorized access");
}

$customer_id = $_SESSION["customer_id"];

if (!isset($_POST['shipment_id'])) {
    die("Invalid request");
}

$shipment_id = (int)$_POST['shipment_id'];

// Get shipment details and verify ownership
$sql = "SELECT s.*, 
        pl.name as pickup_location, pl.city as pickup_city,
        dl.name as delivery_location, dl.city as delivery_city
        FROM shipments s
        LEFT JOIN locations pl ON s.pickup_location_id = pl.id
        LEFT JOIN locations dl ON s.delivery_location_id = dl.id
        WHERE s.id = ? AND s.customer_id = ?";

if ($stmt = mysqli_prepare($conn, $sql)) {
    mysqli_stmt_bind_param($stmt, "ii", $shipment_id, $customer_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($shipment = mysqli_fetch_assoc($result)) {
        // Get tracking history
        $sql = "SELECT * FROM tracking WHERE shipment_id = ? ORDER BY created_at DESC";
        if ($tracking_stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($tracking_stmt, "i", $shipment_id);
            mysqli_stmt_execute($tracking_stmt);
            $tracking_result = mysqli_stmt_get_result($tracking_stmt);
            $tracking_history = [];
            while ($track = mysqli_fetch_assoc($tracking_result)) {
                $tracking_history[] = $track;
            }

            // Output tracking information
?>
            <div class="tracking-details mb-4">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Tracking Number:</strong> <?php echo htmlspecialchars($shipment['tracking_number']); ?></p>
                        <p><strong>From:</strong> <?php echo htmlspecialchars($shipment['pickup_location'] . ' - ' . $shipment['pickup_city']); ?></p>
                        <p><strong>To:</strong> <?php echo htmlspecialchars($shipment['delivery_location'] . ' - ' . $shipment['delivery_city']); ?></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Status:</strong>
                            <span class="badge badge-<?php
                                                        echo $shipment['status'] == 'delivered' ? 'success' : ($shipment['status'] == 'in_transit' ? 'primary' : ($shipment['status'] == 'cancelled' ? 'danger' : 'warning'));
                                                        ?>">
                                <?php echo ucfirst($shipment['status']); ?>
                            </span>
                        </p>
                        <p><strong>Weight:</strong> <?php echo htmlspecialchars($shipment['weight']); ?> kg</p>
                        <?php if ($shipment['dimensions']): ?>
                            <p><strong>Dimensions:</strong> <?php echo htmlspecialchars($shipment['dimensions']); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <h5 class="mb-3">Tracking History</h5>
            <?php if (empty($tracking_history)): ?>
                <div class="alert alert-info">No tracking updates available yet.</div>
            <?php else: ?>
                <div class="timeline">
                    <?php foreach ($tracking_history as $track): ?>
                        <div class="timeline-item">
                            <div class="timeline-date">
                                <?php echo date('d M Y, h:i A', strtotime($track['created_at'])); ?>
                            </div>
                            <div class="timeline-content">
                                <h6><?php echo ucfirst($track['status']); ?></h6>
                                <?php if ($track['location']): ?>
                                    <p class="mb-0">Location: <?php echo htmlspecialchars($track['location']); ?></p>
                                <?php endif; ?>
                                <?php if ($track['remarks']): ?>
                                    <p class="mb-0"><?php echo htmlspecialchars($track['remarks']); ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
<?php endif;
        }
        mysqli_stmt_close($tracking_stmt);
    } else {
        echo '<div class="alert alert-danger">Shipment not found or access denied.</div>';
    }
    mysqli_stmt_close($stmt);
} else {
    echo '<div class="alert alert-danger">Error retrieving tracking information.</div>';
}
?>