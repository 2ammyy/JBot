<?php
session_start();

$showSuccess = false;
$errorMsg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "java";

    $admin_username = trim($_POST['username']);
    $admin_email = trim($_POST['email']);
    $admin_password = $_POST['password'];

    if (!empty($admin_username) && !empty($admin_email) && !empty($admin_password)) {
        try {
            $bdd = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
            $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

          
            $check = $bdd->prepare("SELECT * FROM admin WHERE email = :email");
            $check->execute(['email' => $admin_email]);

            if ($check->rowCount() > 0) {
                $errorMsg = "Cette adresse email est déjà utilisée.";
            } else {
                // Hasher le mot de passe
                $hashedPassword = password_hash($admin_password, PASSWORD_DEFAULT);

              
                $stmt = $bdd->prepare("INSERT INTO admin (username, email, password) VALUES (:username, :email, :password)");
                $stmt->execute([
                    'username' => $admin_username,
                    'email' => $admin_email,
                    'password' => $hashedPassword
                ]);
                $showSuccess = true;
            }
        } catch (PDOException $e) {
            $errorMsg = "Erreur : " . $e->getMessage();
        }
    } else {
        $errorMsg = "Veuillez remplir tous les champs.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter Admin | Panel Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #6366f1;
            --primary-dark: #4f46e5;
            --dark: #1e293b;
            --darker: #0f172a;
            --light: #f8fafc;
            --success: #10b981;
            --error: #ef4444;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--darker);
            color: var(--light);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .card {
            background: linear-gradient(145deg, #1e293b, #0f172a);
            border-radius: 16px;
            width: 100%;
            max-width: 480px;
            padding: 40px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.05);
            position: relative;
            overflow: hidden;
        }

        .card::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(99, 102, 241, 0.1) 0%, transparent 70%);
            animation: rotate 15s linear infinite;
        }

        @keyframes rotate {
            100% {
                transform: rotate(360deg);
            }
        }

        .card-content {
            position: relative;
            z-index: 1;
        }

        h1 {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 8px;
            color: white;
            text-align: center;
        }

        .subtitle {
            color: #94a3b8;
            text-align: center;
            margin-bottom: 32px;
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-size: 14px;
            font-weight: 500;
            color: #e2e8f0;
        }

        input {
            width: 100%;
            padding: 12px 16px;
            background-color: rgba(15, 23, 42, 0.7);
            border: 1px solid #334155;
            border-radius: 8px;
            color: var(--light);
            font-size: 14px;
            transition: all 0.3s ease;
        }

        input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2);
        }

        input::placeholder {
            color: #64748b;
        }

        .btn {
            width: 100%;
            padding: 14px;
            background-color: var(--primary);
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 500;
            font-size: 15px;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
        }

        .btn:hover {
            background-color: var(--primary-dark);
            transform: translateY(-1px);
        }

        .btn:active {
            transform: translateY(0);
        }

        .alert {
            padding: 14px;
            border-radius: 8px;
            margin-bottom: 24px;
            font-size: 14px;
            text-align: center;
        }

        .alert-success {
            background-color: rgba(16, 185, 129, 0.15);
            color: var(--success);
            border: 1px solid rgba(16, 185, 129, 0.3);
        }

        .alert-error {
            background-color: rgba(239, 68, 68, 0.15);
            color: var(--error);
            border: 1px solid rgba(239, 68, 68, 0.3);
        }

        .back-link {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-top: 20px;
            color: #94a3b8;
            font-size: 14px;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .back-link svg {
            margin-right: 8px;
            width: 16px;
            height: 16px;
        }

        .back-link:hover {
            color: var(--primary);
        }

        .password-container {
            position: relative;
        }

        .toggle-password {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #64748b;
        }

        @media (max-width: 480px) {
            .card {
                padding: 30px 20px;
            }
        }
    </style>
</head>
<body>
<div class="card">
    <div class="card-content">
        <h1>Ajouter un administrateur</h1>
        <p class="subtitle">Remplissez les informations pour créer un nouveau compte administrateur</p>

        <?php if ($showSuccess): ?>
            <div class="alert alert-success">Administrateur ajouté avec succès!</div>
        <?php elseif ($errorMsg): ?>
            <div class="alert alert-error"><?= htmlspecialchars($errorMsg) ?></div>
        <?php endif; ?>

        <form method="post" action="add_admin.php">
            <div class="form-group">
                <label for="username">Nom d'utilisateur</label>
                <input type="text" id="username" name="username" placeholder="Entrez le nom d'utilisateur" required>
            </div>

            <div class="form-group">
                <label for="email">Adresse email</label>
                <input type="email" id="email" name="email" placeholder="Entrez l'adresse email" required>
            </div>

            <div class="form-group">
                <label for="password">Mot de passe</label>
                <div class="password-container">
                    <input type="password" id="password" name="password" placeholder="Créez un mot de passe" required>
                    <span class="toggle-password" onclick="togglePasswordVisibility()">👁️</span>
                </div>
            </div>

            <button type="submit" class="btn">Créer le compte</button>
        </form>

        <a href="admin_dashboard.php" class="back-link">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Retour au tableau de bord
        </a>
    </div>
</div>

<script>
    function togglePasswordVisibility() {
        const passwordInput = document.getElementById('password');
        const toggleIcon = document.querySelector('.toggle-password');

        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            toggleIcon.textContent = '👁️';
        } else {
            passwordInput.type = 'password';
            toggleIcon.textContent = '👁️';
        }
    }
</script>
</body>
</html>
