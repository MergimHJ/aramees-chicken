<?php
require_once '../config.php';
verifierConnexion();

$db = getDB();
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id === 0) {
    echo '<p style="color: red;">ID de commande invalide</p>';
    exit();
}

// Récupérer la commande
$stmt = $db->prepare("SELECT * FROM commandes WHERE id = ?");
$stmt->execute([$id]);
$commande = $stmt->fetch();

if (!$commande) {
    echo '<p style="color: red;">Commande introuvable</p>';
    exit();
}

// Récupérer les items de la commande
$stmt = $db->prepare("SELECT * FROM commande_items WHERE commande_id = ?");
$stmt->execute([$id]);
$items = $stmt->fetchAll();
?>

<style>
    .detail-section {
        margin-bottom: 1.5rem;
    }
    
    .detail-section h3 {
        color: #c41e3a;
        margin-bottom: 0.8rem;
        font-size: 1.1rem;
        border-bottom: 2px solid #f0f0f0;
        padding-bottom: 0.5rem;
    }
    
    .info-grid {
        display: grid;
        grid-template-columns: auto 1fr;
        gap: 0.5rem 1rem;
        margin-bottom: 1rem;
    }
    
    .info-label {
        font-weight: 600;
        color: #666;
    }
    
    .info-value {
        color: #333;
    }
    
    .items-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 0.5rem;
    }
    
    .items-table th {
        background: #f8f8f8;
        padding: 0.8rem;
        text-align: left;
        font-weight: 600;
        border-bottom: 2px solid #ddd;
    }
    
    .items-table td {
        padding: 0.8rem;
        border-bottom: 1px solid #eee;
    }
    
    .total-row {
        background: #f8f8f8;
        font-weight: bold;
    }
    
    .status-badge {
        display: inline-block;
        padding: 0.4rem 1rem;
        border-radius: 20px;
        font-size: 0.9rem;
        font-weight: bold;
    }
    
    .status-en_attente {
        background: #fff3cd;
        color: #856404;
    }
    
    .status-en_preparation {
        background: #cce5ff;
        color: #004085;
    }
    
    .status-prete {
        background: #d4edda;
        color: #155724;
    }
    
    .status-terminee {
        background: #d1ecf1;
        color: #0c5460;
    }
    
    .status-annulee {
        background: #f8d7da;
        color: #721c24;
    }
</style>

<div class="detail-section">
    <h3>Informations générales</h3>
    <div class="info-grid">
        <span class="info-label">Numéro :</span>
        <span class="info-value"><strong><?php echo htmlspecialchars($commande['numero_commande']); ?></strong></span>
        
        <span class="info-label">Date :</span>
        <span class="info-value"><?php echo date('d/m/Y à H:i', strtotime($commande['created_at'])); ?></span>
        
        <span class="info-label">Statut :</span>
        <span class="info-value">
            <span class="status-badge status-<?php echo $commande['statut']; ?>">
                <?php echo ucfirst(str_replace('_', ' ', $commande['statut'])); ?>
            </span>
        </span>
        
        <span class="info-label">Type :</span>
        <span class="info-value">
            <?php echo $commande['type_commande'] === 'emporter' ? '🥡 À emporter' : '🍽️ Sur place'; ?>
        </span>
    </div>
</div>

<div class="detail-section">
    <h3>Informations client</h3>
    <div class="info-grid">
        <span class="info-label">Nom :</span>
        <span class="info-value"><?php echo htmlspecialchars($commande['client_nom']); ?></span>
        
        <span class="info-label">Email :</span>
        <span class="info-value">
            <a href="mailto:<?php echo htmlspecialchars($commande['client_email']); ?>">
                <?php echo htmlspecialchars($commande['client_email']); ?>
            </a>
        </span>
        
        <span class="info-label">Téléphone :</span>
        <span class="info-value">
            <a href="tel:<?php echo htmlspecialchars($commande['client_telephone']); ?>">
                <?php echo htmlspecialchars($commande['client_telephone']); ?>
            </a>
        </span>
    </div>
</div>

<?php if ($commande['notes']): ?>
<div class="detail-section">
    <h3>Notes</h3>
    <p style="background: #fff3cd; padding: 1rem; border-radius: 8px; color: #856404;">
        <?php echo nl2br(htmlspecialchars($commande['notes'])); ?>
    </p>
</div>
<?php endif; ?>

<div class="detail-section">
    <h3>Détail des articles</h3>
    <table class="items-table">
        <thead>
            <tr>
                <th>Article</th>
                <th style="text-align: center;">Quantité</th>
                <th style="text-align: right;">Prix unitaire</th>
                <th style="text-align: right;">Total</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $item): ?>
                <tr>
                    <td><?php echo htmlspecialchars($item['nom_produit']); ?></td>
                    <td style="text-align: center;"><?php echo $item['quantite']; ?></td>
                    <td style="text-align: right;"><?php echo number_format($item['prix_unitaire'], 2, ',', ' '); ?> €</td>
                    <td style="text-align: right;"><strong><?php echo number_format($item['prix_total'], 2, ',', ' '); ?> €</strong></td>
                </tr>
            <?php endforeach; ?>
            <tr class="total-row">
                <td colspan="3" style="text-align: right;">TOTAL</td>
                <td style="text-align: right; font-size: 1.2rem; color: #c41e3a;">
                    <?php echo formaterPrix($commande['montant_total']); ?>
                </td>
            </tr>
        </tbody>
    </table>
</div>

<div style="margin-top: 1.5rem; padding-top: 1rem; border-top: 2px solid #f0f0f0; color: #666; font-size: 0.9rem;">
    <p><strong>Dernière mise à jour :</strong> <?php echo date('d/m/Y à H:i', strtotime($commande['updated_at'])); ?></p>
</div>