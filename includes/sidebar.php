<?php
/**
 * Shared Sidebar Navigation
 */
$nav_items = [
    'dashboard' => ['label' => 'Dashboard',         'href' => $base_path . '/index.php',         'icon' => '&#9638;'],
    'books'     => ['label' => 'Book Management',   'href' => $base_path . '/books/index.php',   'icon' => '&#128214;'],
    'members'   => ['label' => 'Member Management', 'href' => $base_path . '/members/index.php', 'icon' => '&#128101;'],
    'issues'    => ['label' => 'Issue Management',  'href' => $base_path . '/issues/index.php',  'icon' => '&#128221;'],
    'reports'   => ['label' => 'Reports',           'href' => $base_path . '/reports/index.php', 'icon' => '&#128202;'],
];
?>
<aside class="sidebar">
    <div class="sidebar-brand">
        <span class="brand-icon">&#128218;</span>
        <div>
            <div class="brand-title">UniLibrary</div>
            <div class="brand-sub">DBMS Project</div>
        </div>
    </div>

    <nav class="sidebar-nav" aria-label="Main Navigation">
        <?php foreach ($nav_items as $key => $item): ?>
            <a href="<?php echo e($item['href']); ?>"
               class="nav-link <?php echo ($current_page === $key) ? 'active' : ''; ?>">
                <span class="nav-icon"><?php echo $item['icon']; ?></span>
                <span><?php echo e($item['label']); ?></span>
            </a>
        <?php endforeach; ?>
    </nav>

    <div class="sidebar-footer">
        <small>DB: <code>library_management</code></small>
    </div>
</aside>
