<?php
session_start();
include('../includes/db.php');

// Validasi login dan ambil ID user
if (!isset($_SESSION['user_id'])) {
    die('User not logged in.');
}
$userId = $_SESSION['user_id'];
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>HOME | GO READING</title>
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css"
      rel="stylesheet"
    />
    <style>
      /* Reset styling */
      * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
      }

      body {
        font-family: Arial, sans-serif;
        display: flex;
        height: 100vh;
        background-color: #e3dfde;
      }

      /* Sidebar styling */
      .sidebar {
        width: 80px;
        background-color: #ffffff;
        border-right: 1px solid #ddd;
        display: flex;
        flex-direction: column;
        align-items: center;
        padding-top: 20px;
      }

      .sidebar-icon {
        width: 40px;
        height: 40px;
        margin: 15px 0;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        color: #6c757d;
        transition: background-color 0.3s ease, color 0.3s ease;
      }

      .sidebar-icon.active,
      .sidebar-icon:hover {
        background-color: #f0f4ff;
        color: #4b82f1;
        border-radius: 10px;
      }

      .sidebar-icon img {
        width: 24px;
        height: 24px;
      }

      /* Content area */
      .content {
        flex-grow: 1;
        padding: 20px;
      }

      /* Dropdown styling */
      .dropdown-toggle {
        background-color: #ffffff;
        color: #333;
        border: 1px solid #ddd;
        padding: 10px 15px;
        border-radius: 8px;
        font-weight: bold;
      }

      .dropdown-menu {
        width: 200px;
      }

      .dropdown-item.active {
        background-color: #fce4e4;
        color: #e74c3c;
      }
      h2 {
        font-size: 4rem;
        color: #5069a2;
        text-align: center;
        font-weight: bold;
        margin-top: 50px;
        margin-bottom: 20px;
      }
      .underline {
        width: 50px;
        height: 4px;
        background-color: #5069a2;
        margin: 0 auto 30px;
      }
      .container {
        max-width: 90%;
        text-align: center;
      }
      .card {
        border: 1px solid #ddd;
        border-radius: 15px;
        padding: 25px;
        transition: box-shadow 0.3s ease, border-color 0.3s ease;
        cursor: pointer;
        background-color: #b9d1ffb4;
        margin-bottom: 30px;
      }
      .card.active {
        border: 2px solid #4b82f1;
        box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.15);
      }
      .card-title {
        font-size: 2rem;
        font-weight: bold;
        color: #ffffff;
        display: block;
        width: 100%;
        height: 120px;
        background-color: #5069a2;
        margin: auto;
        border-radius: 15px;
        line-height: 120px;
      }
      .btn-next {
        background-color: #5069a2;
        color: #ffffff;
        border: none;
        padding: 20px 70px;
        border-radius: 20px;
        font-weight: bold;
        font-size: 3vh;
      }
      .btn-back:hover,
      .btn-next:hover {
        background-color: #3a6cc0;
      }
      .button-group {
        display: flex;
        justify-content: center;
        gap: 20px;
        margin-top: 30px;
      }
      .btn-next:disabled {
        background-color: #ddd;
        color: #bbb;
        cursor: not-allowed;
      }

      /* Mobile view: switch sidebar to bottom bar */
      @media (max-width: 768px) {
        body {
          flex-direction: column;
        }

        .container {
        max-width: 90%;
        text-align: center;
      }

        .sidebar {
          width: 100%;
          height: 60px;
          position: fixed;
          bottom: 0;
          left: 0;
          flex-direction: row;
          justify-content: space-around;
          padding-top: 0;
          border-right: none;
          border-top: 1px solid #ddd;
          z-index: 99;
        }

        .content {
          padding-bottom: 70px; /* Adjust for bottom bar height */
        }

        .sidebar-icon {
          margin: 0;
          z-index: 99;
        }

        h2 {
          font-size: 2rem;
          text-align: center;
          margin-top: 50px;
          margin-bottom: 20px;
        }
        .underline {
          width: 50px;
          height: 4px;
          background-color: #5069a2;
          margin: 0 auto 10px;
        }
        .card {
          border: 1px solid #ddd;
          border-radius: 15px;
          padding: 20px;
          transition: box-shadow 0.3s ease, border-color 0.3s ease;
          cursor: pointer;
          background-color: #b9d1ffb4;
          margin-bottom: 0;
        }
        .card-title {
          font-size: 1.5rem;
          font-weight: bold;
          color: #ffffff;
          display: block;
          width: 100%;
          height: 70px;
          background-color: #5069a2;
          margin: auto;
          border-radius: 15px;
          line-height: 70px;
        }
        .button-group {
          margin-bottom: 100px;
        }
        .btn-next {
          background-color: #5069a2;
          color: #ffffff;
          border: none;
          padding: 10px 30px;
          border-radius: 20px;
          font-weight: bold;
          font-size: 1.2rem;
        }
        .btn-back:hover,
        .btn-next:hover {
          background-color: #3a6cc0;
        }
        .button-group {
          display: flex;
          justify-content: center;
          gap: 20px;
          margin-top: 30px;
        }
        .btn-next:disabled {
          background-color: #ddd;
          color: #bbb;
          cursor: not-allowed;
        }
      }
    </style>
  </head>
  <body>
    <!-- Sidebar -->
    <div class="sidebar">
      <div class="sidebar-icon active"><a href="../home.php"><img src="https://img.icons8.com/ios-filled/50/4b82f1/home.png" alt="Home Icon" /></a></div>
      <div class="sidebar-icon"><a href="books/library.php"><img src="https://img.icons8.com/?size=100&id=59740&format=png&color=6c757d" alt="Library Icon" /></a></div>
      <div class="sidebar-icon"><a href="books/history.php"><img src="https://img.icons8.com/ios-filled/50/6c757d/time-machine.png" alt="History Icon" /></a></div>
      <div class="sidebar-icon"><a href="books/profile.php"><img src="https://img.icons8.com/ios-filled/50/6c757d/user.png" alt="profile Icon" /></a></div>
  </div>

    <div class="container">
      <h2>CHOSE YOUR LEVEL</h2>
      <div class="underline"></div>

      <div class="row justify-content-center g-3 mt-4">
        <div class="col-md-4">
          <div class="card" onclick="selectCard(this)">
            <h5 class="card-title">BEGINNER</h5>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card" onclick="selectCard(this)">
            <!-- <img src="https://img.icons8.com/ios/96/000000/edit.png" alt="UI Designer" /> -->
            <h5 class="card-title">INTERMEDIATE</h5>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card" onclick="selectCard(this)">
            <!-- <img src="https://img.icons8.com/ios/96/000000/source-code.png" alt="Developer" /> -->
            <h5 class="card-title">ADVANCE</h5>
          </div>
        </div>
      </div>

      <div class="button-group mt-4">
        <button class="btn-next" id="nextBtn" disabled onclick="nextStep()">
          Next Step
        </button>
      </div>
    </div>

    <!-- Bootstrap JS -->
    <script>
        function selectCard(card) {
          document
            .querySelectorAll(".card")
            .forEach((c) => c.classList.remove("active"));
          card.classList.add("active");
          document.getElementById("nextBtn").disabled = false; // Enable next button
        }
      
        function nextStep() {
          const selectedCard = document.querySelector(".card.active .card-title").textContent;
      
          // Navigate to the selected level's page
          if (selectedCard === "BEGINNER") {
            window.location.href = "./books/beginer.php";
          } else if (selectedCard === "INTERMEDIATE") {
            window.location.href = "./books/intermediate.php";
          } else if (selectedCard === "ADVANCE") {
            window.location.href = "./books/advance.php";
          }
        }
      
        function goBack() {
          alert("Going back.");
        }
      </script>
      
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>
