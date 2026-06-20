<?php
require_once 'config/database.php';
require_once 'includes/auth.php';

// If already logged in, go to dashboard
if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error = 'Please enter username and password';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['role'] = $user['role'];
            
            $stmt = $pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
            $stmt->execute([$user['id']]);
            
            // Redirect to dashboard
            header('Location: dashboard.php');
            exit();
        } else {
            $error = 'Invalid username or password';
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ResourceSync - Smart Energy Management</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Inter', sans-serif;
      background: #0f172a;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 20px;
      background-image: 
        radial-gradient(ellipse at 10% 20%, rgba(34, 211, 238, 0.08) 0%, transparent 50%),
        radial-gradient(ellipse at 90% 80%, rgba(16, 185, 129, 0.05) 0%, transparent 50%);
    }

    .login-wrapper {
      width: 100%;
      max-width: 1100px;
      animation: fadeInUp 0.6s ease-out;
    }

    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translateY(30px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .login-container {
      display: grid;
      grid-template-columns: 1fr 1fr;
      background: #1e293b;
      border-radius: 24px;
      overflow: hidden;
      border: 1px solid #334155;
      box-shadow: 0 30px 80px rgba(0, 0, 0, 0.6);
      position: relative;
    }

    .login-container::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 3px;
      background: linear-gradient(90deg, #22d3ee, #10b981, #22d3ee);
      background-size: 200% 100%;
      animation: shimmer 3s ease-in-out infinite;
      z-index: 10;
    }

    @keyframes shimmer {
      0%, 100% { background-position: 0% 50%; }
      50% { background-position: 100% 50%; }
    }

    .login-left {
      background: linear-gradient(135deg, #0f172a 0%, #1a2332 100%);
      padding: 50px 40px;
      display: flex;
      flex-direction: column;
      justify-content: center;
      position: relative;
      overflow: hidden;
    }

    .login-left::after {
      content: '';
      position: absolute;
      top: -50%;
      right: -50%;
      width: 100%;
      height: 100%;
      background: radial-gradient(circle, rgba(34, 211, 238, 0.03) 0%, transparent 70%);
      pointer-events: none;
    }

    .brand {
      position: relative;
      z-index: 1;
      margin-bottom: 40px;
    }

    .brand-icon {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: 70px;
      height: 70px;
      background: rgba(34, 211, 238, 0.1);
      border: 1px solid rgba(34, 211, 238, 0.2);
      border-radius: 18px;
      color: #22d3ee;
      font-size: 32px;
      margin-bottom: 16px;
      transition: all 0.3s ease;
    }

    .brand-icon:hover {
      transform: scale(1.05) rotate(-5deg);
      box-shadow: 0 0 40px rgba(34, 211, 238, 0.2);
    }

    .brand h1 {
      font-size: 32px;
      font-weight: 800;
      color: #e2e8f0;
      letter-spacing: -0.5px;
    }

    .brand h1 span {
      color: #22d3ee;
    }

    .tagline {
      color: #94a3b8;
      font-size: 14px;
      margin-top: 4px;
      font-weight: 400;
    }

    .features-preview {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 12px;
      margin-top: 20px;
      position: relative;
      z-index: 1;
    }

    .feature-item {
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 14px 16px;
      background: rgba(15, 23, 42, 0.6);
      border: 1px solid #334155;
      border-radius: 12px;
      color: #cbd5e1;
      font-size: 13px;
      font-weight: 500;
      transition: all 0.3s ease;
      backdrop-filter: blur(10px);
    }

    .feature-item:hover {
      border-color: #22d3ee;
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(34, 211, 238, 0.1);
    }

    .feature-item i {
      color: #22d3ee;
      font-size: 16px;
      width: 20px;
      text-align: center;
    }

    .status-badge {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      background: rgba(16, 185, 129, 0.1);
      border: 1px solid rgba(16, 185, 129, 0.2);
      color: #34d399;
      padding: 6px 16px;
      border-radius: 20px;
      font-size: 12px;
      font-weight: 500;
      margin-top: 20px;
      width: fit-content;
      position: relative;
      z-index: 1;
    }

    .status-badge .dot {
      width: 6px;
      height: 6px;
      background: #34d399;
      border-radius: 50%;
      animation: pulse 2s ease-in-out infinite;
    }

    @keyframes pulse {
      0%, 100% { opacity: 1; transform: scale(1); }
      50% { opacity: 0.4; transform: scale(0.8); }
    }

    .login-right {
      padding: 50px 45px;
      background: #1e293b;
      display: flex;
      flex-direction: column;
      justify-content: center;
    }

    .login-header {
      margin-bottom: 32px;
    }

    .login-header h2 {
      font-size: 26px;
      font-weight: 700;
      color: #e2e8f0;
      margin-bottom: 4px;
    }

    .login-header p {
      color: #94a3b8;
      font-size: 14px;
    }

    .input-group {
      position: relative;
      margin-bottom: 18px;
    }

    .input-group i {
      position: absolute;
      left: 16px;
      top: 50%;
      transform: translateY(-50%);
      color: #64748b;
      font-size: 16px;
      transition: color 0.3s ease;
    }

    .input-group input {
      width: 100%;
      padding: 14px 16px 14px 48px;
      background: #0f172a;
      border: 2px solid #334155;
      border-radius: 12px;
      font-size: 14px;
      color: #e2e8f0;
      transition: all 0.3s ease;
      font-family: inherit;
    }

    .input-group input::placeholder {
      color: #64748b;
    }

    .input-group input:focus {
      outline: none;
      border-color: #22d3ee;
      box-shadow: 0 0 0 4px rgba(34, 211, 238, 0.1);
    }

    .input-group input:focus + i,
    .input-group input:focus ~ i {
      color: #22d3ee;
    }

    .form-options {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin: 20px 0 24px;
    }

    .checkbox-label {
      display: flex;
      align-items: center;
      gap: 8px;
      color: #94a3b8;
      font-size: 13px;
      cursor: pointer;
    }

    .checkbox-label input[type="checkbox"] {
      width: 16px;
      height: 16px;
      accent-color: #22d3ee;
      cursor: pointer;
    }

    .forgot-link {
      color: #22d3ee;
      font-size: 13px;
      text-decoration: none;
      font-weight: 500;
      transition: color 0.2s;
    }

    .forgot-link:hover {
      color: #06b6d4;
      text-decoration: underline;
    }

    .btn-primary {
      width: 100%;
      padding: 15px;
      background: linear-gradient(135deg, #22d3ee 0%, #06b6d4 100%);
      color: #0f172a;
      border: none;
      border-radius: 12px;
      font-size: 15px;
      font-weight: 700;
      cursor: pointer;
      transition: all 0.3s ease;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
    }

    .btn-primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 30px rgba(34, 211, 238, 0.3);
    }

    .btn-primary:active {
      transform: translateY(0);
    }

    .demo-hint {
      display: flex;
      align-items: center;
      gap: 8px;
      justify-content: center;
      margin-top: 16px;
      padding: 12px;
      background: #0f172a;
      border: 1px solid #334155;
      border-radius: 10px;
      color: #94a3b8;
      font-size: 13px;
    }

    .demo-hint i {
      color: #22d3ee;
    }

    .demo-hint strong {
      color: #e2e8f0;
    }

    .login-footer {
      text-align: center;
      margin-top: 24px;
      color: #94a3b8;
      font-size: 14px;
    }

    .login-footer a {
      color: #22d3ee;
      text-decoration: none;
      font-weight: 600;
      transition: color 0.2s;
    }

    .login-footer a:hover {
      color: #06b6d4;
      text-decoration: underline;
    }

    .error-message {
      background: rgba(239, 68, 68, 0.1);
      border: 1px solid rgba(239, 68, 68, 0.2);
      color: #f87171;
      padding: 12px 16px;
      border-radius: 10px;
      margin-bottom: 16px;
      font-size: 14px;
      display: <?php echo $error ? 'flex' : 'none'; ?>;
      align-items: center;
      gap: 10px;
    }

    .error-message i {
      color: #ef4444;
    }

    .toast {
      position: fixed;
      bottom: 30px;
      right: 30px;
      background: #1e293b;
      border: 1px solid #334155;
      padding: 16px 24px;
      border-radius: 12px;
      color: #e2e8f0;
      font-size: 14px;
      z-index: 9999;
      transform: translateY(100px);
      opacity: 0;
      transition: all 0.4s ease;
      box-shadow: 0 10px 40px rgba(0,0,0,0.5);
      max-width: 400px;
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .toast.show {
      transform: translateY(0);
      opacity: 1;
    }

    .toast.success i { color: #10b981; }
    .toast.error i { color: #ef4444; }
    .toast.info i { color: #22d3ee; }

    .btn-primary.loading {
      pointer-events: none;
      opacity: 0.7;
    }

    .btn-primary .spinner {
      display: none;
      width: 20px;
      height: 20px;
      border: 2px solid rgba(15, 23, 42, 0.3);
      border-top-color: #0f172a;
      border-radius: 50%;
      animation: spin 0.8s linear infinite;
    }

    .btn-primary.loading .spinner {
      display: block;
    }

    .btn-primary.loading .btn-text {
      display: none;
    }

    @keyframes spin {
      to { transform: rotate(360deg); }
    }

    @media (max-width: 968px) {
      .login-container {
        grid-template-columns: 1fr;
        border-radius: 20px;
      }

      .login-left {
        padding: 30px 30px 20px;
        border-bottom: 1px solid #334155;
      }

      .login-right {
        padding: 30px;
      }

      .features-preview {
        grid-template-columns: 1fr 1fr;
        margin-top: 16px;
      }

      .brand {
        margin-bottom: 20px;
      }

      .brand h1 {
        font-size: 28px;
      }
    }

    @media (max-width: 480px) {
      .login-left {
        padding: 24px 20px 16px;
      }

      .login-right {
        padding: 24px 20px;
      }

      .features-preview {
        grid-template-columns: 1fr;
        gap: 8px;
      }

      .feature-item {
        padding: 10px 14px;
        font-size: 12px;
      }

      .login-header h2 {
        font-size: 22px;
      }

      .brand h1 {
        font-size: 24px;
      }

      .brand-icon {
        width: 56px;
        height: 56px;
        font-size: 26px;
      }

      .login-container {
        border-radius: 16px;
      }

      .form-options {
        flex-direction: column;
        gap: 10px;
        align-items: flex-start;
      }
    }
  </style>
</head>
<body>

<div class="login-wrapper">
  <div class="login-container">
    <!-- Left Panel -->
    <div class="login-left">
      <div class="brand">
        <div class="brand-icon">
          <i class="fas fa-bolt"></i>
        </div>
        <h1>Resource<span>Sync</span></h1>
        <p class="tagline">Smart Energy Management Platform</p>
      </div>
      
      <div class="features-preview">
        <div class="feature-item">
          <i class="fas fa-chart-line"></i>
          <span>Real-time Analytics</span>
        </div>
        <div class="feature-item">
          <i class="fas fa-leaf"></i>
          <span>Carbon Footprint</span>
        </div>
        <div class="feature-item">
          <i class="fas fa-bell"></i>
          <span>Smart Alerts</span>
        </div>
        <div class="feature-item">
          <i class="fas fa-users"></i>
          <span>Multi-User Support</span>
        </div>
      </div>

      <div class="status-badge">
        <span class="dot"></span>
        System Online • v2.4.0
      </div>
    </div>

    <!-- Right Panel -->
    <div class="login-right">
      <div class="login-header">
        <h2>Welcome Back</h2>
        <p>Sign in to track your energy usage</p>
      </div>

      <?php if ($error): ?>
        <div class="error-message" style="display:flex;">
          <i class="fas fa-exclamation-circle"></i>
          <span><?php echo htmlspecialchars($error); ?></span>
        </div>
      <?php endif; ?>

      <form method="POST" action="login.php" id="loginForm">
        <div class="input-group">
          <i class="fas fa-user"></i>
          <input type="text" id="username" name="username" placeholder="Username" required>
        </div>

        <div class="input-group">
          <i class="fas fa-lock"></i>
          <input type="password" id="password" name="password" placeholder="Password" required>
        </div>

        <div class="form-options">
          <label class="checkbox-label">
            <input type="checkbox" name="remember" checked>
            <span>Remember me</span>
          </label>
          <a href="#" class="forgot-link">Forgot password?</a>
        </div>

        <button type="submit" class="btn-primary" id="loginBtn">
          <span class="spinner"></span>
          <span class="btn-text">
            <i class="fas fa-sign-in-alt"></i>
            Sign In
          </span>
        </button>

        <div class="demo-hint">
          <i class="fas fa-info-circle"></i>
          Demo: <strong>admin</strong> / <strong>admin123</strong>
        </div>
      </form>

      <div class="login-footer">
        <p>Don't have an account? <a href="register.php">Sign up</a></p>
      </div>
    </div>
  </div>
</div>

<!-- Toast Notification -->
<div id="toast" class="toast">
  <i id="toastIcon" class="fas fa-check-circle"></i>
  <span id="toastMessage">Success</span>
</div>

<script>
  // Show toast notification
  function showToast(message, type = 'success') {
    const toast = document.getElementById('toast');
    const toastMessage = document.getElementById('toastMessage');
    const toastIcon = document.getElementById('toastIcon');
    
    toast.className = `toast ${type}`;
    toastMessage.textContent = message;
    
    const icons = {
      success: 'fa-check-circle',
      error: 'fa-exclamation-circle',
      info: 'fa-info-circle'
    };
    
    toastIcon.className = `fas ${icons[type] || icons.info}`;
    
    toast.classList.add('show');
    
    setTimeout(() => {
      toast.classList.remove('show');
    }, 4000);
  }

  // Handle form submission with loading state
  document.getElementById('loginForm').addEventListener('submit', function(e) {
    const loginBtn = document.getElementById('loginBtn');
    loginBtn.classList.add('loading');
  });

  // Handle Forgot password link
  document.querySelector('.forgot-link')?.addEventListener('click', function(e) {
    e.preventDefault();
    showToast('Password reset feature coming soon!', 'info');
  });

  // Auto-dismiss error on typing
  document.querySelectorAll('#loginForm input').forEach(input => {
    input.addEventListener('input', () => {
      const errorMsg = document.querySelector('.error-message');
      if (errorMsg) {
        errorMsg.style.display = 'none';
      }
    });
  });
</script>

</body>
</html>