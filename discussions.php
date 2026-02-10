<?php
// discussion.php - Page de vue d'une discussion
require_once 'config.php';

if (!isLoggedIn()) {
    redirect('index.html');
}

$discussion_id = $_GET['id'] ?? 0;
$user = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Discussion - Dark Romance Hub</title>

</head>
<body>
    <!-- Décorations globales -->
    <!--<div class="mist-overlay"></div>
    <div class="floating-rose">🌹</div>
    <div class="floating-rose">🥀</div>
    <div class="floating-rose">🌹</div>
    <div class="floating-rose">🥀</div>
    <div class="floating-rose">🌹</div>
    <div class="floating-rose">🥀</div>
    <div class="floating-rose">🌹</div>
    <div class="floating-rose">🥀</div>
    <div class="bat-decoration">🦇</div>
    <div class="bat-decoration">🦇</div>
    <div class="bat-decoration">🦇</div>
    <div class="dust-particle"></div>
    <div class="dust-particle"></div>
    <div class="dust-particle"></div>
    <div class="dust-particle"></div>
    <div class="dust-particle"></div>
    <div class="candle-decoration">🕯️</div>
    <div class="candle-decoration-right">🕯️</div>-->

    <header class="header">
        <div class="header-content">
            <div class="logo" onclick="window.location.href='dashboard.php'">🌹 Dark Romance Hub</div>
            <button class="btn btn-secondary" onclick="window.location.href='dashboard.php'">← Retour aux discussions</button>
        </div>
    </header>

    <div class="container">
        <!-- Discussion principale -->
        <div id="discussionMain">
            <div class="loading-message">
                <p>Chargement de la discussion...</p>
            </div>
        </div>

        <!-- Formulaire de réponse -->
        <div class="reply-section">
            <h3>Répondre à cette discussion</h3>
            <form onsubmit="submitReply(event)">
                <textarea id="replyContent" required placeholder="Partagez votre opinion..."></textarea>
                <button type="submit" class="btn btn-primary">Publier la réponse</button>
            </form>
        </div>

        <!-- Liste des réponses -->
        <div id="repliesList"></div>
    </div>

    <script>
        const discussionId = <?php echo $discussion_id; ?>;
        let currentDiscussion = null;

        async function loadDiscussion() {
            try {
                const response = await fetch(`discussions_api.php?action=get&id=${discussionId}`);
                const data = await response.json();
                
                if (data.success) {
                    currentDiscussion = data.discussion;
                    displayDiscussion(data.discussion);
                    displayReplies(data.replies);
                } else {
                    document.getElementById('discussionMain').innerHTML = `
                        <div class="error-message">
                            <p>Discussion non trouvée</p>
                        </div>
                    `;
                }
            } catch (error) {
                console.error('Erreur:', error);
            }
        }

        function displayDiscussion(d) {
            document.getElementById('discussionMain').innerHTML = `
                <div class="discussion-card">
                    <div class="discussion-header">
                        <h1 class="discussion-title">${d.title}</h1>
                        <div class="discussion-meta">
                            <span>👤 ${d.username}</span>
                            <span class="category-badge" style="background: ${d.category_color};">
                                ${d.category_icon} ${d.category_name}
                            </span>
                            <span>👁️ ${d.views_count} vues</span>
                            <span>🕐 ${formatDate(d.created_at)}</span>
                        </div>
                    </div>
                    <div class="discussion-content">
                        ${d.content.replace(/\n/g, '<br>')}
                    </div>
                    <div class="discussion-actions">
                        <button class="action-btn ${d.user_liked ? 'active' : ''}" onclick="toggleLike()" id="likeBtn">
                            ❤️ <span id="likesCount">${d.likes_count}</span> J'aime
                        </button>
                    </div>
                </div>
            `;
        }

        function displayReplies(replies) {
            const container = document.getElementById('repliesList');
            
            if (replies.length === 0) {
                container.innerHTML = `
                    <div class="no-replies-message">
                        <p>Aucune réponse pour le moment. Soyez le premier à répondre !</p>
                    </div>
                `;
                return;
            }
            
            container.innerHTML = `
                <h3 class="replies-title">
                    ${replies.length} Réponse${replies.length > 1 ? 's' : ''}
                </h3>
                ${replies.map(r => `
                    <div class="reply-card">
                        <div class="reply-header">
                            <div class="reply-author">
                                <div class="author-avatar">${r.username.charAt(0).toUpperCase()}</div>
                                <div>
                                    <div class="author-name">${r.username}</div>
                                    <div class="author-date">${formatDate(r.created_at)}</div>
                                </div>
                            </div>
                        </div>
                        <div class="reply-content">${r.content.replace(/\n/g, '<br>')}</div>
                        <div class="reply-actions">
                            <span class="like-count">❤️ ${r.likes_count} J'aime</span>
                        </div>
                    </div>
                `).join('')}
            `;
        }

        async function submitReply(event) {
            event.preventDefault();
            
            const content = document.getElementById('replyContent').value;
            
            try {
                const response = await fetch('discussions_api.php?action=reply', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        discussion_id: discussionId,
                        content: content
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    document.getElementById('replyContent').value = '';
                    loadDiscussion();
                    alert('Réponse publiée !');
                } else {
                    alert(data.message);
                }
            } catch (error) {
                alert('Erreur lors de la publication');
            }
        }

        async function toggleLike() {
            try {
                const response = await fetch('discussions_api.php?action=like', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        discussion_id: discussionId,
                        type: 'discussion'
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    document.getElementById('likesCount').textContent = data.likes_count;
                    const btn = document.getElementById('likeBtn');
                    if (data.action === 'liked') {
                        btn.classList.add('active');
                    } else {
                        btn.classList.remove('active');
                    }
                }
            } catch (error) {
                console.error('Erreur:', error);
            }
        }

        function formatDate(dateString) {
            const date = new Date(dateString);
            const now = new Date();
            const diff = now - date;
            
            const minutes = Math.floor(diff / 60000);
            const hours = Math.floor(diff / 3600000);
            const days = Math.floor(diff / 86400000);
            
            if (minutes < 1) return 'À l\'instant';
            if (minutes < 60) return `Il y a ${minutes}min`;
            if (hours < 24) return `Il y a ${hours}h`;
            if (days < 7) return `Il y a ${days}j`;
            
            return date.toLocaleDateString('fr-FR', { 
                day: 'numeric', 
                month: 'long', 
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        }

        loadDiscussion();
    </script>
</body>
</html>