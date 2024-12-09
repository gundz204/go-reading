<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Go Reading!</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f4f8;
            text-align: center;
        }
        .form-control {
            margin-bottom: 15px;
        }
        .error, .success {
            font-size: 0.9em;
            text-align: left;
        }
        .error {
            color: red;
            display: none;
        }
        .success {
            color: green;
        }
    </style>
</head>
<body>
    <div class="container vh-100 d-flex flex-column justify-content-center align-items-center">
        <img src="../img/go_reading.png" alt="Logo" style="width: 200px;">
        
        <!-- Success Message -->
        <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
            <div class="alert alert-success mt-3 success w-100" style="max-width: 350px">
                Registration successful! continue to <a href="../login/login.php">Login</a>
            </div>
        <?php endif; ?>
        
        <form class="w-100 px-4" style="max-width: 400px;" method="post" action="register-process.php" onsubmit="return validateForm()">
            <input type="email" class="form-control" name="email" placeholder="Email" required>
            <input type="text" class="form-control" name="username" placeholder="Username" required>
            <input type="password" class="form-control" name="password" id="password" placeholder="Password" required>
            <span class="error" id="password_format_error">Password must be at least 8 characters and include both letters and numbers!</span>
            <span class="error" id="password_error">Passwords do not match!</span>
            <input type="password" class="form-control" name="confirm_password" id="confirm_password" placeholder="Confirm Password" required>
            <button type="submit" class="btn btn-primary w-100">Sign Up</button>
            <p class="mt-3">Already have an account? <a href="../login">Sign In</a></p>
        </form>
    </div>

    <script>
        function validateForm() {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            const errorText = document.getElementById('password_error');
            const formatErrorText = document.getElementById('password_format_error');

            // Hide all errors by default
            errorText.style.display = 'none';
            formatErrorText.style.display = 'none';

            // Check if password meets requirements (minimum 8 characters, includes both letters and numbers)
            const passwordPattern = /^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/;
            if (!passwordPattern.test(password)) {
                formatErrorText.style.display = 'block';
                return false;
            }

            // Check if passwords match
            if (password !== confirmPassword) {
                errorText.style.display = 'block';
                return false;
            }

            return true;
        }
    </script>
</body>
</html>
