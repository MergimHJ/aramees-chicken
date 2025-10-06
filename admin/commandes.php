<?php
require_once '../config.php';
verifierConnexion();

$db = getDB();
$message = '';

// Traitement des actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'changer_statut') {
        $id = intval($_POST['id']);
        $statut = securiser($_POST['statut']);
        
        try {
            $stmt = $db->prepare("UPDATE commandes SET statut = ? WHERE id = ?");
            $stmt->execute([$statut, $id]);
            $message = "Statut mis à jour avec succès !";
        } catch (Exception $e) {
            $message = "Erreur : " . $e->getMessage();
        }
    }
}

// Filtres
$filtre_statut = isset($_GET['statut']) ? securiser($_GET['statut']) : '';
$filtre_date = isset($_GET['date']) ? securiser($_GET['date']) : '';

// Construction de la requête
$sql = "SELECT * FROM commandes WHERE 1=1";
$params = [];

if ($filtre_statut) {
    $sql .= " AND statut = ?";
    $params[] = $filtre_statut;
}

if ($filtre_date) {
    $sql .= " AND DATE(created_at) = ?";
    $params[] = $filtre_date;
}

$sql .= " ORDER BY created_at DESC";

$stmt = $db->prepare($sql);
$stmt->execute($params);
$commandes = $stmt->fetchAll();

// Statistiques
$stats = [
    'en_attente' => 0,
    'en_preparation' => 0,
    'prete' => 0,
    'terminee' => 0,
    'annulee' => 0
];

