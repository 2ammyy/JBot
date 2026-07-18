<?php
require_once 'email.php';


$username = isset($_GET['username']) ? $_GET['username'] : '';


$servername = "localhost";
$db_username = "root";
$password = "";
$dbname = "java";

try {
    $bdd = new PDO("mysql:host=$servername;dbname=$dbname", $db_username, $password);
    $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  
    $stmt = $bdd->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->execute(['username' => $username]);

    if ($stmt->rowCount() > 0) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

       
        $email = $user['email'];
        $otpCode = rand(100000, 999999); 

       
        $updateStmt = $bdd->prepare("UPDATE users SET verification_code = :otpCode WHERE username = :username");
        $updateStmt->execute([
            'otpCode' => $otpCode,
            'username' => $username
        ]);

        sendVerificationEmail($email, $username, $otpCode);


    }
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
?>


<p>Un email de vérification a été envoyé à l'adresse fournie.</p>
