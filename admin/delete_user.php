<?php
require_once '../includes/db.php'; // Koneksi ke database

// Ambil id user yang ingin dihapus
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id'])) {
    $id = $_POST['id'];

    // Mulai transaksi untuk memastikan penghapusan dilakukan dengan aman
    $conn->begin_transaction();

    try {
        // Hapus data terkait di tabel user_books (menggunakan user_id sebagai foreign key)
        $deleteBooksSql = "DELETE FROM user_books WHERE user_id = ?";
        $stmt = $conn->prepare($deleteBooksSql);
        $stmt->bind_param("i", $id);
        $stmt->execute();

        // Hapus user dari tabel users
        $deleteUserSql = "DELETE FROM users WHERE id = ?";
        $stmt = $conn->prepare($deleteUserSql);
        $stmt->bind_param("i", $id);
        $stmt->execute();

        // Commit transaksi
        $conn->commit();

        // Redirect atau pesan sukses
        header("Location: manage_users.php");
    } catch (Exception $e) {
        // Jika ada error, rollback transaksi
        $conn->rollback();
        echo "Error: " . $e->getMessage();
    }
}
?>
