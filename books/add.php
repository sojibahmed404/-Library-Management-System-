<?php
/**
 * Book Management - Add New Book
 */
require_once __DIR__ . '/../config/db.php';

$page_title   = 'Add New Book';
$current_page = 'books';
$base_path    = '..';

$error       = '';
$book_name   = '';
$author      = '';
$category_id = '';
$isbn        = '';
$quantity    = 1;

// Load categories for the dropdown
$categories_result = mysqli_query($conn, "SELECT category_id, category_name FROM Category ORDER BY category_name ASC");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $book_name   = trim($_POST['book_name'] ?? '');
    $author      = trim($_POST['author'] ?? '');
    $category_id = (int) ($_POST['category_id'] ?? 0);
    $isbn        = trim($_POST['isbn'] ?? '');
    $quantity    = (int) ($_POST['quantity'] ?? 0);

    if ($book_name === '' || $author === '' || $category_id <= 0 || $quantity < 0) {
        $error = 'Please fill in all required fields and provide a valid non-negative quantity.';
    } else {
        $isbn_value = ($isbn === '') ? null : $isbn;
        try {
            $sql  = "INSERT INTO Book (book_name, author, category_id, isbn, quantity) VALUES (?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, 'ssisi', $book_name, $author, $category_id, $isbn_value, $quantity);
            mysqli_stmt_execute($stmt);

            redirect_with_flash('index.php', 'success', 'Book added successfully.');
        } catch (mysqli_sql_exception $e) {
            if ($e->getCode() === 1062) {
                $error = 'A book with this ISBN already exists.';
            } else {
                $error = 'Failed to add book: ' . $e->getMessage();
            }
        }
    }
}

include __DIR__ . '/../includes/header.php';
?>

<section class="panel form-container">
    <div class="panel-header">
        <h2 class="panel-title">Book Details</h2>
        <a href="index.php" class="btn btn-secondary btn-sm">&larr; Back to Books</a>
    </div>

    <?php if ($error !== ''): ?>
        <div class="alert alert-error"><?php echo e($error); ?></div>
    <?php endif; ?>

    <form method="POST" action="add.php">
        <div class="form-group">
            <label for="book_name">Book Name *</label>
            <input type="text" id="book_name" name="book_name" class="form-control" required value="<?php echo e($book_name); ?>">
        </div>

        <div class="form-group">
            <label for="author">Author *</label>
            <input type="text" id="author" name="author" class="form-control" required value="<?php echo e($author); ?>">
        </div>

        <div class="form-group">
            <label for="category_id">Category *</label>
            <select id="category_id" name="category_id" class="form-control" required>
                <option value="">-- Select Category --</option>
                <?php while ($cat = mysqli_fetch_assoc($categories_result)): ?>
                    <option value="<?php echo (int) $cat['category_id']; ?>"
                        <?php echo ((int) $category_id === (int) $cat['category_id']) ? 'selected' : ''; ?>>
                        <?php echo e($cat['category_name']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="isbn">ISBN</label>
            <input type="text" id="isbn" name="isbn" class="form-control" placeholder="e.g. 978-0132350884" value="<?php echo e($isbn); ?>">
        </div>

        <div class="form-group">
            <label for="quantity">Quantity *</label>
            <input type="number" id="quantity" name="quantity" class="form-control" min="0" required value="<?php echo e($quantity); ?>">
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Save Book</button>
            <a href="index.php" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
