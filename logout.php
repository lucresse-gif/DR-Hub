<?php
// logout.php - Gestion de la déconnexion
require_once 'config.php';
header('Content-Type: application/json');
try {
    // Supprimer les sessions en base de données si elles existent
    if (isset($_SESSION['user_id'])) {
        $stmt = $pdo->prepare("DELETE FROM user_sessions WHERE user_id =
?");
    }
        $stmt->execute([$_SESSION['user_id']]);
    // Détruire toutes les variables de session
    $_SESSION = array();
    // Détruire le cookie de session
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 3600, '/');
    }
    // Détruire la session
    session_destroy();
    echo json_encode([
        'success' => true,
        'message' => 'Déconnexion réussie'
    ]);
} catch(Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Erreur lors de la déconnexion'
    ]);
}
?>