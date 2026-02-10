<?php
// login.php - Gestion de la connexion

// Activer l'affichage des erreurs pour debug
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config.php';

header('Content-Type: application/json');

// Log pour debug
error_log("Login.php appelé");

// Vérifier si la requête est POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit();
}

// Récupérer les données JSON
$input = file_get_contents('php://input');
error_log("Input reçu: " . $input);

$data = json_decode($input, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode(['success' => false, 'message' => 'Erreur JSON: ' . json_last_error_msg()]);
    exit();
}

// Validation des données
$errors = [];

if (empty($data['email'])) {
    $errors[] = "L'email est requis";
}

if (empty($data['password'])) {
    $errors[] = "Le mot de passe est requis";
}

if (!empty($errors)) {
    echo json_encode(['success' => false, 'message' => implode(', ', $errors)]);
    exit();
}

$email = cleanInput($data['email']);
$password = $data['password'];

try {
    // Récupérer l'utilisateur par email
    $stmt = $pdo->prepare("
        SELECT id, username, email, password_hash, is_active 
        FROM users 
        WHERE email = ?
    ");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    // Vérifier si l'utilisateur existe
    if (!$user) {
        echo json_encode(['success' => false, 'message' => 'Email ou mot de passe incorrect']);
        exit();
    }
    
    // Vérifier si le compte est actif
    if (!$user['is_active']) {
        echo json_encode(['success' => false, 'message' => 'Votre compte a été désactivé']);
        exit();
    }
    
    // Vérifier le mot de passe
    if (!password_verify($password, $user['password_hash'])) {
        echo json_encode(['success' => false, 'message' => 'Email ou mot de passe incorrect']);
        exit();
    }
    
    // Créer la session
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['email'] = $user['email'];
    
    // Mettre à jour la date de dernière connexion
    $stmt = $pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
    $stmt->execute([$user['id']]);
    
    // Optionnel : Créer un token de session dans la table user_sessions
    $session_token = generateToken();
    $expires_at = date('Y-m-d H:i:s', strtotime('+30 days'));
    
    $stmt = $pdo->prepare("
        INSERT INTO user_sessions (user_id, session_token, ip_address, user_agent, expires_at) 
        VALUES (?, ?, ?, ?, ?)
    ");
    
    $stmt->execute([
        $user['id'],
        $session_token,
        $_SERVER['REMOTE_ADDR'] ?? null,
        $_SERVER['HTTP_USER_AGENT'] ?? null,
        $expires_at
    ]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Connexion réussie !',
        'user' => [
            'id' => $user['id'],
            'username' => $user['username'],
            'email' => $user['email']
        ],
        'token' => $session_token
    ]);
    
} catch(PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Erreur lors de la connexion. Veuillez réessayer.'
    ]);
}
?>