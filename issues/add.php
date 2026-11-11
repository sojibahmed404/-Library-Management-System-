<?php
/**
 * Issue Management - Issue a Book
 */
require_once __DIR__ . '/../config/db.php';

$page_title   = 'Issue a Book';
$current_page = 'issues';
$base_path    = '..';

$error       = '';
$member_id   = '';
$book_id     = '';
$issue_date  = date('Y-m-d');
$return_date = '';
$status      = 'Issued';

$members_result = mysqli_query($conn, "SELECT member_id, member_name, department FROM Member ORDER BY member_name ASC");
$books_result   = mysqli_query($conn, "SELECT book_id, book_name, author, quantity FROM Book ORDER BY book_name ASC");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $member_id   = (int) ($_POST['member_id'] ?? 0);
    $book_id     = (int) ($_POST['book_id'] ?? 0);
    $issue_date  = trim($_POST['issue_date'] ?? '');
    $return_date = trim($_POST['return_date'] ?? '');
    $status      = trim($_POST['status'] ?? 'Issued');

    $allowed_statuses = ['Issued', 'Returned'];
    if ($member_id <= 0 || $book_id <= 0 || $issue_date === '' || !in_array($status, $allowed_statuses, true)) {
        $error = 'Please select a member, a book, a valid issue date, and status.';
    } else {
        $return_date_val = ($return_date === '') ? null : $return_date;
        if ($status === 'Returned' && $return_date_val === null) {
            $return_date_val = date('Y-m-d');
        }

        try {
            mysqli_begin_transaction($conn);

            // Check available quantity if issuing a book
            if ($status === 'Issued') {
                $qty_stmt = mysqli_prepare($conn, "SELECT quantity FROM Book WHERE book_id = ?");
                mysqli_stmt_bind_param($qty_stmt, 'i', $book_id);
                mysqli_stmt_execute($qty_stmt);
                $qty_res = mysqli_stmt_get_result($qty_stmt);
                $book_row = mysqli_fetch_assoc($qty_res);

                if (!$book_row || (int) $book_row['quantity'] <= 0) {
                    mysqli_rollback($conn);
                    $error = 'This book currently has 0 copies available.';
                }
            }

            if ($error === '') {
                $sql  = "INSERT INTO Issue (book_id, member_id, issue_date, return_date, status) VALUES (?, ?, ?, ?, ?)";
                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, 'iisss', $book_id, $member_id, $issue_date, $return_date_val, $status);
                mysqli_stmt_execute($stmt);

                if ($status === 'Issued') {
                    $dec = mysqli_prepare($conn, "UPDATE Book SET quantity = quantity - 1 WHERE book_id = ?");
                    mysqli_stmt_bind_param($dec, 'i', $book_id);
                    mysqli_stmt_execute($dec);
                }

                mysqli_commit($conn);
                redirect_with_flash('index.php', 'success', 'Book issue record created successfully.');
            }
        } catch (mysqli_sql_exception $e) {
            mysqli_rollback($conn);
            $error = 'Failed to issue book: ' . $e->getMessage();
        }
    }
}

include __DIR__ . '/../includes/header.php';
?>

<section class="panel form-container">
    <div class="panel-header">
        <h2 class="panel-title">Issue Book Form</h2>
        <a href="index.php" class="btn btn-secondary btn-sm">&larr; Back to Issues</a>
    </div>

    <?php if ($error !== ''): ?>
        <div class="alert alert-error"><?php echo e($error); ?></div>
    <?php endif; ?>

    <form method="POST" action="add.php">
        <div class="form-group">
            <label for="member_id">Select Member *</label>
            <select id="member_id" name="member_id" class="form-control" required>
                <option value="">-- Select a Member --</option>
                <?php while ($m = mysqli_fetch_assoc($members_result)): ?>
                    <option value="<?php echo (int) $m['member_id']; ?>"
                        <?php echo ((int) $member_id === (int) $m['member_id']) ? 'selected' : ''; ?>>
                        <?php echo e($m['member_name']); ?><?php echo $m['department'] ? ' (' . e($m['department']) . ')' : ''; ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="book_id">Select Book *</label>
            <select id="book_id" name="book_id" class="form-control" required>
                <option value="">-- Select a Book --</option>
                <?php while ($b = mysqli_fetch_assoc($books_result)): ?>
                    <option value="<?php echo (int) $b['book_id']; ?>"
                        <?php echo ((int) $book_id === (int) $b['book_id']) ? 'selected' : ''; ?>>
                        <?php echo e($b['book_name']); ?> — <?php echo e($b['author']); ?> (Available: <?php echo (int) $b['quantity']; ?>)
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="issue_date">Issue Date *</label>
            <input type="date" id="issue_date" name="issue_date" class="form-control" required value="<?php echo e($issue_date); ?>">
        </div>

        <div class="form-group">
            <label for="return_date">Return Date (Optional)</label>
            <input type="date" id="return_date" name="return_date" class="form-control" value="<?php echo e($return_date); ?>">
        </div>

        <div class="form-group">
            <label for="status">Status *</label>
            <select id="status" name="status" class="form-control" required>
                <option value="Issued" <?php echo ($status === 'Issued') ? 'selected' : ''; ?>>Issued</option>
                <option value="Returned" <?php echo ($status === 'Returned') ? 'selected' : ''; ?>>Returned</option>
            </select>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Issue Book</button>
            <a href="index.php" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
