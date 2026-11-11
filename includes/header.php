<?php
/**
 * Shared HTML Header & Layout Start
 * Expects $page_title (string), $current_page (string), and $base_path ('.' or '..').
 */
if (!isset($base_path)) {
    $base_path = '.';
}
if (!isset($page_title)) {
    $page_title = 'Library Management System';
}
if (!isset($current_page)) {
    $current_page = '';
}
$flash = function_exists('get_flash') ? get_flash() : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($page_title); ?> | Library Management System</title>
    <link rel="stylesheet" href="<?php echo e($base_path); ?>/style.css">
</head>
<body>
<div class="app-layout">
    <?php include __DIR__ . '/sidebar.php'; ?>
    <div class="main-wrapper">
        <header class="top-header">
            <div>
                <h1 class="page-title"><?php echo e($page_title); ?></h1>
                <p class="page-subtitle">Library Management System &middot; PHP &amp; MySQL</p>
            </div>
            <div class="header-badge">Local XAMPP Server</div>
        </header>

        <main class="main-content">
            <?php if ($flash): ?>
                <div class="alert alert-<?php echo $flash['type'] === 'error' ? 'error' : 'success'; ?>">
                    <?php echo e($flash['message']); ?>
                </div>
            <?php endif; ?>
