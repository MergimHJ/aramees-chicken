<?php
require_once '../config.php';

if (estConnecte()) {
    header('Location: index.php');
    exit();
}

$erreur = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = securiser($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $erreur = 'Veuillez remplir tous les champs';
    } else {
        try {
            $db = getDB();
            $stmt = $db->prepare("SELECT * FROM admins WHERE username = ? AND actif = 1");
            $stmt->execute([$username]);
            $admin = $stmt->fetch();
            
            if ($admin && password_verify($password, $admin['password_hash'])) {
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_username'] = $admin['username'];
                $_SESSION['admin_nom'] = $admin['nom_complet'];
                
                $stmt = $db->prepare("UPDATE admins SET dernier_login = NOW() WHERE id = ?");
                $stmt->execute([$admin['id']]);
                
                header('Location: index.php');
                exit();
            } else {
                $erreur = 'Identifiants incorrects';
            }
        } catch (Exception $e) {
            $erreur = 'Erreur de connexion';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion Admin - Aramees Chicken</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #c41e3a, #2c5530);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .login-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.3);
            padding: 3rem;
            width: 100%;
            max-width: 400px;
        }
        
        .logo {
            text-align: center;
            font-size: 3rem;
            margin-bottom: 1rem;
        }
        
        h1 {
            text-align: center;
            color: #c41e3a;
            margin-bottom: 2rem;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        label {
            display: block;
            margin-bottom: 0.5rem;
            color: #333;
            font-weight: 500;
        }
        
        input {
            width: 100%;
            padding: 0.8rem;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }
        
        input:focus {
            outline: none;
            border-color: #c41e3a;
        }
        
        .btn-login {
            width: 100%;
            padding: 1rem;
            background: #c41e3a;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s;
        }
        
        .btn-login:hover {
            background: #a01830;
        }
        
        .erreur {
            background: #fee;
            color: #c41e3a;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            border-left: 4px solid #c41e3a;
        }
        
        .info {
            text-align: center;
            margin-top: 1.5rem;
            padding: 1rem;
            background: #f0f0f0;
            border-radius: 8px;
            font-size: 0.9rem;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo">🍗</div>
        <h1>Administration<br>Aramees Chicken</h1>
        
        <?php if ($erreur): ?>
            <div class="erreur"><?php echo $erreur; ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="username">Nom d'utilisateur</label>
                <input type="text" id="username" name="username" required autofocus>
            </div>
            
            <div class="form-group">
                <label for="password">Mot de passe</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <button type="submit" class="btn-login">Se connecter</button>
        </form>
        
        <div class="info">
            <strong>Identifiants par défaut :</strong><br>
            Username: admin<br>
            Password: admin123<br>
            <em>(À changer après la première connexion)</em>
        </div>
    </div>
</body>
</html>