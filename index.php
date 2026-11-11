<?php
/**
 * Dashboard Page (index.php)
 * Displays summary statistics and recent book issues.
 */
require_once __DIR__ . '/config/db.php';

$page_title   = 'Dashboard';
$current_page = 'dashboard';
$base_path    = '.';

// Fetch dashboard statistics using SQL aggregate queries
$total_books_res = mysqli_query($conn, "SELECT COUNT(*) AS total_titles, COALESCE(SUM(quantity), 0) AS total_copies FROM Book");
$books_stats     = mysqli_fetch_assoc($total_books_res);

$total_members_res = mysqli_query($conn, "SELECT COUNT(*) AS total FROM Member");
$total_members     = (int) mysqli_fetch_assoc($total_members_res)['total'];

$issued_res   = mysqli_query($conn, "SELECT COUNT(*) AS total FROM Issue WHERE status = 'Issued'");
$issued_books = (int) mysqli_fetch_assoc($issued_res)['total'];

$returned_res   = mysqli_query($conn, "SELECT COUNT(*) AS total FROM Issue WHERE status = 'Returned'");
$returned_books = (int) mysqli_fetch_assoc($returned_res)['total'];

// Fetch 5 most recent issue records using INNER JOIN across all 4 tables
$recent_sql = "SELECT
                   i.issue_id,
                   m.member_name,
                   b.book_name,
                   c.category_name,
                   i.issue_date,
                   i.return_date,
                   i.status
               FROM Issue i
               INNER JOIN Member m   ON i.member_id = m.member_id
               INNER JOIN Book b     ON i.book_id = b.book_id
               INNER JOIN Category c ON b.category_id = c.category_id
               ORDER BY i.issue_id DESC
               LIMIT 5";
$recent_result = mysqli_query($conn, $recent_sql);

include __DIR__ . '/includes/header.php';
?>

<section class="stats-grid">
    <div class="stat-card">
        <div class="stat-label">Total Books</div>
        <div class="stat-value"><?php echo (int) $books_stats['total_titles']; ?></div>
        <div class="stat-hint"><?php echo (int) $books_stats['total_copies']; ?> total copies in library</div>
    </div>

    <div class="stat-card">
        <div class="stat-label">Total Members</div>
        <div class="stat-value"><?php echo $total_members; ?></div>
        <div class="stat-hint">Registered library members</div>
    </div>

    <div class="stat-card">
        <div class="stat-label">Issued Books</div>
        <div class="stat-value"><?php echo $issued_books; ?></div>
        <div class="stat-hint">Currently borrowed</div>
    </div>

    <div class="stat-card">
        <div class="stat-label">Returned Books</div>
        <div class="stat-value"><?php echo $returned_books; ?></div>
        <div class="stat-hint">Completed returns</div>
    </div>
</section>

<section class="panel">
    <div class="panel-header">
        <h2 class="panel-title">Recent Book Issues</h2>
        <div class="actions-cell">
            <a href="issues/add.php" class="btn btn-primary btn-sm">+ Issue a Book</a>
            <a href="reports/index.php" class="btn btn-secondary btn-sm">View Full Report</a>
        </div>
    </div>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Issue ID</th>
                    <th>Member Name</th>
                    <th>Book Name</th>
                    <th>Category</th>
                    <th>Issue Date</th>
                    <th>Return Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($recent_result) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($recent_result)): ?>
                        <tr>
                            <td>#<?php echo e($row['issue_id']); ?></td>
                            <td><?php echo e($row['member_name']); ?></td>
                            <td><?php echo e($row['book_name']); ?></td>
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
                        <td colspan="7" class="empty-state">No issue records found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
