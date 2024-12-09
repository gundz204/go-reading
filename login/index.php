<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login - Go Reading!</title>
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css"
      rel="stylesheet"
    />
    <style>
      body {
        background-color: #f4f4f8;
        text-align: center;
      }
      .form-control {
        margin-bottom: 15px;
      }
    </style>
  </head>
  <body>
    <div
      class="container vh-100 d-flex flex-column justify-content-center align-items-center"
    >
      <img src="../img/go_reading.png" alt="Logo" style="width: 200px" />

      <!-- Error Message -->
      <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger w-100 px-4" style="max-width: 350px">
          <?php 
            echo htmlspecialchars($_SESSION['error']); 
            unset($_SESSION['error']); // Hapus error setelah ditampilkan
          ?>
        </div>
      <?php endif; ?>

      <form
        class="w-100 px-4"
        style="max-width: 400px"
        method="post"
        action="login-process.php"
      >
        <input
          type="text"
          class="form-control"
          name="username"
          placeholder="Username"
          required
        />
        <input
          type="password"
          class="form-control"
          name="password"
          placeholder="Password"
          required
        />
        <button type="submit" class="btn btn-primary w-100">Login</button>
        <p class="mt-3"><a href="#">Forgot password?</a></p>
        <p>
          <b>Do you have an account?</b>
          <a href="../register/register.php">Sign Up</a>
        </p>
        <p>or login using</p>
        <div>
          <a href="https://facebook.com">
            <img src="../img/facebook.png" alt="Facebook" style="width: 50px" />
          </a>
          <a href="https://google.com">
            <img src="../img/google.png" alt="Google" style="width: 50px" />
          </a>
        </div>
        <p class="mt-3" style="font-size: 12px">
          By signing up, you agree to our <a href="#">Terms & Privacy Policy</a>
        </p>
      </form>
    </div>
  </body>
</html>
