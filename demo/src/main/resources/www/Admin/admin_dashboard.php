<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit;
}

try {
    $bdd = new PDO("mysql:host=localhost;dbname=java", "root", "");
    $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    
    $stmt = $bdd->query("SELECT COUNT(*) AS total_users FROM users");
    $totalUsers = $stmt->fetch(PDO::FETCH_ASSOC)['total_users'];

   
    $stmt = $bdd->query("SELECT COUNT(*) AS today_users FROM users WHERE DATE(created_at) = CURDATE()");
    $todayUsers = $stmt->fetch(PDO::FETCH_ASSOC)['today_users'];

    
    $stmt2 = $bdd->query("
        SELECT HOUR(created_at) AS hour, COUNT(*) AS count
        FROM users
        WHERE DATE(created_at) = CURDATE()
        GROUP BY HOUR(created_at)
        ORDER BY HOUR(created_at)
    ");
    $stats = $stmt2->fetchAll(PDO::FETCH_ASSOC);

   
    $stmt3 = $bdd->query("
        SELECT username, created_at 
        FROM users
        ORDER BY created_at DESC
        LIMIT 5
    ");
    $recentRegistrations = $stmt3->fetchAll(PDO::FETCH_ASSOC);

    $hours = range(0, 23);
    $counts = array_fill(0, 24, 0);

    foreach ($stats as $row) {
        $counts[$row['hour']] = (int)$row['count'];
    }

    // Find max value for Y-axis
    $maxYValue = max($counts) > 0 ? max($counts) : 1;
} catch (PDOException $e) {
    die("Erreur DB : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #6366f1;
            --primary-dark: #4f46e5;
            --secondary: #10b981;
            --danger: #ef4444;
            --dark: #1e293b;
            --darker: #0f172a;
            --light: #f8fafc;
            --gray: #94a3b8;
            --success: #22c55e;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background-color: #f1f5f9;
            color: var(--dark);
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            line-height: 1.5;
            display: flex;
            min-height: 100vh;
        }

       
        .sidebar {
            background-color: var(--darker);
            width: 280px;
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            transition: all 0.3s ease;
            position: fixed;
            height: 100vh;
            z-index: 100;
        }

        .sidebar-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .sidebar-title {
            color: var(--light);
            font-size: 1.25rem;
            font-weight: 600;
        }

        .sidebar-user {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 2rem;
            padding: 0.75rem;
            border-radius: 0.5rem;
            background-color: rgba(255,255,255,0.05);
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
        }

        .user-info h4 {
            color: white;
            font-size: 0.875rem;
            font-weight: 600;
        }

        .user-info p {
            color: var(--gray);
            font-size: 0.75rem;
        }

        .sidebar-menu {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            flex-grow: 1;
        }

        .menu-item {
            color: var(--gray);
            text-decoration: none;
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            transition: all 0.2s ease;
        }

        .menu-item:hover, .menu-item.active {
            background-color: rgba(99, 102, 241, 0.2);
            color: white;
        }

        .menu-item i {
            width: 24px;
            text-align: center;
        }

        .menu-item.active {
            background-color: var(--primary);
            color: white;
        }

        .sidebar-footer {
            margin-top: auto;
            padding-top: 1rem;
            border-top: 1px solid rgba(255,255,255,0.1);
        }

        
        .main-content {
            flex-grow: 1;
            margin-left: 280px;
            padding: 2rem;
            width: calc(100% - 280px);
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .page-title h1 {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--darker);
        }

        .page-title p {
            color: var(--gray);
            font-size: 0.875rem;
        }

        .header-actions {
            display: flex;
            gap: 1rem;
        }

        .search-bar {
            position: relative;
        }

        .search-bar input {
            padding: 0.5rem 1rem 0.5rem 2.5rem;
            border-radius: 0.5rem;
            border: 1px solid #e2e8f0;
            background-color: white;
            width: 240px;
            transition: all 0.2s ease;
        }

        .search-bar input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2);
        }

        .search-bar i {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray);
        }

        .notification-btn {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            border: none;
            cursor: pointer;
            color: var(--dark);
        }

        .notification-badge {
            position: absolute;
            top: -2px;
            right: -2px;
            background-color: var(--danger);
            color: white;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            font-size: 0.6rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Dashboard Grid */
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .card {
            background-color: white;
            border-radius: 0.75rem;
            padding: 1.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .card-title {
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--gray);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .card-icon {
            width: 40px;
            height: 40px;
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }

        .card-icon.users {
            background-color: var(--primary);
        }

        .card-icon.today {
            background-color: var(--secondary);
        }

        .card-value {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .card-footer {
            display: flex;
            align-items: center;
            font-size: 0.875rem;
            color: var(--gray);
        }

        .card-footer i {
            margin-right: 0.25rem;
        }

        .card-footer.positive {
            color: var(--success);
        }


        .chart-container {
            background-color: white;
            border-radius: 0.75rem;
            padding: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            position: relative;
        }

        .chart-wrapper {
            height: 300px;
            width: 100%;
        }

        .chart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .chart-title {
            font-size: 1.125rem;
            font-weight: 600;
        }

        .chart-actions {
            display: flex;
            gap: 0.5rem;
        }

        .chart-btn {
            padding: 0.375rem 0.75rem;
            border-radius: 0.375rem;
            background-color: white;
            border: 1px solid #e2e8f0;
            font-size: 0.75rem;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .chart-btn:hover {
            background-color: #f8fafc;
        }

        .chart-btn.active {
            background-color: var(--primary);
            color: white;
            border-color: var(--primary);
        }

        
        .activity-container {
            background-color: white;
            border-radius: 0.75rem;
            padding: 1.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .activity-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .activity-title {
            font-size: 1.125rem;
            font-weight: 600;
        }

        .activity-list {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .activity-item {
            display: flex;
            align-items: center;
            padding: 0.75rem;
            border-radius: 0.5rem;
            transition: background-color 0.2s ease;
        }

        .activity-item:hover {
            background-color: #f8fafc;
        }

        .activity-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            margin-right: 1rem;
            flex-shrink: 0;
        }

        .activity-info {
            flex-grow: 1;
        }

        .activity-user {
            font-weight: 600;
            margin-bottom: 0.25rem;
        }

        .activity-description {
            font-size: 0.875rem;
            color: var(--gray);
        }

        .activity-time {
            font-size: 0.75rem;
            color: var(--gray);
            white-space: nowrap;
        }

       
        @media (max-width: 1024px) {
            .sidebar {
                width: 240px;
            }
            .main-content {
                margin-left: 240px;
                width: calc(100% - 240px);
            }
        }

        @media (max-width: 768px) {
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
        }

        
        .btn {
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            border: none;
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
            border: 1px solid var(--primary);
            color: var(--primary);
        }

        .btn-outline:hover {
            background-color: rgba(99, 102, 241, 0.1);
        }

        .text-primary {
            color: var(--primary);
        }
    </style>
</head>
<body>

<aside class="sidebar">
    <div class="sidebar-header">
        <span class="sidebar-title">Admin Dashboard</span>
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
        <div class="page-title">
            <h1>Dashboard Overview</h1>
            <p>Welcome back, <?= htmlspecialchars($_SESSION['admin']) ?>! Here's what's happening with your system today.</p>
        </div>

        <div class="header-actions">
            <div class="search-bar">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="Search...">
            </div>
            <button class="notification-btn">
                <i class="fas fa-bell"></i>
                <span class="notification-badge">3</span>
            </button>
        </div>
    </div>

    <div class="dashboard-grid">
        <div class="card">
            <div class="card-header">
                <span class="card-title">Total Users</span>
                <div class="card-icon users">
                    <i class="fas fa-users"></i>
                </div>
            </div>
            <div class="card-value"><?= number_format($totalUsers) ?></div>
            <a href="manage_users.php" class="card-footer positive">
                <i class="fas fa-arrow-up"></i>
                <span>View all users</span>
            </a>
        </div>

        <div class="card">
            <div class="card-header">
                <span class="card-title">Today's Registrations</span>
                <div class="card-icon today">
                    <i class="fas fa-user-plus"></i>
                </div>
            </div>
            <div class="card-value"><?= number_format($todayUsers) ?></div>
            <div class="card-footer">
                <i class="fas fa-clock"></i>
                <span>As of <?= date('H:i') ?></span>
            </div>
        </div>
    </div>


    <div class="chart-container">
        <div class="chart-header">
            <h2 class="chart-title">User Registrations (Last 24 Hours)</h2>
            <div class="chart-actions">
                <button class="chart-btn active">24h</button>
                <button class="chart-btn">7d</button>
                <button class="chart-btn">30d</button>
            </div>
        </div>
        <div class="chart-wrapper">
            <canvas id="userChart"></canvas>
        </div>
    </div>

 
    <div class="activity-container">
        <div class="activity-header">
            <h2 class="activity-title">Recent Registrations</h2>
            <a href="manage_users.php" class="btn btn-outline">
                <i class="fas fa-eye"></i>
                <span>View All</span>
            </a>
        </div>

        <div class="activity-list">
            <?php foreach ($recentRegistrations as $user): ?>
                <div class="activity-item">
                    <div class="activity-avatar"><?= strtoupper(substr($user['username'], 0, 1)) ?></div>
                    <div class="activity-info">
                        <div class="activity-user"><?= htmlspecialchars($user['username']) ?></div>
                        <div class="activity-description">New user registration</div>
                    </div>
                    <div class="activity-time"><?= date('H:i', strtotime($user['created_at'])) ?></div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</main>

<script>
    
    const ctx = document.getElementById('userChart').getContext('2d');
    const userChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?= json_encode(array_map(function($h) { return $h . ":00"; }, $hours)) ?>,
            datasets: [{
                label: "Registrations",
                data: <?= json_encode($counts) ?>,
                backgroundColor: '#6366f1',
                borderColor: '#6366f1',
                borderWidth: 1,
                borderRadius: 4,
                barPercentage: 0.8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: '#1e293b',
                    titleFont: { size: 14, weight: 'bold' },
                    bodyFont: { size: 12 },
                    padding: 12,
                    cornerRadius: 8,
                    displayColors: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1,
                        precision: 0
                    },
                    min: 0,
                    max: <?= $maxYValue ?>,
                    grid: {
                        color: '#e2e8f0',
                        drawBorder: false
                    }
                },
                x: {
                    grid: {
                        display: false,
                        drawBorder: false
                    },
                    ticks: {
                        color: '#64748b'
                    }
                }
            }
        }
    });


    document.addEventListener('DOMContentLoaded', function() {
        const sidebar = document.querySelector('.sidebar');
        const menuToggle = document.createElement('button');
        menuToggle.innerHTML = '<i class="fas fa-bars"></i>';
        menuToggle.style.position = 'fixed';
        menuToggle.style.top = '1rem';
        menuToggle.style.left = '1rem';
        menuToggle.style.zIndex = '1000';
        menuToggle.style.background = 'var(--primary)';
        menuToggle.style.color = 'white';
        menuToggle.style.border = 'none';
        menuToggle.style.borderRadius = '50%';
        menuToggle.style.width = '40px';
        menuToggle.style.height = '40px';
        menuToggle.style.display = 'none';
        menuToggle.style.justifyContent = 'center';
        menuToggle.style.alignItems = 'center';
        menuToggle.style.cursor = 'pointer';
        menuToggle.style.boxShadow = '0 2px 5px rgba(0,0,0,0.2)';
        document.body.appendChild(menuToggle);

        function checkScreenSize() {
            if (window.innerWidth <= 768) {
                menuToggle.style.display = 'flex';
            } else {
                menuToggle.style.display = 'none';
                sidebar.classList.remove('active');
            }
        }

        window.addEventListener('resize', checkScreenSize);
        checkScreenSize();

        menuToggle.addEventListener('click', function() {
            sidebar.classList.toggle('active');
        });
    });
</script>
</body>
</html>
