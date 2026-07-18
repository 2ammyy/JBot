<?php
$showError = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
$servername = "localhost";
$username = "root";
$password = "";

try {
$bdd = new PDO("mysql:host=$servername;dbname=java", $username, $password);
$bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$email = $_POST['email'];
$plainPassword = $_POST['password'];

if (!empty($email) && !empty($plainPassword)) {
$check = $bdd->prepare("SELECT * FROM users WHERE email = :email");
$check->execute(['email' => $email]);

if ($check->rowCount() == 0) {
$showError = "L'adresse email n'existe pas";
} else {
$user = $check->fetch(PDO::FETCH_ASSOC);
// Vérification du mot de passe
if (password_verify($plainPassword, $user['password'])) {
// Connexion réussie, redirection vers une page protégée (par exemple, dashboard.php)
    header("Location: http://localhost:8086/");
    exit();

} else {
$showError = "Mot de passe incorrect";
}
}
} else {
$showError = "Tous les champs doivent être remplis.";
}
} catch (PDOException $e) {
echo "Erreur : " . $e->getMessage();
}
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Login | JBot</title>

    
    <link rel="icon" href="log.png" type="image/png" style="width: 100px; height: auto;">

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
            flex-direction: column;
        }

     
        .logo-container {
            position: absolute;
            top: 20px;
            left: 20px;
        }

        .logo {
            height: 40px;
            width: auto;
        }

        .chatbot-container {
            margin-bottom: 30px;
            width: 150px;
            height: 150px;
            position: relative;
        }

        .chatbot {
            width: 120px;
            height: 120px;
            background-color: #5a5eff;
            border-radius: 50%;
            position: relative;
            margin: 0 auto;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .chatbot:before {
            content: "";
            position: absolute;
            bottom: -15px;
            left: 50%;
            transform: translateX(-50%);
            width: 0;
            height: 0;
            border-left: 15px solid transparent;
            border-right: 15px solid transparent;
            border-top: 20px solid #5a5eff;
        }

        .eye {
            width: 25px;
            height: 35px;
            background-color: white;
            border-radius: 50%;
            position: absolute;
            top: 40px;
        }

        .eye.left {
            left: 30px;
        }

        .eye.right {
            right: 30px;
        }

        .pupil {
            width: 12px;
            height: 12px;
            background-color: #000;
            border-radius: 50%;
            position: absolute;
            top: 12px;
            left: 6px;
            transition: all 0.3s ease;
        }

        .eye-lid {
            width: 25px;
            height: 35px;
            background-color: #5a5eff;
            border-radius: 50%;
            position: absolute;
            top: 40px;
            transform: scaleY(0);
            transform-origin: top;
            transition: transform 0.3s ease;
        }

        .eye-lid.left {
            left: 30px;
        }

        .eye-lid.right {
            right: 30px;
        }

        .mouth {
            width: 40px;
            height: 10px;
            background-color: white;
            border-radius: 5px;
            position: absolute;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%);
            overflow: hidden;
        }

        .mouth:before {
            content: "";
            position: absolute;
            width: 100%;
            height: 100%;
            background-color: #000;
            top: 0;
            left: 0;
            border-radius: 5px;
            transform: translateY(0);
            transition: transform 0.3s ease;
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

        .link-row {
            display: flex;
            justify-content: space-between;
            font-size: 14px;
            margin-top: 10px;
        }

        .link-row a {
            color: #5a5eff;
            text-decoration: none;
        }

        .divider {
            margin: 30px 0;
            text-align: center;
            border-bottom: 1px solid #333;
            line-height: 0.1em;
        }

        .divider span {
            background: #121212;
            padding: 0 10px;
            color: #666;
        }

        .google-btn {
            width: 100%;
            padding: 12px;
            border: 1px solid #444;
            background-color: transparent;
            color: white;
            font-size: 16px;
            border-radius: 8px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .google-btn img {
            height: 20px;
        }

        footer {
            text-align: center;
            font-size: 12px;
            color: #888;
            margin-top: 20px;
        }
    </style>
</head>
<body>





<div class="logo-container">
    <img src="log.png" alt="Logo" class="logo" style="width: 100px; height: auto;">
</div>

<div class="chatbot-container">
    <div class="chatbot">
        <div class="eye left">
            <div class="pupil"></div>
        </div>
        <div class="eye right">
            <div class="pupil"></div>
        </div>
        <div class="eye-lid left"></div>
        <div class="eye-lid right"></div>
        <div class="mouth"></div>
    </div>
</div>


<div class="container">
    <form action="login.php" method="post">
        <div class="form-group">
            <input type="text" name="email" placeholder=" email address" required>
        </div>
        <div class="form-group">
            <input type="password" id="password" name="password" placeholder="Password" required>
        </div>

        <button type="submit" class="btn">Log in</button>

        <div class="link-row">
            <a href="forgot_password.php">Forgot password?</a>
            <a href="register.php">Sign up</a>
        </div>



    </form>

    <footer>
        © 2025 JBot • Contact us
    </footer>
</div>


<script>
    const passwordInput = document.getElementById('password');
    const eyeLids = document.querySelectorAll('.eye-lid');
    const pupils = document.querySelectorAll('.pupil');
    const mouth = document.querySelector('.mouth');

    passwordInput.addEventListener('focus', () => {
        eyeLids.forEach(lid => {
            lid.style.transform = 'scaleY(1)';
        });
        mouth.querySelector(':before').style.transform = 'translateY(10px)';
    });

    passwordInput.addEventListener('blur', () => {
        if (passwordInput.value.length === 0) {
            eyeLids.forEach(lid => {
                lid.style.transform = 'scaleY(0)';
            });
            mouth.querySelector(':before').style.transform = 'translateY(0)';
        }
    });

    passwordInput.addEventListener('input', (e) => {
        const hasText = e.target.value.length > 0;

        eyeLids.forEach(lid => {
            lid.style.transform = hasText ? 'scaleY(1)' : 'scaleY(0)';
        });

        mouth.querySelector(':before').style.transform = hasText ? 'translateY(10px)' : 'translateY(0)';

        if (!hasText) {
            pupils.forEach(pupil => {
                pupil.style.transform = 'translate(0, 0)';
            });
        }
    });

    document.addEventListener('mousemove', (e) => {
        if (passwordInput.value.length === 0) {
            const chatbot = document.querySelector('.chatbot');
            const rect = chatbot.getBoundingClientRect();
            const centerX = rect.left + rect.width / 2;
            const centerY = rect.top + rect.height / 2;

            const angle = Math.atan2(e.clientY - centerY, e.clientX - centerX);
            const distance = Math.min(5, Math.sqrt(Math.pow(e.clientX - centerX, 2) + Math.pow(e.clientY - centerY, 2)) / 10);

            const moveX = Math.cos(angle) * distance;
            const moveY = Math.sin(angle) * distance;

            pupils.forEach(pupil => {
                pupil.style.transform = `translate(${moveX}px, ${moveY}px)`;
            });
        }
    });
</script>

</body>
</html>
