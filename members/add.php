<?php
/**
 * Member Management - Add New Member
 */
require_once __DIR__ . '/../config/db.php';

$page_title   = 'Add New Member';
$current_page = 'members';
$base_path    = '..';

$error       = '';
$member_name = '';
$email       = '';
$phone       = '';
$department  = '';

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
            $sql  = "INSERT INTO Member (member_name, email, phone, department) VALUES (?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, 'ssss', $member_name, $email_value, $phone, $department);
            mysqli_stmt_execute($stmt);

            redirect_with_flash('index.php', 'success', 'Member added successfully.');
        } catch (mysqli_sql_exception $e) {
            if ($e->getCode() === 1062) {
                $error = 'A member with this email already exists.';
            } else {
                $error = 'Failed to add member: ' . $e->getMessage();
            }
        }
    }
}

include __DIR__ . '/../includes/header.php';
?>

<section class="panel form-container">
    <div class="panel-header">
        <h2 class="panel-title">Member Details</h2>
        <a href="index.php" class="btn btn-secondary btn-sm">&larr; Back to Members</a>
    </div>

    <?php if ($error !== ''): ?>
        <div class="alert alert-error"><?php echo e($error); ?></div>
    <?php endif; ?>

    <form method="POST" action="add.php">
        <div class="form-group">
            <label for="member_name">Member Name *</label>
            <input type="text" id="member_name" name="member_name" class="form-control" required value="<?php echo e($member_name); ?>">
        </div>

        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" class="form-control" placeholder="student@university.edu" value="<?php echo e($email); ?>">
        </div>

        <div class="form-group">
            <label for="phone">Phone</label>
            <input type="text" id="phone" name="phone" class="form-control" placeholder="01711-000000" value="<?php echo e($phone); ?>">
        </div>

        <div class="form-group">
            <label for="department">Department</label>
            <input type="text" id="department" name="department" class="form-control" placeholder="Computer Science" value="<?php echo e($department); ?>">
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Save Member</button>
            <a href="index.php" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
