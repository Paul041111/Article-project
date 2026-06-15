// ========== JAVASCRIPT with PHP API Integration ==========
// IMPORTANT: Change this to match your folder name!
const API_URL = 'http://localhost/api/api.php'


let currentUser = null;
let articles = [];
let editingArticleId = null;

function showToast(message, isError = false) {
  const toast = document.getElementById('toastNotification');
  if (!toast) return;
  toast.textContent = message;
  toast.style.background = isError ? '#ac4e2e' : '#1f2b1c';
  toast.classList.add('show');
  setTimeout(() => toast.classList.remove('show'), 2800);
}

// API Calls
async function loadArticlesFromAPI() {
  try {
    const response = await fetch(`${API_URL}?route=articles`);
    const data = await response.json();
    if (data.success) {
      articles = data.articles;
      return true;
    }
    return false;
  } catch (error) {
    console.error('Error loading articles:', error);
    return false;
  }
}

async function checkSession() {
  try {
    const response = await fetch(`${API_URL}?route=session`);
    const data = await response.json();
    if (data.success && data.user) {
      currentUser = data.user;
      sessionStorage.setItem('horizon_session', JSON.stringify(currentUser));
      return true;
    }
    return false;
  } catch (error) {
    return false;
  }
}

async function handleLoginRedirect(email, password) {
  try {
    const response = await fetch(`${API_URL}?route=login`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ email, password })
    });
    const data = await response.json();
    if (data.success) {
      currentUser = data.user;
      sessionStorage.setItem('horizon_session', JSON.stringify(currentUser));
      showToast(`Welcome back, ${currentUser.fullName || currentUser.email}!`);
      return true;
    } else {
      showToast(data.error || 'Invalid credentials', true);
      return false;
    }
  } catch (error) {
    showToast('Connection error. Make sure PHP server is running.', true);
    return false;
  }
}

async function handleSignupRedirect(name, email, password) {
  try {
    const response = await fetch(`${API_URL}?route=signup`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ name, email, password })
    });
    const data = await response.json();
    if (data.success) {
      showToast('Account created! Please sign in.');
      return true;
    } else {
      showToast(data.error, true);
      return false;
    }
  } catch (error) {
    showToast('Connection error', true);
    return false;
  }
}

async function saveArticleToAPI(articleData) {
  const response = await fetch(`${API_URL}?route=articles`, {
    method: "POST",
    headers: {
      "Content-Type": "application/json"
    },
    body: JSON.stringify(articleData)
  });

  const data = await response.json();
  console.log("CREATE RESPONSE:", data);

  return data.success;
}

async function deleteArticle(id) {
  const res = await fetch(`${API_URL}?route=articles&id=${id}`, {
    method: "DELETE"
  });

  const data = await res.json();

  if (data.success) {
    showToast("Deleted successfully");
    await loadArticlesFromAPI();
    renderDashboard();
  } else {
    showToast(data.error || "Delete failed", true);
  }
}

// Helper functions
function escapeHtml(str) {
  if (!str) return '';
  return str.replace(/[&<>]/g, function(m) {
    if (m === '&') return '&amp;';
    if (m === '<') return '&lt;';
    if (m === '>') return '&gt;';
    return m;
  });
}

function getAvatarInitial(user) {
  if (!user) return '?';
  if (user.fullName && user.fullName.length > 0) return user.fullName.charAt(0).toUpperCase();
  if (user.email) return user.email.charAt(0).toUpperCase();
  return '?';
}

function getDisplayFullName(user) {
  if (!user) return 'Guest';
  if (user.fullName && user.fullName.trim().length > 0) return user.fullName.trim();
  if (user.email) return user.email.split('@')[0];
  return 'User';
}

