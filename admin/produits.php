<?php
require_once '../config.php';
verifierConnexion();

$db = getDB();
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'ajouter' || $action === 'modifier') {
        $id = $_POST['id'] ?? null;
        $nom = securiser($_POST['nom']);
        $description = securiser($_POST['description']);
        $prix = floatval($_POST['prix']);
        $category_id = intval($_POST['category_id']);
        $emoji = securiser($_POST['emoji']);
        $actif = isset($_POST['actif']) ? 1 : 0;
        $stock_disponible = isset($_POST['stock_disponible']) ? 1 : 0;
        
        try {
            if ($action === 'ajouter') {
                $stmt = $db->prepare("INSERT INTO produits (nom, description, prix, category_id, emoji, actif, stock_disponible) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$nom, $description, $prix, $category_id, $emoji, $actif, $stock_disponible]);
                $message = "Produit ajouté avec succès !";
            } else {
                $stmt = $db->prepare("UPDATE produits SET nom=?, description=?, prix=?, category_id=?, emoji=?, actif=?, stock_disponible=? WHERE id=?");
                $stmt->execute([$nom, $description, $prix, $category_id, $emoji, $actif, $stock_disponible, $id]);
                $message = "Produit modifié avec succès !";
            }
        } catch (Exception $e) {
            $message = "Erreur : " . $e->getMessage();
        }
    }
    
    if ($action === 'supprimer') {
        $id = intval($_POST['id']);
        try {
            $stmt = $db->prepare("DELETE FROM produits WHERE id = ?");
            $stmt->execute([$id]);
            $message = "Produit supprimé avec succès !";
        } catch (Exception $e) {
            $message = "Erreur : " . $e->getMessage();
        }
    }
}

$stmt = $db->query("SELECT p.*, c.nom as categorie_nom FROM produits p LEFT JOIN categories c ON p.category_id = c.id ORDER BY p.id DESC");
$produits = $stmt->fetchAll();

$stmt = $db->query("SELECT * FROM categories WHERE actif = 1 ORDER BY ordre");
$categories = $stmt->fetchAll();

