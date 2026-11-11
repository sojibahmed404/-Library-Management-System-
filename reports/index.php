<?php
/**
 * Reports Page - 4-Table INNER JOIN Report
 * Combines Issue, Member, Book, and Category tables.
 */
require_once __DIR__ . '/../config/db.php';

$page_title   = 'Library Issue Report';
$current_page = 'reports';
$base_path    = '..';

$status_filter = trim($_GET['status'] ?? '');

$sql = "SELECT
            i.issue_id,
            m.member_name,
            b.book_name,
            b.author,
            c.category_name,
            i.issue_date,
            i.return_date,
            i.status
        FROM Issue i
        INNER JOIN Member m   ON i.member_id = m.member_id
        INNER JOIN Book b     ON i.book_id = b.book_id
        INNER JOIN Category c ON b.category_id = c.category_id";

if ($status_filter === 'Issued' || $status_filter === 'Returned') {
    $sql .= " WHERE i.status = ? ORDER BY i.issue_id DESC";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 's', $status_filter);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
} else {
    $sql .= " ORDER BY i.issue_id DESC";
    $result = mysqli_query($conn, $sql);
}

include __DIR__ . '/../includes/header.php';
?>

<section class="panel">
    <div class="panel-header">
        <h2 class="panel-title">Complete Book Issue Report (4-Table INNER JOIN)</h2>
        <div class="actions-cell">
            <a href="index.php" class="btn <?php echo ($status_filter === '') ? 'btn-primary' : 'btn-secondary'; ?> btn-sm">All</a>
            <a href="index.php?status=Issued" class="btn <?php echo ($status_filter === 'Issued') ? 'btn-primary' : 'btn-secondary'; ?> btn-sm">Issued</a>
            <a href="index.php?status=Returned" class="btn <?php echo ($status_filter === 'Returned') ? 'btn-primary' : 'btn-secondary'; ?> btn-sm">Returned</a>
        </div>
    </div>

    <div class="sql-note">
        <strong>SQL Query Used:</strong> Combines <code>Issue</code>, <code>Member</code>, <code>Book</code>, and <code>Category</code> using <code>INNER JOIN</code> on their primary &amp; foreign keys.
    </div>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Issue ID</th>
                    <th>Member Name</th>
                    <th>Book Name</th>
                    <th>Author</th>
                    <th>Category Name</th>
                    <th>Issue Date</th>
                    <th>Return Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($result) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td>#<?php echo e($row['issue_id']); ?></td>
                            <td><?php echo e($row['member_name']); ?></td>
                            <td><?php echo e($row['book_name']); ?></td>
                            <td><?php echo e($row['author']); ?></td>
                            <td><?php echo e($row['category_name']); ?></td>
                            <td><?php echo e($row['issue_date']); ?></td>
                            <td><?php echo $row['return_date'] ? e($row['return_date']) : '<em>Not returned</em>'; ?></td>
                            <td>
                                <span class="badge <?php echo ($row['status'] === 'Returned') ? 'badge-returned' : 'badge-issued'; ?>">
                                    <?php echo e($row['status']); ?>
                                </span>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="empty-state">No records found for this report.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
