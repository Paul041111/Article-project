<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
  <title>Horizon • Sign In</title>
  <link rel="icon" href="data:,">
  <link rel="stylesheet" href="public/assets/style.css">
</head>
<body>
<div class="auth-container">
  <div class="auth-card">
    <div class="auth-header">
      <div class="logo">⟡ horizon</div>
      <h2>Welcome back</h2>
      <p style="font-size: 0.85rem; opacity: 0.8; margin-top: 0.25rem;">Sign in to your account</p>
    </div>
    <div class="auth-body">
      <form id="signinForm">
        <div class="input-group">
          <label>Email address</label>
          <input type="email" id="signinEmail" placeholder="hello@horizon.com" autocomplete="email">
          <span class="error-msg" id="signinEmailError"></span>
        </div>
        <div class="input-group">
          <label>Password</label>
          <input type="password" id="signinPassword" placeholder="••••••••" autocomplete="current-password">
          <span class="error-msg" id="signinPasswordError"></span>
        </div>
        <button type="submit" class="btn-primary">Sign In →</button>
        <div class="demo-note">
          <p>✨ Demo accounts:</p>
          <p><strong>demo@horizon.com</strong> / password123 (Sarah Chen)</p>
          <p><strong>alice@example.com</strong> / password123 (Alice Montgomery)</p>
          <p><strong>bob@example.com</strong> / password123 (Robert Johnson)</p>
        </div>
      </form>
      <div class="auth-footer">
        <p>Don't have an account? <a href="signup.php" class="link-btn">Create account</a></p>
      </div>
    </div>
  </div>
</div>
<div id="toastNotification" class="toast"></div>
<script src="public/assets/java.js"></script>
<script>
  // Check if already logged in
  (async function() {
    sessionStorage.clear();
    if (typeof checkSession !== 'undefined') {
      const sessionValid = await checkSession();
      if (sessionValid) {
        console.log(checkSession())
        // window.location.href = 'public/dashboard.php';
      }
    } else {
      console.error('JavaScript file not loaded properly');
    }
  })();

  document.getElementById('signinForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const email = document.getElementById('signinEmail').value.trim();
    const password = document.getElementById('signinPassword').value;
    const emailError = document.getElementById('signinEmailError');
    const passError = document.getElementById('signinPasswordError');
    
    emailError.innerText = '';
    passError.innerText = '';
    
    if (!email) { emailError.innerText = 'Email required'; return; }
    if (!password) { passError.innerText = 'Password required'; return; }
    
    if (typeof handleLoginRedirect !== 'undefined') {
      const success = await handleLoginRedirect(email, password);
      if (success) {
        window.location.href = 'public/dashboard.php';
      }
    } else {
      alert('JavaScript file not loaded. Please check the console.');
    }
  });
</script>
</body>
</html>