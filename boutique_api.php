<?php

require_once 'config.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Non authentifié']);
    exit();
}

$action = $_GET['action'] ?? '';

try {
    switch ($action) {
        case 'get_categories':
            // Récupérer les catégories avec le nombre de livres
            $stmt = $pdo->query("
                SELECT 
                    c.*,
                    COUNT(b.id) as books_count
                FROM book_categories c
                LEFT JOIN books b ON c.id = b.category_id AND b.is_available = TRUE
                WHERE c.is_active = TRUE
                GROUP BY c.id
                ORDER BY c.display_order ASC
            ");
            $categories = $stmt->fetchAll();
            
            echo json_encode(['success' => true, 'categories' => $categories]);
            break;
            
        case 'get_books':
            // Récupérer les livres d'une catégorie
            $category_id = $_GET['category_id'] ?? 0;
            
            $sql = "
                SELECT 
                    b.*,
                    c.name as category_name,
                    (SELECT COUNT(*) FROM user_library WHERE book_id = b.id AND user_id = ?) as is_owned
                FROM books b
                JOIN book_categories c ON b.category_id = c.id
                WHERE b.is_available = TRUE
            ";
            
            $params = [$_SESSION['user_id']];
            
            if ($category_id) {
                $sql .= " AND b.category_id = ?";
                $params[] = $category_id;
            }
            
            $sql .= " ORDER BY b.is_featured DESC, b.created_at DESC";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $books = $stmt->fetchAll();
            
            echo json_encode(['success' => true, 'books' => $books]);
            break;
            
        case 'get_book':
            // Récupérer un livre en particulier
            $book_id = $_GET['book_id'] ?? 0;
            
            $stmt = $pdo->prepare("
                SELECT 
                    b.*,
                    c.name as category_name,
                    (SELECT COUNT(*) FROM user_library WHERE book_id = b.id AND user_id = ?) as is_owned,
                    (SELECT AVG(rating) FROM book_reviews WHERE book_id = b.id) as avg_rating,
                    (SELECT COUNT(*) FROM book_reviews WHERE book_id = b.id) as reviews_count
                FROM books b
                JOIN book_categories c ON b.category_id = c.id
                WHERE b.id = ? AND b.is_available = TRUE
            ");
            $stmt->execute([$_SESSION['user_id'], $book_id]);
            $book = $stmt->fetch();
            
            if (!$book) {
                echo json_encode(['success' => false, 'message' => 'Livre non trouvé']);
                exit();
            }
            
            echo json_encode(['success' => true, 'book' => $book]);
            break;
            
        case 'get_featured':
            $stmt = $pdo->prepare("
                SELECT 
                    b.*,
                    c.name as category_name,
                    (SELECT COUNT(*) FROM user_library WHERE book_id = b.id AND user_id = ?) as is_owned
                FROM books b
                JOIN book_categories c ON b.category_id = c.id
                WHERE b.is_featured = TRUE AND b.is_available = TRUE
                ORDER BY b.created_at DESC
                LIMIT 6
            ");
            $stmt->execute([$_SESSION['user_id']]);
            $books = $stmt->fetchAll();
            
            echo json_encode(['success' => true, 'books' => $books]);
            break;
            
        case 'check_ownership':
            // Vérifier si l'utilisateur possède déjà un livre
            $book_id = $_GET['book_id'] ?? 0;
            
            $stmt = $pdo->prepare("
                SELECT id FROM user_library 
                WHERE user_id = ? AND book_id = ?
            ");
            $stmt->execute([$_SESSION['user_id'], $book_id]);
            $owned = $stmt->fetch() ? true : false;
            
            echo json_encode(['success' => true, 'owned' => $owned]);
            break;
            
        case 'search':
            // Rechercher des livres
            $query = $_GET['q'] ?? '';
            
            if (strlen($query) < 2) {
                echo json_encode(['success' => false, 'message' => 'Requête trop courte']);
                exit();
            }
            
            $searchTerm = "%$query%";
            $stmt = $pdo->prepare("
                SELECT 
                    b.*,
                    c.name as category_name,
                    (SELECT COUNT(*) FROM user_library WHERE book_id = b.id AND user_id = ?) as is_owned
                FROM books b
                JOIN book_categories c ON b.category_id = c.id
                WHERE b.is_available = TRUE 
                AND (b.title LIKE ? OR b.author LIKE ? OR b.description LIKE ?)
                ORDER BY b.is_featured DESC, b.created_at DESC
                LIMIT 20
            ");
            $stmt->execute([$_SESSION['user_id'], $searchTerm, $searchTerm, $searchTerm]);
            $books = $stmt->fetchAll();
            
            echo json_encode(['success' => true, 'books' => $books]);
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