$stmt = $db->query("SELECT statut, COUNT(*) as total FROM commandes GROUP BY statut");
while ($row = $stmt->fetch()) {
    $stats[$row['statut']] = $row['total'];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Commandes - Aramees Chicken</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
        }
        
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            bottom: 0;
            width: 250px;
            background: linear-gradient(180deg, #c41e3a, #2c5530);
            color: white;
            padding: 2rem 0;
            overflow-y: auto;
        }
        
        .logo-admin {
            text-align: center;
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
        }
        
        .sidebar h2 {
            text-align: center;
            margin-bottom: 2rem;
            font-size: 1.2rem;
        }
        
        .menu {
            list-style: none;
        }
        
        .menu li {
            margin-bottom: 0.5rem;
        }
        
        .menu a {
            display: block;
            padding: 1rem 2rem;
            color: white;
            text-decoration: none;
            transition: background 0.3s;
        }
        
        .menu a:hover,
        .menu a.active {
            background: rgba(255,255,255,0.2);
        }
        
        .main-content {
            margin-left: 250px;
            padding: 2rem;
        }
        
        .header {
            background: white;
            padding: 1.5rem 2rem;
            border-radius: 10px;
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .header h1 {
            color: #c41e3a;
        }
        
        .btn-logout {
            background: #c41e3a;
            color: white;
            border: none;
            padding: 0.7rem 1.5rem;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
            text-decoration: none;
            display: inline-block;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        .stat-card h3 {
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 0.5rem;
        }
        
        .stat-value {
            font-size: 2rem;
            font-weight: bold;
            color: #c41e3a;
        }
        
        .card {
            background: white;
            border-radius: 10px;
            padding: 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }
        
        .card h2 {
            color: #c41e3a;
            margin-bottom: 1.5rem;
        }
        
        .filters {
            display: flex;
            gap: 1rem;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
        }
        
        .filters select,
        .filters input {
            padding: 0.7rem;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 1rem;
        }
        
        .filters button {
            padding: 0.7rem 1.5rem;
            background: #2c5530;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th {
            background: #f8f8f8;
            padding: 1rem;
            text-align: left;
            font-weight: 600;
        }
        
        td {
            padding: 1rem;
            border-bottom: 1px solid #eee;
        }
        
        tr:hover {
            background: #f9f9f9;
        }
        
        .badge {
            display: inline-block;
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: bold;
        }
        
        .badge-en_attente {
            background: #fff3cd;
            color: #856404;
        }
        
        .badge-en_preparation {
            background: #cce5ff;
            color: #004085;
        }
        
        .badge-prete {
            background: #d4edda;
            color: #155724;
        }
        
        .badge-terminee {
            background: #d1ecf1;
            color: #0c5460;
        }
        
        .badge-annulee {
            background: #f8d7da;
            color: #721c24;
        }
        
        .actions {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }
        
        .btn {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 500;
            text-decoration: none;
            display: inline-block;
            font-size: 0.9rem;
        }
        
        .btn-primary {
            background: #2c5530;
            color: white;
        }
        
        .btn-warning {
            background: #f4a460;
            color: #333;
        }
        
        .btn-success {
            background: #28a745;
            color: white;
        }
        
        .btn-info {
            background: #17a2b8;
            color: white;
        }
        
        .message {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            background: #d4edda;
            color: #155724;
            border-left: 4px solid #28a745;
        }
        
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.7);
            z-index: 3000;
            align-items: center;
            justify-content: center;
        }
        
        .modal.active {
            display: flex;
        }
        
        .modal-content {
            background: white;
            border-radius: 10px;
            max-width: 500px;
            width: 90%;
            padding: 2rem;
        }
        
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 1rem;
        }
        
        .close-modal {
            background: none;
            border: none;
            font-size: 2rem;
            cursor: pointer;
        }
        
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                position: relative;
            }
            
            .main-content {
                margin-left: 0;
            }
            
            table {
                font-size: 0.85rem;
            }
            
            .actions {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <aside class="sidebar">
        <div class="logo-admin">🍗</div>
        <h2>Aramees Chicken<br>Admin</h2>
        <ul class="menu">
            <li><a href="index.php">📊 Dashboard</a></li>
            <li><a href="commandes.php" class="active">📦 Commandes</a></li>
            <li><a href="produits.php">🍽️ Produits</a></li>
            <li><a href="categories.php">📂 Catégories</a></li>
            <li><a href="parametres.php">⚙️ Paramètres</a></li>
        </ul>
    </aside>
    
    <main class="main-content">
        <div class="header">
            <h1>Gestion des Commandes</h1>
            <a href="logout.php" class="btn-logout">Déconnexion</a>
        </div>
        
        <?php if ($message): ?>
            <div class="message"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        
        <div class="stats-grid">
            <div class="stat-card">
                <h3>En attente</h3>
                <div class="stat-value"><?php echo $stats['en_attente']; ?></div>
            </div>
            <div class="stat-card">
                <h3>En préparation</h3>
                <div class="stat-value"><?php echo $stats['en_preparation']; ?></div>
            </div>
            <div class="stat-card">
                <h3>Prêtes</h3>
                <div class="stat-value"><?php echo $stats['prete']; ?></div>
            </div>
            <div class="stat-card">
                <h3>Terminées</h3>
                <div class="stat-value"><?php echo $stats['terminee']; ?></div>
            </div>
            <div class="stat-card">
                <h3>Annulées</h3>
                <div class="stat-value"><?php echo $stats['annulee']; ?></div>
            </div>
        </div>
        
        <div class="card">
            <h2>Filtres</h2>
            <form method="GET" class="filters">
                <select name="statut">
                    <option value="">Tous les statuts</option>
                    <option value="en_attente" <?php echo $filtre_statut === 'en_attente' ? 'selected' : ''; ?>>En attente</option>
                    <option value="en_preparation" <?php echo $filtre_statut === 'en_preparation' ? 'selected' : ''; ?>>En préparation</option>
                    <option value="prete" <?php echo $filtre_statut === 'prete' ? 'selected' : ''; ?>>Prête</option>
                    <option value="terminee" <?php echo $filtre_statut === 'terminee' ? 'selected' : ''; ?>>Terminée</option>
                    <option value="annulee" <?php echo $filtre_statut === 'annulee' ? 'selected' : ''; ?>>Annulée</option>
                </select>
                
                <input type="date" name="date" value="<?php echo htmlspecialchars($filtre_date); ?>">
                
                <button type="submit">Filtrer</button>
                <a href="commandes.php" class="btn btn-warning">Réinitialiser</a>
            </form>
        </div>
        
        <div class="card">
            <h2>Liste des commandes (<?php echo count($commandes); ?>)</h2>
            <?php if (count($commandes) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>N° Commande</th>
                            <th>Client</th>
                            <th>Contact</th>
                            <th>Type</th>
                            <th>Montant</th>
                            <th>Statut</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($commandes as $cmd): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($cmd['numero_commande']); ?></strong></td>
                                <td><?php echo htmlspecialchars($cmd['client_nom']); ?></td>
                                <td>
                                    📧 <?php echo htmlspecialchars($cmd['client_email']); ?><br>
                                    📞 <?php echo htmlspecialchars($cmd['client_telephone']); ?>
                                </td>
                                <td><?php echo $cmd['type_commande'] === 'emporter' ? '🥡 À emporter' : '🍽️ Sur place'; ?></td>
                                <td><?php echo formaterPrix($cmd['montant_total']); ?></td>
                                <td>
                                    <span class="badge badge-<?php echo $cmd['statut']; ?>">
                                        <?php echo ucfirst(str_replace('_', ' ', $cmd['statut'])); ?>
                                    </span>
                                </td>
                                <td><?php echo date('d/m/Y H:i', strtotime($cmd['created_at'])); ?></td>
                                <td class="actions">
                                    <button class="btn btn-info" onclick="voirDetails(<?php echo $cmd['id']; ?>)">Détails</button>
                                    <button class="btn btn-primary" onclick="changerStatut(<?php echo $cmd['id']; ?>, '<?php echo $cmd['statut']; ?>')">Statut</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p style="text-align: center; color: #666; padding: 2rem;">Aucune commande trouvée</p>
            <?php endif; ?>
        </div>
    </main>
    
    <!-- Modal Statut -->
    <div class="modal" id="modalStatut">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Changer le statut</h2>
                <button class="close-modal" onclick="fermerModal('modalStatut')">×</button>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="changer_statut">
                <input type="hidden" name="id" id="statut_id">
                
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Nouveau statut :</label>
                    <select name="statut" id="statut_select" style="width: 100%; padding: 0.8rem; border: 2px solid #ddd; border-radius: 8px; font-size: 1rem;">
                        <option value="en_attente">En attente</option>
                        <option value="en_preparation">En préparation</option>
                        <option value="prete">Prête</option>
                        <option value="terminee">Terminée</option>
                        <option value="annulee">Annulée</option>
                    </select>
                </div>
                
                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 1rem;">Mettre à jour</button>
            </form>
        </div>
    </div>
    
    <!-- Modal Détails -->
    <div class="modal" id="modalDetails">
        <div class="modal-content" style="max-width: 700px;">
            <div class="modal-header">
                <h2>Détails de la commande</h2>
                <button class="close-modal" onclick="fermerModal('modalDetails')">×</button>
            </div>
            <div id="detailsContent">Chargement...</div>
        </div>
    </div>

    <script>
        function changerStatut(id, statutActuel) {
            document.getElementById('statut_id').value = id;
            document.getElementById('statut_select').value = statutActuel;
            document.getElementById('modalStatut').classList.add('active');
        }
        
        function fermerModal(modalId) {
            document.getElementById(modalId).classList.remove('active');
        }
        
        async function voirDetails(id) {
            document.getElementById('modalDetails').classList.add('active');
            
            try {
                const response = await fetch(`commande-details-ajax.php?id=${id}`);
                const html = await response.text();
                document.getElementById('detailsContent').innerHTML = html;
            } catch (error) {
                document.getElementById('detailsContent').innerHTML = '<p style="color: red;">Erreur lors du chargement des détails</p>';
            }
        }
        
        // Fermer les modals en cliquant à l'extérieur
        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.classList.remove('active');
            }
        }
        
        // Auto-refresh toutes les 30 secondes pour les nouvelles commandes
        setInterval(() => {
            location.reload();
        }, 30000);
    </script>
</body>
</html>