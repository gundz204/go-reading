<?php
require_once '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];

    // Mulai transaksi untuk menjaga konsistensi data
    $conn->begin_transaction();

    try {
        // Hapus entri terkait di tabel user_books
        $sql_user_books = "DELETE FROM user_books WHERE book_id = ?";
        $stmt_user_books = $conn->prepare($sql_user_books);
        $stmt_user_books->bind_param('i', $id);
        $stmt_user_books->execute();

        // Hapus buku dari tabel books
        $sql_books = "DELETE FROM books WHERE id = ?";
        $stmt_books = $conn->prepare($sql_books);
        $stmt_books->bind_param('i', $id);
        $stmt_books->execute();

        // Commit transaksi jika semua berhasil
        $conn->commit();

        // Redirect ke halaman manage_books.php
        header('Location: manage_books.php');
        exit;
    } catch (Exception $e) {
        // Rollback transaksi jika ada kesalahan
        $conn->rollback();
        echo "Failed to delete book. Error: " . $e->getMessage();
    }
}
?>
