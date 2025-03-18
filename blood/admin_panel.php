<?php
include 'config.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit();
}

$donors = $conn->query("SELECT * FROM users WHERE role = 'donor'")->fetchAll(PDO::FETCH_ASSOC);
$blood_seekers = $conn->query("SELECT * FROM users WHERE role = 'blood_seeker'")->fetchAll(PDO::FETCH_ASSOC);
$requests = $conn->query("SELECT br.*, u.name AS seeker_name FROM blood_requests br JOIN users u ON br.blood_seeker_id = u.id")->fetchAll(PDO::FETCH_ASSOC);

//req
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
    $request_id = $_POST['request_id'];
    $action = $_POST['action'];
    $assigned_donor_id = isset($_POST['assigned_donor_id']) ? $_POST['assigned_donor_id'] : null;
    $admin_response = isset($_POST['admin_response']) ? $_POST['admin_response'] : null;

    $status = $action == 'accept' ? 'accepted' : 'rejected';
    $stmt = $conn->prepare("UPDATE blood_requests SET status = :status, assigned_donor_id = :assigned_donor_id, admin_response = :admin_response WHERE id = :request_id");
    $stmt->bindParam(':status', $status);
    $stmt->bindParam(':assigned_donor_id', $assigned_donor_id);
    $stmt->bindParam(':admin_response', $admin_response);
    $stmt->bindParam(':request_id', $request_id);
    $stmt->execute();

    header("Location: admin_panel.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Admin Panel</h1>
            <a href="logout.php" class="btn logout">Logout</a>
        </header>
        <div class="section">
            <h2>Donors</h2>
            <table class="inventory-table">
                <thead>
                    <tr><th>Name</th><th>Blood Group</th><th>Contact</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($donors as $donor): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($donor['name']); ?></td>
                            <td><?php echo htmlspecialchars($donor['blood_group']); ?></td>
                            <td>
                                <button class="form-btn contact-btn" onclick="alert('Phone: <?php echo htmlspecialchars($donor['phone']); ?>\nLocation: <?php echo htmlspecialchars($donor['location']); ?>')">Contact</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="section">
            <h2>Blood Seekers</h2>
            <table class="inventory-table">
                <thead>
                    <tr><th>Name</th><th>Blood Group</th><th>Phone</th><th>Location</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($blood_seekers as $seeker): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($seeker['name']); ?></td>
                            <td><?php echo htmlspecialchars($seeker['blood_group']); ?></td>
                            <td><?php echo htmlspecialchars($seeker['phone']); ?></td>
                            <td><?php echo htmlspecialchars($seeker['location']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="section">
            <h2>Blood Requests</h2>
            <table class="inventory-table">
                <thead>
                    <tr><th>Seeker Name</th><th>Blood Group</th><th>Units Needed</th><th>Status</th><th>Action</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($requests as $request): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($request['seeker_name']); ?></td>
                            <td><?php echo htmlspecialchars($request['blood_group']); ?></td>
                            <td><?php echo htmlspecialchars($request['units_needed']); ?></td>
                            <td class="<?php echo $request['status'] == 'rejected' ? 'error' : ($request['status'] == 'accepted' ? 'success-message' : ''); ?>">
                                <?php echo $request['status'] == 'pending' ? 'Waiting' : htmlspecialchars($request['status']); ?>
                            </td>
                            <td>
                                <?php if ($request['status'] == 'pending'): ?>
                                    <form method="POST" action="">
                                        <input type="hidden" name="request_id" value="<?php echo $request['id']; ?>">
                                        <select name="assigned_donor_id" required>
                                            <option value="" disabled selected>Select Donor</option>
                                            <?php foreach ($donors as $donor): ?>
                                                <?php if ($donor['blood_group'] == $request['blood_group']): ?>
                                                    <option value="<?php echo $donor['id']; ?>"><?php echo htmlspecialchars($donor['name']); ?> (<?php echo $donor['blood_group']; ?>)</option>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </select>
                                        <textarea name="admin_response" placeholder="Optional message" class="form-input"></textarea>
                                        <button type="submit" name="action" value="accept" class="form-btn">Accept</button>
                                        <button type="submit" name="action" value="reject" class="form-btn reject-btn">Reject</button>
                                    </form>
                                <?php else: ?>
                                    <span>Action Taken</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>