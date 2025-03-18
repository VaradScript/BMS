<?php
include 'config.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'donor') {
    header("Location: index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = $_POST['full_name'];
    $blood_group = $_POST['blood_group'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $last_donation = $_POST['last_donation'] ?: NULL;
    $user_id = $_SESSION['user_id'];

    $sql = "INSERT INTO donors (user_id, full_name, blood_group, email, phone, last_donation) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isssss", $user_id, $full_name, $blood_group, $email, $phone, $last_donation);
    if ($stmt->execute()) {
        $email_sent = sendEmail($email, "Thank You for Registering", "Dear $full_name, thank you for registering as a {$blood_group} donor!");
        if ($email_sent) {
            echo "<p style='color: green;'>Donor registered successfully! Check your email.</p>";
        } else {
            echo "<p style='color: orange;'>Donor registered, but email sending failed.</p>";
        }
    } else {
        echo "<p style='color: red;'>Error: " . $conn->error . "</p>";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register Donor | VaradScript</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Donor Registration</h1>
        <section class="card">
            <form method="POST" action="">
                <input type="text" name="full_name" placeholder="Full Name" required>
                <select name="blood_group" required>
                    <option value="">Select Blood Group</option>
                    <option value="A+">A+</option>
                    <option value="A-">A-</option>
                    <option value="B+">B+</option>
                    <option value="B-">B-</option>
                    <option value="AB+">AB+</option>
                    <option value="AB-">AB-</option>
                    <option value="O+">O+</option>
                    <option value="O-">O-</option>
                </select>
                <input type="email" name="email" placeholder="Email" required>
                <input type="tel" name="phone" placeholder="Phone" required>
                <input type="date" name="last_donation" placeholder="Last Donation Date">
                <button type="submit">Register</button>
            </form>
            <p><a href="logout.php">Logout</a></p>
        </section>
    </div>
</body>
</html>