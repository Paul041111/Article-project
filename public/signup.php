<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
  <title>Horizon • Sign Up</title>
  <link rel="icon" href="data:,">
  <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<div class="auth-container">
  <div class="auth-card">
    <div class="auth-header">
      <div class="logo">⟡ horizon</div>
      <h2>Create account</h2>
      <p style="font-size: 0.85rem; opacity: 0.8; margin-top: 0.25rem;">Join the Horizon community</p>
    </div>
    <div class="auth-body">
      <form id="signupForm">
        <div class="input-group">
          <label>Full name (will be shown on your articles)</label>
          <input type="text" id="signupName" placeholder="e.g., Alexandra Morgan" autocomplete="name">
          <span class="error-msg" id="signupNameError"></span>
        </div>
        <div class="input-group">
          <label>Email address</label>
          <input type="email" id="signupEmail" placeholder="alex@horizon.com" autocomplete="email">
          <span class="error-msg" id="signupEmailError"></span>
        </div>
        <div class="input-group">
          <label>Password</label>
          <input type="password" id="signupPassword" placeholder="Create a password (min 6 chars)">
          <span class="error-msg" id="signupPasswordError"></span>
        </div>
        <div class="input-group">
          <label>Confirm password</label>
          <input type="password" id="signupConfirm" placeholder="Confirm your password">
          <span class="error-msg" id="signupConfirmError"></span>
        </div>
        <button type="submit" class="btn-primary">Register →</button>
        <p class="demo-note" style="margin-top: 1rem;">Your full name will appear on every article you create.</p>
      </form>
      <div class="auth-footer">
        <p>Already have an account? <a href="../index.php" class="link-btn">Sign in</a></p>
      </div>
    </div>
  </div>
</div>
<div id="toastNotification" class="toast"></div>
<script src="assets/java.js"></script>
<script>
  document.getElementById('signupForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const name = document.getElementById('signupName').value.trim();
    const email = document.getElementById('signupEmail').value.trim();
    const password = document.getElementById('signupPassword').value;
    const confirm = document.getElementById('signupConfirm').value;
    
    document.getElementById('signupNameError').innerText = '';
    document.getElementById('signupEmailError').innerText = '';
    document.getElementById('signupPasswordError').innerText = '';
    document.getElementById('signupConfirmError').innerText = '';
    
    if (!name) { document.getElementById('signupNameError').innerText = 'Full name required'; return; }
    if (!email) { document.getElementById('signupEmailError').innerText = 'Email required'; return; }
    if (!password) { document.getElementById('signupPasswordError').innerText = 'Password required'; return; }
    if (password !== confirm) { document.getElementById('signupConfirmError').innerText = 'Passwords do not match'; return; }
    if (password.length < 6) { document.getElementById('signupPasswordError').innerText = 'Password must be at least 6 characters'; return; }
    
    if (typeof handleSignupRedirect !== 'undefined') {
      const success = await handleSignupRedirect(name, email, password);
      if (success) {
        window.location.href = 'index.php';
      }
    } else {
      alert('JavaScript file not loaded. Please check the console.');
    }
  });
</script>
</body>
</html>