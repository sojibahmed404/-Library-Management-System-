<?php
/**
 * Issue Management - Update Issue Status & Return Date
 */
require_once __DIR__ . '/../config/db.php';

$page_title   = 'Update Issue Status';
$current_page = 'issues';
$base_path    = '..';

$issue_id = (int) ($_GET['id'] ?? $_POST['issue_id'] ?? 0);
if ($issue_id <= 0) {
    redirect_with_flash('index.php', 'error', 'Invalid issue ID.');
}

// Fetch current issue record along with member and book names
$sql = "SELECT i.issue_id, i.book_id, i.member_id, i.issue_date, i.return_date, i.status,
               m.member_name, b.book_name
        FROM Issue i
        INNER JOIN Member m ON i.member_id = m.member_id
        INNER JOIN Book b   ON i.book_id = b.book_id
        WHERE i.issue_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, 'i', $issue_id);
mysqli_stmt_execute($stmt);
$res   = mysqli_stmt_get_result($stmt);
$issue = mysqli_fetch_assoc($res);

if (!$issue) {
    redirect_with_flash('index.php', 'error', 'Issue record not found.');
}

$error       = '';
$return_date = $issue['return_date'] ?? date('Y-m-d');
$status      = $issue['status'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_return_date = trim($_POST['return_date'] ?? '');
    $new_status      = trim($_POST['status'] ?? '');
    $allowed         = ['Issued', 'Returned'];

    if (!in_array($new_status, $allowed, true)) {
        $error = 'Invalid status selected.';
    } else {
        $return_date_val = ($new_return_date === '') ? null : $new_return_date;
        if ($new_status === 'Returned' && $return_date_val === null) {
            $return_date_val = date('Y-m-d');
        }
        if ($new_status === 'Issued' && $new_return_date === '') {
            $return_date_val = null;
        }

        try {
            mysqli_begin_transaction($conn);

            $old_status = $issue['status'];
            $book_id    = (int) $issue['book_id'];

            // Adjust book stock only when status changes between Issued and Returned
            if ($old_status === 'Issued' && $new_status === 'Returned') {
                $inc = mysqli_prepare($conn, "UPDATE Book SET quantity = quantity + 1 WHERE book_id = ?");
                mysqli_stmt_bind_param($inc, 'i', $book_id);
                mysqli_stmt_execute($inc);
            } elseif ($old_status === 'Returned' && $new_status === 'Issued') {
                $qty_stmt = mysqli_prepare($conn, "SELECT quantity FROM Book WHERE book_id = ?");
                mysqli_stmt_bind_param($qty_stmt, 'i', $book_id);
                mysqli_stmt_execute($qty_stmt);
                $book_row = mysqli_fetch_assoc(mysqli_stmt_get_result($qty_stmt));
                if (!$book_row || (int) $book_row['quantity'] <= 0) {
                    mysqli_rollback($conn);
                    $error = 'Cannot change status back to Issued because 0 copies are available.';
                } else {
                    $dec = mysqli_prepare($conn, "UPDATE Book SET quantity = quantity - 1 WHERE book_id = ?");
                    mysqli_stmt_bind_param($dec, 'i', $book_id);
                    mysqli_stmt_execute($dec);
                }
            }

            if ($error === '') {
                $upd = mysqli_prepare($conn, "UPDATE Issue SET return_date = ?, status = ? WHERE issue_id = ?");
                mysqli_stmt_bind_param($upd, 'ssi', $return_date_val, $new_status, $issue_id);
                mysqli_stmt_execute($upd);

                mysqli_commit($conn);
                redirect_with_flash('index.php', 'success', 'Issue status updated successfully.');
            }
        } catch (mysqli_sql_exception $e) {
            mysqli_rollback($conn);
            $error = 'Failed to update issue status: ' . $e->getMessage();
        }
    }

    $return_date = $new_return_date;
    $status      = $new_status;
}

include __DIR__ . '/../includes/header.php';
?>

<section class="panel form-container">
    <div class="panel-header">
        <h2 class="panel-title">Update Issue #<?php echo (int) $issue_id; ?></h2>
        <a href="index.php" class="btn btn-secondary btn-sm">&larr; Back to Issues</a>
    </div>

    <?php if ($error !== ''): ?>
        <div class="alert alert-error"><?php echo e($error); ?></div>
    <?php endif; ?>

    <div class="sql-note">
        <strong>Member:</strong> <?php echo e($issue['member_name']); ?><br>
        <strong>Book:</strong> <?php echo e($issue['book_name']); ?><br>
        <strong>Issue Date:</strong> <?php echo e($issue['issue_date']); ?>
    </div>

    <form method="POST" action="update.php?id=<?php echo (int) $issue_id; ?>">
        <input type="hidden" name="issue_id" value="<?php echo (int) $issue_id; ?>">

        <div class="form-group">
            <label for="status">Status *</label>
            <select id="status" name="status" class="form-control" required>
                <option value="Issued" <?php echo ($status === 'Issued') ? 'selected' : ''; ?>>Issued</option>
                <option value="Returned" <?php echo ($status === 'Returned') ? 'selected' : ''; ?>>Returned</option>
            </select>
        </div>

        <div class="form-group">
            <label for="return_date">Return Date</label>
            <input type="date" id="return_date" name="return_date" class="form-control" value="<?php echo e($return_date); ?>">
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Save Update</button>
            <a href="index.php" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
