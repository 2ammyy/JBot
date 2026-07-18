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

    $stmt = $bdd->prepare("SELECT * FROM users WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        header("Location: admin_dashboard.php");
        exit;
    }

    $message = '';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $username = $_POST['username'];
        $email = $_POST['email'];

        if (!empty($username) && !empty($email)) {
            $updateStmt = $bdd->prepare("UPDATE users SET username = :username, email = :email WHERE id = :id");
            $updateStmt->execute([
                'username' => $username,
                'email' => $email,
                'id' => $id
            ]);
            $message = "User updated successfully!";
            $user['username'] = $username;
            $user['email'] = $email;
        } else {
            $message = "Please fill in all fields.";
        }
    }
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User | Admin Panel</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #4361ee;
            --primary-dark: #3a0ca3;
            --secondary: #f72585;
            --dark: #212529;
            --light: #f8f9fa;
            --success: #4cc9f0;
            --warning: #f8961e;
            --danger: #ef233c;
            --gray: #6c757d;
            --border-radius: 8px;
            --box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #f5f7fa;
            color: #333;
            line-height: 1.6;
        }

        .admin-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .admin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 1px solid #e0e0e0;
        }

        .admin-title {
            font-size: 24px;
            font-weight: 600;
            color: var(--dark);
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
            transition: var(--transition);
        }

        .back-link:hover {
            color: var(--primary-dark);
            transform: translateX(-3px);
        }

        .card {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            padding: 30px;
            margin-bottom: 30px;
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }

        .card-title {
            font-size: 20px;
            font-weight: 600;
            color: var(--dark);
        }

        .user-id {
            background: var(--primary);
            color: white;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 500;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--dark);
        }

        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: var(--border-radius);
            font-size: 16px;
            transition: var(--transition);
        }

        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.2);
            outline: none;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 12px 24px;
            border: none;
            border-radius: var(--border-radius);
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
            gap: 8px;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
        }

        .alert {
            padding: 15px;
            border-radius: var(--border-radius);
            margin-bottom: 20px;
            font-weight: 500;
        }

        .alert-success {
            background: rgba(76, 201, 240, 0.2);
            border-left: 4px solid var(--success);
            color: #0a6c74;
        }

        .alert-danger {
            background: rgba(239, 35, 60, 0.1);
            border-left: 4px solid var(--danger);
            color: #b71c1c;
        }

        @media (max-width: 768px) {
            .admin-container {
                padding: 15px;
            }

            .card {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
<div class="admin-container">
    <header class="admin-header">
        <h1 class="admin-title">Edit User</h1>
        <a href="admin_dashboard.php" class="back-link">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>
    </header>

    <main>
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">User Information</h2>
                <span class="user-id">ID: <?= htmlspecialchars($id) ?></span>
            </div>

            <?php if ($message): ?>
                <div class="alert <?= strpos($message, 'success') !== false ? 'alert-success' : 'alert-danger' ?>">
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>

            <form method="post" action="">
                <div class="form-group">
                    <label for="username" class="form-label">Username</label>
                    <input
                            type="text"
                            id="username"
                            name="username"
                            class="form-control"
                            value="<?= htmlspecialchars($user['username']) ?>"
                            required
                    >
                </div>

                <div class="form-group">
                    <label for="email" class="form-label">Email</label>
                    <input
                            type="email"
                            id="email"
                            name="email"
                            class="form-control"
                            value="<?= htmlspecialchars($user['email']) ?>"
                            required
                    >
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Save Changes
                </button>
            </form>
        </div>
    </main>
</div>
</body>
</html>
