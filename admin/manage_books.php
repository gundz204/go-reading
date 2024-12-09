<?php
require_once '../includes/db.php'; // Include koneksi database

session_start();

// Cek apakah pengguna sudah login, jika tidak, redirect ke login page
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Proses logout jika tombol logout ditekan
if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit;
}

// Ambil data buku dari database
$sql = "SELECT id, title, category, author, content, image_url FROM books";
$result = $conn->query($sql);

// Periksa apakah ada data
$books = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $books[] = $row; // Masukkan setiap baris data ke dalam array $books
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Books</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
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
        img {  }
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
            <h1>Manage Books</h1>
            <p>Manage all books in the library. Add, edit, or delete books.</p>

            <!-- Button to trigger Add Modal -->
            <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#addBookModal">Add Book</button>

            <!-- Books Table -->
            <table class="table table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Author</th>
                        <th>Cover</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($books)): ?>
                        <?php foreach ($books as $book): ?>
                            <tr>
                                <td><?= htmlspecialchars($book['id']) ?></td>
                                <td><?= htmlspecialchars($book['title']) ?></td>
                                <td><?= htmlspecialchars($book['category']) ?></td>
                                <td><?= htmlspecialchars($book['author']) ?></td>
                                <td>
                                    <?php if ($book['image_url']): ?>
                                        <img src="../uploads/<?= htmlspecialchars($book['image_url']) ?>" alt="Cover" style="width: 50px; height: 50px; object-fit: cover;">
                                    <?php else: ?>
                                        <span>No Cover</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <!-- Edit Button -->
                                    <button class="btn btn-primary btn-sm" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#editBookModal" 
                                        data-id="<?= htmlspecialchars($book['id']) ?>"
                                        data-title="<?= htmlspecialchars($book['title']) ?>"
                                        data-category="<?= htmlspecialchars($book['category']) ?>"
                                        data-author="<?= htmlspecialchars($book['author']) ?>"
                                        data-content="<?= htmlspecialchars($book['content']) ?>">
                                        Edit
                                    </button>
                                    <!-- Delete Form -->
                                    <form action="delete_book.php" method="POST" style="display:inline;">
                                        <input type="hidden" name="id" value="<?= htmlspecialchars($book['id']) ?>">
                                        <button class="btn btn-danger btn-sm" onclick="return confirm('Delete this book?')">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center">No books found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add Book Modal -->
    <div class="modal fade" id="addBookModal" tabindex="-1" aria-labelledby="addBookModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="add_book.php" method="POST" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addBookModalLabel">Add Book</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="bookTitle" class="form-label">Title</label>
                            <input type="text" class="form-control" id="bookTitle" name="title" required>
                        </div>
                        <div class="mb-3">
                            <label for="bookCategory" class="form-label">Category</label>
                            <select class="form-control" id="bookCategory" name="category" required>
                                <option value="beginner">Beginner</option>
                                <option value="intermediate">Intermediate</option>
                                <option value="advance">Advance</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="bookAuthor" class="form-label">Author</label>
                            <input type="text" class="form-control" id="bookAuthor" name="author" required>
                        </div>
                        <div class="mb-3">
                            <label for="bookContent" class="form-label">Content</label>
                            <textarea class="form-control" id="bookContent" name="content" rows="5" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="bookCover" class="form-label">Book Cover</label>
                            <input type="file" class="form-control" id="bookCover" name="cover" accept="image/*" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Add Book</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Book Modal -->
    <div class="modal fade" id="editBookModal" tabindex="-1" aria-labelledby="editBookModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="edit_book.php" method="POST" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editBookModalLabel">Edit Book</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="editBookId" name="id">
                        <div class="mb-3">
                            <label for="editBookTitle" class="form-label">Title</label>
                            <input type="text" class="form-control" id="editBookTitle" name="title" required>
                        </div>
                        <div class="mb-3">
                            <label for="editBookCategory" class="form-label">Category</label>
                            <select class="form-control" id="editBookCategory" name="category" required>
                                <option value="beginner">Beginner</option>
                                <option value="intermediate">Intermediate</option>
                                <option value="advance">Advance</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="editBookAuthor" class="form-label">Author</label>
                            <input type="text" class="form-control" id="editBookAuthor" name="author" required>
                        </div>
                        <div class="mb-3">
                            <label for="editBookContent" class="form-label">Content</label>
                            <textarea class="form-control" id="editBookContent" name="content" rows="5" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="editBookCover" class="form-label">Book Cover</label>
                            <input type="file" class="form-control" id="editBookCover" name="cover" accept="image/*">
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
        const editBookModal = document.getElementById('editBookModal');
        editBookModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const id = button.getAttribute('data-id');
            const title = button.getAttribute('data-title');
            const category = button.getAttribute('data-category');
            const author = button.getAttribute('data-author');
            const content = button.getAttribute('data-content');

            editBookModal.querySelector('#editBookId').value = id;
            editBookModal.querySelector('#editBookTitle').value = title;
            editBookModal.querySelector('#editBookCategory').value = category;
            editBookModal.querySelector('#editBookAuthor').value = author;
            editBookModal.querySelector('#editBookContent').value = content;
        });
    </script>
</body>
</html>
