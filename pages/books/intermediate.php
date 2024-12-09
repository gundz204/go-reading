<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>INTERMEDIATE BOOK</title>
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
      .container {
        flex-grow: 1;
        padding: 20px;
        max-width: 100%;
        text-align: center;
      }

      .header {
        padding: 20px 0;
        background-color: white;
        display: block;
        width: 90%;
        height: 20vh;
        margin: 0 auto;
        border-radius: 15px;
        
      }

      .header h2 {
        font-size: 9vh;
        font-weight: bold;
        color: #4863a0;
        margin: 2vh auto;
      }

      /* Search Bar */
      .search-bar {
        margin: 20px 0;
        width: 90%;
      }

      /* Tags Section */
      .tags {
        font-size: 22px;
        font-weight: bold;
        color: #4863a0;
        margin-top: 10px;
        text-align: left;  
        padding-left: 6%;   
      }

      .tag-btn {
        background-color: #e0e0e0;
        color: #4863a0;
        border: none;
        padding: 8px 12px;
        margin: 5px;
        font-size: 14px;
        border-radius: 5px;
        cursor: pointer;
      }

      /* Stories Grid */
      .stories-container {
        margin-top: 20px;
        display: flex;
        flex-wrap: wrap;
        justify-content: flex-start;
        background-color: white;
        width: 90%;
        margin: 3vh auto;
        border-radius: 15px;
        padding: 2%;
      }

      .story-card {
        width: 90px;
        margin: 10px;
        text-align: center;
        text-decoration: none;
      }

      .story-card img {
        width: 100%;
        height: 100px;
        border-radius: 10px;
        box-shadow: 2px 2px 8px rgba(0, 0, 0, 0.2);
      }

      .story-title {
        font-size: 16px;
        margin-top: 8px;
        color: #4863a0;
        font-weight: bold;
      }

      /* Mobile view */
      @media (max-width: 768px) {
        body {
          flex-direction: column;
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
        .header h2 {
          font-size: 5vh;
          font-weight: bold;
          color: #4863a0;
          line-height: 5vh;
          margin: 2.5vh auto;
        }
        .stories-container {
          margin: 2vh auto;
        }
      }
    </style>
  </head>
  <body>
    <!-- Sidebar -->
    <div class="sidebar">
      <div class="sidebar-icon active"><a href="../home.php"><img src="https://img.icons8.com/ios-filled/50/4b82f1/home.png" alt="Home Icon" /></a></div>
      <div class="sidebar-icon"><a href="library.php"><img src="https://img.icons8.com/?size=100&id=59740&format=png&color=6c757d" alt="Library Icon" /></a></div>
      <div class="sidebar-icon"><a href="history.php"><img src="https://img.icons8.com/ios-filled/50/6c757d/time-machine.png" alt="History Icon" /></a></div>
      <div class="sidebar-icon"><a href="profile.php"><img src="https://img.icons8.com/ios-filled/50/6c757d/user.png" alt="profile Icon" /></a></div>
  </div>

    <!-- Main Content -->
    <div class="container">
      <!-- Header Section -->
      <div class="header">
        <h2>INTERMEDIATE STORIES</h2>
      </div>

      <!-- Search Bar -->
      <div class="d-flex justify-content-center">
        <input
            type="text"
            class="form-control search-bar"
            placeholder="Search"
            oninput="searchBook()"
        />
    </div>

      <!-- Tags Section -->
      <div class="tags ">STORY LIST</div>
      <!-- <div class="d-flex justify-content-start flex-wrap" style="padding-left: 4%;">
        <button class="tag-btn bg-light">Fable</button>
        <button class="tag-btn bg-light">Fairy Tale</button>
        <button class="tag-btn bg-light">Myth</button>
      </div> -->

      <!-- Stories Grid -->
      
    
      <div class="stories-container">
        <?php
        // Sertakan file koneksi database
        include('../../includes/db.php');
    
        // Query untuk mengambil data buku dari kategori 'intermediate' saja
        $query = "SELECT id, title, image_url FROM books WHERE category = 'intermediate'";
        $result = $conn->query($query);
    
        // Periksa jika terdapat data
        if ($result->num_rows > 0) {
            // Loop untuk menampilkan setiap buku dari kategori 'intermediate'
            while ($row = $result->fetch_assoc()) {
                ?>
                <div class="story-card" data-title="<?php echo strtolower($row['title']); ?>">
                    <a href="read.php?id=<?php echo $row['id']; ?>">
                        <img src="../../uploads/<?php echo htmlspecialchars($row['image_url']); ?>" alt="<?php echo htmlspecialchars($row['title']); ?>" />
                        <div class="story-title"><?php echo htmlspecialchars($row['title']); ?></div>
                    </a>
                </div>
                <?php
            }
        } else {
            // Pesan jika tidak ada data buku
            echo '<p>No books found in the intermediate category.</p>';
        }
    
        // Tutup koneksi database
        $conn->close();
        ?>
        
        <!-- Pesan "Book not found" -->
        <div id="no-results" style="display: none; text-align: center; margin-top: 20px;">Book not found</div>
    </div>
    
    </div>    

    <!-- Bootstrap JS -->
     <script>
      function searchBook() {
          const searchInput = document.querySelector('.search-bar').value.toLowerCase();
          const stories = document.querySelectorAll('.story-card');
          const noResults = document.getElementById("no-results");
          let found = false;

          if (searchInput === "") {
              // Tampilkan semua buku jika input kosong
              stories.forEach(story => {
                  story.style.display = "block";
              });
              noResults.style.display = "none";
          } else {
              // Filter buku berdasarkan judul
              stories.forEach(story => {
                  const title = story.getAttribute("data-title");
                  if (title.includes(searchInput)) {
                      story.style.display = "block";
                      found = true;
                  } else {
                      story.style.display = "none";
                  }
              });

              // Tampilkan pesan "Book not found" jika tidak ada buku yang cocok
              noResults.style.display = found ? "none" : "block";
          }
      }
     </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>