function renderDashboard() {
  const root = document.getElementById('appRoot');
  const fullName = getDisplayFullName(currentUser);
  const avatarInitial = getAvatarInitial(currentUser);

  root.innerHTML = `
    <div class="page-container">
      <div class="app-header">
        <div class="logo-area">
          <div class="logo-large">⟡ horizon</div>
          <div class="user-info">
            <div class="user-avatar">${escapeHtml(avatarInitial)}</div>
            <span class="user-email">${escapeHtml(fullName)}</span>
          </div>
        </div>
        <div class="header-actions">
          <button class="btn-create" id="createArticleBtn">✍️ Create New Article</button>
          <button class="btn-signout" id="signoutBtn">Sign Out</button>
        </div>
      </div>
      <div class="main-content">
        <h2 style="margin-bottom: 0.8rem; font-size: 1.4rem; color: #1e2a1c;">All Articles</h2>
        <p style="margin-bottom: 1rem; font-size: 0.8rem; color: #8f9b89;">🔧 You can edit or delete only your own articles. Click on any article to view details.</p>
        <div id="articlesGrid" class="articles-grid"></div>
      </div>
    </div>
  `;

  renderAllArticles();

  document.getElementById('createArticleBtn')?.addEventListener('click', () => openArticleModal());
  document.getElementById('signoutBtn')?.addEventListener('click', () => handleSignOut());
}

function renderAllArticles() {
  const container = document.getElementById('articlesGrid');
  if (!container) return;

  if (articles.length === 0) {
    container.innerHTML = `<div class="empty-state">✨ No articles yet. Create the first article!</div>`;
    return;
  }
  
  container.innerHTML = articles.map(article => {
    const isOwner = currentUser && article.author_Email === currentUser.email;
    return `
    <div class="article-card" data-id="${article.id}">
      <div class="article-image" style="background-image: url('${article.image || 'https://picsum.photos/id/100/400/200'}');">
        <span class="article-category">${escapeHtml(article.category)}</span>
      </div>
      <div class="article-content">
        <h3 class="article-title">${escapeHtml(article.title)}</h3>
        <p class="article-excerpt">${escapeHtml(article.excerpt)}</p>
        <div class="article-meta">
          <div class="article-author">
            ✍️ Created by: <strong>${escapeHtml(article.authorName)}</strong>
            ${isOwner ? '<span class="owner-badge">Your article</span>' : ''}
          </div>
          <span>📅 ${new Date(article.createdAt).toLocaleDateString()}</span>
        </div>
      </div>
    </div>
  `}).join('');

  attachArticleCardListeners();
}

function attachArticleCardListeners() {
  document.querySelectorAll('.article-card').forEach(card => {
    card.addEventListener('click', () => {
      const id = card.dataset.id;
      const article = articles.find(a => a.id === id);
      if (article) openDetailModal(article);
    });
  });
}

function openArticleModal(article = null) {
  if (!currentUser) {
    showToast('Please sign in to create or edit articles', true);
    window.location.href = 'index.php';
    return;
  }
  const modal = document.getElementById('articleModal');
  const titleInput = document.getElementById('articleTitle');
  const categorySelect = document.getElementById('articleCategory');
  const excerptInput = document.getElementById('articleExcerpt');
  const contentInput = document.getElementById('articleContent');
  const imageInput = document.getElementById('articleImage');
  const modalTitle = document.getElementById('articleModalTitle');

  if (article) {
    editingArticleId = article.id;
    modalTitle.innerText = 'Edit Article';
    titleInput.value = article.title;
    categorySelect.value = article.category;
    excerptInput.value = article.excerpt;
    contentInput.value = article.content;
    imageInput.value = article.image || '';
  } else {
    editingArticleId = null;
    modalTitle.innerText = 'Create New Article';
    titleInput.value = '';
    categorySelect.value = 'Inspiration';
    excerptInput.value = '';
    contentInput.value = '';
    imageInput.value = '';
  }
  modal.style.display = 'flex';
}

function closeArticleModal() {
  document.getElementById('articleModal').style.display = 'none';
  editingArticleId = null;
}

async function saveArticleFromForm() {
  if (!currentUser) return;

  const title = document.getElementById('articleTitle').value.trim();
  const category = document.getElementById('articleCategory').value;
  const excerpt = document.getElementById('articleExcerpt').value.trim();
  const content = document.getElementById('articleContent').value.trim();
  let image = document.getElementById('articleImage').value.trim();

  if (!title) { showToast('Please enter a title', true); return; }
  if (!excerpt) { showToast('Please enter an excerpt', true); return; }
  if (!content) { showToast('Please enter content', true); return; }
  if (!image) image = 'https://picsum.photos/id/' + Math.floor(Math.random() * 100) + '/400/200';

  const articleData = { title, category, excerpt, content, image };

  const success = await saveArticleToAPI(articleData, !!editingArticleId, editingArticleId);
  if (success) {
    await loadArticlesFromAPI();
    renderDashboard();
    closeArticleModal();
  }
}