$produitEdit = null;
if (isset($_GET['edit'])) {
    $stmt = $db->prepare("SELECT * FROM produits WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $produitEdit = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Produits - Aramees Chicken</title>
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
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #333;
        }
        
        input[type="text"],
        input[type="number"],
        select,
        textarea {
            width: 100%;
            padding: 0.8rem;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 1rem;
        }
        
        textarea {
            min-height: 100px;
            resize: vertical;
        }
        
        input:focus,
        select:focus,
        textarea:focus {
            outline: none;
            border-color: #c41e3a;
        }
        
        .checkbox-group {
            display: flex;
            gap: 2rem;
            align-items: center;
        }
        
        .checkbox-group label {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin: 0;
        }
        
        .checkbox-group input[type="checkbox"] {
            width: auto;
        }
        
        .btn {
            padding: 0.8rem 1.5rem;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 500;
            font-size: 1rem;
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
        
        .btn-danger {
            background: #c41e3a;
            color: white;
        }
        
        .btn-warning {
            background: #f4a460;
            color: #333;
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
        
        .actions {
            display: flex;
            gap: 0.5rem;
        }
        
        .badge {
            display: inline-block;
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: bold;
        }
        
        .badge-success {
            background: #d4edda;
            color: #155724;
        }
        
        .badge-danger {
            background: #f8d7da;
            color: #721c24;
        }
        
        .message {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            background: #d4edda;
            color: #155724;
            border-left: 4px solid #28a745;
        }
        
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                position: relative;
            }
            
            .main-content {
                margin-left: 0;
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
            <li><a href="commandes.php">📦 Commandes</a></li>
            <li><a href="produits.php" class="active">🍽️ Produits</a></li>
            <li><a href="categories.php">📂 Catégories</a></li>
            <li><a href="parametres.php">⚙️ Paramètres</a></li>
        </ul>
    </aside>
    
    <main class="main-content">
        <div class="header">
            <h1>Gestion des Produits</h1>
            <a href="logout.php" class="btn-logout">Déconnexion</a>
        </div>
        
        <?php if ($message): ?>
            <div class="message"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        
        <div class="card">
            <h2><?php echo $produitEdit ? 'Modifier le produit' : 'Ajouter un produit'; ?></h2>
            <form method="POST">
                <input type="hidden" name="action" value="<?php echo $produitEdit ? 'modifier' : 'ajouter'; ?>">
                <?php if ($produitEdit): ?>
                    <input type="hidden" name="id" value="<?php echo $produitEdit['id']; ?>">
                <?php endif; ?>
                
                <div class="form-group">
                    <label for="nom">Nom du produit *</label>
                    <input type="text" id="nom" name="nom" required 
                           value="<?php echo $produitEdit ? htmlspecialchars($produitEdit['nom']) : ''; ?>">
                </div>
                
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description"><?php echo $produitEdit ? htmlspecialchars($produitEdit['description']) : ''; ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="prix">Prix (€) *</label>
                    <input type="number" id="prix" name="prix" step="0.01" required 
                           value="<?php echo $produitEdit ? $produitEdit['prix'] : ''; ?>">
                </div>
                
                <div class="form-group">
                    <label for="category_id">Catégorie *</label>
                    <select id="category_id" name="category_id" required>
                        <option value="">-- Sélectionnez --</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo $cat['id']; ?>"
                                <?php echo ($produitEdit && $produitEdit['category_id'] == $cat['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat['nom']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="emoji">Emoji</label>
                    <input type="text" id="emoji" name="emoji" maxlength="10" 
                           value="<?php echo $produitEdit ? htmlspecialchars($produitEdit['emoji']) : '🍽️'; ?>">
                </div>
                
                <div class="form-group">
                    <div class="checkbox-group">
                        <label>
                            <input type="checkbox" name="actif" 
                                <?php echo (!$produitEdit || $produitEdit['actif']) ? 'checked' : ''; ?>>
                            Actif
                        </label>
                        <label>
                            <input type="checkbox" name="stock_disponible" 
                                <?php echo (!$produitEdit || $produitEdit['stock_disponible']) ? 'checked' : ''; ?>>
                            En stock
                        </label>
                    </div>
                </div>
                
                <div style="display: flex; gap: 1rem;">
                    <button type="submit" class="btn btn-primary">
                        <?php echo $produitEdit ? 'Modifier' : 'Ajouter'; ?>
                    </button>
                    <?php if ($produitEdit): ?>
                        <a href="produits.php" class="btn btn-secondary">Annuler</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
        
        <div class="card">
            <h2>Liste des produits</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Emoji</th>
                        <th>Nom</th>
                        <th>Catégorie</th>
                        <th>Prix</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($produits as $produit): ?>
                        <tr>
                            <td><?php echo $produit['id']; ?></td>
                            <td style="font-size: 2rem;"><?php echo $produit['emoji']; ?></td>
                            <td><strong><?php echo htmlspecialchars($produit['nom']); ?></strong></td>
                            <td><?php echo htmlspecialchars($produit['categorie_nom']); ?></td>
                            <td><?php echo formaterPrix($produit['prix']); ?></td>
                            <td>
                                <span class="badge badge-<?php echo $produit['actif'] ? 'success' : 'danger'; ?>">
                                    <?php echo $produit['actif'] ? 'Actif' : 'Inactif'; ?>
                                </span>
                            </td>
                            <td class="actions">
                                <a href="produits.php?edit=<?php echo $produit['id']; ?>" class="btn btn-warning">Modifier</a>
                                <form method="POST" style="display: inline;" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce produit ?');">
                                    <input type="hidden" name="action" value="supprimer">
                                    <input type="hidden" name="id" value="<?php echo $produit['id']; ?>">
                                    <button type="submit" class="btn btn-danger">Supprimer</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>