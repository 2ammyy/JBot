<?php
session_start();
$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    try {
        $bdd = new PDO("mysql:host=localhost;dbname=java", "root", "");
        $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $bdd->prepare("SELECT * FROM admin WHERE email = :email");
        $stmt->execute(['email' => $email]);

        if ($stmt->rowCount() > 0) {
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($admin['password']) {
                $_SESSION['admin'] = $admin['username'];
                header("Location: admin_dashboard.php");
                exit;
            } else {
                $message = "Mot de passe incorrect.";
            }
        } else {
            $message = "Email introuvable.";
        }
    } catch (PDOException $e) {
        $message = "Erreur de connexion à la base de données.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | Panel Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #6366f1;
            --primary-dark: #4f46e5;
            --dark: #0f172a;
            --light: #f8fafc;
            --error: #ef4444;
            --success: #10b981;
            --gray: #94a3b8;
            --dark-bg: #020617;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--dark-bg);
            color: var(--light);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            background-image: radial-gradient(circle at 10% 20%, rgba(99, 102, 241, 0.1) 0%, transparent 20%);
        }

        .login-card {
            background: rgba(15, 23, 42, 0.8);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            width: 100%;
            max-width: 420px;
            padding: 40px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.05);
            position: relative;
            overflow: hidden;
            z-index: 1;
        }

        .login-card::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(99, 102, 241, 0.1) 0%, transparent 70%);
            animation: rotate 15s linear infinite;
            z-index: -1;
        }

        @keyframes rotate {
            100% {
                transform: rotate(360deg);
            }
        }

        .logo {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo h1 {
            font-size: 24px;
            font-weight: 600;
            color: white;
            margin-bottom: 8px;
        }

        .logo p {
            color: var(--gray);
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 20px;
            position: relative;
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
            padding: 14px 16px;
            background-color: rgba(2, 6, 23, 0.7);
            border: 1px solid #1e293b;
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

        .password-container {
            position: relative;
        }

        .toggle-password {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: var(--gray);
            font-size: 18px;
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
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn:hover {
            background-color: var(--primary-dark);
            transform: translateY(-2px);
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

        .alert-error {
            background-color: rgba(239, 68, 68, 0.15);
            color: var(--error);
            border: 1px solid rgba(239, 68, 68, 0.3);
        }

        .forgot-link {
            display: block;
            text-align: right;
            margin-top: -15px;
            margin-bottom: 20px;
            color: var(--gray);
            font-size: 13px;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .forgot-link:hover {
            color: var(--primary);
        }

        .footer-text {
            text-align: center;
            margin-top: 24px;
            color: var(--gray);
            font-size: 13px;
        }

        @media (max-width: 480px) {
            .login-card {
                padding: 30px 20px;
            }
        }
    </style>
</head>
<body>
<div class="login-card">
    <div class="logo">
        <h1>Connexion Admin</h1>
        <p>Accédez à votre tableau de bord</p>
    </div>

    <?php if ($message): ?>
        <div class="alert alert-error"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form method="post" action="">
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" placeholder="admin@exemple.com" required>
        </div>

        <div class="form-group">
            <label for="password">Mot de passe</label>
            <div class="password-container">
                <input type="password" id="password" name="password" placeholder="••••••••" required>
                <span class="toggle-password" onclick="togglePassword()">👁️</span>
            </div>
        </div>

        <button type="submit" class="btn">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M20 21V19C20 16.7909 18.2091 15 16 15H8C5.79086 15 4 16.7909 4 19V21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M12 11C14.2091 11 16 9.20914 16 7C16 4.79086 14.2091 3 12 3C9.79086 3 8 4.79086 8 7C8 9.20914 9.79086 11 12 11Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            Se connecter
        </button>
    </form>

    <p class="footer-text">© 2025 Admin Panel. Tous droits réservés.</p>
</div>

<script>
    function togglePassword() {
        const passwordInput = document.getElementById('password');
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
        } else {
            passwordInput.type = 'password';
        }
    }
</script>
</body>
</html>
