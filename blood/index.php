<?php
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];
    $blood_group = $_POST['blood_group'];
    $phone = $_POST['phone'];
    $location = $_POST['location'];

    $stmt = $conn->prepare("INSERT INTO users (name, email, password, role, blood_group, phone, location) VALUES (:name, :email, :password, :role, :blood_group, :phone, :location)");
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', $password);
    $stmt->bindParam(':role', $role);
    $stmt->bindParam(':blood_group', $blood_group);
    $stmt->bindParam(':phone', $phone);
    $stmt->bindParam(':location', $location);

    if ($stmt->execute()) {
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['role'] = $user['role'];

        if ($role == 'donor') {
            header("Location: donor_guidance.php");
        } else {
            header("Location: request_blood.php");
        }
        exit();
    } else {
        $error = "Error registering. Email may already be in use.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blood Management System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Blood Management System</h1>
            <p>Helping Save Lives, One Donation at a Time</p>
        </header>
        <div class="section">
            <h2>Register</h2>
            <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
            <form method="POST" action="">
                <input type="text" name="name" class="form-input" placeholder="Full Name" required>
                <input type="email" name="email" class="form-input" placeholder="Email" required>
                <input type="password" name="password" class="form-input" placeholder="Password" required>
                <select name="role" class="form-input" required>
                    <option value="" disabled selected>Select Role</option>
                    <option value="donor">Donor</option>
                    <option value="blood_seeker">Blood Seeker</option>
                </select>
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
                <input type="tel" name="phone" class="form-input" placeholder="Phone Number" required>
                <input type="text" name="location" class="form-input" placeholder="Location" required>
                <button type="submit" class="form-btn">Register</button>
            </form>
            <div class="additional-links">
                <a href="login.php" class="btn">Already Registered? Login</a>
                <a href="admin_login.php" class="btn admin-btn">Admin Login</a>
            </div>
        </div>
    </div>
</body>
</html>