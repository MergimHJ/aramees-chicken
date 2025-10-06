<?php
require_once '../config.php';

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=utf-8');

try {
    $db = getDB();
    
    $category = isset($_GET['category']) ? securiser($_GET['category']) : null;
    
    $sql = "SELECT p.*, c.nom as categorie_nom, c.slug as categorie_slug 
            FROM produits p 
            INNER JOIN categories c ON p.category_id = c.id 
            WHERE p.actif = 1 AND c.actif = 1";
    
    if ($category && $category !== 'tous') {
        $sql .= " AND c.slug = :category";
    }
    
    $sql .= " ORDER BY c.ordre, p.ordre, p.nom";
    
    $stmt = $db->prepare($sql);
    
    if ($category && $category !== 'tous') {
        $stmt->bindParam(':category', $category);
    }
    
    $stmt->execute();
    $produits = $stmt->fetchAll();
    
    $stmtCat = $db->query("SELECT * FROM categories WHERE actif = 1 ORDER BY ordre");
    $categories = $stmtCat->fetchAll();
    
    $menu = [];
    foreach ($produits as $produit) {
        $menu[] = [
            'id' => (int)$produit['id'],
            'nom' => $produit['nom'],
            'description' => $produit['description'],
            'prix' => (float)$produit['prix'],
            'category' => $produit['categorie_slug'],
            'categorie_nom' => $produit['categorie_nom'],
            'emoji' => $produit['emoji'],
            'image' => $produit['image'],
            'stock_disponible' => (bool)$produit['stock_disponible']
        ];
    }
    
    $response = [
        'success' => true,
        'produits' => $menu,
        'categories' => $categories,
        'total' => count($menu)
    ];
    
    envoyerJSON($response);
    
} catch (Exception $e) {
    envoyerJSON([
        'success' => false,
        'message' => 'Erreur lors de la récupération du menu',
        'error' => $e->getMessage()
    ], 500);
}
?>