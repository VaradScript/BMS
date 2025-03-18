<?php
include 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'blood_seeker') {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $blood_seeker_id = $_SESSION['user_id'];
    $blood_group = $_POST['blood_group'];
    $units_needed = $_POST['units_needed'];

    $stmt = $conn->prepare("INSERT INTO blood_requests (blood_seeker_id, blood_group, units_needed) VALUES (:blood_seeker_id, :blood_group, :units_needed)");
    $stmt->bindParam(':blood_seeker_id', $blood_seeker_id);
    $stmt->bindParam(':blood_group', $blood_group);
    $stmt->bindParam(':units_needed', $units_needed);

    if ($stmt->execute()) {
        header("Location: waiting.php");
        exit();
    } else {
        $error = "Error submitting request.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request Blood</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Request Blood</h1>
            <a href="logout.php" class="btn logout">Logout</a>
        </header>
        <div class="section">
            <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
            <form method="POST" action="">
                <select name="blood_group" class="form-input" required>
                    <option value="" disabled selected>Select Blood Group</option>
                    <option value="A+">A+</option>
                    <option value="A-">A-</option>
                    <option value="B+">B+</option>
                    <option value="B-">B-</option>
                    <option value="AB+">AB+</option>
                    <option value="AB-">AB-</option>
                    <option value="O+">O+</option>
                    <option value="O-">O-</option>
                </select>
                <input type="number" name="units_needed" class="form-input" placeholder="Units Needed" required>
                <button type="submit" class="form-btn">Submit Request</button>
            </form>
        </div>
    </div>
</body>
</html>