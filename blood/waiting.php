<?php
include 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'blood_seeker') {
    header("Location: login.php");
    exit();
}

$stmt = $conn->prepare("SELECT br.*, u.name AS donor_name, u.phone AS donor_phone FROM blood_requests br LEFT JOIN users u ON br.assigned_donor_id = u.id WHERE br.blood_seeker_id = :blood_seeker_id ORDER BY br.created_at DESC LIMIT 1");
$stmt->bindParam(':blood_seeker_id', $_SESSION['user_id']);
$stmt->execute();
$request = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Waiting for Response</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Waiting for Response</h1>
            <a href="logout.php" class="btn logout">Logout</a>
        </header>
        <div class="section">
            <h2>Your Blood Request Status</h2>
            <?php if ($request): ?>
                <p><strong>Blood Group:</strong> <?php echo htmlspecialchars($request['blood_group']); ?></p>
                <p><strong>Units Needed:</strong> <?php echo htmlspecialchars($request['units_needed']); ?></p>
                <p><strong>Status:</strong> 
                    <span class="<?php echo $request['status'] == 'rejected' ? 'error' : ($request['status'] == 'accepted' ? 'success-message' : ''); ?>">
                        <?php echo $request['status'] == 'pending' ? 'Waiting' : htmlspecialchars($request['status']); ?>
                    </span>
                </p>
                <p><strong>Admin Response:</strong> 
                    <?php
                    if ($request['status'] == 'accepted' && $request['assigned_donor_id']) {
                        echo "Donor is ready! Contact " . htmlspecialchars($request['donor_name']) . " at " . htmlspecialchars($request['donor_phone']);
                    } elseif ($request['status'] == 'rejected') {
                        echo "Your request was rejected. " . ($request['admin_response'] ? htmlspecialchars($request['admin_response']) : "Please try again later.");
                    } else {
                        echo "Waiting for admin review.";
                    }
                    ?>
                </p>
            <?php else: ?>
                <p>No active requests found. <a href="request_blood.php">Submit a new request</a>.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>