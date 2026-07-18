
<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: admin_dashboard.php");
    exit;
}

$id = (int)$_GET['id'];

try {
    $bdd = new PDO("mysql:host=localhost;dbname=java", "root", "");
    $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $bdd->prepare("DELETE FROM users WHERE id = :id");
    $stmt->execute(['id' => $id]);

    header("Location: admin_dashboard.php");
    exit;

} catch (PDOException $e) {
    die("Erreur DB : " . $e->getMessage());
}
