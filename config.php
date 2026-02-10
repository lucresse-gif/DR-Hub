<?php
// config.php - Configuration de la base de données
// Démarrer la session
session_start();
// Configuration de la base de données
define('DB_HOST', 'localhost');
define('DB_NAME', 'dark_romance_hub');
define('DB_USER', 'root'); // Changez selon votre configuration
define('DB_PASS', ''); // Changez selon votre configuration
// Configuration de sécurité
define('SITE_KEY', 'votre_cle_secrete_unique_123456'); // Changez cette clé !
// Fuseau horaire
date_default_timezone_set('Europe/Paris');
// Connexion à la base de données avec PDO
try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
    
        ]
    );
} catch(PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}
// Fonction pour générer un token sécurisé
function generateToken($length = 32) {
    return bin2hex(random_bytes($length));
}
// Fonction pour hasher les mots de passe
function hashPassword($password) {
    return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
}
// Fonction pour vérifier si l'utilisateur est connecté
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}
// Fonction pour obtenir l'utilisateur connecté
function getCurrentUser() {
    global $pdo;
    if (!isLoggedIn()) {
        return null;
    }
    $stmt = $pdo->prepare("SELECT id, username, email, profile_picture, bio,
is_video_creator FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch();
}
// Fonction pour nettoyer les entrées utilisateur
function cleanInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
// Fonction pour rediriger
function redirect($url) {
    header("Location: " . $url);
    exit();
}
?>