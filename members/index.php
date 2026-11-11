<?php
/**
 * Member Management - View All Members
 */
require_once __DIR__ . '/../config/db.php';

$page_title   = 'Member Management';
$current_page = 'members';
$base_path    = '..';

$sql    = "SELECT member_id, member_name, email, phone, department FROM Member ORDER BY member_id DESC";
$result = mysqli_query($conn, $sql);

include __DIR__ . '/../includes/header.php';
?>

<section class="panel">
    <div class="panel-header">
        <h2 class="panel-title">Library Members</h2>
        <a href="add.php" class="btn btn-primary">+ Add New Member</a>
    </div>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Member Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Department</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($result) > 0): ?>
                    <?php while ($member = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?php echo e($member['member_id']); ?></td>
                            <td><?php echo e($member['member_name']); ?></td>
                            <td><?php echo e($member['email']); ?></td>
                            <td><?php echo e($member['phone']); ?></td>
                            <td><?php echo e($member['department']); ?></td>
                            <td class="actions-cell">
                                <a href="edit.php?id=<?php echo (int) $member['member_id']; ?>" class="btn btn-secondary btn-sm">Edit</a>
                                <a href="delete.php?id=<?php echo (int) $member['member_id']; ?>"
                                   class="btn btn-danger btn-sm"
                                   onclick="return confirm('Are you sure you want to delete this member?');">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="empty-state">No members found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
