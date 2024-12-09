<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Go Reading!</title>
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css"
      rel="stylesheet"
    />
    <style>
      body {
        background-color: #f4f4f8;
        text-align: center;
      }
      .btn-custom {
        width: 150px;
        margin: 10px;
        font-size: 18px;
      }
      .footer-text {
        position: fixed;
        bottom: 10px;
        left: 10px;
        font-size: 12px;
        color: #6c757d; /* Warna teks sesuai kebutuhan */
        text-align: left;
        line-height: 1.5;
      }

      /* Media Query untuk Desktop */
      @media (min-width: 992px) {
        .logo {
          max-width: 300px; /* Gambar akan dibatasi maksimum lebar 300px di desktop */
        }
      }
    </style>
  </head>
  <body>
    <div
      class="container vh-100 d-flex flex-column justify-content-center align-items-center"
    >
      <!-- Gambar Logo -->
      <img src="../img/go_reading.png" alt="Logo" class="img-fluid logo" />
      
      <div>
        <button
          onclick="location.href='register.php'"
          class="btn btn-primary btn-custom"
        >
          Sign Up
        </button>
        <button
          onclick="location.href='login.php'"
          class="btn btn-primary btn-custom"
        >
          Sign In
        </button>
      </div>
      
      <p class="footer-text">
        © Public Domain Stories<br />
        All stories available on this web are sourced from the public domain...
      </p>
    </div>
  </body>
</html>
