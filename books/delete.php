<?php
/**
 * Book Management - Delete Book
 */
require_once __DIR__ . '/../config/db.php';

$book_id = (int) ($_GET['id'] ?? 0);
if ($book_id <= 0) {
    redirect_with_flash('index.php', 'error', 'Invalid book ID.');
}

try {
    $stmt = mysqli_prepare($conn, "DELETE FROM Book WHERE book_id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $book_id);
    mysqli_stmt_execute($stmt);

    if (mysqli_stmt_affected_rows($stmt) > 0) {
        redirect_with_flash('index.php', 'success', 'Book deleted successfully.');
    } else {
        redirect_with_flash('index.php', 'error', 'Book not found.');
    }
} catch (mysqli_sql_exception $e) {
    // Error 1451 = Foreign key constraint fails (book exists in Issue table)
    if ($e->getCode() === 1451) {
        redirect_with_flash('index.php', 'error', 'Cannot delete this book because it has existing issue records.');
    }
    redirect_with_flash('index.php', 'error', 'Failed to delete book: ' . $e->getMessage());
}
?>
