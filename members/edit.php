<?php
/**
 * Member Management - Edit Member
 */
require_once __DIR__ . '/../config/db.php';

$page_title   = 'Edit Member';
$current_page = 'members';
$base_path    = '..';

$member_id = (int) ($_GET['id'] ?? $_POST['member_id'] ?? 0);
if ($member_id <= 0) {
    redirect_with_flash('index.php', 'error', 'Invalid member ID.');
}

$stmt = mysqli_prepare($conn, "SELECT member_id, member_name, email, phone, department FROM Member WHERE member_id = ?");
mysqli_stmt_bind_param($stmt, 'i', $member_id);
mysqli_stmt_execute($stmt);
$res    = mysqli_stmt_get_result($stmt);
$member = mysqli_fetch_assoc($res);

if (!$member) {
    redirect_with_flash('index.php', 'error', 'Member not found.');
}

$error       = '';
$member_name = $member['member_name'];
$email       = $member['email'] ?? '';
$phone       = $member['phone'] ?? '';
$department  = $member['department'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $member_name = trim($_POST['member_name'] ?? '');
    $email       = trim($_POST['email'] ?? '');
    $phone       = trim($_POST['phone'] ?? '');
    $department  = trim($_POST['department'] ?? '');

    if ($member_name === '') {
        $error = 'Member name is required.';
    } else {
        $email_value = ($email === '') ? null : $email;
        try {
            $sql = "UPDATE Member SET member_name = ?, email = ?, phone = ?, department = ? WHERE member_id = ?";
            $upd = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($upd, 'ssssi', $member_name, $email_value, $phone, $department, $member_id);
            mysqli_stmt_execute($upd);

            redirect_with_flash('index.php', 'success', 'Member updated successfully.');
        } catch (mysqli_sql_exception $e) {
            if ($e->getCode() === 1062) {
                $error = 'Another member already uses this email.';
            } else {
                $error = 'Failed to update member: ' . $e->getMessage();
            }
        }
    }
}

include __DIR__ . '/../includes/header.php';
?>

<section class="panel form-container">
    <div class="panel-header">
        <h2 class="panel-title">Edit Member #<?php echo (int) $member_id; ?></h2>
        <a href="index.php" class="btn btn-secondary btn-sm">&larr; Back to Members</a>
    </div>

    <?php if ($error !== ''): ?>
        <div class="alert alert-error"><?php echo e($error); ?></div>
    <?php endif; ?>

    <form method="POST" action="edit.php?id=<?php echo (int) $member_id; ?>">
        <input type="hidden" name="member_id" value="<?php echo (int) $member_id; ?>">

        <div class="form-group">
            <label for="member_name">Member Name *</label>
            <input type="text" id="member_name" name="member_name" class="form-control" required value="<?php echo e($member_name); ?>">
        </div>

        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" class="form-control" value="<?php echo e($email); ?>">
        </div>

        <div class="form-group">
            <label for="phone">Phone</label>
            <input type="text" id="phone" name="phone" class="form-control" value="<?php echo e($phone); ?>">
        </div>

        <div class="form-group">
            <label for="department">Department</label>
            <input type="text" id="department" name="department" class="form-control" value="<?php echo e($department); ?>">
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Update Member</button>
            <a href="index.php" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
