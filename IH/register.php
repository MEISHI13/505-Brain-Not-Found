<?php
require_once 'config/database.php';
require_once 'includes/auth.php';

if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    
    if (empty($username) || empty($password) || empty($full_name)) {
        $error = 'Please fill in all required fields';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters';
    } else {
        // Check if username exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            $error = 'Username already taken';
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, password, full_name, email) VALUES (?, ?, ?, ?)");
            if ($stmt->execute([$username, $hashed_password, $full_name, $email])) {
                $success = 'Registration successful! You can now login.';
            } else {
                $error = 'Registration failed. Please try again.';
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
    <title>Register - EcoStream</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background: #0f172a;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            background-image: 
                radial-gradient(ellipse at 10% 20%, rgba(34, 211, 238, 0.05) 0%, transparent 50%),
                radial-gradient(ellipse at 90% 80%, rgba(16, 185, 129, 0.05) 0%, transparent 50%);
        }
        .register-container {
            background: #1e293b;
            border: 1px solid #334155;
            border-radius: 20px;
            padding: 48px;
            max-width: 440px;
            width: 100%;
            box-shadow: 0 25px 80px rgba(0,0,0,0.6);
            position: relative;
            overflow: hidden;
        }
        .register-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #22d3ee, #10b981, #22d3ee);
            background-size: 200% 100%;
            animation: shimmer 3s ease-in-out infinite;
        }
        @keyframes shimmer {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }
        .register-header {
            text-align: center;
            margin-bottom: 36px;
        }
        .register-header .logo {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 72px;
            height: 72px;
            background: rgba(34, 211, 238, 0.1);
            border: 1px solid rgba(34, 211, 238, 0.2);
            border-radius: 18px;
            margin-bottom: 16px;
            color: #22d3ee;
            font-size: 36px;
            transition: all 0.3s ease;
        }
        .register-header h1 {
            font-size: 28px;
            font-weight: 800;
            color: #e2e8f0;
            letter-spacing: -0.5px;
        }
        .register-header h1 span { color: #22d3ee; }
        .register-header p { color: #94a3b8; margin-top: 6px; font-size: 14px; }
        .form-group { margin-bottom: 20px; }
        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 6px;
            color: #cbd5e1;
            font-size: 13px;
            letter-spacing: 0.3px;
        }
        .form-group label i { margin-right: 6px; color: #22d3ee; font-size: 14px; }
        .form-group input {
            width: 100%;
            padding: 12px 16px;
            background: #0f172a;
            border: 2px solid #334155;
            border-radius: 10px;
            font-size: 14px;
            color: #e2e8f0;
            transition: all 0.3s ease;
            font-family: inherit;
        }
        .form-group input::placeholder { color: #64748b; }
        .form-group input:focus {
            outline: none;
            border-color: #22d3ee;
            box-shadow: 0 0 0 4px rgba(34, 211, 238, 0.1);
        }
        .btn-register {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #22d3ee 0%, #06b6d4 100%);
            color: #0f172a;
            border: none;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-top: 8px;
        }
        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(34, 211, 238, 0.3);
        }
        .error {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.2);
            color: #f87171;
            padding: 12px 16px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .success {
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.2);
            color: #34d399;
            padding: 12px 16px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .login-link {
            text-align: center;
            margin-top: 20px;
            color: #94a3b8;
            font-size: 14px;
        }
        .login-link a { color: #22d3ee; text-decoration: none; font-weight: 600; transition: color 0.2s; }
        .login-link a:hover { color: #06b6d4; text-decoration: underline; }
        .register-footer {
            text-align: center;
            margin-top: 24px;
            font-size: 12px;
            color: #475569;
        }
        .register-footer i { color: #22d3ee; font-size: 12px; }
        @media (max-width: 480px) {
            .register-container { padding: 32px 24px; }
            .register-header h1 { font-size: 24px; }
            .register-header .logo { width: 60px; height: 60px; font-size: 28px; }
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-header">
            <div class="logo"><i class="fas fa-user-plus"></i></div>
            <h1>Eco<span>Stream</span></h1>
            <p>Create your account</p>
        </div>
        
        <?php if ($error): ?>
            <div class="error"><i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="success"><i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label for="full_name"><i class="fas fa-user"></i> Full Name</label>
                <input type="text" id="full_name" name="full_name" placeholder="Enter your full name" required>
            </div>
            <div class="form-group">
                <label for="username"><i class="fas fa-user-circle"></i> Username</label>
                <input type="text" id="username" name="username" placeholder="Choose a username" required>
            </div>
            <div class="form-group">
                <label for="email"><i class="fas fa-envelope"></i> Email (optional)</label>
                <input type="email" id="email" name="email" placeholder="Enter your email">
            </div>
            <div class="form-group">
                <label for="password"><i class="fas fa-lock"></i> Password</label>
                <input type="password" id="password" name="password" placeholder="Min 6 characters" required>
            </div>
            <button type="submit" class="btn-register">
                <i class="fas fa-user-plus"></i> Create Account
            </button>
        </form>
        
        <div class="login-link">
            Already have an account? <a href="login.php">Login here</a>
        </div>
        
        <div class="register-footer">
            <i class="fas fa-shield-alt"></i> Secure Registration • Taylor's Agents of Tech © 2026
        </div>
    </div>
</body>
</html>