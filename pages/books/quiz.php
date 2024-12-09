<?php
session_start();
include('../../includes/db.php');

// Validasi login dan ambil ID user
if (!isset($_SESSION['user_id'])) {
    die('User not logged in.');
}
$userId = $_SESSION['user_id'];

// Cek apakah user sudah memainkan game
$queryCheck = "SELECT played_game, points FROM users WHERE id = ?";
$stmtCheck = $conn->prepare($queryCheck);
$stmtCheck->bind_param("i", $userId);
$stmtCheck->execute();
$resultCheck = $stmtCheck->get_result();
$userData = $resultCheck->fetch_assoc();

if ($userData['played_game'] > 0) {
    die('Anda sudah memainkan game ini.');
}

// Tangkap ID buku
$bookId = isset($_GET['book_id']) ? $_GET['book_id'] : 0;
if ($bookId <= 0) {
    die('Invalid Book ID.');
}

// Ambil data kata Inggris dan Indonesia
$query = "SELECT id, english_word, indonesian_word FROM games WHERE book_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $bookId);
$stmt->execute();
$result = $stmt->get_result();

$wordPairs = [];
while ($row = $result->fetch_assoc()) {
    $wordPairs[] = $row;
}
$stmt->close();

// Pisahkan kata
$englishWords = [];
$indonesianWords = [];

foreach ($wordPairs as $pair) {
    $englishWords[] = ['id' => $pair['id'], 'word' => $pair['english_word']];
    $indonesianWords[] = ['id' => $pair['id'], 'word' => $pair['indonesian_word']];
}

