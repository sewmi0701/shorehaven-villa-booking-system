<?php
$pageTitle = $pageTitle ?? 'Seaside stays, thoughtfully made';
$activePage = $activePage ?? '';
$user = current_user();
?><!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Book your next coastal escape at Shorehaven Villa in Mirissa, Sri Lanka.">
    <meta name="theme-color" content="#092c35">
    <title><?= e($pageTitle) ?> · <?= APP_NAME ?></title>
    <link rel="stylesheet" href="<?= e(url('assets/css/style.css')) ?>">
    <meta name="availability-api" content="<?= e(url('api/availability.php')) ?>">
    <script src="<?= e(url('assets/js/main.js')) ?>" defer></script>
</head>
<body>
<header class="site-header">
    <div class="container nav-wrap">
        <a class="brand" href="<?= e(url('index.php')) ?>"><span class="brand-mark">✺</span><span>SHOREHAVEN<small>COASTAL VILLA · MIRISSA</small></span></a>
        <button class="menu-toggle" type="button" aria-label="Toggle navigation" aria-expanded="false">☰</button>
        <nav class="site-nav" aria-label="Main navigation">
            <a class="<?= $activePage === 'home' ? 'active' : '' ?>" href="<?= e(url('index.php')) ?>">Home</a>
            <a class="<?= $activePage === 'rooms' ? 'active' : '' ?>" href="<?= e(url('rooms.php')) ?>">Our Rooms</a>
            <a href="<?= e(url('index.php#experience')) ?>">The Experience</a>
            <?php if ($user): ?>
                <a class="<?= $activePage === 'bookings' ? 'active' : '' ?>" href="<?= e(url('my-bookings.php')) ?>">My Bookings</a>
                <?php if ($user['role'] === 'admin'): ?><a href="<?= e(url('admin/index.php')) ?>">Dashboard</a><?php endif; ?>
                <form action="<?= e(url('logout.php')) ?>" method="post" class="nav-logout"><?= csrf_field() ?><button class="nav-login" type="submit">Log out</button></form>
            <?php else: ?>
                <a class="nav-login" href="<?= e(url('login.php')) ?>">Log in</a>
            <?php endif; ?>
            <a class="btn btn-small btn-primary" href="<?= e(url('rooms.php')) ?>">Book your stay <span aria-hidden="true">↗</span></a>
        </nav>
    </div>
</header>
<main id="main-content">
<?php foreach (take_flashes() as $msg): ?>
<div class="container"><div class="alert alert-<?= e($msg['type']) ?>" role="status"><?= e($msg['message']) ?></div></div>
<?php endforeach; ?>
