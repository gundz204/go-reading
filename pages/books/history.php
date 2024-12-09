<?php
include '../../includes/db.php'; // Sesuaikan path config.php sesuai struktur folder

// Mulai session
session_start();

// Pastikan ID user ada di session
if (!isset($_SESSION['user_id'])) {
    echo "User not logged in.";
    exit;
}

// Ambil ID user dari session
$userId = $_SESSION['user_id'];

// Query untuk mendapatkan daftar buku yang sudah dibaca, progress-nya, dan URL gambar
$query = "SELECT books.id, books.title, books.image_url, user_books.progress 
          FROM user_books 
          JOIN books ON user_books.book_id = books.id 
          WHERE user_books.user_id = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>History Buku</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Reset styling */
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body { font-family: Arial, sans-serif; display: flex; background-color: #f3f3f3; height: 100vh; }

        /* Sidebar styling */
        .sidebar { width: 80px; background-color: #ffffff; border-right: 1px solid #ddd; display: flex; flex-direction: column; align-items: center; padding-top: 20px; position: fixed; height: 100vh;}
        .sidebar-icon { width: 40px; height: 40px; margin: 15px 0; display: flex; align-items: center; justify-content: center; cursor: pointer; color: #6c757d; transition: background-color 0.3s ease, color 0.3s ease; }
        .sidebar-icon.active, .sidebar-icon:hover { background-color: #f0f4ff; color: #4b82f1; border-radius: 10px; }
        .sidebar-icon img { width: 24px; height: 24px; }

        /* Content area */
        .container { flex-grow: 1; padding: 20px; max-width: 100%; margin-top: 0; margin-left: 20vh;}
        h2 { color: #4a4a8c; text-align: center; margin-bottom: 30px; }

        .book-card { background-color: #fff; border-radius: 10px; padding: 15px; display: flex; align-items: center; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); margin-bottom: 15px; text-decoration: none; color: inherit; }
        .book-image { width: 50px; height: 50px; border-radius: 8px; margin-right: 15px; object-fit: cover; }
        .book-info { flex-grow: 1; }
        .book-title { font-size: 1.1em; color: #333; margin-bottom: 5px; }

        .progress-container { position: relative; background-color: #e0e0e0; border-radius: 5px; height: 8px; width: 100%; margin-top: 5px; }
        .progress-bar { height: 8px; border-radius: 5px; }
        .progress-bar.orange { background-color: #f7a072; }
        .progress-bar.blue { background-color: #6a8dfd; }
        .progress-bar.pink { background-color: #fd6acd; }

        .container h2 { text-align: left; margin-top: 20px; }

        .history-img { display: none; /* Hide by default */ }
        
        /* Mobile view */
        @media (max-width: 768px) {
            body { flex-direction: column; }
            .sidebar { width: 100%; height: 60px; position: fixed; bottom: 0; left: 0; flex-direction: row; justify-content: space-around; padding-top: 0; border-top: 1px solid #ddd; z-index: 99; }
            .sidebar-icon { margin: 0; }
            .history-img { display: block; /* Show on mobile */ text-align: center; /* Center container */ }
            .history-img img { width: 70%; margin: auto; /* Center image */ display: block; }
            .container {
                margin: 0;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-icon "><a href="../home.php"><img src="https://img.icons8.com/ios-filled/50/6c757d/home.png" alt="Home Icon" /></a></div>
        <div class="sidebar-icon"><a href="library.php"><img src="https://img.icons8.com/?size=100&id=59740&format=png&color=6c757d" alt="Library Icon" /></a></div>
        <div class="sidebar-icon active"><img src="https://img.icons8.com/ios-filled/50/4b82f1/time-machine.png" alt="History Icon" /></div>
        <div class="sidebar-icon"><a href="profile.php"><img src="https://img.icons8.com/ios-filled/50/6c757d/user.png" alt="profile Icon" /></a></div>
    </div>

    <!-- Main Content -->
    <div class="container">
        <div class="history-img">
            <img src="../../img/history.png" alt="">
        </div>
        <h2 class="header">History</h2>
        <?php 
        $colors = ["orange", "blue", "pink"];
        $i = 0;
        ?>
        <?php while ($row = $result->fetch_assoc()): ?>
        <a href="read.php?id=<?php echo $row['id']; ?>" class="book-card">
            <img src="../../uploads/<?php echo htmlspecialchars($row['image_url']); ?>" alt="<?php echo htmlspecialchars($row['title']); ?>" class="book-image">
            <div class="book-info">
                <div class="book-title"><?php echo htmlspecialchars($row['title']); ?></div>
                <div class="progress-container">
                    <div class="progress-bar <?php echo $colors[$i % count($colors)]; ?>" style="width: <?php echo $row['progress']; ?>%;"></div>
                </div>
                <span class="progress-text"><?php echo $row['progress']; ?>%</span>
            </div>
        </a>
        <?php $i++; ?>
        <?php endwhile; ?>
    </div>

    <?php
    $stmt->close();
    $conn->close();
    ?>
</body>
</html>
