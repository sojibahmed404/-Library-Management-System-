<?php
/**
 * Member Management - Delete Member
 */
require_once __DIR__ . '/../config/db.php';

$member_id = (int) ($_GET['id'] ?? 0);
if ($member_id <= 0) {
    redirect_with_flash('index.php', 'error', 'Invalid member ID.');
}

try {
    $stmt = mysqli_prepare($conn, "DELETE FROM Member WHERE member_id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $member_id);
    mysqli_stmt_execute($stmt);

    if (mysqli_stmt_affected_rows($stmt) > 0) {
        redirect_with_flash('index.php', 'success', 'Member deleted successfully.');
    } else {
        redirect_with_flash('index.php', 'error', 'Member not found.');
    }
} catch (mysqli_sql_exception $e) {
    if ($e->getCode() === 1451) {
        redirect_with_flash('index.php', 'error', 'Cannot delete this member because they have existing book issue records.');
    }
    redirect_with_flash('index.php', 'error', 'Failed to delete member: ' . $e->getMessage());
}
?>