async function updateArticle(id, data) {
  const res = await fetch(`${API_URL}?route=articles`, {
    method: "PUT",
    headers: {
      "Content-Type": "application/json"
    },
    body: JSON.stringify(data)
  });

  const result = await res.json();

  if (result.success) {
    showToast("Updated successfully");
    await loadArticlesFromAPI();
    renderDashboard();
  } else {
    showToast(result.error || "Update failed", true);
  }
}

function openDetailModal(article) {
  const modal = document.getElementById('detailModal');
  const detailTitle = document.getElementById('detailTitle');
  const detailBody = document.getElementById('detailBody');
  detailTitle.innerText = article.title;
  const isOwner = currentUser.email === currentUser.email;
  console.log(isOwner,currentUser.email,article.author_email,currentUser.email)
  
  detailBody.innerHTML = `
    <div style="margin-bottom: 1rem;">
      <span style="background:#f0ede8; padding:0.2rem 0.8rem; border-radius:2rem;">${escapeHtml(article.category)}</span>
      <span style="margin-left:1rem; font-size:0.75rem;">📅 ${new Date(article.createdAt).toLocaleDateString()}</span>
      <span style="margin-left:1rem; font-size:0.75rem;">🔖 ID: ${escapeHtml(article.id)}</span>
    </div>
    <div style="margin-bottom: 1rem; font-size:0.85rem; background:#f6f3ef; padding:0.5rem 1rem; border-radius:1rem;">
      ✍️ <strong>Author:</strong> ${escapeHtml(article.authorName)} <span style="color:#8f9b89;">(${escapeHtml(article.authorEmail)})</span>
      ${isOwner ? '<span style="margin-left:0.75rem; background:#b45f2b20; color:#b45f2b; padding:0.15rem 0.5rem; border-radius:1rem; font-size:0.7rem;">Your article</span>' : ''}
    </div>
    <div style="margin-bottom:1.5rem;"><img src="${article.image || 'https://picsum.photos/id/104/800/400'}" style="width:100%; max-height:400px; object-fit:cover; border-radius:1rem;"></div>
    <div style="font-size:1rem; line-height:1.6; white-space:pre-wrap; margin-bottom:1.5rem;">${escapeHtml(article.content)}</div>
    <div style="display:flex; gap:1rem; justify-content:flex-end; border-top:1px solid #eee7df; padding-top:1rem;">
      ${isOwner ? `<button id="detailEditBtn" class="btn-secondary" style="background:#2c3a27; color:white;">✏️ Edit</button>
      <button id="detailDeleteBtn" class="btn-secondary" style="background:#ac4e2e; color:white;">🗑️ Delete</button>` : ''}
      <button id="detailCloseBtn" class="btn-secondary">Close</button>
    </div>
  `;
  modal.style.display = 'flex';

  if (isOwner) {
    document.getElementById('detailEditBtn')?.addEventListener('click', () => {
      modal.style.display = 'none';
      openArticleModal(article);
    });
    document.getElementById('detailDeleteBtn')?.addEventListener('click', async () => {
      if (confirm('Delete this article?')) {
        const success = await deleteArticleFromAPI(article.id);
        if (success) {
          modal.style.display = 'none';
          renderDashboard();
          showToast('Article deleted');
        }
      }
    });
  }
  document.getElementById('detailCloseBtn')?.addEventListener('click', () => {
    modal.style.display = 'none';
  });
}

async function handleSignOut() {
  try {
    await fetch(`${API_URL}/logout`, { method: 'POST' });
  } catch(e) {}
  currentUser = null;
  sessionStorage.removeItem('horizon_session');
  window.location.href = '../index.php';
}

function initEventListeners() {
  document.getElementById('articleModalCloseBtn')?.addEventListener('click', () => closeArticleModal());
  document.getElementById('detailModalCloseBtn')?.addEventListener('click', () => {
    document.getElementById('detailModal').style.display = 'none';
  });
  document.getElementById('cancelArticleBtn')?.addEventListener('click', () => closeArticleModal());
  document.getElementById('articleForm')?.addEventListener('submit', (e) => { e.preventDefault(); saveArticleFromForm(); });
  
  window.onclick = (e) => {
    if (e.target === document.getElementById('articleModal')) closeArticleModal();
    if (e.target === document.getElementById('detailModal')) document.getElementById('detailModal').style.display = 'none';
  };
}