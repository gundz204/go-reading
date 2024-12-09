<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$id = $_SESSION['user_id'];

require '../../includes/db.php';

// Siapkan query untuk mengambil data user
$query = $conn->prepare("SELECT * FROM users WHERE id = ?");
$query->bind_param('i', $id);
$query->execute();
$result = $query->get_result();
$user = $result->fetch_assoc();

// Jika user tidak ditemukan
if (!$user) {
    echo "User not found.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Profile Page</title>
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
      rel="stylesheet"
    />
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css"
      rel="stylesheet"
    />
    <style>
      body {
        background-color: #f4f4f4;
        color: #4a4a4a;
        font-family: Arial, sans-serif;
      }

      /* Profile */
      .profile {
        max-width: 600px;
        margin: 20px auto;
        padding: 20px;
        background-color: #fff;
        border-radius: 8px;
        text-align: center;
      }

      /* Profile Picture Section */
      .profile-picture-wrapper {
        position: relative;
        display: inline-block;
        border-radius: 50%;
        overflow: hidden;
        background-color: #003366;
        padding: 3px;
      }

      .profile-picture-container {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        overflow: hidden;
      }

      .profile-picture-container img {
        width: 100%;
        height: 100%;
        object-fit: cover;
      }

      /* Profile Info Section */
      .profile-info {
        margin-top: 15px;
        text-align: center;
      }

      .profile-info h2 {
        font-size: 1.5rem;
        margin: 0;
      }

      .profile-info p {
        font-size: 1rem;
        color: #4a4a4a;
        margin: 0;
      }

      .change-picture {
        font-size: 0.9rem;
        color: #007bff;
        cursor: pointer;
        margin: 0;
        margin-bottom: 15px;
      }

      /* Main Button Container */
      .btn-custom {
        display: flex;
        align-items: center;
        justify-content: flex-start;
        width: 100%;
        background-color: #5169a1;
        border-radius: 12px;
        padding: 8px;
        margin-top: 10px;
        position: relative;
        border: none;
        color: #ffffff;
        font-weight: bold;
        cursor: pointer;
      }

      /* White Inner Background for Button Text */
      .btn-custom span {
        background-color: #ffffff;
        padding: 10px 16px;
        border-radius: 8px;
        margin-left: 8px;
        color: #5169a1;
        width: 100%;
      }

      .icon-container {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 6px;
        border-radius: 8px;
        background-color: #ffffff;
        margin-right: 10px;
        height: 36px;
      }

      .icon {
        color: #003366;
        font-size: 1.5rem;
      }

      /* Settings Title */
      .settings-title {
        font-size: 1.25rem;
        font-weight: bold;
        margin: 20px 0 10px;
        text-align: left;
        color: #003366;
      }

      .settings-buttons-wrapper {
        background-color: #5169a1;
        padding: 10px;
        border-radius: 12px;
      }

      .logout-btn {
        text-align: center;
        color: #003366;
        margin-top: 20px;
        font-size: 1.1rem;
      }

      .logout-btn i {
        font-size: 2.5rem;
      }

      .logout-btn a {
        color: #003366;
        text-decoration: none;
        font-weight: bold;
        margin-top: 0;
      }

      .logout-icon-text {
        display: flex;
        flex-direction: column;
        align-items: center;
        margin-top: 0;
      }

      /* New Profile Text */
      .profile-title {
        font-size: 1.75rem;
        font-weight: bold;
        color: #003366;
        margin-bottom: 15px;
      }

      @media (max-width: 768px) {
        body {
          background-color: white;
        }

        .profile {
          margin: 0 auto;
          padding: 20px;
        }
      }
    </style>
  </head>
  <body>
    <div class="profile">
      <div class="back d-flex justify-content-start">
        <a href="../home.php"><img src="https://img.icons8.com/?size=30&id=40217&format=png&color=000000" alt=""></a>
      </div>
      <!-- Profile Text Above Picture -->
      <div class="profile-title">Profile</div>

      <!-- Profile Picture Section -->
      <div class="profile-picture-wrapper">
        <div class="profile-picture-container">
            <img 
                src="<?= $user['profile_image'] ? 'uploads/' . htmlspecialchars($user['profile_image']) : 'https://via.placeholder.com/120'; ?>" 
                alt="Profile Picture" />
        </div>
    </div>

      <!-- Profile Info Section -->
      <div class="profile-info">
        <h2><?= htmlspecialchars($user['username']) ?></h2>
        <p>@<?= htmlspecialchars($user['username']) ?></p>
        <div class="change-picture">
          <a href="change_picture.php" style="text-decoration: none; color: #007bff;">Change Picture</a>
      </div>
      </div>

      <!-- My Achievement Button -->
      <div class="settings-buttons-wrapper">
        <a href="user_score.php" style="text-decoration: none;">
          <button class="btn-custom">
            <div class="icon-container achievement-icon">
              <i class="bi bi-trophy icon"></i>
            </div>
            <span>My Achievement</span>
          </button>
        </a>
      </div>

      <!-- Settings Section -->
      <div class="settings-title"><i class="bi bi-gear"></i> Settings</div>

      <!-- Settings Buttons Wrapper -->
      <div class="settings-buttons-wrapper">
        <a href="change_username.php"style="text-decoration: none;">
          <button class="btn-custom">
              <div class="icon-container">
                  <i class="bi bi-person icon"></i>
              </div>
              <span>Change Username</span>
          </button>
      </a>

        <a href="change_password.php" style="text-decoration: none;">
          <button class="btn-custom">
              <div class="icon-container">
                  <i class="bi bi-lock icon"></i>
              </div>
              <span>Change Password</span>
          </button>
      </a>
      </div>

      <!-- Logout Section -->
      <a href="logout.php" style="text-decoration: none;"> 
        <div class="logout-btn">
          <div class="logout-icon-text">
              <i class="bi bi-box-arrow-right"></i>
              Log out
          </div>
      </div>
      </a>
    </div>

    <!-- Bootstrap Icons and JS -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>
