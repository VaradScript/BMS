<?php
include 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'donor') {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donor Guidance</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h1>
            <p>Steps to Become a Blood Donor</p>
        </header>
        <div class="section">
            <h2>Basic Steps to Donate Blood</h2>
            <ul class="guidance-list">
                <li><strong>Eligibility Check</strong>: Ensure you are healthy, aged 18-65, and weigh at least 50 kg.</li>
                <li><strong>Hydrate and Eat</strong>: Drink plenty of water and have a good meal before donating.</li>
                <li><strong>Wait for Contact</strong>: An admin will contact you if a Blood Seeker needs your blood type.</li>
                <li><strong>Visit a Donation Center</strong>: Follow the admin’s instructions to donate at a nearby center.</li>
                <li><strong>Rest and Recover</strong>: Rest for a few minutes after donation and avoid heavy activities.</li>
            </ul>
            <a href="logout.php" class="btn">Exit</a>
        </div>
    </div>
</body>
</html>