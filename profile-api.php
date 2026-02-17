<?php
// profile_api.php - API pour gérer le profil utilisateur

require_once 'config.php';

//header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Non authentifié']);
    exit();
}

$action = $_GET['action'] ?? '';
$method = $_SERVER['REQUEST_METHOD'];

try {
    switch ($action) {
        case 'get':
            // Récupérer les informations du profil
            $user = getCurrentUser();
            echo json_encode(['success' => true, 'user' => $user]);
            break;
            
        case 'update':
            if ($method !== 'POST') {
                echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
                exit();
            }
            
            $data = json_decode(file_get_contents('php://input'), true);
            
            $user_id = $_SESSION['user_id'];
            $username = cleanInput($data['username'] ?? '');
            $email = cleanInput($data['email'] ?? '');
            $bio = cleanInput($data['bio'] ?? '');
            $is_video_creator = isset($data['is_video_creator']) ? (bool)$data['is_video_creator'] : false;
            
            // Validation
            if (empty($username) || empty($email)) {
                echo json_encode(['success' => false, 'message' => 'Le nom d\'utilisateur et l\'email sont requis']);
                exit();
            }
            
            if (strlen($username) < 3) {
                echo json_encode(['success' => false, 'message' => 'Le nom d\'utilisateur doit contenir au moins 3 caractères']);
                exit();
            }
            
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                echo json_encode(['success' => false, 'message' => 'Email invalide']);
                exit();
            }
            
            // Vérifier si le username est déjà pris par quelqu'un d'autre
            $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
            $stmt->execute([$username, $user_id]);
            if ($stmt->fetch()) {
                echo json_encode(['success' => false, 'message' => 'Ce nom d\'utilisateur est déjà pris']);
                exit();
            }
            
            // Vérifier si l'email est déjà pris par quelqu'un d'autre
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
            $stmt->execute([$email, $user_id]);
            if ($stmt->fetch()) {
                echo json_encode(['success' => false, 'message' => 'Cet email est déjà utilisé']);
                exit();
            }
            
            // Mettre à jour le profil
            $stmt = $pdo->prepare("
                UPDATE users 
                SET username = ?, email = ?, bio = ?, is_video_creator = ?
                WHERE id = ?
            ");
            $stmt->execute([$username, $email, $bio, $is_video_creator, $user_id]);
            
            // Mettre à jour la session
            $_SESSION['username'] = $username;
            $_SESSION['email'] = $email;
            
            echo json_encode(['success' => true, 'message' => 'Profil mis à jour avec succès']);
            break;
            
        case 'change_password':
            if ($method !== 'POST') {
                echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
                exit();
            }
            
            $data = json_decode(file_get_contents('php://input'), true);
            
            $old_password = $data['old_password'] ?? '';
            $new_password = $data['new_password'] ?? '';
            $confirm_password = $data['confirm_password'] ?? '';
            
            if (empty($old_password) || empty($new_password) || empty($confirm_password)) {
                echo json_encode(['success' => false, 'message' => 'Tous les champs sont requis']);
                exit();
            }
            
            if ($new_password !== $confirm_password) {
                echo json_encode(['success' => false, 'message' => 'Les mots de passe ne correspondent pas']);
                exit();
            }
            
            if (strlen($new_password) < 8) {
                echo json_encode(['success' => false, 'message' => 'Le mot de passe doit contenir au moins 8 caractères']);
                exit();
            }
            
            // Vérifier l'ancien mot de passe
            $stmt = $pdo->prepare("SELECT password_hash FROM users WHERE id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $user = $stmt->fetch();
            
            if (!password_verify($old_password, $user['password_hash'])) {
                echo json_encode(['success' => false, 'message' => 'Ancien mot de passe incorrect']);
                exit();
            }
            
            // Mettre à jour le mot de passe
            $new_password_hash = hashPassword($new_password);
            $stmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
            $stmt->execute([$new_password_hash, $_SESSION['user_id']]);
            
            echo json_encode(['success' => true, 'message' => 'Mot de passe changé avec succès']);
            break;
            
        case 'get_avatars':
            // Récupérer la liste des avatars présets
            $stmt = $pdo->query("
                SELECT id, filename, display_name, category 
                FROM preset_avatars 
                WHERE is_active = TRUE
                ORDER BY category, id
            ");
            $avatars = $stmt->fetchAll();
            
            echo json_encode(['success' => true, 'avatars' => $avatars]);
            break;
            
        case 'set_avatar':
            if ($method !== 'POST') {
                echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
                exit();
            }
            
            $data = json_decode(file_get_contents('php://input'), true);
            $avatar_id = (int)($data['avatar_id'] ?? 0);
            
            // Vérifier que l'avatar existe
            $stmt = $pdo->prepare("SELECT filename FROM preset_avatars WHERE id = ? AND is_active = TRUE");
            $stmt->execute([$avatar_id]);
            $avatar = $stmt->fetch();
            
            if (!$avatar) {
                echo json_encode(['success' => false, 'message' => 'Avatar non trouvé']);
                exit();
            }
            
            // Mettre à jour l'avatar de l'utilisateur
            $avatar_path = 'avatars/' . $avatar['filename'];
            $stmt = $pdo->prepare("
                UPDATE users 
                SET profile_picture = ?, avatar_type = 'preset'
                WHERE id = ?
            ");
            $stmt->execute([$avatar_path, $_SESSION['user_id']]);
            
            echo json_encode([
                'success' => true, 
                'message' => 'Avatar mis à jour',
                'avatar_path' => $avatar_path
            ]);
            break;
            
        case 'upload_avatar':
            if ($method !== 'POST') {
                echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
                exit();
            }
            
            // Vérifier qu'un fichier a été uploadé
            if (!isset($_FILES['avatar']) || $_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
                echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'upload']);
                exit();
            }
            
            $file = $_FILES['avatar'];
            
            // Vérifier le type de fichier
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime_type = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);
            
            if (!in_array($mime_type, $allowed_types)) {
                echo json_encode(['success' => false, 'message' => 'Type de fichier non autorisé. Utilisez JPG, PNG, GIF ou WEBP']);
                exit();
            }
            
            // Vérifier la taille (max 5MB)
            if ($file['size'] > 5 * 1024 * 1024) {
                echo json_encode(['success' => false, 'message' => 'Le fichier est trop volumineux (max 5MB)']);
                exit();
            }
            
            // Créer le dossier uploads s'il n'existe pas
            $upload_dir = 'uploads/avatars/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            // Générer un nom de fichier unique
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = 'user_' . $_SESSION['user_id'] . '_' . time() . '.' . $extension;
            $filepath = $upload_dir . $filename;
            
            // Déplacer le fichier uploadé
            if (!move_uploaded_file($file['tmp_name'], $filepath)) {
                echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'enregistrement du fichier']);
                exit();
            }
            
            // Supprimer l'ancien avatar custom si existe
            $stmt = $pdo->prepare("SELECT profile_picture, avatar_type FROM users WHERE id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $old_user = $stmt->fetch();
            
            if ($old_user['avatar_type'] === 'custom' && file_exists($old_user['profile_picture'])) {
                unlink($old_user['profile_picture']);
            }
            
            // Mettre à jour la base de données
            $stmt = $pdo->prepare("
                UPDATE users 
                SET profile_picture = ?, avatar_type = 'custom'
                WHERE id = ?
            ");
            $stmt->execute([$filepath, $_SESSION['user_id']]);
            
            echo json_encode([
                'success' => true,
                'message' => 'Photo de profil mise à jour',
                'avatar_path' => $filepath
            ]);
            break;
            
        case 'delete_account':
            if ($method !== 'POST') {
                echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
                exit();
            }
            
            $data = json_decode(file_get_contents('php://input'), true);
            $password = $data['password'] ?? '';
            
            if (empty($password)) {
                echo json_encode(['success' => false, 'message' => 'Mot de passe requis']);
                exit();
            }
            
            // Vérifier le mot de passe
            $stmt = $pdo->prepare("SELECT password_hash FROM users WHERE id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $user = $stmt->fetch();
            
            if (!password_verify($password, $user['password_hash'])) {
                echo json_encode(['success' => false, 'message' => 'Mot de passe incorrect']);
                exit();
            }
            
            // Supprimer le compte
            $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            
            // Détruire la session
            session_destroy();
            
            echo json_encode(['success' => true, 'message' => 'Compte supprimé']);
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Action non reconnue']);
            break;
    }
    
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Erreur serveur',
        'error' => $e->getMessage()
    ]);
}
?>

