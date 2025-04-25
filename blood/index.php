<?php
session_start();
include 'config.php';

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize inputs
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $raw_password = $_POST['password'];
    $role = filter_input(INPUT_POST, 'role', FILTER_SANITIZE_STRING);
    $blood_group = filter_input(INPUT_POST, 'blood_group', FILTER_SANITIZE_STRING);
    $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
    $location = filter_input(INPUT_POST, 'location', FILTER_SANITIZE_STRING);

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format";
    } 
    // Validate password strength
    elseif (strlen($raw_password) < 8) {
        $error = "Password must be at least 8 characters long";
    } else {
        $password = password_hash($raw_password, PASSWORD_DEFAULT);

        try {
            $conn->beginTransaction();
            
            $stmt = $conn->prepare("INSERT INTO users (name, email, password, role, blood_group, phone, location) 
                                   VALUES (:name, :email, :password, :role, :blood_group, :phone, :location)");
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

                $conn->commit();
                
                if ($role == 'donor') {
                    header("Location: donor_guidance.php");
                } else {
                    header("Location: request_blood.php");
                }
                exit();
            }
        } catch (PDOException $e) {
            $conn->rollBack();
            if ($e->getCode() == 23000) { // Duplicate entry
                $error = "Email already in use. Please use a different email.";
            } else {
                $error = "Registration failed. Please try again later.";
                // Log the error for admin: error_log($e->getMessage());
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | Blood Management System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #e63946;
            --secondary: #f1faee;
            --dark: #1d3557;
            --light: #a8dadc;
            --white: #ffffff;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }
        
        body {
            background-color: #f9f9f9;
            color: #333;
            line-height: 1.6;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        header {
            text-align: center;
            margin-bottom: 30px;
            padding: 20px 0;
            border-bottom: 1px solid #eee;
        }
        
        header h1 {
            color: var(--primary);
            font-size: 2.5rem;
            margin-bottom: 10px;
        }
        
        header p {
            color: var(--dark);
            font-size: 1.1rem;
        }
        
        .register-section {
            max-width: 600px;
            margin: 0 auto;
            background: var(--white);
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .register-section h2 {
            text-align: center;
            color: var(--dark);
            margin-bottom: 20px;
            font-size: 1.8rem;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: var(--dark);
            font-weight: 500;
        }
        
        .form-input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
            transition: border 0.3s;
        }
        
        .form-input:focus {
            border-color: var(--primary);
            outline: none;
            box-shadow: 0 0 0 2px rgba(230, 57, 70, 0.2);
        }
        
        select.form-input {
            appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 10px center;
            background-size: 1em;
        }
        
        .password-container {
            position: relative;
        }
        
        .password-toggle {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #777;
        }
        
        .btn {
            display: inline-block;
            background: var(--primary);
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 500;
            text-align: center;
            transition: all 0.3s;
            width: 100%;
        }
        
        .btn:hover {
            background: #c1121f;
            transform: translateY(-2px);
        }
        
        .btn-outline {
            background: transparent;
            border: 1px solid var(--primary);
            color: var(--primary);
        }
        
        .btn-outline:hover {
            background: var(--primary);
            color: white;
        }
        
        .btn-admin {
            background: var(--dark);
        }
        
        .btn-admin:hover {
            background: #14213d;
        }
        
        .additional-links {
            margin-top: 20px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        
        .alert {
            padding: 12px 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        
        .alert-error {
            background-color: #ffebee;
            color: #c62828;
            border: 1px solid #ef9a9a;
        }
        
        .alert-success {
            background-color: #e8f5e9;
            color: #2e7d32;
            border: 1px solid #a5d6a7;
        }
        
        .password-strength {
            margin-top: 5px;
            height: 5px;
            background: #eee;
            border-radius: 5px;
            overflow: hidden;
        }
        
        .strength-meter {
            height: 100%;
            width: 0;
            transition: width 0.3s, background 0.3s;
        }
        
        @media (max-width: 768px) {
            .register-section {
                padding: 20px;
            }
            
            header h1 {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1><i class="fas fa-heartbeat" style="margin-right: 10px;"></i>Blood Management System</h1>
            <p>Join our community to save lives through blood donation</p>
        </header>
        
        <div class="register-section">
            <h2>Create Your Account</h2>
            
            <?php if ($error): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="" id="registrationForm">
                <div class="form-group">
                    <label for="name">Full Name</label>
                    <input type="text" id="name" name="name" class="form-input" placeholder="John Doe" required
                           value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
                </div>
                
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" class="form-input" placeholder="john@example.com" required
                           value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="password-container">
                        <input type="password" id="password" name="password" class="form-input" placeholder="At least 8 characters" required>
                        <span class="password-toggle" id="togglePassword">
                            <i class="far fa-eye"></i>
                        </span>
                    </div>
                    <div class="password-strength">
                        <div class="strength-meter" id="strengthMeter"></div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="role">I want to:</label>
                    <select id="role" name="role" class="form-input" required>
                        <option value="" disabled selected>Select your role</option>
                        <option value="donor" <?php echo (isset($_POST['role']) && $_POST['role'] == 'donor') ? 'selected' : ''; ?>>Donate Blood</option>
                        <option value="blood_seeker" <?php echo (isset($_POST['role']) && $_POST['role'] == 'blood_seeker') ? 'selected' : ''; ?>>Request Blood</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="blood_group">Blood Group</label>
                    <select id="blood_group" name="blood_group" class="form-input" required>
                        <option value="" disabled selected>Select your blood group</option>
                        <option value="A+" <?php echo (isset($_POST['blood_group']) && $_POST['blood_group'] == 'A+') ? 'selected' : ''; ?>>A+</option>
                        <option value="A-" <?php echo (isset($_POST['blood_group']) && $_POST['blood_group'] == 'A-') ? 'selected' : ''; ?>>A-</option>
                        <option value="B+" <?php echo (isset($_POST['blood_group']) && $_POST['blood_group'] == 'B+') ? 'selected' : ''; ?>>B+</option>
                        <option value="B-" <?php echo (isset($_POST['blood_group']) && $_POST['blood_group'] == 'B-') ? 'selected' : ''; ?>>B-</option>
                        <option value="AB+" <?php echo (isset($_POST['blood_group']) && $_POST['blood_group'] == 'AB+') ? 'selected' : ''; ?>>AB+</option>
                        <option value="AB-" <?php echo (isset($_POST['blood_group']) && $_POST['blood_group'] == 'AB-') ? 'selected' : ''; ?>>AB-</option>
                        <option value="O+" <?php echo (isset($_POST['blood_group']) && $_POST['blood_group'] == 'O+') ? 'selected' : ''; ?>>O+</option>
                        <option value="O-" <?php echo (isset($_POST['blood_group']) && $_POST['blood_group'] == 'O-') ? 'selected' : ''; ?>>O-</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="tel" id="phone" name="phone" class="form-input" placeholder="+1234567890" required
                           value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>">
                </div>
                
                <div class="form-group">
                    <label for="location">Location</label>
                    <input type="text" id="location" name="location" class="form-input" placeholder="City, Country" required
                           value="<?php echo isset($_POST['location']) ? htmlspecialchars($_POST['location']) : ''; ?>">
                </div>
                
                <button type="submit" class="btn">Register Now</button>
            </form>
            
            <div class="additional-links">
                <p style="text-align: center; margin: 15px 0;">Already have an account?</p>
                <a href="login.php" class="btn btn-outline"><i class="fas fa-sign-in-alt"></i> Login to Your Account</a>
                <a href="admin_login.php" class="btn btn-admin"><i class="fas fa-lock"></i> Admin Login</a>
            </div>
        </div>
    </div>

    <script>
        // Password toggle visibility
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#password');
        
        togglePassword.addEventListener('click', function() {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            this.innerHTML = type === 'password' ? '<i class="far fa-eye"></i>' : '<i class="far fa-eye-slash"></i>';
        });
        
        // Password strength meter
        password.addEventListener('input', function() {
            const strengthMeter = document.getElementById('strengthMeter');
            const strength = calculatePasswordStrength(this.value);
            
            strengthMeter.style.width = strength.percentage + '%';
            strengthMeter.style.backgroundColor = strength.color;
        });
        
        function calculatePasswordStrength(password) {
            let strength = 0;
            
            // Length check
            if (password.length > 7) strength += 1;
            if (password.length > 11) strength += 1;
            
            // Character variety
            if (/[A-Z]/.test(password)) strength += 1;
            if (/[0-9]/.test(password)) strength += 1;
            if (/[^A-Za-z0-9]/.test(password)) strength += 1;
            
            // Calculate percentage and color
            let percentage = (strength / 5) * 100;
            let color = '#e63946'; // red
            
            if (strength > 3) {
                color = '#4caf50'; // green
            } else if (strength > 1) {
                color = '#ff9800'; // orange
            }
            
            return { percentage, color };
        }
        
        // Form validation
        document.getElementById('registrationForm').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            
            if (password.length < 8) {
                e.preventDefault();
                alert('Password must be at least 8 characters long');
                return false;
            }
            
            return true;
        });
    </script>
</body>
</html>
