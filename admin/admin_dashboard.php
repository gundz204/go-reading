<?php
session_start();
require '../includes/db.php'; // File koneksi ke database

// Cek apakah pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Proses logout
if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit;
}

// Query untuk mendapatkan jumlah user
$query_users = "SELECT COUNT(*) AS total_users FROM users";
$result_users = mysqli_query($conn, $query_users);
$total_users = mysqli_fetch_assoc($result_users)['total_users'];

// Query untuk mendapatkan jumlah buku berdasarkan kategori
$query_books = "SELECT category, COUNT(*) AS total_books FROM books GROUP BY category";
$result_books = mysqli_query($conn, $query_books);

$query_total_books = "SELECT COUNT(*) AS total_books FROM books";
$result_total_books = mysqli_query($conn, $query_total_books);
$total_books_count = mysqli_fetch_assoc($result_total_books)['total_books'];

// Simpan data kategori dan jumlah buku
$categories = [];
$total_books = [];

while ($row = mysqli_fetch_assoc($result_books)) {
    $categories[] = $row['category'];
    $total_books[] = $row['total_books'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            min-height: 100vh;
            overflow-x: hidden;
        }
        .sidebar {
            height: 100vh;
            background-color: #343a40;
            color: #fff;
        }
        .sidebar a {
            color: #adb5bd;
            text-decoration: none;
            display: block;
            padding: 15px;
            border-bottom: 1px solid #495057;
        }
        .sidebar a:hover {
            background-color: #495057;
            color: #fff;
        }
        .logout-btn {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            text-align: center;
            margin-top: 20px;
        }
        .logout-btn:hover {
            background-color: #c82333;
        }
        .stat-box {
            background-color: #f8f9fa;
            border: 1px solid #ddd;
            border-radius: 8px;
            text-align: center;
            padding: 20px;
            margin-bottom: 20px;
        }
        .stat-box h2 {
            font-size: 2.5rem;
            margin: 0;
        }
        .stat-box p {
            margin: 0;
            font-size: 1.2rem;
            color: #6c757d;
        }
    </style>
</head>
<body>
<div class="d-flex">
    <!-- Sidebar -->
    <nav class="sidebar d-flex flex-column">
        <a href="admin_dashboard.php" style="padding: 0;"><img src="../img/go_reading.png" alt="" width="150px" height="120px"></a>
        <a href="manage_users.php">Manage Users</a>
        <a href="manage_books.php">Manage Books</a>
        <a href="manage_games.php">Manage Games</a>
        <a href="?logout=true" class="logout-btn">Logout</a>
    </nav>

    <!-- Main Content -->
    <div class="container p-3">
        <h1>Welcome to Admin Dashboard</h1>
        <p>Use the sidebar to navigate between different sections.</p>

        <div class="row">
            <!-- Total Users -->
            <div class="col-md-6 d-flex justify-content-between flex-column ">
                <div class="stat-box p-6">
                    <h2><?= $total_users; ?></h2>
                    <p>Total Users</p>
                </div>

                <div class="stat-box p-6">
                    <h2><?= $total_books_count; ?></h2>
                    <p>Total Books</p>
                </div>
            </div>

            <!-- Grafik Jumlah Buku -->
            <div class="col-md-6">
                <div class="stat-box">
                    <canvas id="bookChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Data untuk Grafik Buku
    const bookChartCtx = document.getElementById('bookChart').getContext('2d');
    new Chart(bookChartCtx, {
        type: 'bar',
        data: {
            labels: <?= json_encode($categories); ?>,
            datasets: [{
                label: 'Books by Category',
                data: <?= json_encode($total_books); ?>,
                backgroundColor: '#28a745'
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top'
                }
            },
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Categories'
                    }
                },
                y: {
                    title: {
                        display: true,
                        text: 'Number of Books'
                    }
                }
            }
        }
    });
</script>
</body>
</html>
