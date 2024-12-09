<?php
session_start();
include('../../includes/db.php');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    die('User not logged in.');
}

// Capture the currently logged-in user's ID from the session
$userId = $_SESSION['user_id'];

// Query to retrieve books saved by the user
$query = "SELECT b.id, b.title, b.image_url FROM books b JOIN saved_books sb ON b.id = sb.book_id WHERE sb.user_id = ?";
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
    <title>My Library</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
      /* General Reset and Styling */
      * { margin: 0; padding: 0; box-sizing: border-box; }
      body { font-family: Arial, sans-serif; display: flex; height: 100vh; background-color: #e3dfde; }

      /* Sidebar Styling */
      .sidebar { width: 80px; background-color: #ffffff; border-right: 1px solid #ddd; display: flex; flex-direction: column; align-items: center; padding-top: 20px; position: fixed; height: 100vh;}
      .sidebar-icon { width: 40px; height: 40px; margin: 15px 0; display: flex; align-items: center; justify-content: center; cursor: pointer; color: #6c757d; transition: background-color 0.3s ease, color 0.3s ease; }
      .sidebar-icon.active, .sidebar-icon:hover { background-color: #f0f4ff; color: #4b82f1; border-radius: 10px; }
      .sidebar-icon img { width: 24px; height: 24px; }

      /* Header and Content Styling */
      .header { padding: 20px 0; background-color: white; width: 90%; height: 30vh; margin: 0 auto; border-radius: 15px; }
      .header h2 { font-size: 9vh; font-weight: bold; color: #4863a0; margin: 2vh auto; text-align: center; line-height: 20vh;}
      .search-bar { margin: 20px 0; width: 90%; }

      /* Book Grid and Card Styling */
      .stories-container { display: flex; flex-wrap: wrap; justify-content: start; background-color: white; width: 90%; margin: 3vh auto; border-radius: 15px; padding: 2%; }
      .story-card { width: 150px; margin: 20px; text-align: center; cursor: pointer; }
      .story-card img { width: 100%; height: 200px; border-radius: 8px; box-shadow: 2px 2px 8px rgba(0, 0, 0, 0.2); }
      .story-title { font-size: 16px; color: #4863a0; font-weight: bold; margin-top: 8px; }

      /* Responsive Layout */
      @media (max-width: 768px) {
        body { flex-direction: column; }
        .sidebar { width: 100%; height: 60px; position: fixed; bottom: 0; flex-direction: row; justify-content: space-around; padding-top: 0; border-top: 1px solid #ddd; }
        .header h2 { font-size: 5vh; line-height: 10vh;}
        .header { padding: 20px 0; background-color: white; width: 90%; height: 20vh; margin: 0 auto; border-radius: 15px; }
        .stories-container { display: flex; flex-wrap: wrap; justify-content: center; background-color: white; width: 90%; margin: 3vh auto; border-radius: 15px; padding: 2%; }
        .story-card { width: 80px; margin: 20px auto; text-align: center; cursor: pointer; }
        .story-card img { width: 100%; height: 100px; border-radius: 8px; box-shadow: 2px 2px 8px rgba(0, 0, 0, 0.2); }
        .story-title { font-size: 16px; color: #4863a0; font-weight: bold; margin-top: 8px; }
      }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-icon "><a href="../home.php"><img src="https://img.icons8.com/ios-filled/50/6c757d/home.png" alt="Home Icon" /></a></div>
        <div class="sidebar-icon active"><a href="library.php"><img src="https://img.icons8.com/?size=100&id=59740&format=png&color=4b82f1" alt="Library Icon" /></a></div>
        <div class="sidebar-icon "><a href="history.php"><img src="https://img.icons8.com/ios-filled/50/6c757d/time-machine.png" alt="History Icon" /></a></div>
        <div class="sidebar-icon"><a href="profile.php"><img src="https://img.icons8.com/ios-filled/50/6c757d/user.png" alt="profile Icon" /></a></div>
    </div>

    <!-- Main Content -->
    <div class="container mt-5">
        <div class="header mb-4"><h2>MY LIBRARIES</h2></div>
        <div class="container d-flex justify-content-end mb-3 align-items-center" style="width: 90%; ;">
            <div class="bg-light" style="padding: 5px; border-radius: 50%;">
                <img src="https://img.icons8.com/?size=60&id=59740&format=png&color=FCC419" alt="">
            </div>
        </div>
        <div class="stories-container">
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="story-card">
                    <a href="read.php?id=<?php echo $row['id']; ?>">
                        <img src="../../uploads/<?php echo htmlspecialchars($row['image_url']); ?>" alt="<?php echo htmlspecialchars($row['title']); ?>" />
                        <div class="story-title"><?php echo htmlspecialchars($row['title']); ?></div>
                    </a>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
