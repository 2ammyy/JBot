<?php
$showSuccess = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $servername = "localhost";
    $username = "root";
    $password = "";

    try {
        $bdd = new PDO("mysql:host=$servername;dbname=java", $username, $password);
        $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $id = $_POST['id'];
        $username = $_POST['username'];
        $email = $_POST['email'];
        $plainPassword = $_POST['password'];

        if (!empty($id) && !empty($username) && !empty($email) && !empty($plainPassword)) {
            $check = $bdd->prepare("SELECT * FROM users WHERE email = :email");
            $check->execute(['email' => $email]);

            if ($check->rowCount() > 0) {
               echo "l'adresse email est déjà utilisé";
                exit;
            } else {
                $hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT);
                $stmt = $bdd->prepare("INSERT INTO users (id, username, email, password) VALUES (:id, :username, :email, :password)");
                $stmt->execute([
                    'id' => $id,
                    'username' => $username,
                    'email' => $email,
                    'password' => $hashedPassword
                ]);

                $showSuccess = true;
            }
        }
    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Sign Up | JBot</title>
    <style>
        .top-logo {
            margin-top: 30px;
            margin-bottom: 10px;
            text-align: center;
        }

        .top-logo .logo {
            height: 60px; 
            width: auto;
        }

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
            position: relative;
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

        .popup {
            display: <?php echo $showSuccess ? 'block' : 'none'; ?>;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: #1e1e1e;
            color: #00ff7f;
            padding: 20px 40px;
            border-radius: 12px;
            box-shadow: 0 0 15px rgba(0, 255, 127, 0.3);
            font-size: 18px;
            z-index: 999;
            animation: fadeIn 0.6s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translate(-50%, -40%); }
            to { opacity: 1; transform: translate(-50%, -50%); }
        }
    </style>
</head>
<body>
<div class="container">
  
    <div class="top-logo">
        <img src="log.png" alt="logo" class="logo" style="width: 200px; height: auto;"/>
    </div>

    <form action="register.php" method="post">
        <div class="form-group">
            <input type="number" name="id" placeholder="Numéro (id)" required>
        </div>
        <div class="form-group">
            <input type="text" name="username" placeholder="Nom d'utilisateur" required>
        </div>
        <div class="form-group">
            <input type="email" name="email" placeholder="Adresse email" required>
        </div>
        <div class="form-group">
            <input type="password" name="password" placeholder="Mot de passe" required>
        </div>
        <button type="submit" class="btn">Créer un compte</button>
    </form>

    <div class="popup">
        ✅ Compte créé avec succès !<br>
        <form action="verification.php" method="get" style="display: inline;">
            <button type="submit" style="background: none; border: none; color: #5a5eff; cursor: pointer; text-decoration: underline; font-size: 1em;">
                Cliquez ici pour la vérification.
            </button>
        </form>
    </div>
    <form action="login.php" method="get" style="margin-top: 10px;">
        <button type="submit" class="btn" style="background-color: #444;">Retour à la page de connexion</button>
    </form>

</div>


</body>
</html>
