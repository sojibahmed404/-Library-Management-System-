<?php
/**
 * Book Management - List & Search Books
 */
require_once __DIR__ . '/../config/db.php';

$page_title   = 'Book Management';
$current_page = 'books';
$base_path    = '..';

$search = trim($_GET['search'] ?? '');

if ($search !== '') {
    $like = '%' . $search . '%';
    $sql  = "SELECT b.book_id, b.book_name, b.author, c.category_name, b.isbn, b.quantity
             FROM Book b
             INNER JOIN Category c ON b.category_id = c.category_id
             WHERE b.book_name LIKE ? OR b.author LIKE ? OR b.isbn LIKE ? OR c.category_name LIKE ?
             ORDER BY b.book_id DESC";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'ssss', $like, $like, $like, $like);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
} else {
    $sql = "SELECT b.book_id, b.book_name, b.author, c.category_name, b.isbn, b.quantity
            FROM Book b
            INNER JOIN Category c ON b.category_id = c.category_id
            ORDER BY b.book_id DESC";
    $result = mysqli_query($conn, $sql);
}

include __DIR__ . '/../includes/header.php';
?>

<section class="panel">
    <div class="toolbar">
        <form method="GET" action="index.php" class="search-form">
            <input type="text"
                   name="search"
                   class="search-input"
                   placeholder="Search by book, author, category, or ISBN..."
                   value="<?php echo e($search); ?>">
            <button type="submit" class="btn btn-secondary">Search</button>
            <?php if ($search !== ''): ?>
                <a href="index.php" class="btn btn-secondary">Reset</a>
            <?php endif; ?>
        </form>

        <a href="add.php" class="btn btn-primary">+ Add New Book</a>
    </div>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Book Name</th>
                    <th>Author</th>
                    <th>Category</th>
                    <th>ISBN</th>
                    <th>Quantity</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($result) > 0): ?>
                    <?php while ($book = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?php echo e($book['book_id']); ?></td>
                            <td><?php echo e($book['book_name']); ?></td>
                            <td><?php echo e($book['author']); ?></td>
                            <td><?php echo e($book['category_name']); ?></td>
                            <td><?php echo e($book['isbn']); ?></td>
                            <td><?php echo e($book['quantity']); ?></td>
                            <td class="actions-cell">
                                <a href="edit.php?id=<?php echo (int) $book['book_id']; ?>" class="btn btn-secondary btn-sm">Edit</a>
                                <a href="delete.php?id=<?php echo (int) $book['book_id']; ?>"
                                   class="btn btn-danger btn-sm"
                                   onclick="return confirm('Are you sure you want to delete this book?');">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="empty-state">No books found matching your search.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
