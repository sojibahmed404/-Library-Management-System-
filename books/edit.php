<?php
/**
 * Book Management - Edit Book
 */
require_once __DIR__ . '/../config/db.php';

$page_title   = 'Edit Book';
$current_page = 'books';
$base_path    = '..';

$book_id = (int) ($_GET['id'] ?? $_POST['book_id'] ?? 0);
if ($book_id <= 0) {
    redirect_with_flash('index.php', 'error', 'Invalid book ID.');
}

// Fetch existing book record
$stmt = mysqli_prepare($conn, "SELECT book_id, book_name, author, category_id, isbn, quantity FROM Book WHERE book_id = ?");
mysqli_stmt_bind_param($stmt, 'i', $book_id);
mysqli_stmt_execute($stmt);
$res  = mysqli_stmt_get_result($stmt);
$book = mysqli_fetch_assoc($res);

if (!$book) {
    redirect_with_flash('index.php', 'error', 'Book not found.');
}

$error       = '';
$book_name   = $book['book_name'];
$author      = $book['author'];
$category_id = $book['category_id'];
$isbn        = $book['isbn'] ?? '';
$quantity    = $book['quantity'];

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
            $sql = "UPDATE Book SET book_name = ?, author = ?, category_id = ?, isbn = ?, quantity = ? WHERE book_id = ?";
            $upd = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($upd, 'ssisii', $book_name, $author, $category_id, $isbn_value, $quantity, $book_id);
            mysqli_stmt_execute($upd);

            redirect_with_flash('index.php', 'success', 'Book updated successfully.');
        } catch (mysqli_sql_exception $e) {
            if ($e->getCode() === 1062) {
                $error = 'Another book already uses this ISBN.';
            } else {
                $error = 'Failed to update book: ' . $e->getMessage();
            }
        }
    }
}

include __DIR__ . '/../includes/header.php';
?>

<section class="panel form-container">
    <div class="panel-header">
        <h2 class="panel-title">Edit Book #<?php echo (int) $book_id; ?></h2>
        <a href="index.php" class="btn btn-secondary btn-sm">&larr; Back to Books</a>
    </div>

    <?php if ($error !== ''): ?>
        <div class="alert alert-error"><?php echo e($error); ?></div>
    <?php endif; ?>

    <form method="POST" action="edit.php?id=<?php echo (int) $book_id; ?>">
        <input type="hidden" name="book_id" value="<?php echo (int) $book_id; ?>">

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
            <input type="text" id="isbn" name="isbn" class="form-control" value="<?php echo e($isbn); ?>">
        </div>

        <div class="form-group">
            <label for="quantity">Quantity *</label>
            <input type="number" id="quantity" name="quantity" class="form-control" min="0" required value="<?php echo e($quantity); ?>">
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Update Book</button>
            <a href="index.php" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
