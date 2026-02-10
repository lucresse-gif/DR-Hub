<?php
// dashboard.php - Mon Compte - Dark Romance Hub
require_once 'config.php';

// Vérifier si l'utilisateur est connecté
if (!isLoggedIn()) {
    redirect('index.html');
}

// Récupérer les informations de l'utilisateur
$user = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="dashboard style.css">
    <title>Dark Romance Hub</title>
</head>
<body>

    <header class="header">
        <div class="header-content">
            <div class="logo" onclick="window.location.reload()">
                Dark Romance Hub
            </div>
            <div class="user-section">
                <div class="user-info">
                    <div class="user-avatar">
                        <?php echo strtoupper(substr($user['username'], 0, 1)); ?>
                    </div>
                    <span class="username"><?php echo htmlspecialchars($user['username']); ?></span>
                </div>
                <button class="btn-disconnect" onclick="logout()">Déconnexion</button>
            </div>
        </div>
    </header>

    <div class="main-container">
        <aside class="sidebar">
            <ul class="tab-menu">
                <li class="tab-item active" onclick="switchTab('accueil')">
                    <span>Forum</span>
                </li>
                <li class="tab-item" onclick="switchTab('discussions')">
                    <span>Discussions</span>
                </li>
                <li class="tab-item" onclick="switchTab('messages')">
                    <span>Messages</span>
                </li>
                <li class="tab-item" onclick="switchTab('membres')">
                    <span>Membres</span>
                </li>
                <li class="tab-item" onclick="switchTab('articles')">
                    <span>Articles</span>
                </li>
                <li class="tab-item" onclick="switchTab('boutique')">
                    <span>Boutique</span>
                </li>
                <li class="tab-item" onclick="switchTab('profil')">
                    <span>Profil</span>
                </li>
            </ul>

            <div class="sidebar-decoration"></div>

            <ul class="tab-menu" style="border-top: 2px solid var(--border-ornate); padding-top: 1rem;">
                <li class="tab-item" onclick="logout()">
                    <span>Déconnexion</span>
                </li>
            </ul>
        </aside>

        <main class="content-area">
            <!-- Onglet Accueil/Forum -->
            <div id="accueil" class="tab-content active">
                <div class="welcome-section">
                    <h1>Bienvenue, <?php echo htmlspecialchars($user['username']); ?></h1>
                    <p>Voici un aperçu de votre activité sur Dark Romance Hub</p>
                </div>

                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-number">12</div>
                        <div class="stat-label">Livres lus</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number">45</div>
                        <div class="stat-label">Discussions</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number">8</div>
                        <div class="stat-label">Vidéos</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number">234</div>
                        <div class="stat-label">Mentions j'aime</div>
                    </div>
                </div>

                <h2 class="section-title">Nouvelle discussion</h2>

                <div class="activity-card">
                    <div class="activity-header">
                        <h3 class="activity-title">Qu'est-ce que tout le monde pense vous attire de la fin ?</h3>
                    </div>
                    <div class="activity-body">
                        <p>Partagez vos réflexions sur ce qui rend les fins de dark romance si captivantes...</p>
                    </div>
                    <div class="activity-meta">
                        <span>💀 Par LilSweet</span>
                        <span>🕐 Il y a 8h</span>
                    </div>
                </div>

                <h2 class="section-title">Vidéo populaire</h2>

                <div class="activity-card">
                    <div class="activity-header">
                        <div style="display: flex; align-items: center; gap: 1rem;">
                            <span style="font-size: 2rem;">❤️</span>
                            <div>
                                <h3 class="activity-title">Top 10 vidéos Dark Romance 2023</h3>
                                <div style="color: #8b7355; font-size: 0.9rem; margin-top: 0.5rem;">Par LilHeart, il y a 12h</div>
                            </div>
                        </div>
                        <div class="activity-badge">❤️ 234</div>
                    </div>
                </div>

                <div class="view-more-container">
                    <button class="btn-view-more">Voir plus d'activité</button>
                </div>
            </div>

            <!-- Autres onglets (à développer) -->
            <div id="discussions" class="tab-content">
                <h2 class="section-title">Discussions</h2>
                <p style="color: #c4a884;"><?php include 'discussions.php' ?>Section en développement...</p>
            </div>

            <div id="messages" class="tab-content">
                <h2 class="section-title">Messages</h2>
                <p style="color: #c4a884;">Section en développement...</p>
            </div>

            <div id="membres" class="tab-content">
                <h2 class="section-title">Membres</h2>
                <p style="color: #c4a884;">Section en développement...</p>
            </div>

            <div id="articles" class="tab-content">
                <h2 class="section-title">Articles</h2>
                <p style="color: #c4a884;">Section en développement...</p>
            </div>

            <div id="boutique" class="tab-content">
                <h2 class="section-title">Boutique</h2>
                <p style="color: #c4a884;">Section en développement...</p>
            </div>

            <div id="profil" class="tab-content">
                <div class="section-header-simple">
                    <h2 class="section-title">Mon Profil</h2>
                    <p class="section-header-description">Personnalisez votre compte</p>
                </div>
                <!-- Section Avatar -->
                <div class="profile-card">
                    <h3 class="profile-section-title">Photo de profil</h3>
                    <div class="avatar-section">
                        <div class="current-avatar-preview">
                            <div class="avatar-large" id="currentAvatarPreview">
                                <?php echo strtoupper(substr($user['username'], 0, 1)); ?>
                            </div>
                        </div>
                        <div class="avatar-actions">
                            <button class="btn btn-secondary" onclick="openAvatarGallery()"> Choisir un avatar</button>
                            <button class="btn btn-secondary" onclick="openAvatarUpload()">Uploader ma photo</button>
                        </div>
                    </div>
                </div>
                <!-- Section Informations -->
                <div class="profile-card">
                    <h3 class="profile-section-title">Informations personnelles</h3>
                    <form id="profileForm" class="profile-form" onsubmit="updateProfile(event)">
                        <div class="form-group">
                            <label>Nom d'utilisateur</label>
                            <input type="text" id="profileUsername" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" id="profileEmail" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Bio</label>
                            <textarea id="profileBio" rows="4" placeholder="Parlez-nous de vous et de vos goûts en lecture..."><?php echo htmlspecialchars($user['bio'] ?? ''); ?></textarea>
                        </div>
                        <div class="form-group">
                            <label class="checkbox-label">
                                <input type="checkbox" id="profileVideoCreator" <?php echo $user['is_video_creator'] ? 'checked' : ''; ?>>
                                <span>Je souhaite créer du contenu vidéo (BookTok)</span>
                            </label>
                            <p class="form-help-text">Activez cette option pour pouvoir publier des vidéos sur la plateforme</p>
                        </div>
                        <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
                    </form>
                </div>
                <!-- Section Sécurité -->
                <div class="profile-card">
                    <h3 class="profile-section-title">Sécurité</h3>
                    <form id="passwordForm" class="profile-form" onsubmit="changePassword(event)">
                        <div class="form-group">
                            <label>Ancien mot de passe</label>
                            <input type="password" id="oldPassword" required>
                        </div>
                        <div class="form-group">
                            <label>Nouveau mot de passe</label>
                            <input type="password" id="newPassword" required>
                        </div>
                        <div class="form-group">
                            <label>Confirmer le mot de passe</label>
                            <input type="password" id="confirmPassword" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Changer le mot de passe</button>
                    </form>
                </div>
                <!-- Section Danger -->
                <div class="profile-card danger-zone">
                    <h3 class="profile-section-title danger-title">Zone dangereuse</h3>
                    <p class="danger-description">Une fois votre compte supprimé, toutes vos données seront définitivement perdues.</p>
                    <button class="btn btn-danger" onclick="confirmDeleteAccount()">Supprimer mon compte</button>
                </div>
            </div>
        </main>
    </div>

    

    <script>
        let currentDiscussions = [];
        let currentCategories = [];

        function switchTab(tabName) {
            const tabs = document.querySelectorAll('.tab-content');
            tabs.forEach(tab => tab.classList.remove('active'));
            
            const tabItems = document.querySelectorAll('.tab-item');
            tabItems.forEach(item => item.classList.remove('active'));
            
            document.getElementById(tabName).classList.add('active');
            event.currentTarget.classList.add('active');
            
            document.querySelector('.content-area').scrollTop = 0;

            // Charger les données si nécessaire
            if (tabName === 'discussions') {
                loadDiscussions();
                loadCategories();
            } else if (tabName === 'profil') {
                loadProfileData();
            }
        }

        async function logout() {
            if (confirm('Êtes-vous sûr de vouloir vous déconnecter ?')) {
                try {
                    const response = await fetch('logout.php', {
                        method: 'POST'
                    });
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        window.location.href = 'index.html';
                    }
                } catch (error) {
                    console.error('Erreur lors de la déconnexion:', error);
                    alert('Erreur lors de la déconnexion. Veuillez réessayer.');
                }
            }
        }

        // === FONCTIONS POUR LES DISCUSSIONS ===

        async function loadCategories() {
            try {
                const response = await fetch('discussions_api.php?action=categories');
                const data = await response.json();
                
                if (data.success) {
                    currentCategories = data.categories;
                    const select = document.getElementById('categoryFilter');
                    select.innerHTML = '<option value="">Toutes les catégories</option>';
                    
                    data.categories.forEach(cat => {
                        select.innerHTML += `<option value="${cat.id}">${cat.icon} ${cat.name} (${cat.discussions_count})</option>`;
                    });
                }
            } catch (error) {
                console.error('Erreur chargement catégories:', error);
            }
        }

        async function loadDiscussions() {
            try {
                const category = document.getElementById('categoryFilter')?.value || '';
                const search = document.getElementById('searchDiscussions')?.value || '';
                
                const response = await fetch(`discussions_api.php?action=list&category_id=${category}&search=${encodeURIComponent(search)}`);
                const data = await response.json();
                
                if (data.success) {
                    currentDiscussions = data.discussions;
                    displayDiscussions(data.discussions);
                }
            } catch (error) {
                console.error('Erreur chargement discussions:', error);
                document.getElementById('discussionsList').innerHTML = `
                    <div style="text-align: center; padding: 3rem; color: #ff6b6b;">
                        <p>Erreur lors du chargement des discussions</p>
                    </div>
                `;
            }
        }

        function displayDiscussions(discussions) {
            const container = document.getElementById('discussionsList');
            
            if (discussions.length === 0) {
                container.innerHTML = `
                    <div class="no-discussions-message">
                        <p>Aucune discussion trouvée</p>
                        <p class="submessage">Soyez le premier à lancer une conversation !</p>
                    </div>
                `;
                return;
            }
            
            container.innerHTML = discussions.map(d => `
                <div class="activity-card" onclick="viewDiscussion(${d.id})">
                    <div class="activity-header">
                        <div class="activity-header-content">
                            ${d.is_pinned ? '<span class="pinned-icon">📌</span>' : ''}
                            <h3 class="activity-title">${d.title}</h3>
                            <div class="activity-badges">
                                <span class="activity-badge category-badge" style="background: ${d.category_color};">
                                    ${d.category_icon} ${d.category_name}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="activity-body">
                        <p>${d.content.substring(0, 200)}${d.content.length > 200 ? '...' : ''}</p>
                    </div>
                    <div class="activity-meta">
                        <span>👤 ${d.username}</span>
                        <span>💬 ${d.replies_count} réponses</span>
                        <span>❤️ ${d.likes_count} likes</span>
                        <span>👁️ ${d.views_count} vues</span>
                        <span>🕐 ${formatDate(d.created_at)}</span>
                    </div>
                </div>
            `).join('');
        }

        function filterDiscussions() {
            loadDiscussions();
        }

        function searchDiscussions() {
            clearTimeout(window.searchTimeout);
            window.searchTimeout = setTimeout(() => {
                loadDiscussions();
            }, 500);
        }

        function openNewDiscussion() {
            const modal = document.createElement('div');
            modal.className = 'modal active';
            modal.innerHTML = `
                <div class="modal-content modal-content-large">
                    <button class="close-modal" onclick="this.closest('.modal').remove()">&times;</button>
                    <h2>Nouvelle Discussion</h2>
                    <form id="newDiscussionForm" class="new-discussion-form" onsubmit="submitNewDiscussion(event)">
                        <div class="form-group">
                            <label>Catégorie</label>
                            <select id="newDiscCategory" required>
                                ${currentCategories.map(c => `<option value="${c.id}">${c.icon} ${c.name}</option>`).join('')}
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Titre</label>
                            <input type="text" id="newDiscTitle" required placeholder="Un titre accrocheur...">
                        </div>
                        <div class="form-group">
                            <label>Contenu</label>
                            <textarea id="newDiscContent" required placeholder="Partagez vos pensées..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary btn-full-width">Créer la discussion</button>
                    </form>
                </div>
            `;
            document.body.appendChild(modal);
        }

        async function submitNewDiscussion(event) {
            event.preventDefault();
            
            const category_id = document.getElementById('newDiscCategory').value;
            const title = document.getElementById('newDiscTitle').value;
            const content = document.getElementById('newDiscContent').value;
            
            try {
                const response = await fetch('discussions_api.php?action=create', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ category_id, title, content })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    document.querySelector('.modal').remove();
                    loadDiscussions();
                    alert('Discussion créée avec succès !');
                } else {
                    alert(data.message);
                }
            } catch (error) {
                alert('Erreur lors de la création');
            }
        }

        function viewDiscussion(id) {
            // Rediriger vers la page de la discussion (à créer)
            window.location.href = `discussion.php?id=${id}`;
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
            
            return date.toLocaleDateString('fr-FR');
        }

        //profil
        function loadProfileData() {
            // Charger l'avatar actuel
            fetch('profile_api.php?action=get')
            .then(response => response.json())
            .then(data => {
                if (data.success && data.user.profile_picture) {
                    updateAvatarPreview(data.user.profile_picture);
                }
            })
            .catch(error => console.error('Erreur chargement profil:', error));
        }
        function updateAvatarPreview(avatarPath) {
            const preview = document.getElementById('currentAvatarPreview');
            if (avatarPath && preview) {
                preview.style.backgroundImage = `url('${avatarPath}')`;
                preview.style.backgroundSize = 'cover';
                preview.style.backgroundPosition = 'center';
                preview.textContent = '';
            }
        }
        async function updateProfile(event) {
            event.preventDefault();
            const username = document.getElementById('profileUsername').value;
            const email = document.getElementById('profileEmail').value;
            const bio = document.getElementById('profileBio').value;
            const is_video_creator =
            document.getElementById('profileVideoCreator').checked;
            try {
                const response = await fetch('profile_api.php?action=update', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ username, email, bio, is_video_creator })
                });
                const data = await response.json();
                if (data.success) {
                    alert('Profil mis à jour avec succès !');
                    // Mettre à jour le nom d'utilisateur dans le header
                    const usernameEl = document.querySelector('.username');
                    const avatarEl = document.querySelector('.user-avatar');
                    if (usernameEl) usernameEl.textContent = username;
                    if (avatarEl) avatarEl.textContent = username.charAt(0).toUpperCase();
                } else {
                    alert(data.message);
                }
            } catch (error) {
                console.error('Erreur:', error);
            }
                alert('Erreur lors de la mise à jour du profil');
        }
        async function changePassword(event) {
            event.preventDefault();
            const old_password = document.getElementById('oldPassword').value;
            const new_password = document.getElementById('newPassword').value;
            const confirm_password =
            document.getElementById('confirmPassword').value;
            try {
                const response = await fetch('profile_api.php?action=change_password', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ old_password, new_password, confirm_password })
                });
                const data = await response.json();
                if (data.success) {
                    alert('Mot de passe changé avec succès !');
                    document.getElementById('passwordForm').reset();
                } else {
                    alert(data.message);
                }
            } catch (error) {
                console.error('Erreur:', error);
                alert('Erreur lors du changement de mot de passe');
            }
        }
        function openAvatarGallery() {
            fetch('profile_api.php?action=get_avatars')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showAvatarGalleryModal(data.avatars);
                    } else {
                        alert('Erreur lors du chargement des avatars');
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    alert('Erreur de connexion');
                });
        }
        function showAvatarGalleryModal(avatars) {
            const modal = document.createElement('div');
            modal.className = 'modal active';
            modal.innerHTML = `
                <div class="modal-content modal-content-large">
                    <button class="close-modal" onclick="this.closest('.modal').remove()">&times;</button>
                    <h2>Choisir un avatar</h2>
                    <div class="avatar-gallery">
                        ${avatars.map(avatar => `
                            <div class="avatar-option" onclick="selectAvatar(${avatar.id},'avatars/${avatar.filename}')">
                                <img src="avatars/${avatar.filename}"
                                    alt="${avatar.display_name}" onerror="this.src='data:image/svg+xml,<svg
                                    xmlns=%22http://www.w3.org/2000/svg%22 width=%22100%22
                                    height=%22100%22><rect fill=%22%238b0000%22 width=%22100%22
                                    height=%22100%22/><text x=%2250%25%22 y=%2250%25%22 font
                                    size=%2240%22 fill=%22%23d4af37%22 text-anchor=%22middle%22
                                    dy=%22.3em%22>?</text></svg>'">
                                <p class="avatar-name">${avatar.display_name}</p>
                            </div>
                        `).join('')}
                    </div>
                </div>
            `;
            document.body.appendChild(modal);
        }
        async function selectAvatar(avatarId, avatarPath) {
            try {
                const response = await fetch('profile_api.php?action=set_avatar', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ avatar_id: avatarId })
                });
                const data = await response.json();
                if (data.success) {
                    updateAvatarPreview(data.avatar_path);
                    document.querySelector('.modal').remove();
                    alert('Avatar mis à jour !');
                } else {
                    alert(data.message);
                }
            } catch (error) {
                console.error('Erreur:', error);
                alert('Erreur lors de la mise à jour');
            }
        }
        function openAvatarUpload() {
            const modal = document.createElement('div');
            modal.className = 'modal active';
            modal.innerHTML = `
                <div class="modal-content">
                    <button class="close-modal" onclick="this.closest('.modal').remove()">&times;</button>
                    <h2>Uploader ma photo</h2>
                    <form id="uploadAvatarForm" class="profile-form" onsubmit="uploadAvatar(event)">
                        <div class="form-group">
                            <label>Choisir une image</label>
                            <input type="file" id="avatarFile" accept="image/*" required class="file-input">
                            <p class="form-help-text">JPG, PNG, GIF ou WEBP - Maximum 5MB</p>
                        </div>
                        <div id="avatarPreviewContainer" class="avatar-preview container"></div>
                        <button type="submit" class="btn btn-primary btn-full width">Uploader</button>
                    </form>
                </div>
            `;
            document.body.appendChild(modal);
            // Prévisualisation
            document.getElementById('avatarFile').addEventListener('change',
            function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        document.getElementById('avatarPreviewContainer').innerHTML = `
                            <img src="${e.target.result}" class="avatar-preview-image">
                        `;
                    };
                    reader.readAsDataURL(file);
                }
            });
        }
        async function uploadAvatar(event) {
            event.preventDefault();
            const fileInput = document.getElementById('avatarFile');
            const file = fileInput.files[0];
            if (!file) {
                alert('Veuillez sélectionner un fichier');
                return;
            }
            const formData = new FormData();
            formData.append('avatar', file);
            try {
                const response = await fetch('profile_api.php?action=upload_avatar', {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();
                if (data.success) {
                    updateAvatarPreview(data.avatar_path);
                    document.querySelector('.modal').remove();
                    alert('Photo de profil mise à jour !');
                } else {
                    alert(data.message);
                }
            } catch (error) {
                console.error('Erreur:', error);
                alert('Erreur lors de l\'upload');
            }
        }
        function confirmDeleteAccount() {
            const password = prompt('Pour confirmer la suppression de votre compte, entrez votre mot de passe :');
            if (password) {
                if (confirm('Êtes-vous VRAIMENT sûr ? Cette action est irréversible !')) {
                    deleteAccount(password);
                }
            }
        }
        async function deleteAccount(password) {
            try {
                const response = await fetch('profile_api.php?action=delete_account', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ password })
                });
                const data = await response.json();
                if (data.success) {
                    alert('Votre compte a été supprimé');
                    window.location.href = 'index.html';
                } else {
                    alert(data.message);
                }
            } catch (error) {
                console.error('Erreur:', error);
                alert('Erreur lors de la suppression');
            }
        }
    </script>
</body>
</html>