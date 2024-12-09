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
// Ambil data user dari database
$sql = "SELECT id, email, username, role, points FROM users";
$result = $conn->query($sql);

// Periksa apakah ada data
$users = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row; // Masukkan setiap baris data ke dalam array $users
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
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
    </style>
</head>
<body>
    <div class="d-flex">
        <nav class="sidebar d-flex flex-column">
            <a href="admin_dashboard.php" style="padding: 0;"><img src="../img/go_reading.png" alt="" width="150px" height="120px"></a>
            <a href="manage_users.php">Manage Users</a>
            <a href="manage_books.php">Manage Books</a>
            <a href="manage_games.php">Manage Games</a>
            <a href="?logout=true" class="logout-btn">Logout</a>
        </nav>
        <div class="container p-3">
            <div class="container">
                <h1>User Management</h1>
                <p>Manage all users from the table below. Click on a user to edit or delete them.</p>
                <div class="table-container">
                    <table class="table table-striped table-bordered">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Email</th>
                                <th>Username</th>
                                <th>Role</th>
                                <th>Points</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($users)): ?>
                                <?php foreach ($users as $user): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($user['id']) ?></td>
                                        <td><?= htmlspecialchars($user['email']) ?></td>
                                        <td><?= htmlspecialchars($user['username']) ?></td>
                                        <td><?= htmlspecialchars($user['role']) ?></td>
                                        <td><?= htmlspecialchars($user['points']) ?></td>
                                        <td>
                                            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editModal" 
                                                onclick='editUser(<?= json_encode($user) ?>)'>Edit</button>
                                            <form action="delete_user.php" method="POST" style="display:inline;">
                                                <input type="hidden" name="id" value="<?= htmlspecialchars($user['id']) ?>">
                                                <button class="btn btn-danger btn-sm" onclick="return confirm('Delete this user?')">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center">No users found</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="update_user.php" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="userId" name="id">
                    <div class="mb-3">
                        <label for="userEmail" class="form-label">Email</label>
                        <input type="email" class="form-control" id="userEmail" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="userName" class="form-label">Username</label>
                        <input type="text" class="form-control" id="userName" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label for="userRole" class="form-label">Role</label>
                        <select class="form-control" id="userRole" name="role" required>
                            <option value="User">User</option>
                            <option value="Admin">Admin</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="userPoints" class="form-label">Points</label>
                        <input type="number" class="form-control" id="userPoints" name="points" required>
                    </div>
                    <div class="mb-3">
                        <label for="userPassword" class="form-label">Password (Leave blank to keep current)</label>
                        <input type="password" class="form-control" id="userPassword" name="password">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function editUser(user) {
            document.getElementById('userId').value = user.id;
            document.getElementById('userEmail').value = user.email;
            document.getElementById('userName').value = user.username;
            document.getElementById('userRole').value = user.role;
            document.getElementById('userPoints').value = user.points;
        }
    </script>
</body>
</html>
