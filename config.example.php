<?php
/**
 * Configuration EXEMPLE - Copiez en config.php et modifiez
 */

define('DB_HOST', 'localhost');
define('DB_NAME', 'aramees_chicken');
define('DB_USER', 'root');  // À remplacer
define('DB_PASS', '');      // À remplacer
define('DB_CHARSET', 'utf8mb4');
define('SITE_URL', 'http://localhost/arameeschicken');
define('SITE_NAME', 'Aramees Chicken');
define('SECRET_KEY', 'CHANGEZ-CETTE-CLE');

date_default_timezone_set('Europe/Brussels');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function getDB() {
    static $pdo = null;
    if ($pdo === null) {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            die("Erreur de connexion : " . $e->getMessage());
        }
    }
    return $pdo;
}

function securiser($data) {
    return htmlspecialchars(trim(stripslashes($data)), ENT_QUOTES, 'UTF-8');
}

function estConnecte() {
    return isset($_SESSION['admin_id']) && isset($_SESSION['admin_username']);
}

function verifierConnexion() {
    if (!estConnecte()) {
        header('Location: login.php');
        exit();
    }
}

function deconnecter() {
    session_destroy();
    header('Location: login.php');
    exit();
}

function genererNumeroCommande() {
    return 'AC' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
}

function formaterPrix($prix) {
    return number_format($prix, 2, ',', ' ') . ' €';
}

function envoyerJSON($data, $code = 200) {
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit();
}

function getParametres() {
    $db = getDB();
    $stmt = $db->query("SELECT cle, valeur FROM parametres");
    $params = [];
    while ($row = $stmt->fetch()) {
        $params[$row['cle']] = $row['valeur'];
    }
    return $params;
}
?>