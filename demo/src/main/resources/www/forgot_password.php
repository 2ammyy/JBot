<?php
$success = false;
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $newPassword = $_POST["new_password"];

    if (!empty($email) && !empty($newPassword)) {
        $servername = "localhost";
        $username = "root";
        $password = "";

        try {
            $bdd = new PDO("mysql:host=$servername;dbname=java", $username, $password);
            $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $check = $bdd->prepare("SELECT * FROM users WHERE email = :email");
            $check->execute(['email' => $email]);

            if ($check->rowCount() > 0) {
                $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                $update = $bdd->prepare("UPDATE users SET password = :password WHERE email = :email");
                $update->execute([
                    'password' => $hashedPassword,
                    'email' => $email
                ]);
                $success = true;
            } else {
                $error = "❌ Adresse email non trouvée.";
            }
        } catch (PDOException $e) {
            $error = "Erreur : " . $e->getMessage();
        }
    } else {
        $error = "Veuillez remplir tous les champs.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Réinitialiser le mot de passe</title>
    <style>
        body {
            margin: 0;
            background-color: #000;
            color: white;
            font-family: 'Segoe UI', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            background-color: #121212;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.7);
            width: 400px;
            max-width: 90%;
        }

        .form-group {
            margin-bottom: 20px;
        }

        input {
            width: 100%;
            padding: 14px;
            border: none;
            border-radius: 8px;
            background-color: #1e1e1e;
            color: white;
            font-size: 16px;
        }

        input:focus {
            outline: 2px solid #5a5eff;
        }

        .btn {
            width: 100%;
            padding: 14px;
            border: none;
            background-color: #5a5eff;
            color: white;
            font-size: 16px;
            font-weight: bold;
            border-radius: 8px;
            cursor: pointer;
        }

        .btn:hover {
            background-color: #3a3bff;
        }

        .message {
            margin-top: 15px;
            font-size: 15px;
            color: <?php echo $success ? '#00ff7f' : '#ff5050'; ?>;
            text-align: center;
        }

        .back-btn {
            margin-top: 10px;
            background-color: #444;
        }
    </style>
</head>
<body>
<div class="container">
    <h2 style="text-align: center;">🔒 Mot de passe oublié</h2>

    <?php if (!$success): ?>
        <form action="forgot_password.php" method="post">
            <div class="form-group">
                <input type="email" name="email" placeholder="Votre adresse email" required>
            </div>
            <div class="form-group">
                <input type="password" name="new_password" placeholder="Nouveau mot de passe" required>
            </div>
            <button type="submit" class="btn">Réinitialiser le mot de passe</button>
        </form>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="message">✅ Mot de passe mis à jour avec succès !</div>
        <form action="login.php" method="get">
            <button type="submit" class="btn back-btn">Retour à la page de connexion</button>
        </form>
    <?php elseif (!empty($error)): ?>
        <div class="message"><?php echo $error; ?></div>
    <?php endif; ?>
</div>
</body>
</html>
