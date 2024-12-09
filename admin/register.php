<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include '../includes/db.php'; // File koneksi database
    
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $role = 'admin'; // Set default role ke admin

    // Periksa apakah username sudah ada di database
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    // Jika username sudah ada, tampilkan pesan error
    if ($stmt->num_rows > 0) {
        $error_message = "Username already exists, please choose a different username.";
    } else {
        // Jika username belum ada, lanjutkan dengan proses pendaftaran
        $stmt = $conn->prepare("INSERT INTO users (email, username, password, role) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $email, $username, $password, $role);

        if ($stmt->execute()) {
            echo "Admin account created successfully.";
            header("Location: login.php");
            exit;
        } else {
            echo "Error: " . $stmt->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Admin</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa; /* Light background */
        }
        .register-container {
            margin-top: 100px;
        }
        .card {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        .form-control:focus {
            box-shadow: none;
            border-color: #6c757d;
        }
        .btn-primary {
            background-color: #007bff;
            border: none;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container register-container d-flex justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h2 class="text-center mb-4">Register as Admin</h2>
                    <!-- Tampilkan error jika ada -->
                    <?php if (isset($error_message)) : ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($error_message); ?></div>
                    <?php endif; ?>
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" id="email" name="email" class="form-control" placeholder="Enter your email" required>
                        </div>
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" id="username" name="username" class="form-control" placeholder="Enter your username" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" id="password" name="password" class="form-control" placeholder="Enter your password" required>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Register</button>
                        </div>
                    </form>
                </div>
                <div class="card-footer text-center">
                    <small class="text-muted">Already have an account? <a href="login.php" class="text-decoration-none">Login</a></small>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
