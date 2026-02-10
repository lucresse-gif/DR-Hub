<?php
    // register.php - Gestion de l'inscription
    require_once 'config.php';
    header('Content-Type: application/json');
    // Vérifier si la requête est POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
        exit();
    }
    // Récupérer les données JSON
    $data = json_decode(file_get_contents('php://input'), true);
    // Validation des données
    $errors = [];
    // Username
    if (empty($data['username'])) {
        $errors[] = "Le nom d'utilisateur est requis";
    } elseif (strlen($data['username']) < 3) {
        $errors[] = "Le nom d'utilisateur doit contenir au moins 3 caractères";
    } elseif (strlen($data['username']) > 50) {
        $errors[] = "Le nom d'utilisateur ne peut pas dépasser 50 caractères";
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $data['username'])) {
        $errors[] = "Le nom d'utilisateur ne peut contenir que des lettres, chiffres et
    underscores";
    }
    // Email
    if (empty($data['email'])) {
        $errors[] = "L'email est requis";
    } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = "L'email n'est pas valide";
    }
    // Mot de passe
    if (empty($data['password'])) {
        $errors[] = "Le mot de passe est requis";
    } elseif (strlen($data['password']) < 8) {
        $errors[] = "Le mot de passe doit contenir au moins 8 caractères";
    }
    // Si des erreurs existent, les retourner
    if (!empty($errors)) {
        echo json_encode(['success' => false, 'message' => implode(', ', $errors)]);
        exit();
    }
    // Nettoyer les données
    $username = cleanInput($data['username']);
    $email = cleanInput($data['email']);
    $password = $data['password'];
    try {
        // Vérifier si l'email existe déjà
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            echo json_encode(['success' => false, 'message' => 'Cet email est déjà utilisé']);
            exit();
        }
        // Vérifier si le username existe déjà
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            echo json_encode(['success' => false, 'message' => "Ce nom d'utilisateur est déjà pris"]);
            exit();
        }
        // Hasher le mot de passe
        $password_hash = hashPassword($password);
        // Insérer l'utilisateur dans la base de données
        $stmt = $pdo->prepare("
            INSERT INTO users (username, email, password_hash) 
            VALUES (?, ?, ?)
        ");
        $stmt->execute([$username, $email, $password_hash]);
        // Récupérer l'ID du nouvel utilisateur
        $user_id = $pdo->lastInsertId();
        // Créer la session
        $_SESSION['user_id'] = $user_id;
        $_SESSION['username'] = $username;
        $_SESSION['email'] = $email;
        // Mettre à jour la date de dernière connexion
        $stmt = $pdo->prepare("UPDATE users SET last_login = NOW() WHERE id= ?");
        $stmt->execute([$user_id]);
        echo json_encode([
            'success' => true, 
            'message' => 'Inscription réussie !',
            'user' => [
                'id' => $user_id,
                'username' => $username,
                'email' => $email
            ]
        ]);
    } catch(PDOException $e) {
        echo json_encode([
            'success' => false, 
            'message' => 'Erreur lors de l\'inscription. Veuillez réessayer.'
        ]);
    }
?>