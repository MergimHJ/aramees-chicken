<?php
/**
 * Dashboard administrateur
 */

require_once '../config.php';
verifierConnexion();

$db = getDB();

// Statistiques du jour
$aujourdhui = date('Y-m-d');
$stmt = $db->prepare("SELECT COUNT(*) as total, COALESCE(SUM(montant_total), 0) as chiffre 
                      FROM commandes 
                      WHERE DATE(created_at) = ?");
$stmt->execute([$aujourdhui]);
$statsJour = $stmt->fetch();

// Commandes en attente
$stmt = $db->query("SELECT COUNT(*) as total FROM commandes WHERE statut = 'en_attente'");
$commandesAttente = $stmt->fetch()['total'];

// Dernières commandes
$stmt = $db->query("SELECT * FROM commandes ORDER BY created_at DESC LIMIT 10");
$dernieresCommandes = $stmt->fetchAll();

// Nombre de produits actifs
$stmt = $db->query("SELECT COUNT(*) as total FROM produits WHERE actif = 1");
$produitsActifs = $stmt->fetch()['total'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Aramees Chicken</title>
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
        
        .btn-logout:hover {
            background: #a01830;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .stat-card h3 {
            color: #666;
            font-size: 0.9rem;
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
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th {
            background: #f8f8f8;
            padding: 1rem;
            text-align: left;
            font-weight: 600;
            color: #333;
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
        
        .badge-attente {
            background: #fff3cd;
            color: #856404;
        }
        
        .badge-preparation {
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
        
        .btn {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 500;
            text-decoration: none;
            display: inline-block;
            margin-right: 0.5rem;
        }
        
        .btn-primary {
            background: #2c5530;
            color: white;
        }
        
        .btn-primary:hover {
            background: #1e3d22;
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #545b62;
        }
        
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                position: relative;
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .header {
                flex-direction: column;
                gap: 1rem;
            }
        }
    </style>
</head>
<body>
    <aside class="sidebar">
        <div class="logo-admin">🍗</div>
        <h2>Aramees Chicken<br>Admin</h2>
        <ul class="menu">
            <li><a href="index.php" class="active">📊 Dashboard</a></li>
            <li><a href="commandes.php">📦 Commandes</a></li>
            <li><a href="produits.php">🍽️ Produits</a></li>
            <li><a href="categories.php">📂 Catégories</a></li>
            <li><a href="parametres.php">⚙️ Paramètres</a></li>
        </ul>
    </aside>
    
    <main class="main-content">
        <div class="header">
            <h1>Dashboard</h1>
            <div>
                <span>Bonjour, <?php echo htmlspecialchars($_SESSION['admin_nom'] ?? $_SESSION['admin_username']); ?></span>
                <a href="logout.php" class="btn-logout">Déconnexion</a>
            </div>
        </div>
        
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Commandes aujourd'hui</h3>
                <div class="stat-value"><?php echo $statsJour['total']; ?></div>
            </div>
            
            <div class="stat-card">
                <h3>Chiffre d'affaires du jour</h3>
                <div class="stat-value"><?php echo formaterPrix($statsJour['chiffre']); ?></div>
            </div>
            
            <div class="stat-card">
                <h3>Commandes en attente</h3>
                <div class="stat-value"><?php echo $commandesAttente; ?></div>
            </div>
            
            <div class="stat-card">
                <h3>Produits actifs</h3>
                <div class="stat-value"><?php echo $produitsActifs; ?></div>
            </div>
        </div>
        
        <div class="card">
            <h2>Dernières commandes</h2>
            <?php if (count($dernieresCommandes) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>N° Commande</th>
                            <th>Client</th>
                            <th>Montant</th>
                            <th>Statut</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($dernieresCommandes as $commande): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($commande['numero_commande']); ?></strong></td>
                                <td><?php echo htmlspecialchars($commande['client_nom']); ?></td>
                                <td><?php echo formaterPrix($commande['montant_total']); ?></td>
                                <td>
                                    <span class="badge badge-<?php echo $commande['statut']; ?>">
                                        <?php echo ucfirst(str_replace('_', ' ', $commande['statut'])); ?>
                                    </span>
                                </td>
                                <td><?php echo date('d/m/Y H:i', strtotime($commande['created_at'])); ?></td>
                                <td>
                                    <a href="commande-details.php?id=<?php echo $commande['id']; ?>" class="btn btn-primary">Voir</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p style="text-align: center; color: #666; padding: 2rem;">Aucune commande pour le moment</p>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>