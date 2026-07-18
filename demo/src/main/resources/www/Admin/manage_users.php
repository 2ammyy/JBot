<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit;
}




try {
    $bdd = new PDO("mysql:host=localhost;dbname=java", "root", "");
    $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $bdd->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

 
    $countUsersStmt = $bdd->prepare("SELECT COUNT(*) as total FROM users");
    $countUsersStmt->execute();
    $userCount = $countUsersStmt->fetch()['total'];

    $usersStmt = $bdd->prepare("SELECT id, username, email FROM users ORDER BY id ASC");
    $usersStmt->execute();
    $users = $usersStmt->fetchAll();

} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    die("An error occurred. Please try again later.");
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | User Management</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary: #6366f1;
            --primary-dark: #4f46e5;
            --primary-light: #a5b4fc;
            --danger: #ef4444;
            --danger-dark: #dc2626;
            --success: #10b981;
            --dark: #1e293b;
            --darker: #0f172a;
            --light: #f8fafc;
            --gray: #94a3b8;
            --gray-dark: #64748b;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f1f5f9;
            color: var(--dark);
            line-height: 1.6;
        }

        .container {
            display: flex;
            min-height: 100vh;
        }

      
        .sidebar {
            width: 280px;
            background: var(--darker);
            color: white;
            padding: 1.5rem;
            position: fixed;
            height: 100vh;
            transition: all 0.3s;
        }

        .sidebar-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: white;
        }

        .sidebar-menu {
            margin-top: 2rem;
        }

        .menu-item {
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
            margin-bottom: 0.5rem;
            border-radius: 0.5rem;
            color: var(--gray);
            text-decoration: none;
            transition: all 0.2s;
        }

        .menu-item:hover, .menu-item.active {
            background-color: rgba(99, 102, 241, 0.2);
            color: white;
        }

        .menu-item i {
            margin-right: 0.75rem;
            width: 24px;
            text-align: center;
        }

      
        .main-content {
            flex: 1;
            margin-left: 280px;
            padding: 2rem;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .page-title {
            font-size: 1.75rem;
            font-weight: 600;
            color: var(--darker);
        }

        .user-menu {
            display: flex;
            align-items: center;
        }

        .user-menu img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 0.75rem;
        }

        .logout-btn {
            background: var(--danger);
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            transition: all 0.2s;
        }

        .logout-btn:hover {
            background: var(--danger-dark);
        }

        .logout-btn i {
            margin-right: 0.5rem;
        }

       
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            border-radius: 0.75rem;
            padding: 1.5rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            transition: transform 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-2px);
        }

        .stat-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1rem;
        }

        .stat-card-title {
            font-size: 0.875rem;
            color: var(--gray-dark);
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .stat-card-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: rgba(99, 102, 241, 0.1);
            color: var(--primary);
        }

        .stat-card-value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--darker);
            margin-bottom: 0.25rem;
        }

        .stat-card-diff {
            font-size: 0.875rem;
            color: var(--success);
            display: flex;
            align-items: center;
        }

        .stat-card-diff.danger {
            color: var(--danger);
        }

        .table-container {
            background: white;
            border-radius: 0.75rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            overflow: hidden;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background-color: var(--primary);
            color: white;
        }

        th {
            padding: 1rem 1.5rem;
            text-align: left;
            font-weight: 500;
        }

        td {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #e2e8f0;
        }

        tr:last-child td {
            border-bottom: none;
        }

        tr:hover td {
            background-color: #f8fafc;
        }

        .badge {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .badge-success {
            background-color: rgba(16, 185, 129, 0.1);
            color: var(--success);
        }

        .badge-warning {
            background-color: rgba(245, 158, 11, 0.1);
            color: #f59e0b;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            font-size: 0.875rem;
            border: none;
        }

        .btn-sm {
            padding: 0.375rem 0.75rem;
            font-size: 0.75rem;
        }

        .btn-primary {
            background-color: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background-color: var(--primary-dark);
        }

        .btn-outline {
            background-color: transparent;
            border: 1px solid var(--gray);
            color: var(--gray-dark);
        }

        .btn-outline:hover {
            background-color: #f1f5f9;
        }

        .btn-danger {
            background-color: var(--danger);
            color: white;
        }

        .btn-danger:hover {
            background-color: var(--danger-dark);
        }

        .actions-cell {
            display: flex;
            gap: 0.5rem;
        }

      
        @media (max-width: 768px) {
            .container {
                flex-direction: column;
            }

            .sidebar {
                transform: translateX(-100%);
                position: fixed;
            }
            .sidebar.active {
                transform: translateX(0);
            }
            .main-content {
                margin-left: 0;
                width: 100%;
            }
            .header {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }
            .search-bar input {
                width: 100%;
            }
            .dashboard-grid {
                grid-template-columns: 1fr;
            }


            .main-content {
                margin-left: 0;
                padding: 1rem;
            }

            .stats-container {
                grid-template-columns: 1fr;
            }

            .header {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }

            .user-menu {
                width: 100%;
                justify-content: flex-end;
            }
        }
    </style>
</head>
<body>
<div class="container">
   
    <aside class="sidebar">
        <div class="sidebar-header">
            <span class="sidebar-title">Admin Panel</span>
        </div>

        <div class="sidebar-user">
            <div class="user-avatar"><?= strtoupper(substr($_SESSION['admin'], 0, 1)) ?></div>
            <div class="user-info">
                <h4><?= htmlspecialchars($_SESSION['admin']) ?></h4>
                <p>Administrator</p>
            </div>
        </div>

        <nav class="sidebar-menu">
            <a href="admin_dashboard.php" class="menu-item active">
                <i class="fas fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
            <a href="manage_users.php" class="menu-item">
                <i class="fas fa-users"></i>
                <span>Manage Users</span>
            </a>
            <a href="add_admin.php" class="menu-item">
                <i class="fas fa-user-plus"></i>
                <span>Add Admin</span>
            </a>
            <a href="admin_logout.php" class="menu-item">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
        </nav>
    </aside>

    <main class="main-content">
        <div class="header">
            <h1 class="page-title">User Management</h1>
            <div class="user-menu">
                <button class="logout-btn" onclick="window.location.href='logout.php'">
                    <i class="fas fa-sign-out-alt"></i>
                    Logout
                </button>
            </div>
        </div>

     
        <div class="stats-container">
            <div class="stat-card">
                <div class="stat-card-header">
                    <span class="stat-card-title">Total Users</span>
                    <div class="stat-card-icon">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
                <h3 class="stat-card-value"><?= htmlspecialchars($userCount) ?></h3>
                <div class="stat-card-diff">
                    <i class="fas fa-arrow-up"></i>
                    <span>12% from last month</span>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-card-header">
                    <span class="stat-card-title">Active Users</span>
                    <div class="stat-card-icon">
                        <i class="fas fa-user-check"></i>
                    </div>
                </div>
                <h3 class="stat-card-value"></h3>
                <div class="stat-card-diff">
                    <i class="fas fa-arrow-up"></i>
                    <span>8% from last month</span>
                </div>
            </div>


        </div>

        
        <div class="table-container">
            <table>
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= htmlspecialchars($user['id']) ?></td>
                        <td><?= htmlspecialchars($user['username']) ?></td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td><span class="badge badge-success">Active</span></td>
                        <td class="actions-cell">
                            <a href="admin_edit_user.php?id=<?= $user['id'] ?>" class="btn btn-primary btn-sm">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <a href="admin_delete_user.php?id=<?= $user['id'] ?>" class="btn btn-danger btn-sm"
                               onclick="return confirm('Are you sure you want to delete this user?');">
                                <i class="fas fa-trash"></i> Delete
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>

<script>
    
    document.querySelectorAll('.btn-danger').forEach(button => {
        button.addEventListener('click', (e) => {
            if (!confirm('Are you sure you want to delete this user?')) {
                e.preventDefault();
            }
        });
    });
</script>
</body>
</html>
