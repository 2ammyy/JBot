<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "java";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $code = $_POST['code'];

    try {
        $bdd = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
        $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $bdd->prepare("SELECT * FROM users WHERE email = :email AND verification_code = :code");
        $stmt->execute([
            'email' => $email,
            'code' => $code
        ]);

        if ($stmt->rowCount() > 0) {
            $update = $bdd->prepare("UPDATE users SET verification_code = NULL WHERE email = :email");
            $update->execute(['email' => $email]);

            echo "✅ Vérification réussie. Votre compte est activé.";
        } else {
            echo "❌ Code incorrect. Veuillez réessayer.";
        }
    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
    }
}
?>
<!Doctype html>
<html>
<body><a href="chatbot.php">
    <button style="margin-top: 15px; padding: 10px 20px; background-color: #6A5ACD; color: white; border: none; border-radius: 8px; cursor: pointer;">
        Continuer
    </button></a>
</body>
</html>
