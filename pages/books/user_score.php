<?php
session_start();
include('../../includes/db.php');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    die('User not logged in.');
}

$userId = $_SESSION['user_id'];

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve user data
$query = "SELECT username, points, profile_image FROM users WHERE id = ?";
$stmt = $conn->prepare($query);

if (!$stmt) {
    die("Error preparing statement: " . $conn->error);
}

$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$userData = $result->fetch_assoc();
$stmt->close();

// Calculate the number of unlocked icons based on points
$totalIcons = 15; // Total number of icons to display
$points = $userData['points'];
$unlockedIcons = floor($points / 100); // Each 25 points unlocks an icon

$query = "SELECT id, points FROM users ORDER BY points DESC";
$result = $conn->query($query);

if (!$result) {
    die("Error retrieving scores: " . $conn->error);
}

// Initialize variables for ranking
$rank = 0;
$totalUsers = 0;
$userRank = null;

while ($row = $result->fetch_assoc()) {
    $totalUsers++;
    $rank++;

    // Check if the current row is the logged-in user
    if ($row['id'] == $userId) {
        $userRank = $rank;
        $points = $row['points'];
    }
}

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Score</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; display: flex; background-color: #e3dfde; min-height: 100vh; }

        /* Sidebar Styling */
        .sidebar { width: 80px; background-color: #ffffff; border-right: 1px solid #ddd; display: flex; flex-direction: column; align-items: center; padding-top: 20px; height: 100vh; position: fixed; }
        .sidebar-icon { width: 40px; height: 40px; margin: 15px 0; display: flex; align-items: center; justify-content: center; cursor: pointer; color: #6c757d; transition: background-color 0.3s ease, color 0.3s ease; }
        .sidebar-icon.active, .sidebar-icon:hover { background-color: #f0f4ff; color: #4b82f1; border-radius: 10px; }
        .sidebar-icon img { width: 24px; height: 24px; }

        /* Content Styling */
        .content { margin-left: 80px; flex-grow: 1; }

        /* Icon Grid Styling */
        .icon-grid { display: flex; flex-wrap: wrap; justify-content: start; gap: 10px; }

        .icon { width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; }
        .icon img { width: 100%; height: 100%; }

        /* Score Display */
        .score-display { padding: 20px; border-radius: 10px; text-align: center; }
        .score-display h2 { font-size: 32px; color: #4863a0; }

        /* Button Styling */
        .btn { margin-top: 20px; }

        /* Responsive Layout */
        @media (max-width: 768px) {
            body { flex-direction: column; }
            .sidebar { width: 100%; height: 60px; position: fixed; bottom: 0; flex-direction: row; justify-content: space-around; padding-top: 0; border-top: 1px solid #ddd; }
            .header { width: 90%; height: 20vh; margin: 0 auto; }
            .header h1 { font-size: 5vh; line-height: 10vh; }
            .icon-grid { justify-content: center; }
            .content { margin: 0 auto; }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-icon"><a href="../home.php"><img src="https://img.icons8.com/ios-filled/50/6c757d/home.png" alt="Home Icon" /></a></div>
        <div class="sidebar-icon"><a href="library.php"><img src="https://img.icons8.com/?size=100&id=59740&format=png&color=6c757d" alt="Invoices Icon" /></a></div>
        <div class="sidebar-icon"><a href="history.php"><img src="https://img.icons8.com/ios-filled/50/6c757d/time-machine.png" alt="Settings Icon" /></a></div>
        <div class="sidebar-icon active"><img src="https://img.icons8.com/ios-filled/50/4b82f1/user.png" alt="User Icon" /></div>
    </div>

    <!-- Main Content -->
    <div class="content">
        <div class="container mt-3">
            <div class="score-display">
                <h2 class="mb-3"><strong>My Achievements</strong></h2>
                <img 
                    src="<?= $userData['profile_image'] ? 'uploads/' . htmlspecialchars($userData['profile_image']) : 'https://via.placeholder.com/120'; ?>" 
                    alt="Profile Picture" 
                    class="mb-3" 
                    style="width: 120px; height: 120px; border-radius: 50%; object-fit: cover;">
                <h2><strong><?= htmlspecialchars($userData['username']) ?></strong></h2>

                <div class="container rounded-pill mb-4 mt-4 p-2" style="background-color: #4863a0;">
                    <div class="row align-items-center " style="margin-left: 2%;">
                        <div class="col-8 d-flex align-items-center gap-2">
                            <div>
                                <img src="https://img.icons8.com/?size=50&id=104236&format=png&color=000000" alt="">
                            </div>
                            <div>
                                <h3 class="text-light">Total Score</h3>
                            </div>
                        </div>
                        <div class="col-4 text-center">
                            <h3 class="text-light"><strong><?= htmlspecialchars($points) ?></strong></h3>
                        </div>
                    </div>
                </div>                

                <!-- Display score and rank -->
                <div class="container rounded-pill mb-4 mt-4 p-2" style="background-color: #4863a0;">
                    <div class="row align-items-center" style="margin-left: 2%;">
                        <div class="col-8 d-flex align-items-center gap-2">
                            <div>
                                <img src="https://img.icons8.com/?size=50&id=16951&format=png&color=000000" alt="">
                            </div>
                            <div>
                                <h3 class="text-light">Rank</h3>
                            </div>
                        </div>
                        <div class="col-4 text-center">
                            <h3 class="text-light"><strong><?= htmlspecialchars($userRank) ?>/<?= htmlspecialchars($totalUsers) ?></strong></h3>
                        </div>
                    </div>
                </div>              
            </div>

            <!-- Icons Display -->
            <div class="icon-grid">
                <?php for ($i = 1; $i <= $totalIcons; $i++): ?>
                    <div class="icon" style="width: 100px; height: 100px; display: flex; justify-content: center; align-items: center; border-radius: 8px;">
                        <?php
                        // Check if user points are sufficient to unlock this icon
                        if ($points >= $i * 100): 
                            $iconSrc = "../../img/$i.png";
                            $altText = "Unlocked Icon $i";
                        else:
                            $iconSrc = "../../img/lock.png";
                            $altText = "Locked Icon";
                        endif;
                        ?>
                        <img src="<?= $iconSrc; ?>" alt="<?= $altText; ?>" style="width: 90px; height: 90px;">
                    </div>
                <?php endfor; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