shuffle($englishWords);
shuffle($indonesianWords);
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

      .game-container {
            background-color: #3a539b;
            border-radius: 15px;
            width: 100%;
            max-width: 1000px;
            min-height: 80vh; /* Set minimum height */
            margin: auto; /* Untuk memusatkan jika diperlukan */
            padding: 20px; /* Tambahkan padding agar konten tidak terlalu mepet */
        }

        h1 {
            text-align: center;
            color: white;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .word-column {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .word-pair {
            width: 120px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #3a539b;
            background-color: white;
            margin: 5px 0;
            border-radius: 10px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            transition: background-color 0.3s;
        }

        .word-box {
            display: flex;
            justify-content: center;
        }

      @keyframes flash {
            0% {
                background-color: red;
            }
            50% {
                background-color: white;
            }
            100% {
                background-color: red;
            }
        }

        .flash {
            animation: flash 0.5s infinite;
        }

        .title-game {
            margin: 20px auto;
            width: 80%;
            background-color: #8096d6;
            border-radius: 15px;
        }

        .title-game h1 {
            color: white;
            padding: 10px;
        }

        .score-display {
            color: white;
        }

        .word-pair.selected {
            color: white;
        }

      /* Responsive Layout */
      @media (max-width: 768px) {
        body { flex-direction: column; }
        .sidebar { width: 100%; height: 60px; position: fixed; bottom: 0; flex-direction: row; justify-content: space-around; padding-top: 0; border-top: 1px solid #ddd; }
        
        .game-container {
            background-color: #3a539b;
            border-radius: 15px;
            height 100%;
        }

        .word-box {
            display: flex;
            justify-content: center;
        }
        
        .title-game {
            background-color: white;
        }

        .title-game {
            margin: 20px auto;
            width: 80%;
            background-color: #8096d6;
            border-radius: 15px;
        }

        .title-game h1 {
            color: white;
            padding: 10px;
        }
        
        

      }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
      <div class="sidebar-icon "><img src="https://img.icons8.com/ios-filled/50/6c757d/home.png" alt="Home Icon" /></div>
      <div class="sidebar-icon active"><img src="https://img.icons8.com/?size=100&id=59740&format=png&color=4b82f1" alt="Invoices Icon" /></div>
      <div class="sidebar-icon"><img src="https://img.icons8.com/ios-filled/50/6c757d/time-machine.png" alt="Settings Icon" /></div>
      <div class="sidebar-icon"><img src="https://img.icons8.com/ios-filled/50/6c757d/user.png" alt="Settings Icon" /></div>
    </div>

    <!-- Main Content -->
    <div class="container mt-5 game-container">
        <div class="title-game">
            <h1>match the word</h1>
        </div>

        <div class="row word-box" style="width: 80%; margin: auto;" id="game-container">
            <!-- // nampung kata bahasa inggris -->
            <div class="col-6 word-column" id="english-column">
                <h4 style="color: white; margin-bottom: 2vh;">English</h4>
                <?php foreach ($englishWords as $word): ?>
                    <div class="word-pair mb-2" data-pair-id="<?= $word['id'] ?>" data-language="english"><?= $word['word'] ?></div>
                <?php endforeach; ?>
            </div>

            <!-- // nampung kata bahasa indo -->
            <div class="col-6 word-column" id="indonesian-column">
                <h4 style="color: white; margin-bottom: 2vh;">Indonesia</h4>
                <?php foreach ($indonesianWords as $word): ?>
                    <div class="word-pair mb-2" data-pair-id="<?= $word['id'] ?>" data-language="indonesian"><?= $word['word'] ?></div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="mt-3 text-center col-12 score-display">
            <h4>Your Score: <span id="score">0</span></h4>
            <h4 id="gameStatus"></h4>
            <button id="finishGame" class="btn btn-md btn-warning mt-2 mb-2" style="display:none; margin: auto; ">Finish</button>
        </div>
    </div>

    <!-- // contoh -->
     <br><br><br><br><br>
    

    <script>
        const gameContainer = document.getElementById("game-container");
        const scoreDisplay = document.getElementById("score");
        const finishButton = document.getElementById("finishGame");

        let score = 0;
        let selectedPairs = [];
        const maxScore = <?= count($wordPairs) * 10 ?>;

        gameContainer.addEventListener("click", function (event) {
            const target = event.target;
            if (target.classList.contains("word-pair") && selectedPairs.length < 2) {
                const language = target.getAttribute("data-language");
                const pairId = target.getAttribute("data-pair-id");

                target.classList.toggle("selected");
                target.style.backgroundColor = getRandomColor();

                selectedPairs.push({ pairId, language, element: target });

                if (selectedPairs.length === 2) {
                    checkMatch();
                }
            }
        });

        function getRandomColor() {
            const letters = '0123456789ABCDEF';
            let color = '#';
            for (let i = 0; i < 6; i++) {
                color += letters[Math.floor(Math.random() * 16)];
            }
            return color;
        }

        function checkMatch() {
            const [firstPair, secondPair] = selectedPairs;

            if (firstPair.pairId === secondPair.pairId) {
                firstPair.element.classList.add("correct");
                secondPair.element.classList.add("correct");
                firstPair.element.style.backgroundColor = "green";
                secondPair.element.style.backgroundColor = "green";
                score += 10;
                scoreDisplay.textContent = score;
                selectedPairs = [];
            } else {
                firstPair.element.classList.add("flash");
                secondPair.element.classList.add("flash");

                setTimeout(() => {
                    firstPair.element.classList.remove("flash");
                    secondPair.element.classList.remove("flash");
                    firstPair.element.style.backgroundColor = "white";
                    firstPair.element.style.color = "#3a539b";
                    secondPair.element.style.backgroundColor = "white";
                    secondPair.element.style.color = "#3a539b";
                }, 1000);

                selectedPairs = [];
            }

            if (score === maxScore) {
                finishButton.style.display = "block";
            }
        }

        finishButton.addEventListener("click", function () {
            alert("Congratulations! You've completed the game!");
            saveScore(score);
            window.location.href = "user_score.php"; // Redirect to the homepage
        });

        function saveScore(finalScore) {
            fetch('save_score.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ score: finalScore })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log("Score saved successfully!");
                } else {
                    console.error("Failed to save score.");
                }
            })
            .catch(error => console.error('Error:', error));
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>