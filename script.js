// Fonction pour changer d'onglet
function switchTab(tabName) {
    // Désactiver tous les onglets
    const tabs = document.querySelectorAll('.tab-content');
    tabs.forEach(tab => tab.classList.remove('active'));
    const tabItems = document.querySelectorAll('.tab-item');
    tabItems.forEach(item => item.classList.remove('active'));
    // Activer l'onglet sélectionné
    document.getElementById(tabName).classList.add('active');
    event.currentTarget.classList.add('active');
    // Scroll vers le haut du contenu
    document.querySelector('.content-area').scrollTop = 0;
    if (tabName === 'discussions') {
        loadDiscussions();
        loadCategories();
    } else if (tabName === 'profil'){
        loadProfilData();
    }
}
// Fonction de déconnexion
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

//Connexion et Inscription
