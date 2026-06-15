<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
  <title>Horizon • Dashboard</title>
  <link rel="icon" href="data:,">
  <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<div id="appRoot"></div>

<!-- DETAIL MODAL (shared for article view) -->
<div id="detailModal" class="modal">
  <div class="modal-content detail-modal-content">
    <div class="modal-header">
      <h3 id="detailTitle">Article Details</h3>
      <button class="modal-close-btn" id="detailModalCloseBtn" aria-label="Close">✕</button>
    </div>
    <div class="modal-body" id="detailBody"></div>
  </div>
</div>

<!-- ARTICLE MODAL (create/edit) -->
<div id="articleModal" class="modal">
  <div class="modal-content article-modal">
    <div class="modal-header">
      <h3 id="articleModalTitle">Create New Article</h3>
      <button class="modal-close-btn" id="articleModalCloseBtn" aria-label="Close">✕</button>
    </div>
    <div class="modal-body">
      <form id="articleForm">
        <div class="form-group">
          <label>Title</label>
          <input type="text" id="articleTitle" placeholder="Enter article title" required>
        </div>
        <div class="form-group">
          <label>Category</label>
          <select id="articleCategory">
            <option value="Inspiration">✨ Inspiration</option>
            <option value="Mindfulness">🧘 Mindfulness</option>
            <option value="Productivity">⚡ Productivity</option>
            <option value="Wellness">🌿 Wellness</option>
          </select>
        </div>
        <div class="form-group">
          <label>Excerpt</label>
          <textarea id="articleExcerpt" placeholder="Brief summary..." rows="2"></textarea>
        </div>
        <div class="form-group">
          <label>Full Content</label>
          <textarea id="articleContent" placeholder="Write your article here..." rows="5"></textarea>
        </div>
        <div class="form-group">
          <label>Image URL</label>
          <input type="text" id="articleImage" placeholder="https://picsum.photos/400/200?random">
        </div>
        <div style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 1rem;">
          <button type="button" class="btn-secondary" id="cancelArticleBtn">Cancel</button>
          <button type="submit" class="btn-primary" style="width: auto;">Save Article</button>
        </div>
      </form>
    </div>
  </div>
</div>

<div id="toastNotification" class="toast"></div>

<script src="assets/java.js"></script>
<script>
  async function init() {
    // Wait for the DOM to be fully loaded
    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', initApp);
    } else {
      initApp();
    }
  }
  
  async function initApp() {
    // Check if functions are defined
    if (typeof initEventListeners !== 'undefined') {
      initEventListeners();
    } else {
      console.error('JavaScript functions not loaded');
      return;
    }
    
    await loadArticlesFromAPI();
    const sessionValid = await checkSession();
    if (!sessionValid) {
      currentUser = null;
      sessionStorage.removeItem('horizon_session');
      window.location.href = '../index.php';
      return;
    }
    renderDashboard();
  }
  
  init();
</script>
</body>
</html>