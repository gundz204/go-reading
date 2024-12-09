<?php
require_once '../includes/db.php';
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Handle logout
if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit;
}

// Fetch all games
$sql = "SELECT g.*, b.title AS book_title 
        FROM games g
        JOIN books b ON g.book_id = b.id";
$result = $conn->query($sql);

$games = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $games[] = $row;
    }
}

// Fetch all books for the dropdown
$sqlBooks = "SELECT id, title FROM books";
$resultBooks = $conn->query($sqlBooks);

$books = [];
if ($resultBooks->num_rows > 0) {
    while ($row = $resultBooks->fetch_assoc()) {
        $books[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Games</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/sidebar.css">
    <style>
        body { min-height: 100vh; overflow-x: hidden; }
        .sidebar { height: 100vh; background-color: #343a40; color: #fff;}
        .sidebar a { color: #adb5bd; text-decoration: none; padding: 15px; display: block; border-bottom: 1px solid #495057; }
        .sidebar a:hover { background-color: #495057; color: #fff; }
        .content { margin-left: 250px; padding: 20px; }
        .sidebar .logo { font-size: 1.5rem; font-weight: bold; text-align: center; padding: 20px 0; }
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
    <div class="container my-4">
        <h1>Manage Games</h1>
        <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#addGameModal">Add Game</button>

        <!-- Games Table -->
        <table class="table table-bordered">
            <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>English Word</th>
                <th>Indonesian Word</th>
                <th>Book</th>
                <th>Pair ID</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php if (!empty($games)): ?>
                <?php foreach ($games as $game): ?>
                    <tr>
                        <td><?= $game['id'] ?></td>
                        <td><?= $game['english_word'] ?></td>
                        <td><?= $game['indonesian_word'] ?></td>
                        <td><?= $game['book_title'] ?></td>
                        <td><?= $game['pair_id'] ?></td>
                        <td>
                            <button class="btn btn-primary btn-sm"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editGameModal"
                                    data-id="<?= $game['id'] ?>"
                                    data-english="<?= $game['english_word'] ?>"
                                    data-indonesian="<?= $game['indonesian_word'] ?>"
                                    data-book="<?= $game['book_id'] ?>"
                                    data-pair="<?= $game['pair_id'] ?>">
                                Edit
                            </button>
                            <form action="delete_game.php" method="POST" style="display:inline;">
                                <input type="hidden" name="id" value="<?= $game['id'] ?>">
                                <button class="btn btn-danger btn-sm" onclick="return confirm('Delete this game?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" class="text-center">No games found</td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add Game Modal -->
<div class="modal fade" id="addGameModal" tabindex="-1" aria-labelledby="addGameModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="add_game.php" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="addGameModalLabel">Add Game</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="englishWord" class="form-label">English Word</label>
                        <input type="text" class="form-control" id="englishWord" name="english_word" required>
                    </div>
                    <div class="mb-3">
                        <label for="indonesianWord" class="form-label">Indonesian Word</label>
                        <input type="text" class="form-control" id="indonesianWord" name="indonesian_word" required>
                    </div>
                    <div class="mb-3">
                        <label for="book" class="form-label">Book</label>
                        <select class="form-control" id="book" name="book_id" required>
                            <?php foreach ($books as $book): ?>
                                <option value="<?= $book['id'] ?>"><?= $book['title'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Add Game</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Game Modal -->
<div class="modal fade" id="editGameModal" tabindex="-1" aria-labelledby="editGameModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="edit_game.php" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="editGameModalLabel">Edit Game</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="editGameId" name="id">

                    <div class="mb-3">
                        <label for="editEnglishWord" class="form-label">English Word</label>
                        <input type="text" class="form-control" id="editEnglishWord" name="english_word" required>
                    </div>
                    <div class="mb-3">
                        <label for="editIndonesianWord" class="form-label">Indonesian Word</label>
                        <input type="text" class="form-control" id="editIndonesianWord" name="indonesian_word" required>
                    </div>
                    <div class="mb-3">
                        <label for="editBook" class="form-label">Book</label>
                        <select class="form-control" id="editBook" name="book_id" required>
                            <?php foreach ($books as $book): ?>
                                <option value="<?= $book['id'] ?>"><?= $book['title'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="editPairId" class="form-label">Pair ID</label>
                        <input type="text" class="form-control" id="editPairId" name="pair_id">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const editGameModal = document.getElementById('editGameModal');
    editGameModal.addEventListener('show.bs.modal', event => {
        const button = event.relatedTarget;

        const id = button.getAttribute('data-id');
        const englishWord = button.getAttribute('data-english');
        const indonesianWord = button.getAttribute('data-indonesian');
        const bookId = button.getAttribute('data-book');
        const pairId = button.getAttribute('data-pair');

        document.getElementById('editGameId').value = id;
        document.getElementById('editEnglishWord').value = englishWord;
        document.getElementById('editIndonesianWord').value = indonesianWord;
        document.getElementById('editBook').value = bookId;
        document.getElementById('editPairId').value = pairId;
    });
</script>
</body>
</html>
