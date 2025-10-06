<?php
require_once '../config.php';

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    envoyerJSON(['success' => false, 'message' => 'Méthode non autorisée'], 405);
}

try {
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
    
    if (!$data) {
        envoyerJSON(['success' => false, 'message' => 'Données invalides'], 400);
    }
    
    $erreurs = [];
    
    if (empty($data['client_nom'])) {
        $erreurs[] = 'Le nom est requis';
    }
    
    if (empty($data['client_email']) || !filter_var($data['client_email'], FILTER_VALIDATE_EMAIL)) {
        $erreurs[] = 'Email invalide';
    }
    
    if (empty($data['client_telephone'])) {
        $erreurs[] = 'Le téléphone est requis';
    }
    
    if (empty($data['items']) || !is_array($data['items']) || count($data['items']) === 0) {
        $erreurs[] = 'Le panier est vide';
    }
    
    if (!empty($erreurs)) {
        envoyerJSON(['success' => false, 'message' => 'Validation échouée', 'erreurs' => $erreurs], 400);
    }
    
    $db = getDB();
    $db->beginTransaction();
    
    try {
        $montantTotal = 0;
        $itemsValides = [];
        
        foreach ($data['items'] as $item) {
            $stmt = $db->prepare("SELECT * FROM produits WHERE id = ? AND actif = 1 AND stock_disponible = 1");
            $stmt->execute([$item['id']]);
            $produit = $stmt->fetch();
            
            if (!$produit) {
                throw new Exception("Produit indisponible: " . ($item['nom'] ?? 'inconnu'));
            }
            
            $quantite = (int)$item['quantite'];
            $prixUnitaire = (float)$produit['prix'];
            $prixTotal = $prixUnitaire * $quantite;
            
            $itemsValides[] = [
                'produit_id' => $produit['id'],
                'nom' => $produit['nom'],
                'prix_unitaire' => $prixUnitaire,
                'quantite' => $quantite,
                'prix_total' => $prixTotal
            ];
            
            $montantTotal += $prixTotal;
        }
        
        $numeroCommande = genererNumeroCommande();
        $typeCommande = isset($data['type_commande']) ? securiser($data['type_commande']) : 'emporter';
        $notes = isset($data['notes']) ? securiser($data['notes']) : null;
        
        $stmt = $db->prepare("
            INSERT INTO commandes 
            (numero_commande, client_nom, client_email, client_telephone, type_commande, montant_total, notes, statut) 
            VALUES 
            (?, ?, ?, ?, ?, ?, ?, 'en_attente')
        ");
        
        $stmt->execute([
            $numeroCommande,
            securiser($data['client_nom']),
            securiser($data['client_email']),
            securiser($data['client_telephone']),
            $typeCommande,
            $montantTotal,
            $notes
        ]);
        
        $commandeId = $db->lastInsertId();
        
        $stmt = $db->prepare("
            INSERT INTO commande_items 
            (commande_id, produit_id, nom_produit, prix_unitaire, quantite, prix_total) 
            VALUES 
            (?, ?, ?, ?, ?, ?)
        ");
        
        foreach ($itemsValides as $item) {
            $stmt->execute([
                $commandeId,
                $item['produit_id'],
                $item['nom'],
                $item['prix_unitaire'],
                $item['quantite'],
                $item['prix_total']
            ]);
        }
        
        $db->commit();
        
        envoyerJSON([
            'success' => true,
            'message' => 'Commande créée avec succès',
            'commande' => [
                'id' => $commandeId,
                'numero' => $numeroCommande,
                'montant_total' => $montantTotal,
                'statut' => 'en_attente'
            ]
        ], 201);
        
    } catch (Exception $e) {
        $db->rollBack();
        throw $e;
    }
    
} catch (Exception $e) {
    envoyerJSON([
        'success' => false,
        'message' => 'Erreur lors de la création de la commande',
        'error' => $e->getMessage()
    ], 500);
}
?>