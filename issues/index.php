<?php
/**
 * Issue Management - View All Issue Records
 */
require_once __DIR__ . '/../config/db.php';

$page_title   = 'Issue Management';
$current_page = 'issues';
$base_path    = '..';

$sql = "SELECT
            i.issue_id,
            m.member_name,
            b.book_name,
            i.issue_date,
            i.return_date,
            i.status
        FROM Issue i
        INNER JOIN Member m ON i.member_id = m.member_id
        INNER JOIN Book b   ON i.book_id = b.book_id
        ORDER BY i.issue_id DESC";
$result = mysqli_query($conn, $sql);

include __DIR__ . '/../includes/header.php';
?>

<section class="panel">
    <div class="panel-header">
        <h2 class="panel-title">Book Issue &amp; Return Records</h2>
        <a href="add.php" class="btn btn-primary">+ Issue a Book</a>
    </div>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Issue ID</th>
                    <th>Member</th>
                    <th>Book</th>
                    <th>Issue Date</th>
                    <th>Return Date</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($result) > 0): ?>
                    <?php while ($issue = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td>#<?php echo e($issue['issue_id']); ?></td>
                            <td><?php echo e($issue['member_name']); ?></td>
                            <td><?php echo e($issue['book_name']); ?></td>
                            <td><?php echo e($issue['issue_date']); ?></td>
                            <td><?php echo $issue['return_date'] ? e($issue['return_date']) : '<em>Not returned</em>'; ?></td>
                            <td>
                                <span class="badge <?php echo ($issue['status'] === 'Returned') ? 'badge-returned' : 'badge-issued'; ?>">
                                    <?php echo e($issue['status']); ?>
                                </span>
                            </td>
                            <td class="actions-cell">
                                <a href="update.php?id=<?php echo (int) $issue['issue_id']; ?>" class="btn btn-secondary btn-sm">Update Status</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="empty-state">No issue records found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
